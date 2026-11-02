# How to Build the Documentation

We use Material for MkDocs for our documentation.
See https://squidfunk.github.io/mkdocs-material/getting-started/.

## Requirements

- Python 3.11 or newer
- pip

## Installation

Install Material for MkDocs, which brings MkDocs with it, into the `venv` virtual environment:

```console
make install
```

The pinned version is in `requirements.txt`.

## Build the Docs

Convert the user guide first (see the [README](../README.md)), then build it:

```console
make build
```

This runs `mkdocs build --strict`, which fails on a broken link or a nav entry without a page.
It then runs `admin/llms.py`, which writes `llms.txt`, `llms-full.txt` and a Markdown copy of
each page beside its HTML in `site/`.
The script reads only the nav and the Markdown, so it does not depend on MkDocs.

## See the Docs

```console
make serve
```

Without `make`, run the same steps by hand:

```console
python3 -m venv venv
venv/bin/pip install -r requirements.txt
venv/bin/mkdocs build --strict
venv/bin/python admin/llms.py
venv/bin/mkdocs serve
```

This repository does not deploy the documentation. Publishing happens in CodeIgniter4 once the converted guide moves there.
