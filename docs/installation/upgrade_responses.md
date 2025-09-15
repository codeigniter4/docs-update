# Upgrade HTTP Responses

- [Documentations](#documentations)
- [What has been changed](#what-has-been-changed)
- [Upgrade Guide](#upgrade-guide)
- [Code Example](#code-example)
    - [CodeIgniter Version 3.x](#codeigniter-version-3x)
    - [CodeIgniter Version 4.x](#codeigniter-version-4x)

## Documentations

- <a href="http://codeigniter.com/userguide3/libraries/output.html" target="_blank">Output Class Documentation CodeIgniter 3.X</a>

- [HTTP Responses Documentation CodeIgniter 4.X](#/outgoing/response)

## What has been changed

- The methods have been renamed

## Upgrade Guide

1.  The methods in the HTTP Responses class are named slightly different. The most important change in the naming is the switch from underscored method names to camelCase. The method `set_content_type()` from version 3 is now named `setContentType()` and so on.
2.  In the most cases you have to change `$this->output` to `$this->response` followed by the method. You can find all methods in [Response](../outgoing/response.md).

## Code Example

### CodeIgniter Version 3.x

```php
--8<--
installation/upgrade_responses/ci3sample/001.php
--8<--
```

### CodeIgniter Version 4.x

```php
--8<--
installation/upgrade_responses/001.php
--8<--
```
