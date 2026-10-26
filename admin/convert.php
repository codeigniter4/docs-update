<?php

declare(strict_types=1);

/**
 * @return array<string, string>
 */
function loadEnvConfig(): array {
    $envFile = __DIR__ . '/../.env';
    $config = [
        'DOCS_PROJECT_PATH' => '',
    ];

    if (file_exists($envFile)) {
        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $line = trim($line);
            if (strpos($line, '#') === 0 || empty($line)) {
                continue;
            }

            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                $config[trim($key)] = trim($value);
            }
        }
    }

    foreach (array_keys($config) as $key) {
        $value = getenv($key);

        if ($value !== false) {
            $config[$key] = $value;
        }
    }

    return $config;
}

/**
 * Writes `$message` and a newline, in the ANSI SGR `$style` when `$stream` is a terminal and `NO_COLOR` is unset.
 *
 * @param resource $stream
 */
function writeLine(string $message, string $style = '', $stream = STDOUT): void {
    if ($style !== '' && (string) getenv('NO_COLOR') === '' && stream_isatty($stream)) {
        $message = sprintf("\033[%sm%s\033[0m", $style, $message);
    }

    fwrite($stream, $message . "\n");
}

function getSourceDirectory(): string {
    $projectPath = loadEnvConfig()['DOCS_PROJECT_PATH'];

    if ($projectPath === '') {
        writeLine('Error: DOCS_PROJECT_PATH is not set.', STYLE_ERROR, STDERR);
        writeLine('Set it in the environment or in .env to the user_guide_src/source folder of a CodeIgniter4 checkout.', '', STDERR);
        exit(1);
    }

    $projectPath = rtrim($projectPath, '/') . '/';

    if (!is_dir($projectPath)) {
        writeLine(sprintf('Error: DOCS_PROJECT_PATH does not exist: %s', $projectPath), STYLE_ERROR, STDERR);
        exit(1);
    }

    return $projectPath;
}

function checkPandocVersion(): void {
    exec('pandoc --version', $output, $exitCode);

    if ($exitCode !== 0) {
        writeLine(sprintf('Error: pandoc is not installed. Install pandoc %s.', PANDOC_VERSION), STYLE_ERROR, STDERR);
        exit(1);
    }

    $installed = trim(str_replace('pandoc', '', $output[0]));

    if ($installed !== PANDOC_VERSION) {
        writeLine(sprintf('Warning: pandoc %s is installed, but the conversion is verified against pandoc %s.', $installed, PANDOC_VERSION), STYLE_WARNING, STDERR);
    }
}

const STYLE_ERROR   = '31';
const STYLE_WARNING = '33';
const STYLE_SUCCESS = '32';
const STYLE_STEP    = '1';

define('PANDOC_VERSION', '3.12.1');
define('PANDOC_WORKERS', 8);
define('SOURCE_DIR', getSourceDirectory());
define('TARGET_DIR', __DIR__ . '/../docs/');

define('BEFORE_FILTERS', [
    'toctree',
    'includes',
    'docLink',
    'classReference',
    'listIndentation',
]);
define('AFTER_FILTERS', [
    'images',
    'alerts',
    'dollarSign',
    'classReference',
    'listIndentation',
    'toctree',
    'contents',
    'docLink',
    'admonitions',
    'includes',
]);

function loadFilters(): void {
    $filters = array_unique(array_merge(BEFORE_FILTERS, AFTER_FILTERS));
    foreach ($filters as $filter) {
        include_once(__DIR__ . '/filters/' . $filter . '.php');
    }
}

/**
 * Filtered copy of `$path`, saved next to it as `$path_` until applyAfterFilters() deletes it.
 */
function applyBeforeFilters(string $path): string {
    $file = [
        'path'   => $path . '_',
        'folder' => dirname($path),
    ];

    copy($path, $file['path']);

    foreach (BEFORE_FILTERS as $action) {
        $function = 'before_filter_' . $action;

        if (function_exists($function)) {
            updateFile($file, $function);
        }
    }

    return $file['path'];
}

function applyAfterFilters(string $path, string $sourceCopy): void {
    if (file_exists($sourceCopy)) {
        unlink($sourceCopy);
    }

    $file = [
      'path'   => $path,
      'folder' => dirname($path),
    ];
    foreach (AFTER_FILTERS as $action) {
        $function = 'after_filter_' . $action;

        if (function_exists($function)) {
            updateFile($file, $function);
        }
    }
}

/**
 * @param array{path: string, folder: string} $file
 * @param callable-string                     $function
 */
