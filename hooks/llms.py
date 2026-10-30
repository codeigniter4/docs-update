"""MkDocs hook writing `llms.txt`, `llms-full.txt` and each page's Markdown beside its HTML in the built site."""

from __future__ import annotations

from pathlib import Path
from typing import Iterable, Iterator

from mkdocs.config.defaults import MkDocsConfig
from mkdocs.structure.files import Files
from mkdocs.structure.nav import Navigation, Section
from mkdocs.structure.pages import Page

_nav: Navigation
_files: Files


def on_nav(nav: Navigation, config: MkDocsConfig, files: Files) -> Navigation:
    global _nav, _files
    _nav, _files = nav, files

    return nav


def on_post_build(config: MkDocsConfig) -> None:
    site = Path(config.site_dir)
    in_nav = {page.file.src_uri for page in _nav.pages}
    outside_nav = [file for file in _files.documentation_pages() if file.src_uri not in in_nav]
    ordered = [page.file for page in _nav.pages] + outside_nav

    for file in ordered:
        (site / file.src_uri).write_text(Path(file.abs_src_path).read_text(encoding="utf-8"), encoding="utf-8")

    index = [f"# {config.site_name}", "", f"> {config.site_description}", "", *_index_lines(_nav.items)]

    if outside_nav:
        index += ["", "## Other pages", ""]
        index += [f"- [{file.name}]({file.src_uri})" for file in outside_nav]

    (site / "llms.txt").write_text("\n".join(index) + "\n", encoding="utf-8")
    (site / "llms-full.txt").write_text(
        "\n\n".join(Path(file.abs_src_path).read_text(encoding="utf-8").strip() for file in ordered) + "\n",
        encoding="utf-8",
    )


def _index_lines(items: Iterable[object], depth: int = 0) -> Iterator[str]:
    indent = "    " * max(depth - 1, 0)

    for item in items:
        if isinstance(item, Section):
            yield from ["", f"## {item.title}", ""] if depth == 0 else [f"{indent}- {item.title}"]
            yield from _index_lines(item.children, depth + 1)
        elif isinstance(item, Page):
            yield f"{indent}- [{item.title}]({item.file.src_uri})"
