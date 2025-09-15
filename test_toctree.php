<?php

$testRst = ".. toctree::
    :maxdepth: 2
    :titlesonly:

    installation/index
    tutorial/index";

file_put_contents('test_toctree.rst', $testRst);
$pandocOutput = shell_exec("pandoc -f rst -t commonmark --wrap=none test_toctree.rst");

echo "RST Input:\n";
echo $testRst;
echo "\n" . str_repeat("=", 50) . "\n";
echo "Pandoc Output:\n";
echo $pandocOutput;

unlink('test_toctree.rst');