function updateFile(array $file, string $function): void {
    $data = file_get_contents($file['path']);
    $result = $function($data, $file['folder'], $file['path']);

    if ($result === null) {
        writeLine(sprintf('Error: %s failed on %s (%s), leaving the file unfiltered', $function, $file['path'], preg_last_error_msg()), STYLE_ERROR, STDERR);
        return;
    }

    file_put_contents($file['path'], $result);
}

/**
 * `$path` relative to the source folder, or `$path` itself when it lies outside it.
 */
function sourceRelative(string $path): string {
    foreach ([realpath(SOURCE_DIR), rtrim(SOURCE_DIR, '/')] as $root) {
        if ($path === $root || str_starts_with($path, $root . '/')) {
            return ltrim(substr($path, strlen($root)), '/');
        }
    }

    return $path;
}

/**
 * Markdown link from a page in `$directory` to the page `$page`, both relative to the docs root.
 */
function relativeLink(string $directory, string $page): string {
    $from = $directory === '' ? [] : explode('/', $directory);
    $to   = explode('/', $page);

    while ($from !== [] && count($to) > 1 && $from[0] === $to[0]) {
        array_shift($from);
        array_shift($to);
    }

    return str_repeat('../', count($from)) . implode('/', $to) . '.md';
}

function clean(string $target): void {
    if (!is_dir($target)) {
        mkdir($target, 0755, true);
        return;
    }

    $dir = opendir($target);

    while (($file = readdir($dir)) !== false) {
        if ($file == '.' || $file == '..') {
            continue;
        }

        $targetFile = $target . '/' . $file;

        if (is_dir($targetFile)) {
            clean($targetFile);
            rmdir($targetFile);
        } else {
            unlink($targetFile);
        }
    }
    closedir($dir);
}

function copyDirectory(string $source, string $target): bool {
    if (!is_dir($source)) {
        return false;
    }

    if (!is_dir($target)) {
        mkdir($target, 0755, true);
    }

    $dir = opendir($source);
    while (($file = readdir($dir)) !== false) {
        if ($file == '.' || $file == '..') {
            continue;
        }

        $sourceFile = $source . '/' . $file;
        $targetFile = $target . '/' . $file;

        if (is_dir($sourceFile)) {
            copyDirectory($sourceFile, $targetFile);
        } else {
            echo "Copying asset: $sourceFile\n";
            copy($sourceFile, $targetFile);
        }
    }
    closedir($dir);
    return true;
}

function copyAssets(): void {
    printf("Copying directory: %simages to %simages\n", SOURCE_DIR, TARGET_DIR);
    copyDirectory(SOURCE_DIR . 'images', TARGET_DIR . 'images');

    createMkDocsAssets();
}

function createMkDocsAssets(): void {
    $staticDir = SOURCE_DIR . '_static/';
    $assetsDir = TARGET_DIR . 'assets/';

    if (!is_dir($staticDir)) {
        writeLine('Warning: _static directory not found, skipping MkDocs assets creation', STYLE_WARNING);
        return;
    }

    if (!is_dir($assetsDir)) {
        mkdir($assetsDir, 0755, true);
    }

    $iconMapping = [
        'ci-logo-text.svg' => 'flame.svg',
        'favicon.ico' => 'favicon.ico'
    ];

    foreach ($iconMapping as $sourceFile => $targetFile) {
        $sourcePath = $staticDir . $sourceFile;
        $targetPath = $assetsDir . $targetFile;

        if (file_exists($sourcePath)) {
            echo "Creating MkDocs asset: $targetPath\n";
            copy($sourcePath, $targetPath);
        }
    }

    createMkDocsThemeCSS();
}

/**
 * Copies the theme CSS and JavaScript from `admin/assets`.
 */
