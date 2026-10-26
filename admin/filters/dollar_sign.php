<?php

declare(strict_types=1);

function after_filter_dollar_sign(string $data, string $folder): string {
    return str_replace('\$', '$', $data);
}
