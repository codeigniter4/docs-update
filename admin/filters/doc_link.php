<?php

declare(strict_types=1);

function before_filter_doc_link(string $data, string $folder, string $path): ?string
{
    $directory = source_relative($folder);
    $page = source_relative(preg_replace('/\.rst_?$/', '', $path));

    $data = preg_replace_callback('/:ref:`([^`]*?)(?:\s*<([^`<>]+)>)?`/', function (array $matches) use ($directory, $page): string {
        $target = rst_anchors()['labels'][strtolower($matches[2] ?? $matches[1])] ?? null;

        if ($target === null) {
            return $matches[0];
        }

        return sprintf(
            '[%s](%s#%s)',
            isset($matches[2]) ? $matches[1] : $target['title'],
            $target['page'] === $page ? '' : relative_link($directory, $target['page']),
            $target['anchor'],
        );
    }, $data);

    $data = preg_replace_callback('/(?<![`:\w])`([^`<>]+)`_(?!_)/', function (array $matches) use ($page): string {
        $anchor = rst_anchors()['sections'][$page][reference_name($matches[1])] ?? null;

        return $anchor === null ? $matches[0] : sprintf('[%s](#%s)', $matches[1], $anchor);
    }, $data);

    // Sphinx shows only a PHP role's display text, with RST's escaped backslashes collapsed.
    $data = preg_replace_callback('/:php:[a-z]+:`([^`]+?)(?:\s*<[^>`]+>)?`/', function (array $matches): string {
        return sprintf('``%s``', str_replace('\\\\', '\\', $matches[1]));
    }, $data);

    $data = preg_replace_callback('/\|(version|release)\|/', fn (array $matches): string => sphinx_setting($matches[1]), $data);

    // Runs before the generic `Text <target>` pattern below, which also matches :doc:`Text <path>`.
    $data = preg_replace_callback('/:doc:`([^`]*?)\s*<([^`>]*)>`/', fn (array $matches): string => sprintf('[%s](%s)', $matches[1], doc_target($matches[2], $directory)), $data);

    $data = preg_replace_callback('/(?<!`)`(?!`)([^`]+?) <([^>]+?)>`(?!`)_{0,2}/', function (array $matches) use ($directory, $page): string {
        $text = $matches[1];
        $reference = $matches[2];

        // A placeholder, since pandoc escapes raw HTML written into the RST.
        if (preg_match('/^https?:\/\//', $reference)) {
            // An escaped `@` stops pandoc reading the marker and a `@handle` text as an email address, and an escaped `_` stops it reading `Name_` in the URL as a reference.
            return sprintf('{{EXTERNAL_LINK_START}}%s{{EXTERNAL_LINK_MIDDLE}}%s{{EXTERNAL_LINK_END}}', str_replace('_', '\\_', $reference), str_replace('@', '\\@', $text));
        }

        if (preg_match('/^(?:(.+)\.html)?#(.+)$/', $reference, $url) === 1) {
            $targetPage = $url[1] === '' ? $page : normalize_path($directory . '/' . $url[1]);

            return sprintf('[%s](%s#%s)', $text, $url[1] === '' ? '' : $url[1] . '.md', php_object_anchor($targetPage, $url[2]) ?? $url[2]);
        }

        $targetFile = find_matching_file($reference);

        if ($targetFile) {
            return sprintf('[%s](%s)', $text, $targetFile);
        }

        if (strpos($reference, '-') !== false || strpos($reference, '_') !== false) {
            return sprintf('[%s](#%s)', $text, str_replace('_', '-', $reference));
        }

        return sprintf('[%s](#%s)', $text, $reference);
    }, $data);

    return preg_replace_callback('/:doc:`(.*?)`/', function (array $matches) use ($directory): string {
        $text = ucwords(str_replace('_', ' ', basename($matches[1])));

        return sprintf('[%s](%s)', $text, doc_target($matches[1], $directory));
    }, $data);
}

