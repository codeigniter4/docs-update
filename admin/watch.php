<?php

declare(strict_types=1);

/**
 * @return array<string, int>
 */
function snapshot(string $sourceDir): array
{
    $files = [];
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($sourceDir, FilesystemIterator::SKIP_DOTS),
    );

    /** @var SplFileInfo $file */
    foreach ($iterator as $file) {
        $path = substr($file->getPathname(), strlen($sourceDir));

        if (
            str_starts_with($path, '_static/')
            || str_starts_with($path, 'images/')
            || str_ends_with($path, '.rst_')
        ) {
            continue;
        }

        $files[$path] = $file->getMTime();
    }

    return $files;
}

/**
 * The page that a changed source file belongs to: itself for an RST file,
 * the page named by the folder for a sample.
 */
function owning_page(string $path, string $sourceDir): ?string
{
    if (str_ends_with($path, '.rst')) {
        return substr($path, 0, -4);
    }

    for ($directory = dirname($path); $directory !== '.'; $directory = dirname($directory)) {
        if (is_file($sourceDir . $directory . '.rst')) {
            return $directory;
        }
    }

    return null;
}

function convert(string $page = ''): bool
{
    $start = microtime(true);

    exec(
        sprintf(
            'php %s %s 2>&1',
            escapeshellarg(__DIR__ . '/convert.php'),
            $page === '' ? '' : escapeshellarg($page),
        ),
        $output,
        $exitCode,
    );

    printf(
        "[%s] %s %s (%.1fs)\n",
        date('H:i:s'),
        $exitCode === 0 ? 'Converted' : 'Failed',
        $page === '' ? 'the whole guide' : $page,
        microtime(true) - $start,
    );

    foreach ($output as $line) {
        if ($exitCode !== 0 || preg_match('/warning|not found/i', $line)) {
            echo '    ', $line, "\n";
        }
    }

    return $exitCode === 0;
}

function main(): void
{
    $sourceDir = rtrim((string) getenv('DOCS_PROJECT_PATH'), '/') . '/';
    $wholeGuideThreshold = 15;

    if (!is_dir($sourceDir)) {
        fwrite(STDERR, "Set DOCS_PROJECT_PATH to the user guide source.\n");
        exit(1);
    }

    $known = snapshot($sourceDir);

    printf("Watching %s\n", $sourceDir);

    while (true) {
        usleep(500000);

        if (snapshot($sourceDir) === $known) {
            continue;
        }

        // An editor or `git stash pop` may still be writing files.
        usleep(300000);

        $current = snapshot($sourceDir);
        $changed = array_keys(array_diff_assoc($current, $known) + array_diff_assoc($known, $current));
        $addedOrRemoved = array_keys(array_diff_key($current, $known) + array_diff_key($known, $current));
        $known = $current;

        $pages = array_unique(array_filter(array_map(
            fn (string $path): ?string => owning_page($path, $sourceDir),
            $changed,
        )));
        $pageListChanged = array_filter(
            $addedOrRemoved,
            fn (string $path): bool => str_ends_with($path, '.rst'),
        ) !== [];

        if ($pageListChanged || count($pages) >= $wholeGuideThreshold) {
            convert();

            continue;
        }

        foreach ($pages as $page) {
            convert($page);
        }
    }
}

main();
