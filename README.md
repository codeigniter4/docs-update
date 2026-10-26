# CodeIgniter Documentation Project

The aim of this project is to convert the CodeIgniter User Guide from the existing reStructuredText format
to Markdown format, making it easier to maintain and contribute to, as well as allowing all of our projects
to share a common documentation format.

## Configuration

The converter reads the user guide from the `user_guide_src/source` folder of a CodeIgniter4 checkout.
To configure it, copy `.env.dist` to `.env` and set the path:

```bash
cp .env.dist .env

DOCS_PROJECT_PATH=/path/to/CodeIgniter4/user_guide_src/source/
```

An environment variable of the same name takes precedence over `.env`.

## The Conversion Process

The conversion process is being done using pandoc to do the initial conversion.
From there, each section and file will be manually reviewed and cleaned up as needed.

Once the conversion is complete, we will start taking a look at the overall structure of the documentation
and see what changes need to be made for consistency, clarity, and ease of understanding.

## Requirements

To convert, pandoc 3.12.1 is required. The converter warns when another version is installed.
To install it, see <https://github.com/jgm/pandoc/releases/tag/3.12.1>.

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

### Converting all files

To convert all files from rst to md:

```bash
php ./admin/convert.php
```

> [!NOTE]
> This will first delete all files and directories in the `./docs` directory, except for the `assets` directory,
which contains images and other assets used by the documentation.

You can also convert individual files:

```bash
php ./admin/convert.php filename
```

See [how to use filters](./admin/how_to_use_filters.md) for more details about the conversion process.

### Verifying the converted files

To compare the converted files with their RST source:

```bash
php ./admin/verify.php --source /path/to/CodeIgniter4/user_guide_src/source --output ./docs
```

It prints a table of checks, lists the first failures of each, and exits non-zero when any check fails.
`--summary <file>` also appends the results as Markdown to that file.
The [Verify conversion](./.github/workflows/verify.yml) workflow runs both against the `develop` and `4.8` branches of CodeIgniter4.

### Previewing the converted files

See the [How to Build Docs](./admin/how_to_build_docs.md) document for instructions on building
and previewing the converted files.
