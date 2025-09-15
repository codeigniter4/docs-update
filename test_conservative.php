<?php

require_once 'admin/filters/codeBlocks.php';

echo "Testing conservative filter:\n";

$content = "Text

    function test() {
        return true;
    }

More text

    - List item
    - Another item

Final text";

echo "Result:\n";
echo after_filter_codeBlocks($content, '');
echo "\n";
