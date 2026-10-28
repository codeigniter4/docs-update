<?php

declare(strict_types=1);

/**
 * @return array<string, string>
 */
function load_env(): array
{
    $dotenv = dirname(__DIR__) . '/.env';
    $config = ['DOCS_PROJECT_PATH' => ''];

    if (is_file($dotenv)) {
        $lines = file($dotenv, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        assert($lines !== false);

        foreach ($lines as $line) {
            $line = trim($line);

            if (str_starts_with($line, '#')) {
                continue;
            }

            if (!str_contains($line, '=')) {
                continue;
            }

            [$key, $value] = array_map(trim(...), explode('=', $line, 2));

            if (array_key_exists($key, $config)) {
                $config[$key] = $value;
            }
        }
    }

    foreach (array_keys($config) as $key) {
        $value = (string) getenv($key);

        if ($value !== '') {
            $config[$key] = $value;
        }
    }

    return $config;
}

/**
 * @return array{before: list<string>, after: list<string>}
 */
function load_filters(): array
{
    $beforeFilters = [
        'toctree',
        'includes',
        'doc_link',
        'class_reference',
        'list_indentation',
    ];
    $afterFilters = [
        'images',
        'alerts',
        'dollar_sign',
        'line_breaks',
        'escapes',
        'class_reference',
        'list_indentation',
        'toctree',
        'contents',
        'doc_link',
        'admonitions',
        'includes',
    ];

    foreach (array_unique(array_merge($beforeFilters, $afterFilters)) as $filter) {
        include_once sprintf('%s/admin/filters/%s.php', dirname(__DIR__), $filter);
    }

    return ['before' => $beforeFilters, 'after' => $afterFilters];
}

function color_line(string $line, string $foreground = '', string $background = ''): string
{
    $set = [];

    if ($foreground !== '') {
        $set[] = $foreground;
    }

    if ($background !== '') {
        $set[] = $background;
    }

    if ($set !== []) {
        $line = sprintf("\033[%sm%s\033[0m", implode(';', $set), $line);
    }

    return $line;
}

function write_line(string $line, string $foreground = '', string $background = ''): void
{
    fwrite(STDOUT, color_line($line, $foreground, $background) . PHP_EOL);
}

function check_pandoc(string $version): bool
{
    exec('pandoc --version', $output, $exitCode);

    if ($exitCode !== 0) {
        write_line(sprintf('pandoc is not installed. Install pandoc %s.', $version), '31');
        return false;
    }

    $installed = trim(str_replace('pandoc', '', $output[0]));

    if ($installed !== $version) {
        write_line(sprintf('pandoc %s is installed, but the conversion is verified against pandoc %s.', $installed, $version), '33');
    }

    return true;
}

/**
 * Source folder of the user guide, with a trailing slash, set once by passing `$directory`.
 */
function source_dir(?string $directory = null): string
{
    static $sourceDir = '';

    if ($directory !== null) {
        $sourceDir = rtrim($directory, '/') . '/';
    }

    return $sourceDir;
}

/**
 * `$path` relative to the source folder, or `$path` itself when it lies outside it.
 */
function source_relative(string $path): string
{
    foreach ([realpath(source_dir()), rtrim(source_dir(), '/')] as $root) {
        if ($path === $root || str_starts_with($path, $root . '/')) {
            return ltrim(substr($path, strlen($root)), '/');
        }
    }

    return $path;
}

/**
 * Markdown link from a page in `$directory` to the page `$page`, both relative to the docs root.
 */
function relative_link(string $directory, string $page): string
{
    $from = $directory === '' ? [] : explode('/', $directory);
    $to = explode('/', $page);

    while ($from !== [] && count($to) > 1 && $from[0] === $to[0]) {
        array_shift($from);
        array_shift($to);
    }

    return str_repeat('../', count($from)) . implode('/', $to) . '.md';
}

function clean_dir(string $directory): void
{
    $files = array_diff(scandir($directory), ['.', '..']);

    foreach ($files as $file) {
        $path = $directory . DIRECTORY_SEPARATOR . $file;

        if (is_dir($path)) {
            clean_dir($path);
            rmdir($path);
        } else {
            unlink($path);
        }
    }
}

/**
 * @param array<string, string>  $only
 */
function mirror_directory(string $sourceDir, string $outputDir, array $only = []): void
{
    if (!is_dir($sourceDir)) {
        write_line(sprintf('Source directory does not exist: %s', $sourceDir), '31');
        return;
    }

    $files = array_diff(scandir($sourceDir), ['.', '..']);

    if (!is_dir($outputDir)) {
        mkdir($outputDir, 0777, true);
    }

    foreach ($files as $file) {
        if ($only !== [] && !array_key_exists($file, $only)) {
            continue;
        }

        $sourcePath = $sourceDir . DIRECTORY_SEPARATOR . $file;
        $outputPath = $outputDir . DIRECTORY_SEPARATOR . ($only[$file] ?? $file);

        if (is_dir($sourcePath)) {
            mirror_directory($sourcePath, $outputPath);
        } else {
            write_line(sprintf(' - Copying %s (%s)', color_line($file, '32'), color_line($outputPath, '34')));
            copy($sourcePath, $outputPath);
        }
    }
}

/**
 * Conversion jobs for the RST pages under `$sourceDir`, copying the other files except PHP samples, which the includes filter inlines.
 *
 * @return list<array{source: string, output: string}>
 */
function get_jobs(string $sourceDir, string $outputDir): array
{
    if (!is_dir($sourceDir)) {
        write_line(sprintf('Source directory does not exist: %s', $sourceDir), '31');
        return [];
    }

    $jobs = [];

    $files = array_diff(scandir($sourceDir), ['.', '..', '_static', 'images', '.nojekyll', 'conf.py', 'assets']);

    foreach ($files as $file) {
        $sourcePath = rtrim($sourceDir, '\\/') . DIRECTORY_SEPARATOR . $file;
        $outputPath = rtrim($outputDir, '\\/') . DIRECTORY_SEPARATOR . $file;

        if (is_dir($sourcePath)) {
            $jobs = array_merge($jobs, get_jobs($sourcePath, $outputPath));
        } elseif (is_file($sourcePath) && pathinfo($sourcePath, PATHINFO_EXTENSION) === 'rst') {
            $jobs[] = [
                'source' => $sourcePath,
                'output' => dirname($outputPath) . DIRECTORY_SEPARATOR . basename($outputPath, '.rst') . '.md',
            ];
        } elseif (is_file($sourcePath) && !in_array(pathinfo($sourcePath, PATHINFO_EXTENSION), ['php', 'rst_'], true)) {
            if (!is_dir($outputDir)) {
                mkdir($outputDir, 0777, true);
            }

            write_line(sprintf(' - Copying %s (%s)', color_line($file, '32'), color_line($outputPath, '34')));
            copy($sourcePath, $outputPath);
        }
    }

    return $jobs;
}

/**
 * Apply the conversion jobs to generate Markdown files from reStructuredText.
 *
 * @param list<array{source: string, output: string}> $jobs
 * @param array{before: list<string>, after: list<string>} $filters
 *
 * @return int Number of pages that pandoc failed on
 */
function apply_conversion(array $jobs, array $filters): int
{
    $copies = [];

    foreach ($jobs as $i => $job) {
        write_line(sprintf(' - Converting %s to %s', color_line(basename($job['source']), '32'), color_line($job['output'], '34')));

        $copies[$i] = $job['source'] . '_';
        copy($job['source'], $copies[$i]);
        apply_filters($copies[$i], 'before', $filters['before']);

        if (!is_dir(dirname($job['output']))) {
            mkdir(dirname($job['output']), 0777, true);
        }
    }

    // Every page is converted before any after-filter runs, so the after-filters can read every page's pandoc output.
    $exitCodes = run_pandoc($jobs, $copies);
    $failures = 0;

    foreach ($jobs as $i => $job) {
        unlink($copies[$i]);

        if ($exitCodes[$i] !== 0) {
            write_line(sprintf('pandoc failed with exit code %d on %s', $exitCodes[$i], $job['source']), '31');
            $failures++;
            continue;
        }

        apply_filters($job['output'], 'after', $filters['after']);
    }

    return $failures;
}

/**
 * Runs each `{$stage}_filter_{$name}()` that exists on the file at `$path`, keeping the last good content when one fails.
 *
 * @param list<string> $names
 */
function apply_filters(string $path, string $stage, array $names): void
{
    foreach ($names as $name) {
        $function = sprintf('%s_filter_%s', $stage, $name);

        if (!function_exists($function)) {
            continue;
        }

        $result = $function((string) file_get_contents($path), dirname($path), $path);

        if ($result === null) {
            write_line(sprintf('%s failed on %s (%s), leaving the file unfiltered', $function, $path, preg_last_error_msg()), '31');
            continue;
        }

        file_put_contents($path, $result);
    }
}

/**
 * @param list<array{source: string, output: string}> $jobs
 * @param array<int, string> $copies
 *
 * @return array<int, int> Exit codes keyed like `$jobs`
 */
function run_pandoc(array $jobs, array $copies): array
{
    $exitCodes = [];
    $running = [];
    $next = 0;
    $workers = 8;

    while ($next < count($jobs) || $running !== []) {
        while ($next < count($jobs) && count($running) < $workers) {
            $command = ['pandoc', '-f', 'rst', '-t', 'commonmark+pipe_tables', '--wrap=none', '-o', $jobs[$next]['output'], $copies[$next]];
            // pandoc writes to `-o`, and sharing the converter's stdout with it loses the converter's later output.
            $process = proc_open($command, [0 => ['file', '/dev/null', 'r'], 1 => ['file', '/dev/null', 'w'], 2 => ['pipe', 'w']], $pipes);
            stream_set_blocking($pipes[2], false);

            $running[$next] = ['process' => $process, 'stderr' => $pipes[2], 'messages' => ''];
            $next++;
        }

        foreach ($running as $i => &$job) {
            $job['messages'] .= stream_get_contents($job['stderr']);
            $status = proc_get_status($job['process']);

            if (!$status['running']) {
                $job['messages'] .= stream_get_contents($job['stderr']);
                $exitCodes[$i] = $status['exitcode'];

                foreach (array_filter(explode("\n", $job['messages']), fn (string $line): bool => trim($line) !== '') as $message) {
                    write_line($message, '33');
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

/**
 * Writes the MkDocs nav, built from the toctrees under the root page, to `.nav.yml` in `$outputDir` for `mkdocs.yml` to inherit.
 */
function write_nav(string $outputDir): void
{
    $seen = ['index'];
    $lines = ['nav:', '  - Home: index.md'];

    foreach (page_toctrees('index') as $toctree) {
        foreach ($toctree['entries'] as $entry) {
            array_push($lines, ...nav_item($entry, 0, $seen));
        }
    }

    file_put_contents($outputDir . '.nav.yml', implode("\n", $lines) . "\n");
}

/**
 * Nav lines for `$entry`, as a section holding the page and its toctree entries when it has any, skipping pages already in `$seen`.
 *
 * @param array{title: ?string, page: string} $entry
 * @param list<string> $seen
 *
 * @return list<string>
 */
function nav_item(array $entry, int $depth, array &$seen): array
{
    if (in_array($entry['page'], $seen, true)) {
        return [];
    }

    $seen[] = $entry['page'];
    $prefix = str_repeat('    ', $depth) . '  - ';
    $file = $entry['page'] . '.md';
    $title = fn (string $title): string => json_encode($title, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
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

function convert_whole_docs(string $sourceDir, string $outputDir): int
{
    write_line(sprintf('Starting full conversion on %s', $sourceDir), '32');

    if (!is_dir($outputDir)) {
        write_line(sprintf('Creating output directory: %s', $outputDir), '32');
        mkdir($outputDir, 0777, true);
    } else {
        write_line(sprintf('Cleaning output directory: %s', $outputDir), '32');
        clean_dir($outputDir);
    }

    write_line(sprintf('Copying images from %simages/ to %simages/', $sourceDir, $outputDir), '32');
    mirror_directory($sourceDir . 'images', $outputDir . 'images');

    write_line(sprintf('Copying static files from %s_static/ to %sassets/', $sourceDir, $outputDir), '32');
    mirror_directory($sourceDir . '_static', $outputDir . 'assets', [
        'ci-logo-text.svg' => 'flame.svg',
        'favicon.ico' => 'favicon.ico'
    ]);

    $cssDir = dirname(__DIR__) . '/admin/assets';

    write_line(sprintf('Copying CSS files from %s to %sassets/css', $cssDir, $outputDir), '32');
    mirror_directory($cssDir, $outputDir . 'assets/css');

    write_line(sprintf('Loading filters from %s/admin/filters/', dirname(__DIR__)), '32');
    $filters = load_filters();

    write_line('Converting reStructuredText documents to Markdown', '32');
    $failures = apply_conversion(get_jobs($sourceDir, $outputDir), $filters);

    if ($failures > 0) {
        write_line(sprintf('Conversion failed for %d page(s).', $failures), '31');
        return 1;
    }

    write_line(sprintf('Writing the MkDocs nav to %s.nav.yml', $outputDir), '32');
    write_nav($outputDir);

    write_line('Conversion completed successfully.', '32');

    return 0;
}

/**
 * Converts the single page `$page`, given relative to `$sourceDir` with or without its `.rst` extension.
 */
function convert_single_doc(string $page, string $sourceDir, string $outputDir): int
{
    $page = preg_replace('/\.rst$/', '', $page);
    $sourcePath = $sourceDir . $page . '.rst';

    if (!is_file($sourcePath)) {
        write_line(sprintf('Page %s.rst not found in %s', $page, $sourceDir), '31');
        return 1;
    }

    write_line(sprintf('Converting %s', $sourcePath), '32');

    if (apply_conversion([['source' => (string) realpath($sourcePath), 'output' => $outputDir . $page . '.md']], load_filters()) > 0) {
        return 1;
    }

    write_line('Conversion completed successfully.', '32');

    return 0;
}

function main(): void
{
    $projectPath = load_env()['DOCS_PROJECT_PATH'];

    if ($projectPath === '') {
        write_line('DOCS_PROJECT_PATH is not set in the environment.', '31');
        exit(1);
    }

    if (!is_dir($projectPath)) {
        write_line(sprintf('DOCS_PROJECT_PATH does not exist or is not a directory: %s', $projectPath), '31');
        exit(1);
    }

    if (!check_pandoc('3.12.1')) {
        exit(1);
    }

    $sourceDir = source_dir($projectPath);
    $outputDir = dirname(__DIR__) . '/docs/';

    if (!isset($_SERVER['argv'][1])) {
        exit(convert_whole_docs($sourceDir, $outputDir));
    }

    exit(convert_single_doc($_SERVER['argv'][1], $sourceDir, $outputDir));
}

main();
