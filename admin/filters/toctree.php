<?php

declare(strict_types=1);

function after_filter_toctree(string $data, string $folder): ?string {
    $pattern = '/<div class="toctree"[^>]*>\s*(.*?)\s*<\/div>/s';

    return preg_replace_callback($pattern, function(array $matches) use ($folder): string {
        $content = trim($matches[1]);

        $result = [];

        // pandoc writes the entries either as "Title \<filename\>" pairs or as bare filenames.
        preg_match_all('/([^\\\\]+?)\s*\\\\<([^\\\\>]+?)\\\\>/', $content, $entryMatches, PREG_SET_ORDER);

        if (count($entryMatches) > 0) {
            foreach ($entryMatches as $match) {
                $title = trim($match[1]);
                $filename = trim($match[2]);

            if (empty($filename)) continue;

            if ((strpos($filename, '/index') !== false) &&
                (strpos($filename, '/') !== false)) {
                $subdirToctree = getSubdirectoryToctree($filename, $folder);
                if ($subdirToctree) {
                    $result = array_merge($result, $subdirToctree);
                    continue;
                }
            }

            if (!empty($title)) {
                $displayName = $title;
            } else {
                $headerDisplayName = getFirstHeaderFromFile($filename, $folder);

                if ($headerDisplayName) {
                    $displayName = $headerDisplayName;
                } else {
                    $displayName = ucwords(str_replace(['_', '-'], ' ', basename($filename, '.md')));
                }
            }

            $linkPath = $filename;
            if (strpos($filename, '../') !== 0 && !str_ends_with($filename, '.md')) {
                $linkPath = $filename . '.md';
            } elseif (strpos($filename, '../') === 0 && !str_ends_with($filename, '.md')) {
                $linkPath = $filename . '.md';
            }

            $result[] = "- [$displayName]($linkPath)";
        }
        } else {
            $lines = preg_split('/\s+/', $content);

            foreach ($lines as $filename) {
                $filename = trim($filename);
                if (empty($filename)) continue;

                if ((strpos($filename, '/index') !== false) &&
                    (strpos($filename, '/') !== false)) {
                    $subdirToctree = getSubdirectoryToctree($filename, $folder);
                    if ($subdirToctree) {
                        $result = array_merge($result, $subdirToctree);
                        continue;
                    }
                }

                $headerDisplayName = getFirstHeaderFromFile($filename, $folder);

                if ($headerDisplayName) {
                    $displayName = $headerDisplayName;
                } else {
                    $displayName = ucwords(str_replace(['_', '-'], ' ', basename($filename, '.md')));
                }

                $linkPath = $filename;
                if (strpos($filename, '../') !== 0 && !str_ends_with($filename, '.md')) {
                    $linkPath = $filename . '.md';
                } elseif (strpos($filename, '../') === 0 && !str_ends_with($filename, '.md')) {
                    $linkPath = $filename . '.md';
                }

                $result[] = "- [$displayName]($linkPath)";
            }
        }

        return implode("\n", $result);
    }, $data);
}

/**
 * Entries of the first toctree in the RST index at `$subdirPath`, led by that index's title, or null when it has none.
 *
 * @return list<string>|null
 */
