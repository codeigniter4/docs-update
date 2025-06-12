<?php

// Handle toctree functionality from RST
function after_filter_toctree($data, $folder) {
    // Look for HTML toctree divs created by pandoc
    $pattern = '/<div class="toctree"[^>]*>\s*([^<]+)\s*<\/div>/';

    return preg_replace_callback($pattern, function($matches) use ($folder) {
        $content = trim($matches[1]);

        // Split the content by whitespace to get individual filenames
        $filenames = preg_split('/\s+/', $content);

        $result = [];

        foreach ($filenames as $filename) {
            if (empty($filename)) continue;

            $filename = trim($filename);

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
