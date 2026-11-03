<?php

declare(strict_types=1);

/**
 * Appends the ids that Sphinx gave each heading, so that links to the old anchors still land.
 */
function after_filter_anchor_aliases(string $data, string $folder, string $path): ?string
{
    $queues = [];

    $page = substr($path, strlen(dirname(__DIR__, 2) . '/docs/'), -strlen('.md'));

    foreach (alias_entries($page) as $entry) {
        $queues[$entry['slug']][] = $entry['ids'];
    }

    if ($queues === []) {
        return $data;
    }

    $fence = null;
    $lines = explode("\n", $data);

    foreach ($lines as $i => $line) {
        if (preg_match('/^\s*(```+|~~~+)/', $line, $matches) === 1) {
            $fence = $fence === null ? $matches[1][0] : ($matches[1][0] === $fence ? null : $fence);

            continue;
        }

        if ($fence !== null || preg_match('/^#{1,6}\s+(.+?)\s*$/', $line, $matches) !== 1) {
            continue;
        }

        $slug = heading_slug(html_entity_decode(preg_replace('/\[([^\]]*)\]\([^)]*\)/', '$1', $matches[1]), ENT_QUOTES | ENT_HTML5));

        foreach (isset($queues[$slug]) ? array_shift($queues[$slug]) : [] as $id) {
            $lines[$i] .= sprintf('<a id="%s"></a>', $id);
        }
    }

    return implode("\n", $lines);
}

/**
 * The headings of the source page `$page` in order, each with its MkDocs slug and the Sphinx ids that differ from it.
 *
 * @return list<array{slug: string, ids: list<string>}>
 */
function alias_entries(string $page): array
{
    $file = source_dir() . $page . '.rst';

    if (!is_file($file)) {
        return [];
    }

    $lines = file($file, FILE_IGNORE_NEW_LINES);
    $entries = [];
    $labels = [];
    $namespace = '';
    $class = '';

    foreach ($lines as $i => $line) {
        if (preg_match('/^\s*\.\. _([^:`]+):\s*$/', $line, $matches) === 1) {
            $labels[] = docutils_id($matches[1]);
        } elseif (is_rst_heading($lines, $i)) {
            $text = rst_heading_text($line);
            $entries[] = alias_entry(heading_slug($text), [...$labels, docutils_id($text)]);
            $labels = [];
        } elseif (preg_match('/^\s*\.\.\s+php:(namespace|class|interface|trait|method|staticmethod|function|attr|const):: *(.+?)\s*$/', $line, $matches) === 1) {
            [, $kind, $signature] = $matches;
            $name = preg_replace('/[\s(].*$/', '', $signature);
            $prefix = $namespace === '' ? '' : $namespace . '\\';

            if ($kind === 'namespace') {
                $namespace = $signature;
                $class = '';
                $entries[] = alias_entry(heading_slug($signature), ['namespace-' . $signature]);
            } elseif (in_array($kind, ['class', 'interface', 'trait'], true)) {
                $class = $prefix . $name;
                $entries[] = alias_entry(heading_slug($name), [$class]);
            } else {
                $owner = $class !== '' && !in_array($kind, ['function', 'const'], true) ? $class . '::' : $prefix;
                $entries[] = alias_entry(heading_slug($kind === 'const' ? $name : $signature), [$owner . $name]);
            }
        }
    }

    return $entries;
}

/**
 * The entry for a heading with the MkDocs slug `$slug`, keeping only the ids of `$ids` that differ from it.
 *
 * @param list<string> $ids
 *
 * @return array{slug: string, ids: list<string>}
 */
function alias_entry(string $slug, array $ids): array
{
    return ['slug' => $slug, 'ids' => array_values(array_unique(array_diff($ids, [$slug, ''])))];
}

/**
 * The MkDocs slug of the heading text `$text`, which Python-Markdown reads without its HTML tags.
 */
function heading_slug(string $text): string
{
    return markdown_slug(preg_replace('/<[^>]*>/', '', $text));
}

/**
 * The id that docutils makes from the section title or label `$text`.
 */
function docutils_id(string $text): string
{
    return preg_replace('/^[-0-9]+|-+$/', '', preg_replace('/[^a-z0-9]+/', '-', strtolower($text)));
}
