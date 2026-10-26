<?php

declare(strict_types=1);

/**
 * Outdents RST list items indented by seven or more spaces to two.
 */
function before_filter_list_indentation(string $data, string $folder): ?string {
    $pattern = '/^(\s{7,})(-\s+)/m';

    $data = preg_replace($pattern, '  $2', $data);

    return $data;
}

/**
 * Outdents Markdown list items indented by four or more spaces and puts a blank line before each list.
 */
function after_filter_list_indentation(string $data, string $folder): ?string {
    $pattern = '/^(\s{4,})(-\s+)/m';

    $data = preg_replace($pattern, '$2', $data);

    if ($data === null) {
        return null;
    }

    // Python-Markdown starts a list only after a blank line.
    $pattern = '/^(?!\h*(?:[-*+]|\d+\.)\s)(\h*\S[^\n]*)\n(-\s+)/m';
    $data = preg_replace($pattern, '$1' . "\n\n" . '$2', $data);

    return $data;
}
