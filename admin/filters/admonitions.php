<?php

declare(strict_types=1);

const ADMONITION_CLASSES = ['attention', 'caution', 'danger', 'error', 'hint', 'important', 'note', 'seealso', 'tip', 'warning'];

/**
 * Admonition type and `sprintf()` title for each Sphinx version directive.
 */
const VERSION_ADMONITIONS = [
    'versionadded'   => ['success', 'Available from version %s'],
    'versionchanged' => ['info', 'Changed in version %s'],
    'deprecated'     => ['danger', 'Deprecated since version %s'],
];

function after_filter_admonitions(string $data, string $folder): string {
    return implode("\n", convert_admonitions(explode("\n", $data)));
}

/**
 * @param list<string> $lines
 *
 * @return list<string>
 */
function convert_admonitions(array $lines): array {
    $result = [];

    for ($i = 0; $i < count($lines); $i++) {
        // pandoc wraps a paragraph that follows an RST label in a div carrying the label as its id.
        if (preg_match('/^\s*<div id="[^"]*">\s*$/', $lines[$i]) && ($end = closing_div_line($lines, $i)) !== null) {
            array_push($result, ...convert_admonitions(array_slice($lines, $i + 1, $end - $i - 1)));
            $i = $end;

            continue;
        }

        if (
            ! preg_match('/^(\s*)<div class="([a-z]+)"[^>]*>\s*$/', $lines[$i], $matches)
            || ! (in_array($matches[2], ADMONITION_CLASSES, true) || isset(VERSION_ADMONITIONS[$matches[2]]))
            || ($end = closing_div_line($lines, $i)) === null
        ) {
            $result[] = $lines[$i];

            continue;
        }

        $indent = $matches[1];
        $body   = array_map(
            fn (string $line): string => str_starts_with($line, $indent) ? substr($line, strlen($indent)) : ltrim($line),
            array_slice($lines, $i + 1, $end - $i - 1),
        );

        [$type, $title, $body] = admonition_header($matches[2], $body);

        $result[] = sprintf('%s!!! %s "%s"', $indent, $type, $title);

        foreach (trim_blank_lines(convert_admonitions($body)) as $line) {
            $result[] = trim($line) === '' ? '' : $indent . '    ' . $line;
        }

        $i = $end;
    }

    return $result;
}

/**
 * @param list<string> $lines
 */
function closing_div_line(array $lines, int $start): ?int {
    $depth = 0;

    for ($i = $start; $i < count($lines); $i++) {
        $depth += preg_match('/^\s*<div\b/', $lines[$i]) - preg_match('/^\s*<\/div>\s*$/', $lines[$i]);

        if ($depth === 0) {
            return $i;
        }
    }

    return null;
}

/**
 * Type, title and remaining body of an admonition, taken from pandoc's title div or a version directive's leading version number.
 *
 * @param list<string> $body
 *
 * @return array{string, string, list<string>}
 */
function admonition_header(string $class, array $body): array {
    $first = array_key_first(array_filter($body, fn (string $line): bool => trim($line) !== ''));

    if (isset(VERSION_ADMONITIONS[$class])) {
        [$type, $title] = VERSION_ADMONITIONS[$class];

        if ($first !== null && preg_match('/^(\d+(?:\.\d+)*)\s*(.*)$/', trim($body[$first]), $matches)) {
            $body[$first] = $matches[2];

            return [$type, sprintf($title, $matches[1]), array_values($body)];
        }

        return [$type, sprintf($title, '?'), $body];
    }

    if ($first !== null && preg_match('/^<div class="title">\s*$/', trim($body[$first])) && ($end = closing_div_line($body, $first)) !== null) {
        $title = trim(implode(' ', array_map(trim(...),array_slice($body, $first + 1, $end - $first - 1))));

        return [$class, $title, array_values(array_slice($body, $end + 1))];
    }

    return [$class, ucfirst($class), $body];
}
