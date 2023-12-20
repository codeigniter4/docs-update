<?php

define('SOURCE_DIR', __DIR__ . '/../docs-rst/');
define('TARGET_DIR', __DIR__ . '/../docs/');

// Deletes all files and directories within the target directory,
// except for the assets directory.
function clean($target) {
    $dir = opendir($target);

    while (($file = readdir($dir)) !== false) {
        if ($file == '.' || $file == '..') {
            continue;
        }

        $targetFile = $target . '/' . $file;

        if (is_dir($targetFile)) {
            if ($file == 'assets') {
                continue;
            }
            clean($targetFile);
            rmdir($targetFile);
        } else {
            unlink($targetFile);
        }
    }
    closedir($dir);
}

// Recursively scan the source directory,
// using pandoc to convert any .rst files to .md
// and copying any other files and directories as-is
function convert($source, $target) {
    $dir = opendir($source);

    if (! is_dir($target)) {
        mkdir($target);
    }

    while (($file = readdir($dir)) !== false) {
        if ($file == '.' || $file == '..') {
            continue;
        }

        $sourceFile = realpath($source . '/' . $file);
        $targetFile = $target . '/' . $file;

        if (is_dir($sourceFile)) {
            convert($sourceFile, $targetFile);
        } else if (preg_match('/\.rst$/', $file)) {
            echo "Converting $sourceFile\n";
            $targetFile = preg_replace('/\.rst$/', '.md', $targetFile);
            exec("pandoc -f rst -t gfm -o $targetFile $sourceFile");
        } else {
            echo "Copying $sourceFile\n";
            copy($sourceFile, $targetFile);
        }
    }
    closedir($dir);
}

clean(TARGET_DIR);
convert(SOURCE_DIR, TARGET_DIR);
