<?php

declare(strict_types=1);

/**
 * @return array<string, string>
 */
function load_env_config(): array {
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
function write_line(string $message, string $style = '', $stream = STDOUT): void {
    if ($style !== '' && (string) getenv('NO_COLOR') === '' && stream_isatty($stream)) {
        $message = sprintf("\033[%sm%s\033[0m", $style, $message);
    }

    fwrite($stream, $message . "\n");
}

function get_source_directory(): string {
    $projectPath = load_env_config()['DOCS_PROJECT_PATH'];

    if ($projectPath === '') {
        write_line('Error: DOCS_PROJECT_PATH is not set.', STYLE_ERROR, STDERR);
        write_line('Set it in the environment or in .env to the user_guide_src/source folder of a CodeIgniter4 checkout.', '', STDERR);
        exit(1);
    }

    $projectPath = rtrim($projectPath, '/') . '/';

    if (!is_dir($projectPath)) {
        write_line(sprintf('Error: DOCS_PROJECT_PATH does not exist: %s', $projectPath), STYLE_ERROR, STDERR);
        exit(1);
    }

    return $projectPath;
}

function check_pandoc_version(): void {
    exec('pandoc --version', $output, $exitCode);

    if ($exitCode !== 0) {
        write_line(sprintf('Error: pandoc is not installed. Install pandoc %s.', PANDOC_VERSION), STYLE_ERROR, STDERR);
        exit(1);
    }

    $installed = trim(str_replace('pandoc', '', $output[0]));

    if ($installed !== PANDOC_VERSION) {
        write_line(sprintf('Warning: pandoc %s is installed, but the conversion is verified against pandoc %s.', $installed, PANDOC_VERSION), STYLE_WARNING, STDERR);
    }
}

const STYLE_ERROR   = '31';
const STYLE_WARNING = '33';
const STYLE_SUCCESS = '32';
const STYLE_STEP    = '1';

define('PANDOC_VERSION', '3.12.1');
define('PANDOC_WORKERS', 8);
define('SOURCE_DIR', get_source_directory());
define('TARGET_DIR', __DIR__ . '/../docs/');

define('BEFORE_FILTERS', [
    'toctree',
    'includes',
    'doc_link',
    'class_reference',
    'list_indentation',
]);
define('AFTER_FILTERS', [
    'images',
    'alerts',
    'dollar_sign',
    'line_breaks',
    'class_reference',
    'list_indentation',
    'toctree',
    'contents',
    'doc_link',
    'admonitions',
    'includes',
]);

function load_filters(): void {
    $filters = array_unique(array_merge(BEFORE_FILTERS, AFTER_FILTERS));
    foreach ($filters as $filter) {
        include_once(__DIR__ . '/filters/' . $filter . '.php');
    }
}

/**
 * Filtered copy of `$path`, saved next to it as `$path_` until apply_after_filters() deletes it.
 */
function apply_before_filters(string $path): string {
    $file = [
        'path'   => $path . '_',
        'folder' => dirname($path),
    ];

    copy($path, $file['path']);

    foreach (BEFORE_FILTERS as $action) {
        $function = 'before_filter_' . $action;

        if (function_exists($function)) {
            update_file($file, $function);
        }
    }

    return $file['path'];
}

function apply_after_filters(string $path, string $sourceCopy): void {
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
            update_file($file, $function);
        }
    }
}

/**
 * @param array{path: string, folder: string} $file
 * @param callable-string                     $function
 */
function update_file(array $file, string $function): void {
    $data = file_get_contents($file['path']);
    $result = $function($data, $file['folder'], $file['path']);

    if ($result === null) {
        write_line(sprintf('Error: %s failed on %s (%s), leaving the file unfiltered', $function, $file['path'], preg_last_error_msg()), STYLE_ERROR, STDERR);
        return;
    }

    file_put_contents($file['path'], $result);
}

/**
 * `$path` relative to the source folder, or `$path` itself when it lies outside it.
 */
function source_relative(string $path): string {
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
function relative_link(string $directory, string $page): string {
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

function copy_directory(string $source, string $target): bool {
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
            copy_directory($sourceFile, $targetFile);
        } else {
            echo "Copying asset: $sourceFile\n";
            copy($sourceFile, $targetFile);
        }
    }
    closedir($dir);
    return true;
}

function copy_assets(): void {
    printf("Copying directory: %simages to %simages\n", SOURCE_DIR, TARGET_DIR);
    copy_directory(SOURCE_DIR . 'images', TARGET_DIR . 'images');

    create_mkdocs_assets();
}

