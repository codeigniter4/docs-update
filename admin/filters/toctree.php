<?php

declare(strict_types=1);

/**
 * Rendered toctree lists held back from pandoc, keyed by the number in their `TocTreePlaceholder<n>` marker.
 *
 * @return list<string>
 */
function &toctree_lists(): array
{
    static $lists = [];

    return $lists;
}

/**
 * Replaces each visible toctree with a `TocTreePlaceholder<n>` marker line and drops each hidden one.
 */
function before_filter_toctree(string $data, string $folder, string $path): ?string
{
    $page = source_relative(preg_replace('/\.rst_?$/', '', $path));
    $lists = &toctree_lists();

    return preg_replace_callback(toctree_pattern(), function (array $matches) use ($page, &$lists): string {
        $toctree = parse_toctree($matches[1], $page);

        if ($toctree['hidden']) {
            return "\n";
        }

        $lists[] = implode("\n", render_toctree($toctree, dirname($page) === '.' ? '' : dirname($page), 0, [$page]));

        return sprintf("TocTreePlaceholder%d\n\n", count($lists) - 1);
    }, $data);
}

function after_filter_toctree(string $data, string $folder): ?string
{
    $lists = &toctree_lists();

    return preg_replace_callback('/^TocTreePlaceholder(\d+)[ \t]*$/m', fn (array $matches): string => $lists[(int) $matches[1]], $data);
}

function toctree_pattern(): string
{
    return '/^\.\. toctree::[ \t]*\r?\n((?:[ \t]+[^\r\n]*\r?\n|[ \t]*\r?\n)*)/m';
}

/**
 * Toctree with the directive body `$body`, its entries resolved against the source page `$page`.
 *
 * @return array{hidden: bool, includeHidden: bool, maxDepth: int, entries: list<array{title: ?string, page: string}>}
 */
function parse_toctree(string $body, string $page): array
{
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

        $target = normalize_path(str_starts_with($line, '/') ? $line : $directory . '/' . $line);

        if (isset($options['glob']) && strpbrk($target, '*?[') !== false) {
            $matched = array_map(fn (string $file): string => source_relative(substr($file, 0, -4)), glob(source_dir() . $target . '.rst'));
            sort($matched);

            foreach (array_diff($matched, [$page]) as $match) {
                $entries[] = ['title' => null, 'page' => $match];
            }

            continue;
        }

        $entries[] = ['title' => $title, 'page' => $target];
    }

    return [
        'hidden' => isset($options['hidden']),
        'includeHidden' => isset($options['includehidden']),
        'maxDepth' => (int) ($options['maxdepth'] ?? 0) > 0 ? (int) $options['maxdepth'] : PHP_INT_MAX,
        'entries' => $entries,
    ];
}

/**
 * Toctrees of the source page `$page`, hidden ones included.
 *
 * @return list<array{hidden: bool, includeHidden: bool, maxDepth: int, entries: list<array{title: ?string, page: string}>}>
 */
function page_toctrees(string $page): array
{
    static $cache = [];

    if (!isset($cache[$page])) {
        preg_match_all(toctree_pattern(), (string) @file_get_contents(source_dir() . $page . '.rst'), $matches);

        $cache[$page] = array_map(fn (string $body): array => parse_toctree($body, $page), $matches[1]);
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
function render_toctree(array $toctree, string $directory, int $depth, array $ancestors): array
{
    $lines = [];

    foreach ($toctree['entries'] as $entry) {
        $lines[] = sprintf('%s- [%s](%s)', str_repeat('    ', $depth), $entry['title'] ?? page_title($entry['page']), relative_link($directory, $entry['page']));

        if ($depth + 1 >= $toctree['maxDepth'] || in_array($entry['page'], $ancestors, true)) {
            continue;
        }

        foreach (page_toctrees($entry['page']) as $child) {
            if ($child['hidden'] && !$toctree['includeHidden']) {
                continue;
            }

            $child['maxDepth'] = $toctree['maxDepth'];
            $child['includeHidden'] = $toctree['includeHidden'];

            array_push($lines, ...render_toctree($child, $directory, $depth + 1, [...$ancestors, $entry['page']]));
        }
    }

    return $lines;
}

/**
 * Title of the source page `$page`, in Markdown, or its file name in title case when it has none.
 */
function page_title(string $page): string
{
    $rst = (string) @file_get_contents(source_dir() . $page . '.rst');
    $punctuation = '[!-\/:-@\[-`{-~]';

    if (preg_match(sprintf('/^(?:(%1$s)\1{2,}[ \t]*\r?\n)?(?!%1$s+[ \t]*\r?$)(\S[^\r\n]*?)[ \t]*\r?\n(%1$s)\3{2,}[ \t]*\r?$/m', $punctuation), $rst, $matches) === 1) {
        return str_replace('``', '`', $matches[2]);
    }

    return ucwords(str_replace(['_', '-'], ' ', basename($page)));
}

function normalize_path(string $path): string
{
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
