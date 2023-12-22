<?php

// Handle alerts like: note, warning, etc.
function after_filter_alerts($data, $folder) {
    // Simple markdown version
    $pattern = '/> \[!([A-Z]+)]/';

    $result = preg_replace_callback($pattern, function($matches) {
        return '!!! ' . strtolower($matches[1]);
    }, $data);

    $result = preg_replace('/^>/m', '    ', $result);

    // HTML version
    if (str_contains($result, '<div class="title">')) {
        $pattern = '/<div class="(important|note)">\s*<div class="title">\s*(.*?)\s*<\/div>\s*(.*?)\s*<\/div>/s';

        $result = preg_replace_callback($pattern, function ($matches) {
            $class   = $matches[1];
            $title   = trim($matches[2]);
            $content = $matches[3];

            // Add indentation to every line
            $content = preg_replace('/(^|\n)(?!$)/', '$1    ', $content);

            return "!!! $class \"$title\"\n$content";
        }, $result);
    }

    return $result;
}
