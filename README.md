# CodeIgniter Documentation Project

The aim of this project is to convert the CodeIgniter User Guide from the existing reStructuredText format
to Markdown format, making it easier to maintain and contribute to, as well as allowing all of our projects
to share a common documentation format.

## Configuration

The converter reads the user guide from the `user_guide_src/source` folder of a CodeIgniter4 checkout.
To configure it, copy `.env.dist` to `.env`:

```bash
cp .env.dist .env
```

Then set the path in `.env`:

```ini
DOCS_PROJECT_PATH=/path/to/CodeIgniter4/user_guide_src/source/
```

An environment variable of the same name takes precedence over `.env`.

## The Conversion Process

Each RST page goes through three passes:

1. The `before` filters rewrite a copy of the page. Among other things, they inline each
   `literalinclude`, `code-block` and `::` literal block as a placeholder, so no later step touches sample code.
2. pandoc converts every copy to CommonMark with pipe tables, eight pages at a time.
3. The `after` filters turn the result into MkDocs Markdown: admonitions, class references, links,
   and finally the fenced code blocks that replace the placeholders.

See [how to use filters](./admin/how_to_use_filters.md) for details.
Once the conversion is complete, the converted pages will be reviewed by hand, and the overall structure
of the documentation revisited for consistency, clarity, and ease of understanding.

## Requirements

- PHP 8.1 or newer.
- pandoc 3.12.1. The converter warns when another version is installed.
  To install it, see <https://github.com/jgm/pandoc/releases/tag/3.12.1>.
- Python 3, to build the site.
- `make`, optionally, for the shortcuts below.

Generation of the final documentation uses [MkDocs](https://www.mkdocs.org/) with the
[Material for MkDocs](https://squidfunk.github.io/mkdocs-material/) theme.
See [How to Build Docs](./admin/how_to_build_docs.md) for more details.

## Next Steps:

- [x] Build converter script to convert all files from rst to md
- [x] Modify converter to insert code samples and remove unnecessary files.
- [ ] Review and clean up converted files
- [ ] Review and clean up overall structure of documentation
- [ ] Standardize how pages are written to include cohesive structure and examples

## Commands:

The `Makefile` wraps each command below. Run `make` to list the targets.

### Converting all files

To convert all files from rst to md:

```bash
make convert
# or
php ./admin/convert.php
```

> [!NOTE]
> This first deletes everything in the `./docs` directory, then copies the images and theme assets back in.

It also writes the MkDocs nav to `docs/.nav.yml`, built from the toctrees, which `mkdocs.yml` inherits.
It exits non-zero when pandoc fails on any page.

You can also convert a single page, given by its path relative to the source folder, without the extension:

```bash
make convert PAGE=models/model
# or
php ./admin/convert.php models/model
```

### Verifying the converted files

To compare the converted files with their RST source:

```bash
make verify
# or
php ./admin/verify.php --source /path/to/CodeIgniter4/user_guide_src/source --output ./docs
```

`make verify` reads the source path from `DOCS_PROJECT_PATH`.

It prints a table of checks, lists the first failures of each, and exits non-zero when any check fails.
`--summary <file>` also appends the results as Markdown to that file.
The [Verify conversion](./.github/workflows/verify.yml) workflow converts, verifies and builds with `mkdocs build --strict`
the `develop` and `4.8` branches of CodeIgniter4 on every push, on every pull request, and nightly.

### Previewing the converted files

See the [How to Build Docs](./admin/how_to_build_docs.md) document for instructions on building
and previewing the converted files.
