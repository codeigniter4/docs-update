<?php

function before_filter_docLink($data, $folder) {
    // We only need a parent folder
    $folder = explode('/', trim($folder,  '/'));
    $folder = array_pop($folder);

    // :doc:`View Decorators <../outgoing/view_decorators>`
    // :doc:`Feature Tests </testing/feature>`
    // :doc:`configuration <configuration>`
    $data = preg_replace_callback('/:doc:`(.*?) <(.*?)>`/', function ($matches) use ($folder) {
        $text = $matches[1];
        $url  = $matches[2];

        if (identifyRelativePath($url) === '../') {
            return "`$text <$url.md>`_";
        }

        if (identifyRelativePath($url) === './') {
            $url = str_replace('./', '', $url);
            return "`$text <$url.md>`_";
        }

        if (identifyRelativePath($url) === '/') {
            if (str_contains($url, $folder)) {
                $url = explode('/', $url);
                $url = array_pop($url);
            } else {
                $url = '..' . $url;
            }
        }

        return "`$text <$url.md>`_";
    }, $data);

    // :doc:`../outgoing/view_decorators`
    // :doc:`./backward_compatibility_notes`
    return preg_replace_callback('/:doc:`(.*?)`/', function ($matches) use ($folder) {
        $url  = $matches[1];
        $text = explode('/', $url);
        $text = ucwords(str_replace('_', ' ', array_pop($text)));

        if (identifyRelativePath($url) === '../') {
            return "`$text <$url.md>`_";
        }

        if (identifyRelativePath($url) === './') {
            $url = str_replace('./', '', $url);
            return "`$text <$url.md>`_";
        }

        if (identifyRelativePath($url) === '/') {
            if (str_contains($url, $folder)) {
                $url = explode('/', $url);
                $url = array_pop($url);
            } else {
                $url = '..' . $url;
            }
        }

        return "`$text <$url.md>`_";
    }, $data);
}

// helper function
function identifyRelativePath($input)
{
    $pattern = '/^(\.\.\/|\.\/|\/)/';

    if (preg_match($pattern, $input, $matches) === 1) {
        return $matches[1];
    }

    return false;
}