function createMkDocsThemeCSS(): void {
    $assetsDir = TARGET_DIR . 'assets/';
    $assetsCssDir = $assetsDir . 'css/';
    $assetsJsDir = $assetsDir . 'js/';
    $adminAssetsDir = __DIR__ . '/assets/';

    if (!is_dir($assetsCssDir)) {
        mkdir($assetsCssDir, 0755, true);
    }

    if (!is_dir($assetsJsDir)) {
        mkdir($assetsJsDir, 0755, true);
    }

    $cssFiles = [
        'codeigniter.css' => 'codeigniter.css',
        'codeigniter_dark_mode.css' => 'codeigniter_dark_mode.css'
    ];

    foreach ($cssFiles as $sourceFile => $targetFile) {
        $sourcePath = $adminAssetsDir . $sourceFile;
        $targetPath = $assetsCssDir . $targetFile;

        if (file_exists($sourcePath)) {
            echo "Copying MkDocs theme CSS: $sourcePath to $targetPath\n";
            copy($sourcePath, $targetPath);
        } else {
            writeLine(sprintf('Warning: CSS file not found: %s', $sourcePath), STYLE_WARNING);
        }
    }

    $jsFiles = [
        'hljs.js' => 'hljs.js'
    ];

    foreach ($jsFiles as $sourceFile => $targetFile) {
        $sourcePath = $adminAssetsDir . $sourceFile;
        $targetPath = $assetsJsDir . $targetFile;

        if (file_exists($sourcePath)) {
            echo "Copying MkDocs theme JS: $sourcePath to $targetPath\n";
            copy($sourcePath, $targetPath);
        } else {
            writeLine(sprintf('Warning: JS file not found: %s', $sourcePath), STYLE_WARNING);
        }
    }
}

/**
 * Runs the before-filters, pandoc and the after-filters as three passes, so every after-filter sees every page's pandoc output.
 *
 * @param list<array{source: string, target: string}> $jobs
 *
 * @return int Number of files that pandoc failed on
 */
function convertRstToMd(array $jobs): int {
    $copies = [];

    foreach ($jobs as $i => $job) {
        echo "Converting {$job['source']} to {$job['target']}\n";
        $copies[$i] = applyBeforeFilters($job['source']);
    }

    $exitCodes = runPandoc($jobs, $copies);
    $failures  = 0;

    foreach ($jobs as $i => $job) {
        applyAfterFilters($job['target'], $copies[$i]);

        if ($exitCodes[$i] !== 0) {
            writeLine(sprintf('Error: pandoc failed with exit code %d on %s', $exitCodes[$i], $job['source']), STYLE_ERROR, STDERR);
            $failures++;
        }
    }

    return $failures;
}

/**
 * @param list<array{source: string, target: string}> $jobs
 * @param list<string>                                $copies
 *
 * @return array<int, int> Exit codes keyed like `$jobs`
 */
function runPandoc(array $jobs, array $copies): array {
    $exitCodes = [];
    $running   = [];
    $next      = 0;

    while ($next < count($jobs) || $running !== []) {
        while ($next < count($jobs) && count($running) < PANDOC_WORKERS) {
            $command = ['pandoc', '-f', 'rst', '-t', 'commonmark+pipe_tables', '--wrap=none', '-o', $jobs[$next]['target'], $copies[$next]];
            // pandoc writes to `-o`, and sharing the converter's stdout with it loses the converter's later output.
            $process = proc_open($command, [0 => ['file', '/dev/null', 'r'], 1 => ['file', '/dev/null', 'w'], 2 => ['pipe', 'w']], $pipes);
            stream_set_blocking($pipes[2], false);

            $running[$next] = ['process' => $process, 'stderr' => $pipes[2], 'messages' => ''];
            $next++;
        }

        foreach ($running as $i => &$job) {
            $job['messages'] .= stream_get_contents($job['stderr']);
            $status           = proc_get_status($job['process']);

            if (! $status['running']) {
                $job['messages'] .= stream_get_contents($job['stderr']);
                $exitCodes[$i]    = $status['exitcode'];

                foreach (array_filter(explode("\n", $job['messages']), fn (string $line): bool => trim($line) !== '') as $message) {
                    writeLine($message, STYLE_WARNING, STDERR);
                }

                fclose($job['stderr']);
                proc_close($job['process']);
                unset($running[$i]);
            }
        }

        unset($job);

        usleep(5000);
    }

    return $exitCodes;
}

function ensureDirectory(string $directory): void {
    if (! is_dir($directory)) {
        mkdir($directory, 0755, true);
    }
}

/**
 * Mirrors `$source` into `$target`, creating only the directories that receive a file, appending the RST pages to `$jobs` and skipping the PHP samples, which the includes filter inlines, and any `.rst_` copies left by an interrupted run.
 *
 * @param list<array{source: string, target: string}> $jobs Accumulated RST conversion jobs
 */