/**
 * Markdown link target of the `:doc:` path `$target`, written in the source directory `$directory`.
 */
function doc_target(string $target, string $directory): string
{
    if (str_starts_with($target, '/')) {
        return relative_link($directory, normalize_path($target));
    }

    return (str_starts_with($target, './') ? substr($target, 2) : $target) . '.md';
}

/**
 * MkDocs heading anchors in the source, for each `.. _label:` by its lowercased label, and for each section title by page and reference name.
 *
 * @return array{labels: array<string, array{page: string, title: string, anchor: string}>, sections: array<string, array<string, string>>}
 */
function rst_anchors(): array
{
    static $targets = null;

    if ($targets !== null) {
        return $targets;
    }

    $targets = ['labels' => [], 'sections' => []];

    foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator(source_dir(), FilesystemIterator::SKIP_DOTS)) as $file) {
        if ($file->getExtension() !== 'rst') {
            continue;
        }

        $page = source_relative(substr($file->getPathname(), 0, -4));
        $lines = file($file->getPathname(), FILE_IGNORE_NEW_LINES);
        $labels = [];
        $anchors = [];

        foreach ($lines as $i => $line) {
            if (preg_match('/^\s*\.\. _([^:`]+):\s*$/', $line, $matches) === 1) {
                $labels[] = strtolower($matches[1]);
            } elseif (is_rst_heading($lines, $i)) {
                $text = preg_replace(['/:[\w:.+-]+:(?=`)/', '/\s*<[^`<>]*>(?=`)/'], '', $line);
                $anchor = unique_anchor(markdown_slug($text), $anchors);

                $targets['sections'][$page][reference_name(str_replace(['`', '*', '\\'], '', $text))] ??= $anchor;

                foreach ($labels as $label) {
                    $targets['labels'][$label] = ['page' => $page, 'title' => trim($line), 'anchor' => $anchor];
                }

                $labels = [];
            }
        }
    }

    return $targets;
}

/**
 * RST reference name of the link text or section title `$text`, compared case-insensitively with its whitespace collapsed.
 */
function reference_name(string $text): string
{
    return strtolower(preg_replace('/\s+/', ' ', trim($text)));
}

/**
 * MkDocs anchor of the signature heading for the PHP function or method `$name` in the source page `$page`, which Sphinx anchors as `#$name`.
 */
function php_object_anchor(string $page, string $name): ?string
{
    $rst = (string) @file_get_contents(source_dir() . $page . '.rst');

    if (preg_match(sprintf('/^\s*\.\. php:(?:method|staticmethod|function)::\s*(%s\s*\(.*)$/m', preg_quote($name, '/')), $rst, $matches) !== 1) {
        return null;
    }

    return markdown_slug($matches[1]);
}

/**
 * Whether `$lines[$i]` is a section title, underlined by a punctuation run at least as long.
 *
 * @param list<string> $lines
 */
function is_rst_heading(array $lines, int $i): bool
{
    $title = rtrim($lines[$i]);
    $under = rtrim($lines[$i + 1] ?? '');

    return $title !== ''
        && !ctype_space($title[0])
        && preg_match('/^([!-\/:-@\[-`{-~])\1*$/', $title) !== 1
        && preg_match('/^([!-\/:-@\[-`{-~])\1+$/', $under) === 1
        && strlen($under) >= mb_strlen($title)
        && ($i === 0 || trim($lines[$i - 1]) === '' || rtrim($lines[$i - 1]) === $under);
}

/**
 * Python-Markdown's default `toc` slug of the heading text `$text`.
 */
function markdown_slug(string $text): string
{
    $text = preg_replace('/[^\x00-\x7F]/', '', Normalizer::normalize($text, Normalizer::FORM_KD));
    $text = strtolower(trim(preg_replace('/[^\w\s-]/', '', $text)));

    return preg_replace('/[-\s]+/', '-', $text);
}

