<?php

declare(strict_types=1);

/**
 * @return array<string, string>
 */
function loadEnvConfig(): array {
    $envFile = __DIR__ . '/../.env';
    $config = [
        'DOCS_SOURCE_TYPE' => 'local',
        'DOCS_PROJECT_PATH' => ''
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

function getSourceDirectory(): string {
    $config = loadEnvConfig();

    if ($config['DOCS_SOURCE_TYPE'] === 'project' && !empty($config['DOCS_PROJECT_PATH'])) {
        $projectPath = rtrim($config['DOCS_PROJECT_PATH'], '/') . '/';
        if (!is_dir($projectPath)) {
            echo "Error: Project path does not exist: $projectPath\n";
            echo "Please check your .env file configuration.\n";
            exit(1);
        }
        return $projectPath;
    }

    return __DIR__ . '/../docs-rst/';
}

function checkPandocVersion(): void {
    exec('pandoc --version', $output, $exitCode);

    if ($exitCode !== 0) {
        fwrite(STDERR, "Error: pandoc is not installed. Install pandoc " . PANDOC_VERSION . ".\n");
        exit(1);
    }

    $installed = trim(str_replace('pandoc', '', $output[0]));

    if ($installed !== PANDOC_VERSION) {
        fwrite(STDERR, "Warning: pandoc $installed is installed, but the conversion is verified against pandoc " . PANDOC_VERSION . ".\n");
    }
}

define('PANDOC_VERSION', '3.12.1');
define('PANDOC_WORKERS', 8);
define('SOURCE_DIR', getSourceDirectory());
define('TARGET_DIR', __DIR__ . '/../docs/');

define('BEFORE_FILTERS', [
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
    'codeBlocks',
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
    $result = $function($data, $file['folder']);

    if ($result === null) {
        fwrite(STDERR, "Error: $function failed on {$file['path']} (" . preg_last_error_msg() . "), leaving the file unfiltered\n");
        return;
    }

    file_put_contents($file['path'], $result);
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
    $assetsToTransfer = [
        '_static' => '_static',
        'images' => 'images',
        '.nojekyll' => '.nojekyll',
        'conf.py' => 'conf.py'
    ];

    foreach ($assetsToTransfer as $source => $target) {
        $sourcePath = SOURCE_DIR . $source;
        $targetPath = TARGET_DIR . $target;

        if (is_dir($sourcePath)) {
            echo "Copying directory: $sourcePath to $targetPath\n";
            copyDirectory($sourcePath, $targetPath);
        } elseif (file_exists($sourcePath)) {
            echo "Copying file: $sourcePath to $targetPath\n";
            copy($sourcePath, $targetPath);
        }
    }

    createMkDocsAssets();
}

function createMkDocsAssets(): void {
    $staticDir = TARGET_DIR . '_static/';
    $assetsDir = TARGET_DIR . 'assets/';

    if (!is_dir($staticDir)) {
        echo "Warning: _static directory not found, skipping MkDocs assets creation\n";
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
            echo "Warning: CSS file not found: $sourcePath\n";
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
            echo "Warning: JS file not found: $sourcePath\n";
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
            fwrite(STDERR, "Error: pandoc failed with exit code {$exitCodes[$i]} on {$job['source']}\n");
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
            $command        = ['pandoc', '-f', 'rst', '-t', 'commonmark', '--wrap=none', '-o', $jobs[$next]['target'], $copies[$next]];
            $running[$next] = proc_open($command, [1 => STDOUT, 2 => STDERR], $pipes);
            $next++;
        }

        foreach ($running as $i => $process) {
            $status = proc_get_status($process);

            if (! $status['running']) {
                $exitCodes[$i] = $status['exitcode'];
                proc_close($process);
                unset($running[$i]);
            }
        }

        usleep(5000);
    }

    return $exitCodes;
}

/**
 * Mirrors `$source` into `$target`, appending the RST pages to `$jobs` and skipping the PHP samples, which the includes filter inlines.
 *
 * @param list<array{source: string, target: string}> $jobs Accumulated RST conversion jobs
 */
function convert(string $source, string $target, array &$jobs): void {
    $dir = opendir($source);

    if (! is_dir($target)) {
        mkdir($target);
    }

    // Copied by copyAssets() instead.
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
        } else if (preg_match('/\.rst$/', $file)) {
            $jobs[] = ['source' => $sourceFile, 'target' => preg_replace('/\.rst$/', '.md', $targetFile)];
        } elseif (! str_ends_with($file, '.php')) {
            echo "Copying $sourceFile\n";
            copy($sourceFile, $targetFile);
        }
    }
    closedir($dir);
}

function convertSingleFile(string $filename): bool {
    loadFilters();

    $filename = preg_replace('/\.rst$/', '', $filename);

    $sourceFile = realpath(SOURCE_DIR . $filename . '.rst');
    $targetFile = TARGET_DIR . $filename . '.md';

    if ($sourceFile === false) {
        echo "Error: File '$filename.rst' not found in " . SOURCE_DIR . "\n";
        return false;
    }

    $targetDir = dirname($targetFile);
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }

    if (convertRstToMd([['source' => $sourceFile, 'target' => $targetFile]]) > 0) {
        return false;
    }

    echo "Conversion completed successfully!\n";
    return true;
}

checkPandocVersion();

if (isset($argv[1])) {
    $filename = $argv[1];
    exit(convertSingleFile($filename) ? 0 : 1);
} else {
    echo "Starting full conversion...\n";

    if (!is_dir(TARGET_DIR)) {
        echo "Creating target directory...\n";
        mkdir(TARGET_DIR, 0755, true);
    }

    echo "Cleaning target directory...\n";
    clean(TARGET_DIR);

    echo "Copying essential assets...\n";
    copyAssets();

    echo "Loading filters...\n";
    loadFilters();

    echo "Converting RST files to Markdown...\n";
    $jobs = [];
    convert(SOURCE_DIR, TARGET_DIR, $jobs);
    $failures = convertRstToMd($jobs);

    if ($failures > 0) {
        fwrite(STDERR, "Conversion failed for $failures file(s).\n");
        exit(1);
    }

    echo "Conversion completed successfully!\n";
}
