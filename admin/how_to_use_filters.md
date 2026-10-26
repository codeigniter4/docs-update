# How to use filters

Filters are plain functions that automate changes in the docs conversion process.

All filters are declared in `convert.php`, in two lists:

- `BEFORE_FILTERS` run on a copy of the original page (`.rst`), saved next to it as `.rst_` until the page is converted.
- `AFTER_FILTERS` run on the converted page (`.md`).

They run in the order listed. Each name matches a file in the `filters` directory, without the `.php` extension.
The file defines `before_filter_<name>()`, `after_filter_<name>()`, or both, each taking the page content, its folder and, when needed, its path:

```php
function after_filter_example(string $data, string $folder, string $path): ?string
```

A filter returns the new content, or `null` when a `preg_*` call fails, in which case the page keeps its previous content and the converter reports the error.

The conversion runs in three passes, so every after filter sees the pandoc output of every page:
all before filters on every page, then pandoc on every page, then all after filters on every page.

## Available filters

| Filter | Before | After |
| --- | --- | --- |
| `includes` | Replaces each `literalinclude`, `code-block` and `::` literal block with a `CodeBlockPlaceholder<n>` line | Runs last, and replaces each placeholder with its code as a fenced block |
| `doc_link` | Converts `:doc:`, `:ref:` and `:php:*:` roles, external links and `\|version\|` | Unescapes the link syntax and converts `title-ref` and label spans |
| `class_reference` | Converts `php:namespace`, `php:class`, `php:method`, `php:function`, `php:attr` and their fields into headings and lists | Fixes list nesting for Python-Markdown |
| `list_indentation` | Outdents deeply indented RST list items | Outdents list items and adds the blank line that Python-Markdown needs before a list |
| `images` | | Converts image tags to Markdown |
| `alerts` | | Converts `> [!NOTE]` alerts and unwraps the blockquotes left by indented RST text |
| `dollar_sign` | | Unescapes `\$` |
| `line_breaks` | | Turns the backslash line breaks written by pandoc into the two trailing spaces that Python-Markdown reads as a line break |
| `toctree` | Replaces each `toctree` with a `TocTreePlaceholder<n>` line holding its nested link list, and drops hidden ones | Replaces each placeholder with its list |
| `contents` | | Removes `contents` directives, since the sidebar shows the page contents |
| `admonitions` | | Converts note, warning, version and other admonitions into `!!!` blocks, nested ones included |

## Usage

```console
php ./admin/convert.php
```

Then check the result with `admin/verify.php`, as described in the [README](../README.md).