/**
 * `$id`, suffixed `_1`, `_2` and so on past the ids in `$used` as Python-Markdown does, then added to `$used`.
 *
 * @param array<string, true> $used
 */
function unique_anchor(string $id, array &$used): string
{
    while ($id === '' || isset($used[$id])) {
        $id = preg_match('/^(.*)_(\d+)$/', $id, $matches) === 1 ? sprintf('%s_%d', $matches[1], (int) $matches[2] + 1) : $id . '_1';
    }

    $used[$id] = true;

    return $id;
}

/**
 * A string setting such as `version` from the source's Sphinx `conf.py`, which Sphinx substitutes for `|version|`.
 */
function sphinx_setting(string $name): string
{
    static $config = null;

    $config ??= (string) @file_get_contents(source_dir() . 'conf.py');

    return preg_match(sprintf("/^%s\s*=\s*'([^']*)'/m", preg_quote($name, '/')), $config, $matches) === 1 ? $matches[1] : $name;
}

/**
 * Link to the page holding the `$reference` label, or null when no page is known.
 */
function find_matching_file(string $reference): ?string
{
    // Labels whose page cannot be guessed from the label itself.
    $patterns = [
        'urls-remove-index-php' => 'general/urls.md#removing-indexphp',
        'urls-remove-index-php-apache' => 'general/urls.md#removing-indexphp-with-apache',
        'environment-apache' => 'general/environments.md#apache',
        'environment-nginx' => 'general/environments.md#nginx',
        'setting-development-mode' => 'general/environments.md#setting-development-mode',
        'environment-constant' => 'general/environments.md#environment-constant',
        'dotenv-file' => 'general/configuration.md#environment-variables'
    ];

    if (isset($patterns[$reference])) {
        $filePath = $patterns[$reference];
        return '../' . $filePath;
    }

    $possiblePaths = [
        "general/{$reference}.md",
        "concepts/{$reference}.md",
        "installation/{$reference}.md",
        "libraries/{$reference}.md"
    ];

    foreach ($possiblePaths as $path) {
        if (file_exists(source_dir() . substr($path, 0, -3) . '.rst')) {
            return '../' . $path;
        }
    }

    return null;
}

function after_filter_doc_link(string $data, string $folder): ?string
{
    // pandoc escapes brackets and parentheses that it reads as plain text.
    $data = str_replace('\\]', ']', $data);
    $data = str_replace('\\(', '(', $data);
    $data = str_replace('\\)', ')', $data);
    $data = str_replace('\\[', '[', $data);

    $data = str_replace(':ref:\\[', '[', $data);
    $data = str_replace(':doc:\\[', '[', $data);
    $data = str_replace('\\]\\(', '](', $data);

    $data = preg_replace('/:ref:\[([^\]]+)\]\(([^)]+)\)/', '[$1]($2)', $data);
    $data = preg_replace('/:doc:\[([^\]]+)\]\(([^)]+)\)/', '[$1]($2)', $data);

    $data = preg_replace('/:ref:`([^`]+)`_?/', '[$1]', $data);
    $data = preg_replace('/:doc:`([^`]+)`_?/', '[$1]', $data);

    $data = preg_replace('/(?<!`)`([^`\n]+)`_/', '[$1]', $data);

    $data = preg_replace('/{{EXTERNAL_LINK_START}}([^{]+){{EXTERNAL_LINK_MIDDLE}}([^{]+){{EXTERNAL_LINK_END}}/', '[$2]($1)', $data);

    // pandoc writes RST's default role as a title-ref span, which the guide uses for code.
    $data = preg_replace_callback('/<span class="title-ref">(.*?)<\/span>/', function (array $matches): string {
        return sprintf('`%s`', preg_replace('/\\\\([!-\/:-@\[-`{-~])/', '$1', $matches[1]));
    }, $data);

    return preg_replace('/<span id="[^"]*">(.*?)<\/span>/', '$1', $data);
}
