<?php

// Handle images
function after_filter_images($data, $folder) {
    $pattern = '/<img(?:[^>]*)\ssrc="([^"]+)"(?:[^>]*)\salt="([^"]+)"(?:[^>]*)>/';

    $replacement = '![${2}](${1})';

    return preg_replace($pattern, $replacement, $data);
}
