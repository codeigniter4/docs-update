<?php

// Handle images
function transform_images($data, $folder) {
    $pattern = '/<img(?:[^>]*)\ssrc="([^"]+)"(?:[^>]*)\salt="([^"]+)"(?:[^>]*)>/';

    $replacement = '![${2}](${1})';

    return preg_replace($pattern, $replacement, $data);
}
