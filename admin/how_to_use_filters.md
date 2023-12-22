# How to use filters

Filters are simple function that automate changes in the docs conversion process.

All filters are declared in the `convert-with-filters.php` file.

We have two types of filters:
* `before`
* `after`

They are executed in the order declared in the array. The name in the array should correspond to the file name without the `.php` extension.

Each filter is located in a separate file, in the "filters" directory.

Each should have at least one function: `before_filter_*` and/or `after_filter_*`, where `*` corresponds to the file name.

Available filters:

- `alerts` - converts notes and warnings to format known from `admonition` extension
- `dollarsign` - converts `\$` next to variables to the proper form
- `images` - converts image tags to markdown
- `includes` - converts all code snippets included from other files to format known from `pymdownx.snippets` extension
- `versionadded` - converts info about from which version given method/option is available

Usage:

    php ./admin/convert-with-filters.php
