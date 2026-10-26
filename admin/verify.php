<?php

declare(strict_types=1);

define('ADMONITIONS', [
    'attention', 'caution', 'danger', 'deprecated', 'error', 'hint', 'important',
    'note', 'seealso', 'tip', 'versionadded', 'versionchanged', 'warning',
]);
define('SKIPPED_DIRECTORIES', ['_static', 'images']);
define('TAIL_TOKENS', 4);
define('ALIGNMENT_WINDOW', 3);
define('MAX_LISTED_FAILURES', 20);

const STYLE_HEADING = '1';
const STYLE_PASS    = '32';
const STYLE_FAIL    = '1;31';

/**
 * `$text` in the ANSI SGR `$style` when standard output is a terminal and `NO_COLOR` is unset.
 */
function styled(string $text, string $style): string {
    if ((string) getenv('NO_COLOR') !== '' || ! stream_isatty(STDOUT)) {
        return $text;
    }

    return sprintf("\033[%sm%s\033[0m", $style, $text);
}

/**
 * ASCII table with a bold header row, padding each cell before styling it.
 *
 * @param list<string>                       $headers
 * @param list<list<array{string, string}>>  $rows         Cells as `[text, style]` pairs
 * @param list<int>                          $rightAligned Column indexes
 */
function renderTable(array $headers, array $rows, array $rightAligned): string {
    $widths = array_map('strlen', $headers);

    foreach ($rows as $row) {
        foreach ($row as $column => [$text]) {
            $widths[$column] = max($widths[$column], strlen($text));
        }
    }

    $border = '+' . implode('+', array_map(fn (int $width): string => str_repeat('-', $width + 2), $widths)) . "+\n";
    $line   = function (array $cells) use ($widths, $rightAligned): string {
        $padded = [];

        foreach ($cells as $column => [$text, $style]) {
            $text     = str_pad($text, $widths[$column], ' ', in_array($column, $rightAligned, true) ? STR_PAD_LEFT : STR_PAD_RIGHT);
            $padded[] = $style === '' ? $text : styled($text, $style);
        }

        return sprintf("| %s |\n", implode(' | ', $padded));
    };

    $table = $border . $line(array_map(fn (string $header): array => [$header, STYLE_HEADING], $headers)) . $border;

    foreach ($rows as $row) {
        $table .= $line($row);
    }

    return $table . $border;
}

function usage(): never {
    fwrite(STDERR, "Usage: php admin/verify.php --source <rst dir> --output <docs dir> [--summary <file>]\n");
    exit(2);
}

/**
 * @return list<string>
 */
function readLines(string $path): array {
    return preg_split('/\r\n|\n|\r/', file_get_contents($path));
}

function indentOf(string $line): int {
    return strlen($line) - strlen(ltrim($line, ' '));
}

function isBlank(string $line): bool {
    return trim($line) === '';
}

/**
 * @param list<string> $lines
 */
function normaliseCode(array $lines): string {
    $lines = array_map('rtrim', $lines);

    while ($lines !== [] && $lines[0] === '') {
        array_shift($lines);
    }

    while ($lines !== [] && end($lines) === '') {
        array_pop($lines);
    }

    $indents = array_map('indentOf', array_filter($lines, fn (string $line): bool => $line !== ''));
    $indent  = $indents === [] ? 0 : min($indents);

    return implode("\n", array_map(fn (string $line): string => substr($line, $indent), $lines));
}

function normaliseSignature(string $signature): string {
    $signature = preg_replace('/\\\\([!-\/:-@\[-`{-~])/', '$1', $signature);

    return preg_replace('/\s+/', ' ', trim($signature));
}

/**
 * @return list<string>
 */
function proseTokens(string $text, bool $markdown): array {
    if ($markdown) {
        $text = preg_replace('/\]\([^)]*\)/', ']', $text);
    } else {
        $text = preg_replace('/:[a-z:]+:`/', '`', $text);
        $text = preg_replace('/\s*<[^<>`]*>`/', '`', $text);
    }

    preg_match_all('/[A-Za-z0-9]+/', strtolower($text), $matches);

    return $matches[0];
}

