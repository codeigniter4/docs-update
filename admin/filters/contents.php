<?php

declare(strict_types=1);

function after_filter_contents(string $data, string $folder): ?string
{
    return preg_replace('/\n*<div class="contents"[^>]*>\s*<\/div>\n*/', "\n\n", $data);
}
