<?php

// Handle contents directive from RST - generates table of contents for current page
function after_filter_contents($data, $folder) {
    // Look for HTML div elements with class="contents" (converted from RST by pandoc)
    // Since the sidebar already provides navigation, we'll remove contents directives
    // instead of generating redundant TOC lists within pages
    $pattern = '/<div class="contents"([^>]*)>\s*<\/div>/';

    return preg_replace_callback($pattern, function($matches) use ($data) {
        // Simply return empty string to remove the contents directive
        return '';
    }, $data);
}
