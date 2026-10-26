<?php

declare(strict_types=1);

function after_filter_images(string $data, string $folder): ?string {
    return preg_replace('/<img(?:[^>]*)\ssrc="([^"]+)"(?:[^>]*)\salt="([^"]+)"(?:[^>]*)>/', '![${2}](${1})', $data);
}
