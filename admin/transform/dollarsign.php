<?php

function transform_dollarsign($data) {
    return str_replace('\$', '$', $data);
}
