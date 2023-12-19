# How to use transformers

Transformers are simple function that automate changes in the docs conversion process.

All transformers are declared in the `transform.php` file.
They are executed in the order declared in the array. The name in the array should correspond to the file name without the `.php` extension.

Each transformer is located in a separate file, in the "transform" directory.

Each has a function `transform_*`, where `*` corresponds to the file name.

Available transformers:

- `images` - converts image tags to markdown
- `alerts` - converts notes and warnings to format known from `admonition` extension
- `includes` - converts all code snippets included from other files to format known from `pymdownx.snippets` extension

Usage:

    php ./admin/transform.php
