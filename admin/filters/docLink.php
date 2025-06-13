<?php

function before_filter_docLink($data, $folder) {
    // We only need a parent folder
    $folder = explode('/', trim($folder,  '/'));
    $folder = array_pop($folder);

    // Handle RST cross-references like `CodeIgniter URLs <urls-remove-index-php-apache>`
    // Convert them to proper markdown links
    $data = preg_replace_callback('/`([^`]+?) <([^>]+?)>`/', function ($matches) {
        $text = $matches[1];
        $reference = $matches[2];
        
        // Try to find actual files that match the reference
        $targetFile = findMatchingFile($reference);
        
        if ($targetFile) {
            return "[{$text}]({$targetFile})";
        }
        
        // For unmapped references, try to convert to a reasonable link
        // Convert underscore/dash patterns to section anchors
        if (strpos($reference, '-') !== false || strpos($reference, '_') !== false) {
            $sectionLink = '#' . str_replace('_', '-', $reference);
            return "[{$text}]({$sectionLink})";
        }
        
        // If we can't map it, at least make it a proper link format
        return "[{$text}](#{$reference})";
    }, $data);

    // :doc:`View Decorators <../outgoing/view_decorators>`
    // :doc:`Feature Tests </testing/feature>`
    // :doc:`configuration <configuration>`
    $data = preg_replace_callback('/:doc:`(.*?) <(.*?)>`/', function ($matches) use ($folder) {
        $text = $matches[1];
        $url  = $matches[2];

        if (identifyRelativePath($url) === '../') {
            return "`$text <$url.md>`_";
        }

        if (identifyRelativePath($url) === './') {
            $url = str_replace('./', '', $url);
            return "`$text <$url.md>`_";
        }

        if (identifyRelativePath($url) === '/') {
            if (str_contains($url, $folder)) {
                $url = explode('/', $url);
                $url = array_pop($url);
            } else {
                $url = '..' . $url;
            }
        }

        return "`$text <$url.md>`_";
    }, $data);

    // :doc:`../outgoing/view_decorators`
    // :doc:`./backward_compatibility_notes`
    return preg_replace_callback('/:doc:`(.*?)`/', function ($matches) use ($folder) {
        $url  = $matches[1];
        $text = explode('/', $url);
        $text = ucwords(str_replace('_', ' ', array_pop($text)));

        if (identifyRelativePath($url) === '../') {
            return "`$text <$url.md>`_";
        }

        if (identifyRelativePath($url) === './') {
            $url = str_replace('./', '', $url);
            return "`$text <$url.md>`_";
        }

        if (identifyRelativePath($url) === '/') {
            if (str_contains($url, $folder)) {
                $url = explode('/', $url);
                $url = array_pop($url);
            } else {
                $url = '..' . $url;
            }
        }

        return "`$text <$url.md>`_";
    }, $data);
}

// helper function
function identifyRelativePath($input)
{
    $pattern = '/^(\.\.\/|\.\/|\/)/';

    if (preg_match($pattern, $input, $matches) === 1) {
        return $matches[1];
    }

    return false;
}

// Helper function to find matching files based on reference patterns
function findMatchingFile($reference) {
    $docsDir = __DIR__ . '/../../docs/';
    
    // Common reference patterns and their likely file locations
    $patterns = [
        'urls-remove-index-php' => 'general/urls.md#removing-indexphp',
        'urls-remove-index-php-apache' => 'general/urls.md#removing-indexphp-with-apache',
        'environment-apache' => 'general/environments.md#apache',
        'environment-nginx' => 'general/environments.md#nginx',
        'setting-development-mode' => 'general/environments.md#setting-development-mode',
        'environment-constant' => 'general/environments.md#environment-constant',
        'dotenv-file' => 'general/configuration.md#environment-variables'
    ];
    
    // Check if we have a direct mapping
    if (isset($patterns[$reference])) {
        $filePath = $patterns[$reference];
        // Convert to relative path from installation folder
        return '../' . $filePath;
    }
    
    // Try to find files by scanning the docs directory
    $possiblePaths = [
        "general/{$reference}.md",
        "concepts/{$reference}.md", 
        "installation/{$reference}.md",
        "libraries/{$reference}.md"
    ];
    
    foreach ($possiblePaths as $path) {
        if (file_exists($docsDir . $path)) {
            return '../' . $path;
        }
    }
    
    return null;
}

function after_filter_docLink($data, $folder) {
    // Clean up any remaining RST-style references and fix escaped markdown links
    
    // Remove escaping backslashes that break markdown links
    $data = str_replace('\\]', ']', $data);
    $data = str_replace('\\(', '(', $data);
    $data = str_replace('\\)', ')', $data);
    
    // Handle the literal backslash-escaped format as it appears in the output
    // Pattern: :ref:\[text\](url) -> [text](url)
    $data = str_replace(':ref:\\[', '[', $data);
    $data = str_replace(':doc:\\[', '[', $data);
    $data = str_replace('\\]\\(', '](', $data);
    
    // Handle any remaining :ref: and :doc: patterns
    $data = preg_replace('/:ref:\[([^\]]+)\]\(([^)]+)\)/', '[$1]($2)', $data);
    $data = preg_replace('/:doc:\[([^\]]+)\]\(([^)]+)\)/', '[$1]($2)', $data);
    
    // Handle :ref: directives that may have been created without links
    $data = preg_replace('/:ref:`([^`]+)`/', '[$1]', $data);
    
    // Handle :doc: directives that may have been created without links
    $data = preg_replace('/:doc:`([^`]+)`/', '[$1]', $data);
    
    return $data;
}