function getSubdirectoryToctree(string $subdirPath, string $folder): ?array {
    $sourcePath = rtrim(SOURCE_DIR, '/') . substr(realpath($folder), strlen(realpath(TARGET_DIR)));

    if (strpos($subdirPath, '../') === 0) {
        $relativePath = substr($subdirPath, 3);
        $subdirRstFile = dirname($sourcePath) . '/' . $relativePath . '.rst';
    } else {
        $subdirRstFile = $sourcePath . '/' . $subdirPath . '.rst';
    }

    if (!file_exists($subdirRstFile)) {
        return null;
    }

    $content = file_get_contents($subdirRstFile);

    if (preg_match('/\.\. toctree::\s*\n((?:[ \t]+:[^:]+:[^\n]*\n)*)\s*\n((?:[ \t]+[^\s][^\n]*\n?)*)/m', $content, $matches)) {
        $toctreeContent = $matches[2];

        $lines = explode("\n", $toctreeContent);
        $entries = [];

        if (preg_match('/^([^\s#*=.][^\n]*)\n[#*=]{3,}[ \t]*$/m', $content, $titleMatch)) {
            $mainTitle = trim($titleMatch[1]);

            if (strpos($subdirPath, '../') === 0) {
                $mainLinkPath = $subdirPath . '.md';
            } else {
                $mainLinkPath = $subdirPath . '.md';
            }

            $entries[] = "- [$mainTitle]($mainLinkPath)";
        }

        foreach ($lines as $line) {
            $trimmed = trim($line);
            if (empty($trimmed)) continue;

            $displayName = '';
            $filename = '';

            if (preg_match('/^(.+?)\s*<(.+?)>$/', $trimmed, $matches)) {
                $displayName = trim($matches[1]);
                $filename = trim($matches[2]);
            } else {
                $filename = $trimmed;
            }

            if (strpos($subdirPath, '../') === 0) {
                $pathParts = explode('/', substr($subdirPath, 3));
                $subdirName = $pathParts[0];
            } else {
                $subdirName = dirname($subdirPath);
                if ($subdirName === '.') {
                    $subdirName = basename($subdirPath, '/index');
                }
            }

            if (strpos($filename, '../') === 0) {
                if (str_ends_with($filename, '/index')) {
                    $currentSubdirPath = dirname($subdirPath);
                    if ($currentSubdirPath === '.') {
                        $currentSubdirPath = '';
                    }

                    $contextFolder = $folder;
                    if (!empty($currentSubdirPath)) {
                        $contextFolder = $folder . '/' . $currentSubdirPath;
                    }

                    $subdirToctree = getSubdirectoryToctree($filename, $contextFolder);
                    if ($subdirToctree) {
                        $entries = array_merge($entries, $subdirToctree);
                        continue;
                    }
                }

                $linkPath = $filename;
                if (!str_ends_with($linkPath, '.md')) {
                    $linkPath .= '.md';
                }

                if (!empty($displayName)) {
                    $finalDisplayName = $displayName;
                } else {
                    $headerDisplayName = getFirstHeaderFromFile($filename, dirname($folder));
                    $finalDisplayName = $headerDisplayName ?: ucwords(str_replace(['_', '-'], ' ', basename($filename, '.md')));
                }
            } else {
                $subdirFile = $subdirName . '/' . $filename;

                $linkPath = $subdirFile;
                if (!str_ends_with($linkPath, '.md')) {
                    $linkPath .= '.md';
                }

                if (!empty($displayName)) {
                    $finalDisplayName = $displayName;
                } else {
                    $headerDisplayName = getFirstHeaderFromFile($subdirFile, TARGET_DIR);
                    $finalDisplayName = $headerDisplayName ?: ucwords(str_replace(['_', '-'], ' ', basename($filename, '.md')));
                }
            }

            $entries[] = "    - [$finalDisplayName]($linkPath)";
        }

        return $entries;
    }

    return null;
}

/**
 * First `#` heading of the converted page at `$filename`, a path relative to the `$folder` output directory.
 */
function getFirstHeaderFromFile(string $filename, string $folder): ?string {
    $targetFile = '';

    $docsPath = realpath($folder) . '/';

    if (strpos($filename, '../') === 0) {
        $relativePath = substr($filename, 3);
        $targetFile = dirname($docsPath) . '/' . $relativePath . '.md';
    } elseif (strpos($filename, '/') !== false) {
        $targetFile = $docsPath . $filename . '.md';
    } else {
        $targetFile = $docsPath . $filename . '.md';
    }

    if (file_exists($targetFile)) {
        $content = file_get_contents($targetFile);

        if (preg_match('/^#\s+(.+)$/m', $content, $matches)) {
            $header = trim($matches[1]);
            return $header;
        }
    }

    return null;
}
