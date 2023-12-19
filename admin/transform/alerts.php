<?php

// Handle alerts like: note, warning, etc.
function transform_alerts($data, $folder) {
    $pattern = '/> \[!([A-Z]+)]/';

    $result = preg_replace_callback($pattern, function($matches) {
        return '!!! ' . strtolower($matches[1]);
    }, $data);

    return preg_replace('/^>/m', '    ', $result);
}
