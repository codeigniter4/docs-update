<?php

declare(strict_types=1);

/**
 * Escapes the underscore that starts a path segment, which pandoc 3.13 reads as an RST inline target.
 */
function before_filter_underscores(string $data, string $folder): ?string
{
    return preg_replace_callback('/(``.+?``)|\S+/', function (array $matches): string {
        if (isset($matches[1]) || str_contains($matches[0], '://') || str_contains($matches[0], '](')) {
            return $matches[0];
        }

        return preg_replace('#(?<=/)_(?=\w)#', '\\\\_', $matches[0]);
    }, $data);
}
