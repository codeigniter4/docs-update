<?php

// Handle toctree functionality from RST
function after_filter_toctree($data, $folder) {
    // Look for HTML toctree divs created by pandoc - handle both with and without <p> tags
    $pattern = '/<div class="toctree"[^>]*>\s*(?:<p>\s*)?([^<]+?)(?:\s*<\/p>)?\s*<\/div>/s';

    return preg_replace_callback($pattern, function($matches) use ($folder) {
        $content = trim($matches[1]);

        // Split the content by whitespace to get individual filenames
        $filenames = preg_split('/\s+/', $content);

        $result = [];

        foreach ($filenames as $filename) {
            if (empty($filename)) continue;

            $filename = trim($filename);

            // Check if this is a subdirectory index reference (e.g., "changelogs/index" or "../changelogs/index")
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

            // Use the header-based name if we found one, otherwise create a fallback
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

        return implode("\n", $result);
    }, $data);
}

function getSubdirectoryToctree($subdirPath, $folder) {
    // Construct the path to the subdirectory's RST index file
    $rstPath = realpath($folder);

    // Handle relative paths properly
    if (strpos($subdirPath, '../') === 0) {
        // For paths like ../changelogs/index, we need to go up one directory
        $relativePath = substr($subdirPath, 3); // Remove '../'
        $subdirRstFile = dirname($rstPath) . '/' . $relativePath . '.rst';
    } else {
        $subdirRstFile = $rstPath . '/' . $subdirPath . '.rst';
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

            // Try to get the first header from the target file in the subdirectory
            $subdirFile = $subdirName . '/' . $trimmed;
            $headerDisplayName = getFirstHeaderFromFile($subdirFile, dirname($folder));

            // Use the header-based name if we found one, otherwise create a fallback
            if ($headerDisplayName) {
                $displayName = $headerDisplayName;
            } else {
                // Create a fallback display name from filename
                $displayName = ucwords(str_replace(['_', '-'], ' ', basename($trimmed, '.md')));
            }

            // Build the correct link path
            $linkPath = $subdirName . '/' . $trimmed;
            if (!str_ends_with($linkPath, '.md')) {
                $linkPath .= '.md';
            }

            $entries[] = "- [$displayName]($linkPath)";
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
