# Upgrade Views

- [Documentations](#documentations)
- [What has been changed](#what-has-been-changed)
- [Upgrade Guide](#upgrade-guide)
- [Code Example](#code-example)
    - [CodeIgniter Version 3.x](#codeigniter-version-3x)
    - [CodeIgniter Version 4.x](#codeigniter-version-4x)

## Documentations

- \[View Documentation CodeIgniter 3.X](#http://codeigniter.com/userguide3/general/views.html)\_

- [View Documentation CodeIgniter 4.X](#/outgoing/views)

## What has been changed

- Your views look much like before, but they are invoked differently ... instead of CI3's `$this->load->view('x');`, you can use `return view('x');`.

- CI4 supports [View Cells](../outgoing/view_cells.md) to build your response in pieces, and [View Layouts](../outgoing/view_layouts.md) for page layout.

- The [Template Parser](../general/../outgoing/view_parser.md) is still there, and substantially enhanced.

## Upgrade Guide

1.  First, move all views to the folder **app/Views**

2.  Change the loading syntax of views in every script where you load views:  

- from `$this->load->view('directory_name/file_name')` to `return view('directory_name/file_name');`

- from `$content = $this->load->view('file', $data, TRUE);` to `$content = view('file', $data);`

3.  (optional) You can change the echo syntax in views from `<?php echo $title; ?>` to `<?= $title ?>`

4.  Remove the line `defined('BASEPATH') OR exit('No direct script access allowed');` if it exists.

## Code Example

### CodeIgniter Version 3.x

Path: **application/views**:

```php
--8<--
installation/upgrade_views/ci3sample/001.php
--8<--
```

### CodeIgniter Version 4.x

Path: **app/Views**:

```php
--8<--
installation/upgrade_views/001.php
--8<--
```
