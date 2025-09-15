<?php
/**
 * Documentation Conversion Configuration
 *
 * This file controls where the documentation source files are located.
 * Copy this file to config.php and modify as needed for your local setup.
 */

return [
    // Documentation source configuration
    'source' => [
        // Type of source: 'local' or 'project'
        // 'local'   - Use the docs-rst folder in this project
        // 'project' - Use docs from a CodeIgniter4 project directory
        'type' => 'local',

        // Path configuration
        'paths' => [
            // Path to local docs-rst folder (relative to this file)
            'local' => __DIR__ . '/../docs-rst/',

            // Path to CodeIgniter4 project user_guide_src directory
            // This should point to the user_guide_src/source folder in a CI4 project
            // Example: '/path/to/CodeIgniter4/user_guide_src/source/'
            'project' => null,
        ]
    ],

    // Target directory for converted docs (relative to this file)
    'target' => __DIR__ . '/../docs/',

    // Filters configuration
    'filters' => [
        'before' => [
            'includes',
            'docLink',
            'classReference',
            'listIndentation',
        ],
        'after' => [
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
        ],
    ],
];