/**
 * @param list<string> $haystack
 * @param list<string> $needle
 */
function containsSequence(array $haystack, array $needle): bool {
    return str_contains(' ' . implode(' ', $haystack) . ' ', ' ' . implode(' ', $needle) . ' ');
}

/**
 * Index one past the last non-blank line indented deeper than `$indent` after `$start`.
 *
 * @param list<string> $lines
 */
function blockEnd(array $lines, int $start, int $indent): int {
    $end = $start + 1;

    for ($i = $start + 1; $i < count($lines); $i++) {
        if (! isBlank($lines[$i])) {
            if (indentOf($lines[$i]) <= $indent) {
                break;
            }

            $end = $i + 1;
        }
    }

    return $end;
}

/**
 * @param list<string> $lines
 *
 * @return array{array<string, string>, int}
 */
function directiveOptions(array $lines, int $start, int $indent): array {
    $options = [];

    for ($i = $start + 1; $i < count($lines); $i++) {
        if (! preg_match('/^(\s*):([a-z-]+):\s*(.*)$/', $lines[$i], $matches) || strlen($matches[1]) <= $indent) {
            break;
        }

        $options[$matches[2]] = trim($matches[3]);
    }

    return [$options, $i];
}

/**
 * @param list<string> $lines
 *
 * @return list<string>
 */
