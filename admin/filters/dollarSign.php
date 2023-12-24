<?php

function after_filter_dollarSign($data, $folder) {
    return str_replace('\$', '$', $data);
}
