<?php

// Handle alerts like: note, warning, etc.
function after_filter_alerts($data, $folder) {
    $pattern = '/> \[!([A-Z]+)]/';

    $result = preg_replace_callback($pattern, function($matches) {
        return '!!! ' . strtolower($matches[1]);
    }, $data);

    $result = preg_replace('/^>/m', '    ', $result);

//    $pattern = '/<div class="note">\s*<div class="title">\s*(.*?)\s*<\/div>\s*(.*?)\s*<\/div>/s';
//    $replacement = '!!! $1' . PHP_EOL . '    $2';
//    $result = preg_replace($pattern, $replacement, $result);

    return $result;
}