function convert(string $source, string $target, array &$jobs): void {
    $dir = opendir($source);

    // Sphinx build files, and the images copied by copyAssets().
    $skipAssets = ['_static', 'images', '.nojekyll', 'conf.py', 'assets'];

    while (($file = readdir($dir)) !== false) {
        if ($file == '.' || $file == '..') {
            continue;
        }

        if (in_array($file, $skipAssets)) {
            continue;
        }

        $sourceFile = realpath($source . '/' . $file);
        $targetFile = $target . '/' . $file;

        if (is_dir($sourceFile)) {
            convert($sourceFile, $targetFile, $jobs);
        } elseif (preg_match('/\.rst$/', $file)) {
            ensureDirectory($target);
            $jobs[] = ['source' => $sourceFile, 'target' => preg_replace('/\.rst$/', '.md', $targetFile)];
        } elseif (! str_ends_with($file, '.php') && ! str_ends_with($file, '.rst_')) {
            ensureDirectory($target);
            echo "Copying $sourceFile\n";
            copy($sourceFile, $targetFile);
        }
    }
    closedir($dir);
}

/**
 * Writes the MkDocs nav, built from the toctrees under the root page, to `docs/.nav.yml` for `mkdocs.yml` to inherit.
 */
function writeMkDocsNav(): void {
    $seen  = ['index'];
    $lines = ['nav:', '  - Home: index.md'];

    foreach (pageToctrees('index') as $toctree) {
        foreach ($toctree['entries'] as $entry) {
            array_push($lines, ...navItem($entry, 0, $seen));
        }
    }

    file_put_contents(TARGET_DIR . '.nav.yml', implode("\n", $lines) . "\n");
}

/**
 * Nav lines for `$entry`, as a section holding the page and its toctree entries when it has any, skipping pages already in `$seen`.
 *
 * @param array{title: ?string, page: string} $entry
 * @param list<string>                        $seen
 *
 * @return list<string>
 */
function navItem(array $entry, int $depth, array &$seen): array {
    if (in_array($entry['page'], $seen, true)) {
        return [];
    }

    $seen[]   = $entry['page'];
    $prefix   = str_repeat('    ', $depth) . '  - ';
    $file     = $entry['page'] . '.md';
    $title    = fn (string $title): string => json_encode($title, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    $children = [];

    foreach (pageToctrees($entry['page']) as $toctree) {
        foreach ($toctree['entries'] as $child) {
            array_push($children, ...navItem($child, $depth + 1, $seen));
        }
    }

    if ($children === []) {
        return [$entry['title'] === null ? $prefix . $file : sprintf('%s%s: %s', $prefix, $title($entry['title']), $file)];
    }

    return [
        sprintf('%s%s:', $prefix, $title($entry['title'] ?? pageTitle($entry['page']))),
        sprintf('%s  - %s', str_repeat('    ', $depth + 1), $file),
        ...$children,
    ];
}

function convertSingleFile(string $filename): bool {
    loadFilters();

    $filename = preg_replace('/\.rst$/', '', $filename);

    $sourceFile = realpath(SOURCE_DIR . $filename . '.rst');
    $targetFile = TARGET_DIR . $filename . '.md';

    if ($sourceFile === false) {
        writeLine(sprintf("Error: File '%s.rst' not found in %s", $filename, SOURCE_DIR), STYLE_ERROR, STDERR);
        return false;
    }

    $targetDir = dirname($targetFile);
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }

    if (convertRstToMd([['source' => $sourceFile, 'target' => $targetFile]]) > 0) {
        return false;
    }

    writeLine('Conversion completed successfully!', STYLE_SUCCESS);
    return true;
}

checkPandocVersion();

if (isset($argv[1])) {
    $filename = $argv[1];
    exit(convertSingleFile($filename) ? 0 : 1);
} else {
    writeLine('Starting full conversion...', STYLE_STEP);

    if (!is_dir(TARGET_DIR)) {
        writeLine('Creating target directory...', STYLE_STEP);
        mkdir(TARGET_DIR, 0755, true);
    }

    writeLine('Cleaning target directory...', STYLE_STEP);
    clean(TARGET_DIR);

    writeLine('Copying essential assets...', STYLE_STEP);
    copyAssets();

    writeLine('Loading filters...', STYLE_STEP);
    loadFilters();

    writeLine('Converting RST files to Markdown...', STYLE_STEP);
    $jobs = [];
    convert(SOURCE_DIR, TARGET_DIR, $jobs);
    $failures = convertRstToMd($jobs);

    if ($failures > 0) {
        writeLine(sprintf('Conversion failed for %d file(s).', $failures), STYLE_ERROR, STDERR);
        exit(1);
    }

    writeLine('Writing the MkDocs nav...', STYLE_STEP);
    writeMkDocsNav();

    writeLine('Conversion completed successfully!', STYLE_SUCCESS);
}