function create_mkdocs_assets(): void {
    $staticDir = SOURCE_DIR . '_static/';
    $assetsDir = TARGET_DIR . 'assets/';

    if (!is_dir($staticDir)) {
        write_line('Warning: _static directory not found, skipping MkDocs assets creation', STYLE_WARNING);
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

    create_mkdocs_theme_css();
}

/**
 * Copies the theme CSS from `admin/assets`.
 */
function create_mkdocs_theme_css(): void {
    $assetsCssDir = TARGET_DIR . 'assets/css/';
    $adminAssetsDir = __DIR__ . '/assets/';

    if (!is_dir($assetsCssDir)) {
        mkdir($assetsCssDir, 0755, true);
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
            write_line(sprintf('Warning: CSS file not found: %s', $sourcePath), STYLE_WARNING);
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
function convert_rst_to_md(array $jobs): int {
    $copies = [];

    foreach ($jobs as $i => $job) {
        echo "Converting {$job['source']} to {$job['target']}\n";
        $copies[$i] = apply_before_filters($job['source']);
    }

    $exitCodes = run_pandoc($jobs, $copies);
    $failures  = 0;

    foreach ($jobs as $i => $job) {
        apply_after_filters($job['target'], $copies[$i]);

        if ($exitCodes[$i] !== 0) {
            write_line(sprintf('Error: pandoc failed with exit code %d on %s', $exitCodes[$i], $job['source']), STYLE_ERROR, STDERR);
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
function run_pandoc(array $jobs, array $copies): array {
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
                    write_line($message, STYLE_WARNING, STDERR);
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

function ensure_directory(string $directory): void {
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

    // Sphinx build files, and the images copied by copy_assets().
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
            ensure_directory($target);
            $jobs[] = ['source' => $sourceFile, 'target' => preg_replace('/\.rst$/', '.md', $targetFile)];
        } elseif (! str_ends_with($file, '.php') && ! str_ends_with($file, '.rst_')) {
            ensure_directory($target);
            echo "Copying $sourceFile\n";
            copy($sourceFile, $targetFile);
        }
    }
    closedir($dir);
}

/**
 * Writes the MkDocs nav, built from the toctrees under the root page, to `docs/.nav.yml` for `mkdocs.yml` to inherit.
 */
function write_mkdocs_nav(): void {
    $seen  = ['index'];
    $lines = ['nav:', '  - Home: index.md'];

    foreach (page_toctrees('index') as $toctree) {
        foreach ($toctree['entries'] as $entry) {
            array_push($lines, ...nav_item($entry, 0, $seen));
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
function nav_item(array $entry, int $depth, array &$seen): array {
    if (in_array($entry['page'], $seen, true)) {
        return [];
    }

    $seen[]   = $entry['page'];
    $prefix   = str_repeat('    ', $depth) . '  - ';
    $file     = $entry['page'] . '.md';
    $title    = fn (string $title): string => json_encode($title, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    $children = [];

    foreach (page_toctrees($entry['page']) as $toctree) {
        foreach ($toctree['entries'] as $child) {
            array_push($children, ...nav_item($child, $depth + 1, $seen));
        }
    }

    if ($children === []) {
        return [$entry['title'] === null ? $prefix . $file : sprintf('%s%s: %s', $prefix, $title($entry['title']), $file)];
    }

    return [
        sprintf('%s%s:', $prefix, $title($entry['title'] ?? page_title($entry['page']))),
        sprintf('%s  - %s', str_repeat('    ', $depth + 1), $file),
        ...$children,
    ];
}

function convert_single_file(string $filename): bool {
    load_filters();

    $filename = preg_replace('/\.rst$/', '', $filename);

    $sourceFile = realpath(SOURCE_DIR . $filename . '.rst');
    $targetFile = TARGET_DIR . $filename . '.md';

    if ($sourceFile === false) {
        write_line(sprintf("Error: File '%s.rst' not found in %s", $filename, SOURCE_DIR), STYLE_ERROR, STDERR);
        return false;
    }

    $targetDir = dirname($targetFile);
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }

    if (convert_rst_to_md([['source' => $sourceFile, 'target' => $targetFile]]) > 0) {
        return false;
    }

    write_line('Conversion completed successfully!', STYLE_SUCCESS);
    return true;
}

check_pandoc_version();

if (isset($argv[1])) {
    $filename = $argv[1];
    exit(convert_single_file($filename) ? 0 : 1);
} else {
    write_line('Starting full conversion...', STYLE_STEP);

    if (!is_dir(TARGET_DIR)) {
        write_line('Creating target directory...', STYLE_STEP);
        mkdir(TARGET_DIR, 0755, true);
    }

    write_line('Cleaning target directory...', STYLE_STEP);
    clean(TARGET_DIR);

    write_line('Copying essential assets...', STYLE_STEP);
    copy_assets();

    write_line('Loading filters...', STYLE_STEP);
    load_filters();

    write_line('Converting RST files to Markdown...', STYLE_STEP);
    $jobs = [];
    convert(SOURCE_DIR, TARGET_DIR, $jobs);
    $failures = convert_rst_to_md($jobs);

    if ($failures > 0) {
        write_line(sprintf('Conversion failed for %d file(s).', $failures), STYLE_ERROR, STDERR);
        exit(1);
    }

    write_line('Writing the MkDocs nav...', STYLE_STEP);
    write_mkdocs_nav();

    write_line('Conversion completed successfully!', STYLE_SUCCESS);
}
