# Upgrade HTML Tables

- [Documentations](#documentations)
- [What has been changed](#what-has-been-changed)
- [Upgrade Guide](#upgrade-guide)
- [Code Example](#code-example)
    - [CodeIgniter Version 3.x](#codeigniter-version-3x)
    - [CodeIgniter Version 4.x](#codeigniter-version-4x)

## Documentations

- <a href="http://codeigniter.com/userguide3/libraries/table.html" target="_blank">HTML Table Documentation CodeIgniter 3.X</a>

- [HTML Table Documentation CodeIgniter 4.X](#/outgoing/table)

## What has been changed

- Only small things like the method names and the loading of the library have changed.

## Upgrade Guide

1.  Within your class change the `$this->load->library('table');` to `$table = new \CodeIgniter\View\Table();`.
2.  From that on you have to replace every line starting with `$this->table` to `$table`. For example: `echo $this->table->generate($query);` will become `echo $table->generate($query);`
3.  The methods in the HTML Table class could be named slightly different. The most important change in the naming is the switch from underscored method names to camelCase. The method `set_heading()` from version 3 is now named `setHeading()` and so on.

## Code Example

### CodeIgniter Version 3.x

```php
--8<--
installation/upgrade_html_tables/ci3sample/001.php
--8<--
```

### CodeIgniter Version 4.x

```php
--8<--
installation/upgrade_html_tables/001.php
--8<--
```
