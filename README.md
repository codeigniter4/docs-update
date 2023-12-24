# CodeIgniter Documentation Project

The aim of this project is to convert the CodeIgniter User Guide from the existing reStructuredText format to Markdown format,
making it easier to maintain and contribute to, as well as allowing all of our projects to share a common documentation format.

## The Conversion Process

The conversion process is being done using pandoc to do the initial conversion.
From there, each section and file will be manually reviewed and cleaned up as needed.

Once the conversion is complete, we will start taking a look at the overall structure of the documentation and see what changes need
to be made for consistency, clarity, and ease of understanding.

## Requirements

To convert, pandoc is required. To install it, see <https://pandoc.org/installing.html>.

## Next Steps:

- [x] Build converter script to convert all files from rst to md
- [ ] Modify converter to insert code samples and remove unnecessary files.
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

You can also run conversion with [filters](./admin/how_to_use_filters):

```bash
php ./admin/convert-with-filters.php
```

### Previewing the converted files

See the [How to Build Docs](./admin/how_to_build_docs.md) document for instructions on building and previewing the converted files.
