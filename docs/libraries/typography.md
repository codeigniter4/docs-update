# Typography

The Typography library contains methods that help you format text in semantically relevant ways.

- [Loading the Library](#loading-the-library)
- [Available static functions](#available-static-functions)

## Loading the Library

Like all services in CodeIgniter, it can be loaded via `Config\Services`, though you usually will not need to load it manually:

```php
--8<--
libraries/typography/001.php
--8<--
```

## Available static functions

The following functions are available:

#### autoTypography($str\[, $reduce_linebreaks = false])

- **Parameters**  

- **$str** `string` Input string

- **$reduce_linebreaks** `bool` Whether to reduce multiple instances of double newlines to two

- **Returns**: HTML-formatted typography-safe string

- **Return type**: `string`

Formats text so that it is semantically and typographically correct HTML.

Usage example:

```php
--8<--
libraries/typography/002.php
--8<--
```

!!! note "Note"
    Typographic formatting can be processor intensive, particularly if

you have a lot of content being formatted. If you choose to use this function you may want to consider [caching](../general/../general/caching.md) your pages.

#### formatCharacters($str)

- **Parameters**  

- **$str** `string` Input string

- **Returns**: String with formatted characters.

- **Return type**: `string`

This function mainly converts double and single quotes to curly entities, but it also converts em-dashes, double spaces, and ampersands.

Usage example:

```php
--8<--
libraries/typography/003.php
--8<--
```

#### nl2brExceptPre($str)

- **Parameters**  

- **$str** `string` Input string

- **Returns**: String with HTML-formatted line breaks

- **Return type**: `string`

Converts newlines to `<br />` tags unless they appear within `<pre>` tags. This function is identical to the native PHP `nl2br()` function, except that it ignores `<pre>` tags.

Usage example:

```php
--8<--
libraries/typography/004.php
--8<--
```
