<?php

declare(strict_types=1);

/**
 * Samples held back from the other filters, keyed by the number in their `CodeBlockPlaceholder<n>` marker.
 *
 * @return list<array{language: string, lines: list<string>}>
 */
function &includedSamples(): array {
    static $samples = [];

    return $samples;
}

/**
 * Replaces each `literalinclude` and `code-block` with a `CodeBlockPlaceholder<n>` marker line at the directive's indentation.
 */
function before_filter_includes(string $data, string $folder): ?string {
    $samples = &includedSamples();
    $lines   = explode("\n", $data);
    $result  = [];

    for ($i = 0; $i < count($lines); $i++) {
        if (! preg_match('/^(\s*)\.\.\s+(literalinclude|code-block)::\s*(\S*)\s*$/', $lines[$i], $matches)) {
            $result[] = $lines[$i];

            continue;
        }

        $indent    = $matches[1];
        $directive = [$lines[$i]];
        $options   = [];

        while (isset($lines[$i + 1]) && preg_match('/^(\s+):([a-z-]+):\s*(.*)$/', $lines[$i + 1], $option) && strlen($option[1]) > strlen($indent)) {
            $options[$option[2]] = trim($option[3]);
            $directive[]         = $lines[++$i];
        }

        if ($matches[2] === 'code-block') {
            $body = [];

            while (isset($lines[$i + 1]) && (trim($lines[$i + 1]) === '' || strlen($lines[$i + 1]) - strlen(ltrim($lines[$i + 1])) > strlen($indent))) {
                $body[] = $lines[++$i];
            }

            array_push($result, sprintf('%sCodeBlockPlaceholder%d', $indent, count($samples)), '');
            $samples[] = ['language' => $matches[3], 'lines' => dedentLines(trimBlankLines($body))];

            continue;
        }

        $file = str_starts_with($matches[3], '/') ? rtrim(SOURCE_DIR, '/') . $matches[3] : $folder . '/' . $matches[3];

        if (! is_file($file)) {
            fwrite(STDERR, sprintf("Warning: sample %s not found for literalinclude in %s\n", $matches[3], $folder));
            array_push($result, ...$directive);

            continue;
        }

        $sample = explode("\n", file_get_contents($file));

        if (isset($options['lines'])) {
            $sample = selectSampleLines($sample, $options['lines']);
        }

        $result[]  = sprintf('%sCodeBlockPlaceholder%d', $indent, count($samples));
        $samples[] = ['language' => $options['language'] ?? 'php', 'lines' => trimBlankLines($sample)];
    }

    return implode("\n", $result);
}

/**
 * Replaces each `CodeBlockPlaceholder<n>` marker line with its sample as a fenced block, prefixed like the marker.
 */
function after_filter_includes(string $data, string $folder): ?string {
    $samples = &includedSamples();

    return preg_replace_callback('/^(.*?)CodeBlockPlaceholder(\d+)[ \t]*$/m', function (array $matches) use ($samples): string {
        $prefix = $matches[1];
        $sample = $samples[(int) $matches[2]];

        preg_match_all('/`+/', implode("\n", $sample['lines']), $runs);
        $fence = str_repeat('`', max(3, ...array_map(fn (string $run): int => strlen($run) + 1, $runs[0] ?: [''])));

        $block = [$prefix . $fence . $sample['language']];

        foreach ($sample['lines'] as $line) {
            $block[] = rtrim($prefix . $line);
        }

        $block[] = $prefix . $fence;

        return implode("\n", $block);
    }, $data);
}

/**
 * Lines picked by a Sphinx `:lines:` option such as `2-`, `3-10` or `1,4-6`, counted from 1.
 *
 * @param list<string> $lines
 *
 * @return list<string>
 */
function selectSampleLines(array $lines, string $spec): array {
    $selected = [];

    foreach (explode(',', $spec) as $part) {
        $part = trim($part);

        if (preg_match('/^(\d*)-(\d*)$/', $part, $matches)) {
            $from = $matches[1] === '' ? 1 : (int) $matches[1];
            $to   = $matches[2] === '' ? count($lines) : (int) $matches[2];
        } else {
            $from = $to = (int) $part;
        }

        for ($i = max(1, $from); $i <= min($to, count($lines)); $i++) {
            $selected[] = $lines[$i - 1];
        }
    }

    return $selected;
}

/**
 * @param list<string> $lines
 *
 * @return list<string>
 */
function dedentLines(array $lines): array {
    $indents = array_map(
        fn (string $line): int => strlen($line) - strlen(ltrim($line)),
        array_filter($lines, fn (string $line): bool => trim($line) !== ''),
    );
    $indent = $indents === [] ? 0 : min($indents);

    return array_map(fn (string $line): string => substr($line, $indent), $lines);
}

/**
 * @param list<string> $lines
 *
 * @return list<string>
 */
function trimBlankLines(array $lines): array {
    while ($lines !== [] && trim($lines[0]) === '') {
        array_shift($lines);
    }

    while ($lines !== [] && trim(end($lines)) === '') {
        array_pop($lines);
    }

    return $lines;
}
