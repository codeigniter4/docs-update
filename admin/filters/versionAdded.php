<?php

function after_filter_versionAdded($data) {
    $pattern = '/<div class="versionadded">\s*([\d.]+)\s*<\/div>/';

    $replacement = '!!! success "Available from version $1"';

    return preg_replace($pattern, $replacement, $data);
}
