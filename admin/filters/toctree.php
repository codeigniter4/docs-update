<?php

// Handle toctree functionality from RST
function after_filter_toctree($data, $folder) {
    // Look for HTML toctree divs created by pandoc with --wrap=none
    $pattern = '/<div class="toctree"[^>]*>\s*(.*?)\s*<\/div>/s';

    return preg_replace_callback($pattern, function($matches) use ($folder) {
        $content = trim($matches[1]);

        // Parse the toctree content which can be in two formats:
        // 1. "Title \<filename\> Title \<filename\> ..." (with titles)
        // 2. "filename filename filename ..." (bare filenames)

        $result = [];

        // First try to match title \<filename\> pairs
        preg_match_all('/([^\\\\]+?)\s*\\\\<([^\\\\>]+?)\\\\>/', $content, $entryMatches, PREG_SET_ORDER);

        if (count($entryMatches) > 0) {
            // Handle format: "Title \<filename\>"
            foreach ($entryMatches as $match) {
                $title = trim($match[1]);
                $filename = trim($match[2]);

            if (empty($filename)) continue;

            // Check if this is a subdirectory index reference (e.g., "changelogs/index" or "../changelogs/index")
            if ((strpos($filename, '/index') !== false) &&
                (strpos($filename, '/') !== false)) {
                $subdirToctree = getSubdirectoryToctree($filename, $folder);
                if ($subdirToctree) {
                    $result = array_merge($result, $subdirToctree);
                    continue;
                }
            }

            // Use the title from the toctree if available, otherwise try to get header from file
            if (!empty($title)) {
                $displayName = $title;
            } else {
                // Try to get the first header from the target file
                $headerDisplayName = getFirstHeaderFromFile($filename, $folder);

                if ($headerDisplayName) {
                    $displayName = $headerDisplayName;
                } else {
                    // Create a fallback display name from filename
                    $displayName = ucwords(str_replace(['_', '-'], ' ', basename($filename, '.md')));
                }
            }

            // Add .md extension if not present and not a relative path
            $linkPath = $filename;
            if (strpos($filename, '../') !== 0 && !str_ends_with($filename, '.md')) {
                $linkPath = $filename . '.md';
            } elseif (strpos($filename, '../') === 0 && !str_ends_with($filename, '.md')) {
                $linkPath = $filename . '.md';
            }

            $result[] = "- [$displayName]($linkPath)";
        }
        } else {
            // Handle format: bare filenames separated by whitespace/newlines
            $lines = preg_split('/\s+/', $content);

            foreach ($lines as $filename) {
                $filename = trim($filename);
                if (empty($filename)) continue;

                // Check if this is a subdirectory index reference
                if ((strpos($filename, '/index') !== false) &&
                    (strpos($filename, '/') !== false)) {
                    $subdirToctree = getSubdirectoryToctree($filename, $folder);
                    if ($subdirToctree) {
                        $result = array_merge($result, $subdirToctree);
                        continue;
                    }
                }

                // Try to get the first header from the target file
                $headerDisplayName = getFirstHeaderFromFile($filename, $folder);

                if ($headerDisplayName) {
                    $displayName = $headerDisplayName;
                } else {
                    // Create a fallback display name from filename
                    $displayName = ucwords(str_replace(['_', '-'], ' ', basename($filename, '.md')));
                }

                // Add .md extension if not present and not a relative path
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

function getSubdirectoryToctree($subdirPath, $folder) {
    // Convert the target folder (docs) back to source folder (docs-rst)
    $targetPath = realpath($folder);

    // Handle both '/docs/' (middle of path) and '/docs' (end of path)
    if (str_ends_with($targetPath, '/docs')) {
        $sourcePath = substr($targetPath, 0, -5) . '/docs-rst';
    } else {
        $sourcePath = str_replace('/docs/', '/docs-rst/', $targetPath);
    }

    // Handle relative paths properly
    if (strpos($subdirPath, '../') === 0) {
        // For paths like ../changelogs/index, we need to go up one directory
        $relativePath = substr($subdirPath, 3); // Remove '../'
        $subdirRstFile = dirname($sourcePath) . '/' . $relativePath . '.rst';
    } else {
        $subdirRstFile = $sourcePath . '/' . $subdirPath . '.rst';
    }

    if (!file_exists($subdirRstFile)) {
        return null;
    }

    // Read the RST file and extract toctree entries
    $content = file_get_contents($subdirRstFile);

    // Look for toctree sections - improved regex to handle multiple toctree blocks
    if (preg_match('/\.\. toctree::\s*\n((?:\s+:[^:]+:[^\n]*\n)*)\s*\n((?:\s+[^\s][^\n]*\n?)*)/m', $content, $matches)) {
        $toctreeContent = $matches[2];

        // Extract individual entries (lines that are indented)
        $lines = explode("\n", $toctreeContent);
        $entries = [];

        foreach ($lines as $line) {
            $trimmed = trim($line);
            if (empty($trimmed)) continue;

            // Parse the toctree entry which can be in format:
            // 1. "filename" (bare filename)
            // 2. "Title <filename>" (title with filename in angle brackets)
            $displayName = '';
            $filename = '';

            if (preg_match('/^(.+?)\s*<(.+?)>$/', $trimmed, $matches)) {
                // Format: "Title <filename>"
                $displayName = trim($matches[1]);
                $filename = trim($matches[2]);
            } else {
                // Format: bare filename
                $filename = $trimmed;
            }

            // Determine the subdirectory name for building proper paths
            if (strpos($subdirPath, '../') === 0) {
                // For ../changelogs/index, the subdir is changelogs
                $pathParts = explode('/', substr($subdirPath, 3));
                $subdirName = $pathParts[0];
            } else {
                // For changelogs/index, the subdir is changelogs
                $subdirName = dirname($subdirPath);
                if ($subdirName === '.') {
                    $subdirName = basename($subdirPath, '/index');
                }
            }

            // Handle relative paths in filename
            if (strpos($filename, '../') === 0) {
                // Check if this is a relative path to another index file
                if (str_ends_with($filename, '/index')) {
                    // For relative paths, we need to call getSubdirectoryToctree with the current subdirectory context
                    // The current context is derived from $subdirPath (e.g., installation/index)
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

                // For "../license", use the filename as-is for link path
                $linkPath = $filename;
                if (!str_ends_with($linkPath, '.md')) {
                    $linkPath .= '.md';
                }

                // Use provided display name or get header from file
                if (!empty($displayName)) {
                    $finalDisplayName = $displayName;
                } else {
                    $headerDisplayName = getFirstHeaderFromFile($filename, dirname($folder));
                    $finalDisplayName = $headerDisplayName ?: ucwords(str_replace(['_', '-'], ' ', basename($filename, '.md')));
                }
            } else {
                // Regular subdirectory file
                $subdirFile = $subdirName . '/' . $filename;

                // Build the correct link path
                $linkPath = $subdirFile;
                if (!str_ends_with($linkPath, '.md')) {
                    $linkPath .= '.md';
                }

                // Use provided display name or get header from file
                if (!empty($displayName)) {
                    $finalDisplayName = $displayName;
                } else {
                    $headerDisplayName = getFirstHeaderFromFile($subdirFile, dirname($folder));
                    $finalDisplayName = $headerDisplayName ?: ucwords(str_replace(['_', '-'], ' ', basename($filename, '.md')));
                }
            }

            $entries[] = "- [$finalDisplayName]($linkPath)";
        }

        return $entries;
    }

    return null;
}

function getFirstHeaderFromFile($filename, $folder) {
    // Construct the full path to the target markdown file
    $targetFile = '';

    // First, determine the docs directory path from the current docs-rst folder
    $docsRstPath = realpath($folder);
    $docsPath = str_replace('/docs-rst/', '/docs/', $docsRstPath . '/');

    if (strpos($filename, '../') === 0) {
        // Handle relative paths that go up directories
        $relativePath = substr($filename, 3); // Remove '../'
        $targetFile = dirname($docsPath) . '/' . $relativePath . '.md';
    } elseif (strpos($filename, '/') !== false) {
        // Handle subdirectory paths
        $targetFile = $docsPath . $filename . '.md';
    } else {
        // Local file in same directory
        $targetFile = $docsPath . $filename . '.md';
    }

    // Try to read the file and extract the first header
    if (file_exists($targetFile)) {
        $content = file_get_contents($targetFile);

        // Look for the first markdown header (# Title)
        if (preg_match('/^#\s+(.+)$/m', $content, $matches)) {
            $header = trim($matches[1]);
            return $header;
        }
    }

    // Fallback: return null to use existing display name
    return null;
}
