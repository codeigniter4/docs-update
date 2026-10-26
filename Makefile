PYTHON ?= python3
VENV   := venv
MKDOCS := $(VENV)/bin/mkdocs

# The environment takes precedence over .env, as in admin/convert.php.
DOCS_PROJECT_PATH ?= $(shell sed -n 's/^DOCS_PROJECT_PATH=//p' .env 2>/dev/null)
export DOCS_PROJECT_PATH

.PHONY: help install convert verify build serve

help:
	@echo 'make install               Install MkDocs into ./$(VENV)'
	@echo 'make convert               Convert the user guide into ./docs'
	@echo 'make convert PAGE=<page>   Convert one page, such as models/model'
	@echo 'make verify                Compare ./docs with the RST source'
	@echo 'make build                 Build the site into ./site, failing on warnings'
	@echo 'make serve                 Preview the site at http://127.0.0.1:8000'

install: $(MKDOCS)

$(MKDOCS): requirements.txt
	$(PYTHON) -m venv $(VENV)
	$(VENV)/bin/pip install --quiet -r requirements.txt
	touch $@

convert:
	php admin/convert.php $(PAGE)

verify:
	php admin/verify.php --source "$(DOCS_PROJECT_PATH)" --output docs

build: $(MKDOCS)
	$(MKDOCS) build --strict

serve: $(MKDOCS)
	$(MKDOCS) serve
