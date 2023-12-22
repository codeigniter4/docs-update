<?php

function after_filter_dollarSign($data) {
    return str_replace('\$', '$', $data);
}
