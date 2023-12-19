<?php

function transform_versionadded($data) {
    $pattern = '/<div class="versionadded">\s*([\d.]+)\s*<\/div>/';

    $replacement = '!!! success "Available from version $1"';

    return preg_replace($pattern, $replacement, $data);
}
