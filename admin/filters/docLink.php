<?php

function before_filter_docLink($data, $folder) {
    return preg_replace_callback('/:doc:`(.*?) <(.*?)>`/', function ($matches) {
        $text = $matches[1];
        $url  = $matches[2];

        // Check if the URL doesn't contain '..'
        if (! str_contains($url, '..')) {
            $url = explode('/', $url);
            $url = array_pop($url);
        }

        return "`$text <$url.md>`_";
    }, $data);
}
