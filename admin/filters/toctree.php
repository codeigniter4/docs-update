<?php

declare(strict_types=1);

/**
 * Rendered toctree lists held back from pandoc, keyed by the number in their `TocTreePlaceholder<n>` marker.
 *
 * @return list<string>
 */
function &toctreeLists(): array {
    static $lists = [];

    return $lists;
}

/**
 * Replaces each visible toctree with a `TocTreePlaceholder<n>` marker line and drops each hidden one.
 */
function before_filter_toctree(string $data, string $folder, string $path): ?string {
    $page  = sourceRelative(preg_replace('/\.rst_?$/', '', $path));
    $lists = &toctreeLists();

    return preg_replace_callback(TOCTREE_PATTERN, function (array $matches) use ($page, &$lists): string {
        $toctree = parseToctree($matches[1], $page);

        if ($toctree['hidden']) {
            return "\n";
        }

        $lists[] = implode("\n", renderToctree($toctree, dirname($page) === '.' ? '' : dirname($page), 0, [$page]));

        return sprintf("TocTreePlaceholder%d\n\n", count($lists) - 1);
    }, $data);
}

function after_filter_toctree(string $data, string $folder): ?string {
    $lists = &toctreeLists();

    return preg_replace_callback('/^TocTreePlaceholder(\d+)[ \t]*$/m', fn (array $matches): string => $lists[(int) $matches[1]], $data);
}

const TOCTREE_PATTERN = '/^\.\. toctree::[ \t]*\r?\n((?:[ \t]+[^\r\n]*\r?\n|[ \t]*\r?\n)*)/m';

/**
 * Toctree with the directive body `$body`, its entries resolved against the source page `$page`.
 *
 * @return array{hidden: bool, includeHidden: bool, maxDepth: int, entries: list<array{title: ?string, page: string}>}
 */
function parseToctree(string $body, string $page): array {
    $options = [];
    $entries = [];
    $directory = dirname($page) === '.' ? '' : dirname($page);

    foreach (preg_split('/\r?\n/', $body) as $line) {
        $line = trim($line);

        if ($line === '' || $line === 'self') {
            continue;
        }

        if (preg_match('/^:([a-z]+):\s*(.*)$/', $line, $matches) === 1) {
            $options[$matches[1]] = $matches[2];
            continue;
        }

        $title = null;

        if (preg_match('/^(.+?)\s*<([^<>]+)>$/', $line, $matches) === 1) {
            [, $title, $line] = $matches;
        }

        $target = normalizePath(str_starts_with($line, '/') ? $line : $directory . '/' . $line);

        if (isset($options['glob']) && strpbrk($target, '*?[') !== false) {
            $matched = array_map(fn (string $file): string => sourceRelative(substr($file, 0, -4)), glob(SOURCE_DIR . $target . '.rst'));
            sort($matched);

            foreach (array_diff($matched, [$page]) as $match) {
                $entries[] = ['title' => null, 'page' => $match];
            }

            continue;
        }

        $entries[] = ['title' => $title, 'page' => $target];
    }

    return [
        'hidden'        => isset($options['hidden']),
        'includeHidden' => isset($options['includehidden']),
        'maxDepth'      => (int) ($options['maxdepth'] ?? 0) > 0 ? (int) $options['maxdepth'] : PHP_INT_MAX,
        'entries'       => $entries,
    ];
}

/**
 * Toctrees of the source page `$page`, hidden ones included.
 *
 * @return list<array{hidden: bool, includeHidden: bool, maxDepth: int, entries: list<array{title: ?string, page: string}>}>
 */
function pageToctrees(string $page): array {
    static $cache = [];

    if (! isset($cache[$page])) {
        preg_match_all(TOCTREE_PATTERN, (string) @file_get_contents(SOURCE_DIR . $page . '.rst'), $matches);

        $cache[$page] = array_map(fn (string $body): array => parseToctree($body, $page), $matches[1]);
    }

    return $cache[$page];
}

/**
 * Nested Markdown list of `$toctree`, linked from the docs directory `$directory`, expanding each entry's own toctrees as Sphinx does.
 *
 * @param array{hidden: bool, includeHidden: bool, maxDepth: int, entries: list<array{title: ?string, page: string}>} $toctree
 * @param list<string>                                                                                                $ancestors
 *
 * @return list<string>
 */
function renderToctree(array $toctree, string $directory, int $depth, array $ancestors): array {
    $lines = [];

    foreach ($toctree['entries'] as $entry) {
        $lines[] = sprintf('%s- [%s](%s)', str_repeat('    ', $depth), $entry['title'] ?? pageTitle($entry['page']), relativeLink($directory, $entry['page']));

        if ($depth + 1 >= $toctree['maxDepth'] || in_array($entry['page'], $ancestors, true)) {
            continue;
        }

        foreach (pageToctrees($entry['page']) as $child) {
            if ($child['hidden'] && ! $toctree['includeHidden']) {
                continue;
            }

            $child['maxDepth']      = $toctree['maxDepth'];
            $child['includeHidden'] = $toctree['includeHidden'];

            array_push($lines, ...renderToctree($child, $directory, $depth + 1, [...$ancestors, $entry['page']]));
        }
    }

    return $lines;
}

/**
 * Title of the source page `$page`, in Markdown, or its file name in title case when it has none.
 */
function pageTitle(string $page): string {
    $rst = (string) @file_get_contents(SOURCE_DIR . $page . '.rst');
    $punctuation = '[!-\/:-@\[-`{-~]';

    if (preg_match(sprintf('/^(?:(%1$s)\1{2,}[ \t]*\r?\n)?(?!%1$s+[ \t]*\r?$)(\S[^\r\n]*?)[ \t]*\r?\n(%1$s)\3{2,}[ \t]*\r?$/m', $punctuation), $rst, $matches) === 1) {
        return str_replace('``', '`', $matches[2]);
    }

    return ucwords(str_replace(['_', '-'], ' ', basename($page)));
}

function normalizePath(string $path): string {
    $parts = [];

    foreach (explode('/', $path) as $part) {
        if ($part === '..') {
            array_pop($parts);
        } elseif ($part !== '' && $part !== '.') {
            $parts[] = $part;
        }
    }

    return implode('/', $parts);
}
