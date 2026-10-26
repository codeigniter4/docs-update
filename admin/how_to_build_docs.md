# How to Build the Documentation

We use Material for MkDocs for our documentation.
See https://squidfunk.github.io/mkdocs-material/getting-started/.

## Requirements

- Python 3
- pip

## Installation

Install Material for MkDocs, which brings MkDocs with it, in a virtual environment:

```console
python3 -m venv venv
source venv/bin/activate
pip install mkdocs-material
```

## Build the Docs

Convert the user guide first (see the [README](../README.md)), then build it:

```console
mkdocs build
```

## See the Docs

```console
mkdocs serve
```

This repository does not deploy the documentation. Publishing happens in CodeIgniter4 once the converted guide moves there.
