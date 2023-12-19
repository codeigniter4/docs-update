<?php

// Handle including code samples
function transform_includes($data, $folder) {
    $pattern = '/<div class="literalinclude"\s*(?:lines="(-?\d+(?:-\d*)?)")?>\s*(.*?)\s*<\/div>/s';

    return preg_replace_callback($pattern, function($matches) use ($folder) {
        $content = trim($matches[2]);
        $lines   = empty($matches[1]) ? '' : ':' . str_replace('-', ':', $matches[1]);
        return "```php\n--8<--\n" . "{$folder}/{$content}{$lines}\n--8<--\n```";
    }, $data);
}
