<?php

// Transformers
$transformers = ['images', 'alerts', 'includes', 'dollarsign', 'versionadded'];

// Docs
$directory = __DIR__ . '/../docs/';

// Scan all
$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS),
    RecursiveIteratorIterator::SELF_FIRST
);

$mdFiles = [];

foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() == 'md') {
        $mdFiles[] = [
            'path'   => $file->getPathname(),
            'folder' => $iterator->getSubPath(),
        ];
    }
}

// Use transformers
foreach ($transformers as $action) {

    // Load individual transformer
    include_once(__DIR__ . '/transform/' . $action . '.php');
    // Prepare function name
    $function = 'transform_' . $action;

    foreach ($mdFiles as $file) {
        // Process file
        updateFile($file, $function);
        $filePath = $file['path'];
        echo "Action: $action - transforming file: $filePath\n";
    }
}

function updateFile($file, $function) {
    $data = file_get_contents($file['path']);
    $data = $function($data, $file['folder']);
    file_put_contents($file['path'], $data);
}
