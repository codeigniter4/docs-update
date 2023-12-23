<?php

// Handle including code samples
function before_filter_includes($data, $folder) {
    $literal = '  .. literalinclude::';
    if (str_contains($data, $literal)) {
        $data = str_replace($literal, '.. literalinclude::', $data);
    }

    return $data;
}

function after_filter_includes($data, $folder) {
    // We only need a parent folder
    $folder = explode('/', trim($folder,  '/'));
    $folder = array_pop($folder);

    $pattern = '/<div class="literalinclude"\s*(?:lines="(-?\d+(?:-\d*)?)")?>\s*(.*?)\s*<\/div>/s';

    return preg_replace_callback($pattern, function($matches) use ($folder) {
        $content = trim($matches[2]);
        $lines   = empty($matches[1]) ? '' : ':' . str_replace('-', ':', $matches[1]);
        return "```php\n--8<--\n" . "{$folder}/{$content}{$lines}\n--8<--\n```";
    }, $data);
}
