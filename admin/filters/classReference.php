<?php

declare(strict_types=1);

function before_filter_classReference(string $data, string $folder): ?string {
    // Sphinx renders static methods and functions the same way as methods.
    $staticMethod = '.. php:staticmethod::';
    if (str_contains($data, $staticMethod)) {
        $data = str_replace($staticMethod, '.. php:method::', $data);
    }

    $function1 = '.. php:function::';
    if (str_contains($data, $function1)) {
        $data = str_replace($function1, '.. php:method::', $data);
    }

    $function2 = '..  php:function::';
    if (str_contains($data, $function2)) {
        $data = str_replace($function2, '.. php:method::', $data);
    }

    $indent = '    .. php:method::';
    if (str_contains($data, $indent)) {
        $data = removeIndentLevel($data, $indent);
    }

    $stringNamespace  = '.. php:namespace::';
    $stringClass      = '.. php:class::';
    $stringMethod     = '.. php:method::';
    $stringParam      = ':param';
    $stringReturn     = ':returns';
    $stringReturnType = ':rtype';
    $stringThrows     = ':throws';

    if (! str_contains($data, $stringMethod)) {
        return $data;
    }

    $lines = explode(PHP_EOL, $data);

    $insideBlock   = false;
    $paramsStarted = false;

    foreach ($lines as $no => &$line) {
        if (str_starts_with($line, $stringMethod)) {
            $insideBlock = true;
        }

        if (str_starts_with($line, $stringNamespace)) {
            $line = str_replace([$stringNamespace, '\\'], ['##', '\\\\'], $line);
        }

        if (str_starts_with($line, $stringClass)) {
            $line = str_replace($stringClass, '###', $line);
        }

        if (str_starts_with($line, $stringMethod)) {
            $line = str_replace($stringMethod, '####', $line);
        }

        if ($insideBlock) {
            $line = preg_replace('/^ {1,4}/', '', $line);

            if (str_starts_with($line, $stringParam)) {
                if (! $paramsStarted) {
                    $paramsStarted = true;
                    $lines[$no - 1] = $lines[$no - 1] . PHP_EOL . "* **Parameters**";
                }
                $line = str_replace($stringParam, "     *", $line);
                $pattern = '/\*\s*(\w+(?:\|\w+)*)\s*\$(\w+):\s*(.+)$/';
                if (preg_match($pattern, $line, $matches) === 1) {
                    $dataType     = $matches[1];
                    $variableName = $matches[2];
                    $description  = $matches[3];

                    $line = "     * **\$$variableName** ``$dataType`` $description";
                } else {
                    $line = preg_replace('/\$([^\s:]+):/', '``\$$1``', $line);
                }

                continue;
            }

            if ($paramsStarted) {
                $paramsStarted = false;
            }

            if (str_starts_with($line, $stringReturn)) {
                $line = str_replace($stringReturn, '* **Returns**', $line);
            }

            if (str_starts_with($line, $stringReturnType)) {
                $pattern = "/$stringReturnType:(.*)$/m";
                $line = preg_replace_callback($pattern, function (array $matches): string {
                    return '* **Return type**: ``' . str_replace('\\\\', '\\', trim($matches[1])) . '``';
                }, $line);
            }

            if (str_starts_with($line, $stringThrows)) {
                $line = str_replace($stringThrows, '* **Throws**', $line);
            }

        }

        $next = $no + 1;

        if ($insideBlock
            && isset($lines[$next])
            && $lines[$next] !== ''
            && $lines[$next] === ltrim($lines[$next])) {
            $insideBlock = false;
        }
    }

    return implode(PHP_EOL, $lines);
}

function after_filter_classReference(string $data, string $folder): ?string {
    // Python-Markdown nests list items by four spaces, not two.
    return str_replace(['\#', '  -'], ['#', '    -'], $data);
}

function removeIndentLevel(string $data, string $indent): string {
    $lines = explode(PHP_EOL, $data);

    $insideBlock = false;

    foreach ($lines as $no => &$line) {
        if (str_starts_with($line, $indent)) {
            $insideBlock = true;
        }

        if ($insideBlock) {
            $line = preg_replace('/^ {4}/', '', $line, 1);
        }

        $next = $no + 1;

        if (
            $insideBlock
            && isset($lines[$next])
            && $lines[$next] !== ''
            && $lines[$next] === ltrim($lines[$next])
        ) {
            $insideBlock = false;
        }
    }

    return implode(PHP_EOL, $lines);
}
