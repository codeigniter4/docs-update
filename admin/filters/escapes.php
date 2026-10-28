<?php

declare(strict_types=1);

/**
 * Rewrites the backslash escapes written by pandoc that Python-Markdown does not honour, outside code spans.
 */
function after_filter_escapes(string $data, string $folder): ?string
{
    return preg_replace_callback('/^(?!\s*CodeBlockPlaceholder\d).+$/m', function (array $matches): string {
        $isTableRow = str_starts_with(ltrim($matches[0]), '|');

        preg_match_all('/(`+).+?(?<!`)\1(?!`)/', $matches[0], $spans, PREG_OFFSET_CAPTURE);

        $result = '';
        $offset = 0;

        foreach ($spans[0] as [$span, $start]) {
            $result .= escape_prose(substr($matches[0], $offset, $start - $offset), $isTableRow) . $span;
            $offset = $start + strlen($span);
        }

        return $result . escape_prose(substr($matches[0], $offset), $isTableRow);
    }, $data);
}

/**
 * `$text` with pandoc's `\<`, `\&` and `\|` written the way Python-Markdown shows them, and with `**strong**` restored after a quote.
 */
function escape_prose(string $text, bool $isTableRow): string
{
    $text = str_replace(['\\<', '\\&'], ['&lt;', '&amp;'], $text);

    if (!$isTableRow) {
        $text = str_replace('\\|', '|', $text);
    }

    // pandoc leaves `**strong**` after a quote mark as text, unlike docutils.
    return preg_replace('/(?<=["\'])\\\\\*\\\\\*(\S(?:[^*\n]*?\S)?)\\\\\*\\\\\*/', '**$1**', $text);
}
