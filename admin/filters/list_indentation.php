<?php

declare(strict_types=1);

/**
 * Puts a blank line between a directive and a list that starts its body.
 */
function before_filter_list_indentation(string $data, string $folder): ?string {
    // pandoc reads a list right under a directive line as one paragraph, unlike docutils.
    return preg_replace('/^([ \t]*\.\. [a-z-]+::[ \t]*\n)([ \t]+(?:[-*+]|\d+\.) )/m', "$1\n$2", $data);
}

/**
 * Re-indents each nested list level by four spaces and puts a blank line before each list that follows text.
 */
function after_filter_list_indentation(string $data, string $folder): ?string {
    $lines  = explode("\n", $data);
    $levels = [];
    $result = [];

    foreach ($lines as $line) {
        if (trim($line) === '') {
            $result[] = '';
            continue;
        }

        $indent = strlen($line) - strlen(ltrim($line, ' '));

        while ($levels !== [] && $indent < end($levels)['source']) {
            array_pop($levels);
        }

        $level = end($levels);

        if (preg_match('/^ *((?:[-*+]|\d+[.)]) +)(?=\S)/', $line, $matches) === 1) {
            $marker = $level === false ? $indent : $level['target'];
            $line   = str_repeat(' ', $marker) . ltrim($line, ' ');

            $previous = end($result);

            // Python-Markdown starts a list only after a blank line.
            if ($previous !== false && trim($previous) !== '' && preg_match('/^ *(?:[-*+]|\d+[.)]) +\S/', $previous) !== 1) {
                $result[] = '';
            }

            $levels[] = ['source' => $indent + strlen($matches[1]), 'target' => $marker + 4];
        } elseif ($level !== false) {
            $line = str_repeat(' ', $level['target'] + $indent - $level['source']) . ltrim($line, ' ');
        }

        $result[] = $line;
    }

    return implode("\n", $result);
}
