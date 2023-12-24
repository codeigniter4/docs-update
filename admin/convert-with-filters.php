<?php

define('SOURCE_DIR', __DIR__ . '/../docs-rst/');
define('TARGET_DIR', __DIR__ . '/../docs/');

define('BEFORE_FILTERS', [
    'includes',
    'docLink',
    'classReference',
]);
define('AFTER_FILTERS', [
    'images',
    'alerts',
    'includes',
    'dollarSign',
    'versionAdded',
    'versionDeprecated',
    'classReference',
]);

// Load all filters
function loadFilters() {
    $filters = array_unique(array_merge(BEFORE_FILTERS, AFTER_FILTERS));
    foreach ($filters as $filter) {
        include_once(__DIR__ . '/filters/' . $filter . '.php');
    }
}

// Apply before filters
function applyBeforeFilters($path) {
    $file = [
        'path'   => $path . '_',
        'folder' => dirname($path),
    ];

    // Make a copy, so we can apply before filters
    // without consequences to source file
    copy($path, $file['path']);

    // Iterate over after filter
    foreach (BEFORE_FILTERS as $action) {
        $function = 'before_filter_' . $action;

        if (function_exists($function)) {
            updateFile($file, $function);
        }
    }

    // Return new file name (copy)
    return $file['path'];
}

// Apply after filters
function applyAfterFilters($path, $sourceCopy) {
    if (file_exists($sourceCopy)) {
        unlink($sourceCopy);
    }

    $file = [
      'path'   => $path,
      'folder' => dirname($path),
    ];
    // Iterate over after filter
    foreach (AFTER_FILTERS as $action) {
        $function = 'after_filter_' . $action;

        if (function_exists($function)) {
            updateFile($file, $function);
        }
    }
}

// Modify file
function updateFile($file, $function) {
    $data = file_get_contents($file['path']);
    $data = $function($data, $file['folder']);
    file_put_contents($file['path'], $data);
}


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

//    $sourceFile     = realpath(SOURCE_DIR) . '/libraries/email.rst';
//    $targetFile     = realpath(TARGET_DIR) . '/email.md';
//    $sourceFileCopy = applyBeforeFilters($sourceFile);
//    exec("pandoc -f rst -t gfm+hard_line_breaks -o $targetFile $sourceFileCopy");
//    applyAfterFilters($targetFile, $sourceFileCopy);
//    exit;
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
            // Work with source copy
            $sourceFileCopy = applyBeforeFilters($sourceFile);
            exec("pandoc -f rst -t gfm+hard_line_breaks -o $targetFile $sourceFileCopy");
            applyAfterFilters($targetFile, $sourceFileCopy);
        } else {
            echo "Copying $sourceFile\n";
            copy($sourceFile, $targetFile);
        }
    }
    closedir($dir);
}

clean(TARGET_DIR);
loadFilters();
convert(SOURCE_DIR, TARGET_DIR);
