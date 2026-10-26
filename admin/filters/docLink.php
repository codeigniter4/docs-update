<?php

declare(strict_types=1);

function before_filter_docLink(string $data, string $folder): ?string {
    $folder = explode('/', trim($folder,  '/'));
    $folder = array_pop($folder);

    // Sphinx shows only a PHP role's display text, with RST's escaped backslashes collapsed.
    $data = preg_replace_callback('/:php:[a-z]+:`([^`]+?)(?:\s*<[^>`]+>)?`/', function (array $matches): string {
        return sprintf('``%s``', str_replace('\\\\', '\\', $matches[1]));
    }, $data);

    $data = preg_replace_callback('/\|(version|release)\|/', fn (array $matches): string => sphinxSetting($matches[1]), $data);

    // Runs before the generic `Text <target>` pattern below, which also matches :doc:`Text <path>`.
    $data = preg_replace_callback('/:doc:`(.*?) <(.*?)>`/', function (array $matches) use ($folder): string {
        $text = $matches[1];
        $url  = $matches[2];

        if (identifyRelativePath($url) === '../') {
            return sprintf('[%s](%s.md)', $text, $url);
        }

        if (identifyRelativePath($url) === './') {
            $url = str_replace('./', '', $url);
            return sprintf('[%s](%s.md)', $text, $url);
        }

        if (identifyRelativePath($url) === '/') {
            if (str_contains($url, $folder)) {
                $url = explode('/', $url);
                $url = array_pop($url);
            } else {
                $url = '..' . $url;
            }
            return sprintf('[%s](%s.md)', $text, $url);
        }

        return sprintf('[%s](%s.md)', $text, $url);
    }, $data);

    $data = preg_replace_callback('/(?<!`)`(?!`)([^`]+?) <([^>]+?)>`(?!`)_?/', function (array $matches): string {
        $text = $matches[1];
        $reference = $matches[2];

        // A placeholder, since pandoc escapes raw HTML written into the RST.
        if (preg_match('/^https?:\/\//', $reference)) {
            return sprintf('{{EXTERNAL_LINK_START}}%s{{EXTERNAL_LINK_MIDDLE}}%s{{EXTERNAL_LINK_END}}', $reference, $text);
        }

        $targetFile = findMatchingFile($reference);

        if ($targetFile) {
            return sprintf('[%s](%s)', $text, $targetFile);
        }

        if (strpos($reference, '-') !== false || strpos($reference, '_') !== false) {
            return sprintf('[%s](#%s)', $text, str_replace('_', '-', $reference));
        }

        return sprintf('[%s](#%s)', $text, $reference);
    }, $data);

    return preg_replace_callback('/:doc:`(.*?)`/', function (array $matches) use ($folder): string {
        $url  = $matches[1];
        $text = explode('/', $url);
        $text = ucwords(str_replace('_', ' ', array_pop($text)));

        if (identifyRelativePath($url) === '../') {
            return sprintf('[%s](%s.md)', $text, $url);
        }

        if (identifyRelativePath($url) === './') {
            $url = str_replace('./', '', $url);
            return sprintf('[%s](%s.md)', $text, $url);
        }

        if (identifyRelativePath($url) === '/') {
            if (str_contains($url, $folder)) {
                $url = explode('/', $url);
                $url = array_pop($url);
            } else {
                $url = '..' . $url;
            }
            return sprintf('[%s](%s.md)', $text, $url);
        }

        return sprintf('[%s](%s.md)', $text, $url);
    }, $data);
}

/**
 * A string setting such as `version` from the source's Sphinx `conf.py`, which Sphinx substitutes for `|version|`.
 */
function sphinxSetting(string $name): string {
    static $config = null;

    $config ??= (string) @file_get_contents(SOURCE_DIR . 'conf.py');

    return preg_match(sprintf("/^%s\s*=\s*'([^']*)'/m", preg_quote($name, '/')), $config, $matches) === 1 ? $matches[1] : $name;
}

function identifyRelativePath(string $input): string|false
{
    $pattern = '/^(\.\.\/|\.\/|\/)/';

    if (preg_match($pattern, $input, $matches) === 1) {
        return $matches[1];
    }

    return false;
}

/**
 * Link to the page holding the `$reference` label, or null when no page is known.
 */
function findMatchingFile(string $reference): ?string {
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
        if (file_exists(SOURCE_DIR . substr($path, 0, -3) . '.rst')) {
            return '../' . $path;
        }
    }

    return null;
}

function after_filter_docLink(string $data, string $folder): ?string {
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
