<?php

declare(strict_types=1);

/**
 * Converts GitHub-style `> [!NOTE]` alerts and unwraps the blockquotes left by indented RST text.
 */
function after_filter_alerts(string $data, string $folder): ?string {
    $result = preg_replace_callback('/> \[!([A-Z]+)]/', function (array $matches): string {
        return '!!! ' . strtolower($matches[1]);
    }, $data);

    // A blockquote two spaces deep is the indented continuation of a list item.
    $result = preg_replace('/^  > ``` /m', '    ``` ', $result);
    $result = preg_replace('/^  > ([^!])/m', '    $1', $result);

    $result = preg_replace('/^> (?!\d+\.|!)/', '    ', $result);

    return preg_replace('/^> (\d+\.\s+)/m', '$1', $result);
}