function selectLines(array $lines, string $spec): array {
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
 * @return array{
 *     signatures: list<array{line: int, text: string}>,
 *     samples: list<array{line: int, label: string, body: string|null}>,
 *     admonitions: list<array{line: int, type: string, tail: list<string>, samples: int}>,
 * }
 */
function parseRst(string $path, string $sourceRoot): array {
    $lines  = readLines($path);
    $result = ['signatures' => [], 'samples' => [], 'admonitions' => []];

    foreach ($lines as $no => $line) {
        if (preg_match('/^\s*\.\.\s+php:(?:method|function|staticmethod)::\s*(.+)$/', $line, $matches)) {
            $result['signatures'][] = ['line' => $no + 1, 'text' => normaliseSignature($matches[1])];

            continue;
        }

        if (preg_match('/^(\s*)\.\.\s+literalinclude::\s*(\S+)/', $line, $matches)) {
            [$options] = directiveOptions($lines, $no, strlen($matches[1]));

            $file = str_starts_with($matches[2], '/')
                ? $sourceRoot . $matches[2]
                : dirname($path) . '/' . $matches[2];

            $body = null;

            if (is_file($file)) {
                $fileLines = explode("\n", file_get_contents($file));

                if (isset($options['lines'])) {
                    $fileLines = selectLines($fileLines, $options['lines']);
                }

                $body = normaliseCode($fileLines);
            }

            $result['samples'][] = ['line' => $no + 1, 'label' => $matches[2], 'body' => $body];

            continue;
        }

        if (preg_match('/^(\s*)\.\.\s+code-block::\s*(\S*)/', $line, $matches)) {
            $indent        = strlen($matches[1]);
            [, $bodyStart] = directiveOptions($lines, $no, $indent);
            $end           = blockEnd($lines, $no, $indent);

            $result['samples'][] = [
                'line'  => $no + 1,
                'label' => 'code-block ' . ($matches[2] === '' ? '(no language)' : $matches[2]),
                'body'  => normaliseCode(array_slice($lines, $bodyStart, max(0, $end - $bodyStart))),
            ];

            continue;
        }

        if (preg_match('/^(\s*)\.\.\s+([a-z]+)::(.*)$/', $line, $matches) && in_array($matches[2], ADMONITIONS, true)) {
            $end  = blockEnd($lines, $no, strlen($matches[1]));
            $body = array_slice($lines, $no + 1, $end - $no - 1);

            $prose  = array_filter($body, fn (string $bodyLine): bool => ! preg_match('/^\s*(\.\.\s+\S+::|:[a-z-]+:)/', $bodyLine));
            $tokens = proseTokens($matches[3] . "\n" . implode("\n", $prose), false);

            $result['admonitions'][] = [
                'line'    => $no + 1,
                'type'    => $matches[2],
                'tail'    => array_slice($tokens, -TAIL_TOKENS),
                'samples' => count(preg_grep('/^\s*\.\.\s+(literalinclude|code-block)::/', $body)),
            ];
        }
    }

    return $result;
}

/**
 * @return array{
 *     lines: list<string>,
 *     headings: list<array{line: int, text: string}>,
 *     fences: list<array{line: int, body: string}>,
 *     admonitions: list<array{line: int, end: int, tokens: list<string>, fences: int}>,
 *     prose: array<int, string>,
 * }
 */
function parseMarkdown(string $path): array {
    $lines  = readLines($path);
    $result = ['lines' => $lines, 'headings' => [], 'fences' => [], 'admonitions' => [], 'prose' => []];
    $fence  = null;

    foreach ($lines as $no => $line) {
        if ($fence !== null) {
            if (preg_match('/^\s*' . preg_quote($fence['marker'][0], '/') . '{' . strlen($fence['marker']) . ',}\s*$/', $line)) {
                $result['fences'][] = ['line' => $fence['line'], 'body' => normaliseCode($fence['body'])];
                $fence              = null;
            } else {
                $fence['body'][] = $line;
            }

            continue;
        }

        if (preg_match('/^\s*(`{3,}|~{3,})/', $line, $matches)) {
            $fence = ['line' => $no + 1, 'marker' => $matches[1], 'body' => []];

            continue;
        }

        $result['prose'][$no + 1] = $line;

        if (preg_match('/^#{1,6}\s+(.+?)\s*$/', $line, $matches)) {
            $result['headings'][] = ['line' => $no + 1, 'text' => normaliseSignature($matches[1])];
        }

        if (preg_match('/^(\s*)!!!\s+\w+(.*)$/', $line, $matches)) {
            $end = blockEnd($lines, $no, strlen($matches[1]));

            $result['admonitions'][] = [
                'line'   => $no + 1,
                'end'    => $end,
                'tokens' => proseTokens($matches[2] . "\n" . implode("\n", array_slice($lines, $no + 1, $end - $no - 1)), true),
            ];
        }
    }

    if ($fence !== null) {
        $result['fences'][] = ['line' => $fence['line'], 'body' => normaliseCode($fence['body'])];
    }

    foreach ($result['admonitions'] as &$admonition) {
        $admonition['fences'] = count(array_filter(
            $result['fences'],
            fn (array $fence): bool => $fence['line'] > $admonition['line'] && $fence['line'] <= $admonition['end'],
        ));
    }

    return $result;
}

/**
 * @return list<string>
 */
function sourcePages(string $root, string $relative = ''): array {
    $pages = [];

    foreach (scandir($root . '/' . $relative) as $entry) {
        if ($entry === '.' || $entry === '..' || in_array($entry, SKIPPED_DIRECTORIES, true)) {
            continue;
        }

        $path = ltrim($relative . '/' . $entry, '/');

        if (is_dir($root . '/' . $path)) {
            $pages = array_merge($pages, sourcePages($root, $path));
        } elseif (str_ends_with($entry, '.rst')) {
            $pages[] = substr($path, 0, -4);
        }
    }

    return $pages;
}

/**
 * @return array{expected: int, actual: int, failures: list<string>}
 */
function newCheck(): array {
    return ['expected' => 0, 'actual' => 0, 'failures' => []];
}

/**
 * @param array{expected: int, actual: int, failures: list<string>} $check
 * @param list<array<string, mixed>>                                 $wanted
 * @param list<array<string, mixed>>                                 $available
 * @param callable(array<string, mixed>): string                     $describe
 */
function matchMultiset(array &$check, array $wanted, array $available, string $key, string $sourceFile, callable $describe): void {
    $pool = array_count_values(array_filter(array_column($available, $key), 'is_string'));

    foreach ($wanted as $item) {
        $check['expected']++;

        if ($item[$key] !== null && ($pool[$item[$key]] ?? 0) > 0) {
            $pool[$item[$key]]--;
            $check['actual']++;
        } else {
            $check['failures'][] = sprintf('%s:%d %s', $sourceFile, $item['line'], $describe($item));
        }
    }
}

/**
 * @param array{expected: int, actual: int, failures: list<string>}                 $admonitions
 * @param array{expected: int, actual: int, failures: list<string>}                 $notesSamples
 * @param list<array{line: int, type: string, tail: list<string>, samples: int}>    $source
 * @param list<array{line: int, end: int, tokens: list<string>, fences: int}>       $output
 */
function checkAdmonitions(array &$admonitions, array &$notesSamples, array $source, array $output, string $sourceFile, string $outputFile): void {
    $pointer = 0;

    foreach ($source as $admonition) {
        $admonitions['expected']++;
        $notesSamples['expected'] += $admonition['samples'];

        $match = null;

        for ($j = $pointer; $j < min(count($output), $pointer + ALIGNMENT_WINDOW); $j++) {
            if (containsSequence($output[$j]['tokens'], $admonition['tail'])) {
                $match   = $output[$j];
                $pointer = $j + 1;

                break;
            }
        }

        if ($match === null) {
            $admonitions['failures'][] = sprintf('%s:%d %s body not found in a `!!!` block of %s', $sourceFile, $admonition['line'], $admonition['type'], $outputFile);

            if ($admonition['samples'] > 0) {
                $notesSamples['failures'][] = sprintf('%s:%d %d sample(s) in an unmatched %s', $sourceFile, $admonition['line'], $admonition['samples'], $admonition['type']);
            }

            continue;
        }

        $admonitions['actual']++;

        if ($admonition['samples'] === 0) {
            continue;
        }

        if ($match['fences'] === $admonition['samples']) {
            $notesSamples['actual'] += $admonition['samples'];
        } else {
            $notesSamples['failures'][] = sprintf(
                '%s:%d %s holds %d sample(s), %s:%d holds %d fenced block(s)',
                $sourceFile,
                $admonition['line'],
                $admonition['type'],
                $admonition['samples'],
                $outputFile,
                $match['line'],
                $match['fences'],
            );
        }
    }
}

/**
 * @param array<string, array{expected: int, actual: int, failures: list<string>}> $checks
 * @param array{lines: list<string>, prose: array<int, string>}                     $markdown
 */
function checkLeftovers(array &$checks, array $markdown, string $outputFile): void {
    $patterns = [
        'leftover-div'        => '/<div\b/i',
        'leftover-span'       => '/<span\b/i',
        'leftover-a'          => '/<a\s/i',
        'leftover-directives' => '/^\s*\.\.\s+[A-Za-z][\w:-]*::/',
        'leftover-roles'      => '/:(doc|ref|php:[a-z]+):/',
    ];

    foreach ($markdown['lines'] as $no => $line) {
        if (str_contains($line, '--8<--')) {
            $checks['leftover-snippets']['actual']++;
            $checks['leftover-snippets']['failures'][] = sprintf('%s:%d', $outputFile, $no + 1);
        }
    }

    foreach ($markdown['prose'] as $no => $line) {
        $withoutCode = preg_replace('/(`+).*?\1/', '', $line);

        foreach ($patterns as $name => $pattern) {
            if (preg_match($pattern, $withoutCode)) {
                $checks[$name]['actual']++;
                $checks[$name]['failures'][] = sprintf('%s:%d %s', $outputFile, $no, trim($line));
            }
        }
    }
}

/**
 * @param array<string, array{expected: int, actual: int, failures: list<string>}> $checks
 */
function report(array $checks, ?string $summaryFile): void {
    $rows     = [];
    $markdown = "## Conversion checks\n\n| Check | Expected | Actual | Result |\n| --- | ---: | ---: | --- |\n";
    $details  = '';

    foreach ($checks as $name => $check) {
        $passed = $check['failures'] === [];

        $rows[]    = [[$name, ''], [(string) $check['expected'], ''], [(string) $check['actual'], ''], $passed ? ['pass', STYLE_PASS] : ['FAIL', STYLE_FAIL]];
        $markdown .= sprintf("| `%s` | %d | %d | %s |\n", $name, $check['expected'], $check['actual'], $passed ? '✅' : '❌');

        if (! $passed) {
            $listed   = array_slice($check['failures'], 0, MAX_LISTED_FAILURES);
            $details .= sprintf("\n<details><summary><code>%s</code>: %d failure(s)</summary>\n\n", $name, count($check['failures']));
            $details .= implode('', array_map(fn (string $failure): string => '- ' . htmlspecialchars($failure, ENT_NOQUOTES) . "\n", $listed));
            $details .= "\n</details>\n";
        }
    }

    echo renderTable(['Check', 'Expected', 'Actual', 'Result'], $rows, [1, 2]);

    foreach ($checks as $name => $check) {
        if ($check['failures'] !== []) {
            printf("\n%s\n", styled(sprintf('%s (%d):', $name, count($check['failures'])), STYLE_HEADING));

            foreach (array_slice($check['failures'], 0, MAX_LISTED_FAILURES) as $failure) {
                echo '  ', $failure, "\n";
            }
        }
    }

    if ($summaryFile !== null) {
        file_put_contents($summaryFile, $markdown . $details, FILE_APPEND);
    }
}

$options = getopt('', ['source:', 'output:', 'summary:']);

if (! isset($options['source'], $options['output']) || ! is_dir($options['source']) || ! is_dir($options['output'])) {
    usage();
}

$sourceRoot = rtrim(realpath($options['source']), '/');
$outputRoot = rtrim(realpath($options['output']), '/');

$names  = ['pages', 'signatures', 'samples', 'admonitions', 'notes-samples', 'leftover-snippets', 'leftover-div', 'leftover-span', 'leftover-a', 'leftover-directives', 'leftover-roles'];
$checks = array_fill_keys($names, newCheck());

foreach (sourcePages($sourceRoot) as $page) {
    $sourceFile = $page . '.rst';
    $outputFile = $page . '.md';

    $checks['pages']['expected']++;

    if (! is_file($outputRoot . '/' . $outputFile)) {
        $checks['pages']['failures'][] = sprintf('%s has no %s', $sourceFile, $outputFile);

        continue;
    }

    if (trim(file_get_contents($outputRoot . '/' . $outputFile)) === '' && trim(file_get_contents($sourceRoot . '/' . $sourceFile)) !== '') {
        $checks['pages']['failures'][] = sprintf('%s is empty', $outputFile);

        continue;
    }

    $checks['pages']['actual']++;

    $rst      = parseRst($sourceRoot . '/' . $sourceFile, $sourceRoot);
    $markdown = parseMarkdown($outputRoot . '/' . $outputFile);

    matchMultiset($checks['signatures'], $rst['signatures'], $markdown['headings'], 'text', $sourceFile, fn (array $item): string => $item['text']);
    matchMultiset($checks['samples'], $rst['samples'], $markdown['fences'], 'body', $sourceFile, fn (array $item): string => $item['body'] === null ? $item['label'] . ' (file not found)' : $item['label']);
    checkAdmonitions($checks['admonitions'], $checks['notes-samples'], $rst['admonitions'], $markdown['admonitions'], $sourceFile, $outputFile);
    checkLeftovers($checks, $markdown, $outputFile);
}

report($checks, $options['summary'] ?? null);

exit(array_filter($checks, fn (array $check): bool => $check['failures'] !== []) === [] ? 0 : 1);
