PYTHON ?= $(shell for p in python3.14 python3.13 python3.12 python3.11 python3; do command -v $$p && break; done)
VENV   := venv
MKDOCS := $(VENV)/bin/mkdocs

# The environment takes precedence over .env, as in admin/convert.php.
DOCS_PROJECT_PATH ?= $(shell sed -n 's/^DOCS_PROJECT_PATH=//p' .env 2>/dev/null)
export DOCS_PROJECT_PATH

.PHONY: help install convert verify build serve watch

help:
	@echo 'make install               Install MkDocs into ./$(VENV)'
	@echo 'make convert               Convert the user guide into ./docs'
	@echo 'make convert PAGE=<page>   Convert one page, such as models/model'
	@echo 'make verify                Compare ./docs with the RST source'
	@echo 'make build                 Build the site and its llms files into ./site, failing on warnings'
	@echo 'make serve                 Preview the site at http://127.0.0.1:8000'
	@echo 'make watch                 Preview the site, reconverting the RST source as it changes'

install: $(MKDOCS)

$(MKDOCS): requirements.txt
	$(PYTHON) -c 'import sys; sys.exit(None if sys.version_info >= (3, 11) else "mkdocs-material needs Python 3.11 or newer. Run make install PYTHON=python3.12.")'
	$(PYTHON) -m venv --clear $(VENV)
	$(VENV)/bin/pip install --quiet -r requirements.txt
	touch $@

convert:
	php admin/convert.php $(PAGE)

verify:
	php admin/verify.php --source "$(DOCS_PROJECT_PATH)" --output docs

build: $(MKDOCS)
	$(MKDOCS) build --strict
	$(VENV)/bin/python admin/llms.py

serve: $(MKDOCS)
	$(MKDOCS) serve

watch: $(MKDOCS)
	php admin/watch.php & watcher=$$!; trap 'kill $$watcher' EXIT INT TERM; $(MKDOCS) serve
