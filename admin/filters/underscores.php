<?php

declare(strict_types=1);

/**
 * Escapes the underscore that starts a path segment and the `::` between two words, which pandoc 3.13 reads as an RST inline target and as a link.
 */
function before_filter_underscores(string $data, string $folder): ?string
{
    $lines = explode("\n", $data);

    foreach (array_keys($lines) as $i) {
        $escaped = escape_inline_targets($lines[$i]);
        $added = strlen($escaped) - strlen($lines[$i]);
        $lines[$i] = $escaped;

        if ($added > 0 && isset($lines[$i + 1]) && is_adornment($lines[$i + 1])) {
            $lines[$i + 1] .= str_repeat($lines[$i + 1][0], $added);

            if ($i > 0 && is_adornment($lines[$i - 1]) && $lines[$i - 1][0] === $lines[$i + 1][0] && ($i === 1 || trim($lines[$i - 2]) === '')) {
                $lines[$i - 1] .= str_repeat($lines[$i - 1][0], $added);
            }
        }
    }

    return implode("\n", $lines);
}

function escape_inline_targets(string $line): string
{
    return preg_replace_callback('/(`[^`]+`|``.+?``)|[^\s`]+/', function (array $matches): string {
        if (isset($matches[1]) || str_contains($matches[0], '://') || str_contains($matches[0], '](')) {
            return $matches[0];
        }

        return preg_replace(['#(?<=/)_(?=\w)#', '/(?<=\w)::(?=\w)/'], ['\\\\_', '\\\\::'], $matches[0]);
    }, $line);
}

/**
 * Whether `$line` is a run of one punctuation character, as under an RST section title.
 */
function is_adornment(string $line): bool
{
    return preg_match('/^([!-\/:-@\[-`{-~])\1+$/', $line) === 1;
}
