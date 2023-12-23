<?php

function after_filter_versionDeprecated($data) {

    $pattern = '/<div class="deprecated">\s*([\d.]+)\s*<\/div>/';

    $replacement = '!!! danger "Deprecated since version $1"';

    return preg_replace($pattern, $replacement, $data);
}
