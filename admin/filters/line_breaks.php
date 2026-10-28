<?php

declare(strict_types=1);

/**
 * Replaces pandoc's backslash hard line breaks with the two trailing spaces that Python-Markdown reads as one.
 */
function after_filter_line_breaks(string $data, string $folder): ?string
{
    return preg_replace('/(?<!\\\\)\\\\$/m', '  ', $data);
}
