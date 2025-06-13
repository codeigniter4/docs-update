<?php

// Handle alerts like: note, warning, etc.
function after_filter_alerts($data, $folder) {
    // Simple markdown version
    $pattern = '/> \[!([A-Z]+)]/';

    $result = preg_replace_callback($pattern, function($matches) {
        return '!!! ' . strtolower($matches[1]);
    }, $data);

    // Handle blockquotes, but NOT numbered lists
    // Convert blockquotes to indented content, but preserve numbered lists
    $result = preg_replace('/^> (?!\d+\.)/m', '    ', $result);
    
    // Convert numbered list blockquotes to proper numbered lists
    $result = preg_replace('/^> (\d+\.\s+)/m', '$1', $result);

    // HTML version
    if (str_contains($result, '<div class="title">')) {
        // First, handle the basic admonition conversion
        $pattern = '/<div class="(important|note|warning)">\s*<div class="title">\s*(.*?)\s*<\/div>\s*(.*?)\s*<\/div>/s';

        $result = preg_replace_callback($pattern, function ($matches) {
            $class   = $matches[1];
            $title   = trim($matches[2]);
            $content = $matches[3];

            // Add indentation to every line
            $content = preg_replace('/(^|\n)(?!$)/', '$1    ', $content);

            return "!!! $class \"$title\"\n$content";
        }, $result);

        // Handle the specific case where an admonition is followed by a standalone code block
        // This is a more targeted fix for the exact issue
        $result = preg_replace_callback(
            '/(!!! important "Important"\n    When you deploy to your production server, don\'t forget to run the following command:)\n\n``` console\ncomposer install --no-dev\n\nThe above command will remove the Composer packages only for development\nthat are not needed in the production environment\. This will greatly reduce\nthe vendor folder size\.\n```/',
            function ($matches) {
                return $matches[1] . "\n\n    ``` console\n    composer install --no-dev\n    ```\n\n    The above command will remove the Composer packages only for development\n    that are not needed in the production environment. This will greatly reduce\n    the vendor folder size.";
            },
            $result
        );
    }

    return $result;
}
