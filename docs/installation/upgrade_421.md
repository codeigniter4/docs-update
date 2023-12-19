# Upgrading from 4.2.0 to 4.2.1

Please refer to the upgrade instructions corresponding to your
installation method.

- `Composer Installation App Starter Upgrading <app-starter-upgrading>`
- `Composer Installation Adding CodeIgniter4 to an Existing Project Upgrading <adding-codeigniter4-upgrading>`
- `Manual Installation Upgrading <installing-manual-upgrading>`

<div class="contents" local="" depth="2">

</div>

## Mandatory File Changes

### app/Config/Mimes.php

- The mapping of file extensions to MIME types in
  **app/Config/Mimes.php** was updated to fix a bug. Also, the logic of
  `Mimes::getExtensionFromType()` was changed.

## Breaking Changes

### get_cookie()

If there is a cookie with a prefixed name and a cookie with the same
name without a prefix, the previous `get_cookie()` had the tricky
behavior of returning the cookie without the prefix.

For example, when `Config\Cookie::$prefix` is `prefix_`, there are two
cookies, `test` and `prefix_test`:

``` php
$_COOKIES = [
    'test'        => 'Non CI Cookie',
    'prefix_test' => 'CI Cookie',
];
```

Previously, `get_cookie()` returns the following:

``` php
get_cookie('test');        // returns "Non CI Cookie"
get_cookie('prefix_test'); // returns "CI Cookie"
```

Now the behavior has been fixed as a bug, and has been changed like the
following.

``` php
get_cookie('test');              // returns "CI Cookie"
get_cookie('prefix_test');       // returns null
get_cookie('test', false, null); // returns "Non CI Cookie"
```

If you depend on the previous behavior, you need to change your code.

> [!NOTE]
> In the example above, if there is only one cookie `prefix_test`, the
> previous `get_cookie('test')` also returns `"CI Cookie"`.

## Breaking Enhancements

## Project Files

Numerous files in the **project space** (root, app, public, writable)
received updates. Due to these files being outside of the **system**
scope they will not be changed without your intervention. There are some
third-party CodeIgniter modules available to assist with merging changes
to the project space: [Explore on
Packagist](https://packagist.org/explore/?query=codeigniter4%20updates).

> [!NOTE]
> Except in very rare cases for bug fixes, no changes made to files for
> the project space will break your application. All changes noted here
> are optional until the next major version, and any mandatory changes
> will be covered in the sections above.

### Content Changes

### All Changes

This is a list of all files in the **project space** that received
changes; many will be simple comments or formatting that have no effect
on the runtime:

- app/Config/Mimes.php
