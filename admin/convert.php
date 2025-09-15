<?php

define('SOURCE_DIR', __DIR__ . '/../docs-rst/');
define('TARGET_DIR', __DIR__ . '/../docs/');

define('BEFORE_FILTERS', [
    'includes',
    'docLink',
    'classReference',
    'listIndentation',
]);
define('AFTER_FILTERS', [
    'images',
    'alerts',
    'includes',
    'dollarSign',
    'versionAdded',
    'versionDeprecated',
    'classReference',
    'listIndentation',
    'toctree',
    'contents',
    'docLink',
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


// Completely empties the target directory
function clean($target) {
    if (!is_dir($target)) {
        return;
    }

    $dir = opendir($target);

    while (($file = readdir($dir)) !== false) {
        if ($file == '.' || $file == '..') {
            continue;
        }

        $targetFile = $target . '/' . $file;

        if (is_dir($targetFile)) {
            clean($targetFile);
            rmdir($targetFile);
        } else {
            unlink($targetFile);
        }
    }
    closedir($dir);
}

// Copy a directory and all its contents recursively
function copyDirectory($source, $target) {
    if (!is_dir($source)) {
        return false;
    }

    if (!is_dir($target)) {
        mkdir($target, 0755, true);
    }

    $dir = opendir($source);
    while (($file = readdir($dir)) !== false) {
        if ($file == '.' || $file == '..') {
            continue;
        }

        $sourceFile = $source . '/' . $file;
        $targetFile = $target . '/' . $file;

        if (is_dir($sourceFile)) {
            copyDirectory($sourceFile, $targetFile);
        } else {
            echo "Copying asset: $sourceFile\n";
            copy($sourceFile, $targetFile);
        }
    }
    closedir($dir);
    return true;
}

// Copy essential assets that are needed for the docs
function copyAssets() {
    $assetsToTransfer = [
        '_static' => '_static',
        'images' => 'images',
        '.nojekyll' => '.nojekyll',
        'conf.py' => 'conf.py'
    ];

    foreach ($assetsToTransfer as $source => $target) {
        $sourcePath = SOURCE_DIR . $source;
        $targetPath = TARGET_DIR . $target;

        if (is_dir($sourcePath)) {
            echo "Copying directory: $sourcePath to $targetPath\n";
            copyDirectory($sourcePath, $targetPath);
        } elseif (file_exists($sourcePath)) {
            echo "Copying file: $sourcePath to $targetPath\n";
            copy($sourcePath, $targetPath);
        }
    }
}

// Core conversion function that handles the actual pandoc conversion
function convertRstToMd($sourceFile, $targetFile) {
    echo "Converting $sourceFile to $targetFile\n";

    // Work with source copy
    $sourceFileCopy = applyBeforeFilters($sourceFile);
    exec("pandoc -f rst -t commonmark --wrap=none -o $targetFile $sourceFileCopy");
    applyAfterFilters($targetFile, $sourceFileCopy);
}

// Recursively scan the source directory,
// using pandoc to convert any .rst files to .md
// and copying any other files and directories as-is
function convert($source, $target) {
    $dir = opendir($source);

    if (! is_dir($target)) {
        mkdir($target);
    }

    // Assets that are handled separately by copyAssets()
    $skipAssets = ['_static', 'images', '.nojekyll', 'conf.py'];

    while (($file = readdir($dir)) !== false) {
        if ($file == '.' || $file == '..') {
            continue;
        }

        // Skip assets that are copied separately
        if (in_array($file, $skipAssets)) {
            continue;
        }

        $sourceFile = realpath($source . '/' . $file);
        $targetFile = $target . '/' . $file;

        if (is_dir($sourceFile)) {
            convert($sourceFile, $targetFile);
        } else if (preg_match('/\.rst$/', $file)) {
            $targetFile = preg_replace('/\.rst$/', '.md', $targetFile);
            convertRstToMd($sourceFile, $targetFile);
        } else {
            echo "Copying $sourceFile\n";
            copy($sourceFile, $targetFile);
        }
    }
    closedir($dir);
}

// Convert a single RST file
function convertSingleFile($filename) {
    loadFilters();

    // Remove .rst extension if provided
    $filename = preg_replace('/\.rst$/', '', $filename);

    $sourceFile = realpath(SOURCE_DIR . $filename . '.rst');
    $targetFile = TARGET_DIR . $filename . '.md';

    if (!file_exists($sourceFile)) {
        echo "Error: File '$filename.rst' not found in " . SOURCE_DIR . "\n";
        return false;
    }

    // Ensure target directory exists
    $targetDir = dirname($targetFile);
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }

    convertRstToMd($sourceFile, $targetFile);

    echo "Conversion completed successfully!\n";
    return true;
}

// Check for CLI argument
if (isset($argv[1])) {
    $filename = $argv[1];
    convertSingleFile($filename);
} else {
    // Do full conversion
    echo "Starting full conversion...\n";
    echo "Cleaning target directory...\n";
    clean(TARGET_DIR);

    echo "Copying essential assets...\n";
    copyAssets();

    echo "Loading filters...\n";
    loadFilters();

    echo "Converting RST files to Markdown...\n";
    convert(SOURCE_DIR, TARGET_DIR);

    echo "Conversion completed successfully!\n";
}
