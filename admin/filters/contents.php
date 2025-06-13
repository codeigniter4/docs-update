<?php

// Handle contents directive from RST - generates table of contents for current page
function after_filter_contents($data, $folder) {
    // Look for HTML div elements with class="contents" (converted from RST by pandoc)
    $pattern = '/<div class="contents"([^>]*)>\s*<\/div>/';

    return preg_replace_callback($pattern, function($matches) use ($data) {
        $attributes = $matches[1];
        $depth = 3; // Default depth
        $local = true; // Default to local (current page only)

        // Parse attributes from the div tag
        if (preg_match('/depth="(\d+)"/', $attributes, $depthMatch)) {
            $depth = (int)$depthMatch[1];
        }
        if (preg_match('/local(?:="[^"]*")?/', $attributes)) {
            $local = true;
        }

        // Generate TOC based on headers in the document
        return generateTableOfContents($data, $depth, $local);
    }, $data);
}

function generateTableOfContents($content, $maxDepth = 3, $local = true) {
    // Find all headers in the content
    $headers = [];
    $lines = explode("\n", $content);

    for ($i = 0; $i < count($lines); $i++) {
        $line = $lines[$i];

        // Check for markdown headers (# ## ### etc.)
        if (preg_match('/^(#{1,6})\s+(.+)$/', $line, $matches)) {
            $level = strlen($matches[1]);
            $title = trim($matches[2]);

            // Skip H1 headings - start from H2 as top level
            if ($level == 1) {
                continue;
            }

            // Adjust level so H2 becomes level 1, H3 becomes level 2, etc.
            $adjustedLevel = $level - 1;

            if ($adjustedLevel <= $maxDepth) {
                $headers[] = [
                    'level' => $adjustedLevel,
                    'title' => $title,
                    'anchor' => generateAnchor($title)
                ];
            }
        }
    }

    // Generate the TOC markdown
    if (empty($headers)) {
        return '';
    }

    $toc = [];

    foreach ($headers as $header) {
        // Create proper indentation: level 1 = no indent, level 2 = 4 spaces, level 3 = 8 spaces, etc.
        // Using 4 spaces per level for proper markdown nested list formatting
        $indent = str_repeat('    ', $header['level'] - 1);
        $toc[] = $indent . '- [' . $header['title'] . '](#' . $header['anchor'] . ')';
    }

    return implode("\n", $toc);
}

function generateAnchor($title) {
    // Convert title to a URL-friendly anchor
    $anchor = strtolower($title);

    // Remove special characters and replace spaces with hyphens
    $anchor = preg_replace('/[^a-z0-9\s-]/', '', $anchor);
    $anchor = preg_replace('/\s+/', '-', $anchor);
    $anchor = trim($anchor, '-');

    return $anchor;
}
