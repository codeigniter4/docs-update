<?php

/**
 * Fix excessive list indentation in RST files before Pandoc conversion
 * Convert 7+ space indented list items to proper RST list formatting
 */
function before_filter_listIndentation($data, $folder) {
    // Pattern to match lines with excessive indentation (7+ spaces) followed by a dash
    $pattern = '/^(\s{7,})(-\s+)/m';

    // Replace with 2 spaces + dash for proper RST list formatting
    $data = preg_replace($pattern, '  $2', $data);

    return $data;
}

/**
 * Fix excessive list indentation in markdown files after Pandoc conversion
 * Convert 4+ space indented list items to proper markdown list formatting
 * and ensure blank lines before lists
 */
function after_filter_listIndentation($data, $folder) {
    // Pattern to match lines with 4+ spaces followed by a dash (markdown code block territory)
    $pattern = '/^(\s{4,})(-\s+)/m';

    // Replace with no leading spaces for proper markdown list formatting
    $data = preg_replace($pattern, '$2', $data);

    // Ensure blank line before list items
    // Look for lines ending with text followed immediately by a list item
    $pattern = '/([^\n])\n(-\s+)/';
    $data = preg_replace($pattern, '$1' . "\n\n" . '$2', $data);

    return $data;
}
