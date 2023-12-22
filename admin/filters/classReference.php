<?php

function before_filter_classReference($data, $folder) {
    $stringClass  = '.. php:class::';
    $stringMethod = '.. php:method::';
    $stringParam  = ':param';

    if (! str_contains($data, $stringMethod)) {
        return $data;
    }

    $lines = explode(PHP_EOL, $data);

    $insideBlock = false;

    foreach ($lines as $no => &$line) {
        if (str_starts_with($line, $stringMethod)) {
            $insideBlock = true;
        }

        if (str_starts_with($line, $stringClass)) {
            $line = str_replace($stringClass, '##', $line);
        }

        if (str_starts_with($line, $stringMethod)) {
            $line = str_replace($stringMethod, '###', $line);
        }

        if ($insideBlock) {
            $line = ltrim($line);

            if (str_starts_with($line, $stringParam)) {
                $line = str_replace($stringParam, '-', $line);
                //$line = rtrim($line, ':');
                $line = preg_replace('/\$([^\s:]+):/', '``\$$1``', $line);
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

function after_filter_classReference($data, $folder) {
    return str_replace('\#', '#', $data);
}
