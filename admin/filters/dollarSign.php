<?php

declare(strict_types=1);

function after_filter_dollarSign(string $data, string $folder): string {
    return str_replace('\$', '$', $data);
}
