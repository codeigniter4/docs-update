# Upgrade Localization

- [Documentations](#documentations)
- [What has been changed](#what-has-been-changed)
- [Upgrade Guide](#upgrade-guide)
- [Code Example](#code-example)
    - [CodeIgniter Version 3.x](#codeigniter-version-3x)
    - [CodeIgniter Version 4.x](#codeigniter-version-4x)

## Documentations

- <a href="http://codeigniter.com/userguide3/libraries/language.html" target="_blank">Language Documentation CodeIgniter 3.X</a>

- [Localization Documentation CodeIgniter 4.X](#/outgoing/localization)

## What has been changed

- In CI4 the language files return the language lines as array.

## Upgrade Guide

1.  Specify the default language in **Config/App.php**:

> ```php
--8<--
installation/>
> upgrade_localization/001.php
>
>
--8<--
```

2.  Now move your language files to **app/Language/\<locale\>**.
3.  After that you have to change the syntax within the language files. Below in the Code Example you will see how the language array within the file should look like.
4.  Remove from every file the language loader `$this->lang->load($file, $lang);`.
5.  Replace the method to load the language line `$this->lang->line('error_email_missing')` with `echo lang('Errors.errorEmailMissing');`.

## Code Example

### CodeIgniter Version 3.x

```php
--8<--
installation/upgrade_localization/ci3sample/002.php
--8<--
```

### CodeIgniter Version 4.x

```php
--8<--
installation/upgrade_localization/002.php
--8<--
```
