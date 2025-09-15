# Upgrade Validations

- [Documentations of Library](#documentations-of-library)
- [What has been changed](#what-has-been-changed)
- [Upgrade Guide](#upgrade-guide)
- [Code Example](#code-example)
    - [CodeIgniter Version 3.x](#codeigniter-version-3x)
    - [CodeIgniter Version 4.x](#codeigniter-version-4x)

## Documentations of Library

- <a href="http://codeigniter.com/userguide3/libraries/form_validation.html" target="_blank">Form Validation Documentation CodeIgniter 3.X</a>

- [Validation Documentation CodeIgniter 4.X](#/libraries/validation)

## What has been changed

- If you want to change validation error display, you have to set CI4 [validation View templates](#validation-customizing-error-display).

- CI4 validation has no <a href="http://www.codeigniter.com/userguide3/libraries/form_validation.html#callbacks-your-own-validation-methods" target="_blank">Callbacks</a> in CI3. Use [Callable Rules](#validation-using-callable-rule) (since v4.5.0) or [Closure Rules](#validation-using-closure-rule) (since v4.3.0) or [Rule Classes](#validation-using-rule-classes) instead.

- In CI3, Callbacks/Callable rules were prioritized, but in CI4, Closure/Callable Rules are not prioritized, and are checked in the order in which they are listed.

- Since v4.5.0, [Callable Rules](#validation-using-callable-rule) has been introduced, but it is a bit different from CI3's <a href="http://www.codeigniter.com/userguide3/libraries/form_validation.html#callable-use-anything-as-a-rule" target="_blank">Callable</a>.

- CI4 validation format rules do not permit empty string.

- CI4 validation never changes your data.

- Since v4.3.0, `validation_errors()` has been introduced, but the API is different from CI3's.

## Upgrade Guide

1.  Within the view which contains the form you have to change:

    > - `<?php echo validation_errors(); ?>` to `<?= validation_list_errors() ?>`

2.  Within the controller you have to change the following:

    > - `$this->load->helper(array('form', 'url'));` to `helper(['form', 'url']);`
    > - remove the line `$this->load->library('form_validation');`
    > - `if ($this->form_validation->run() == FALSE)` to `if (! $this->validate([]))`
    > - `$this->load->view('myform');` to `return view('myform', ['validation' => $this->validator,]);`

3.  You have to change the validation rules. The new syntax is to set the rules as array in the controller:

> ```php
--8<--
installation/>
> upgrade_validations/001.php
>
>
--8<--
```

## Code Example

### CodeIgniter Version 3.x

Path: **application/views**:

    <html>
    <head>
        <title>My Form</title>
    </head>
    <body>

        <?php echo validation_errors(); ?>

        <?php echo form_open('form'); ?>

        <h5>Username</h5>
        <input type="text" name="username" value="" size="50" />

        <h5>Password</h5>
        <input type="text" name="password" value="" size="50" />

        <h5>Password Confirm</h5>
        <input type="text" name="passconf" value="" size="50" />

        <h5>Email Address</h5>
        <input type="text" name="email" value="" size="50" />

        <div><input type="submit" value="Submit" /></div>

        </form>

    </body>
    </html>

Path: **application/controllers**:

```php
--8<--
installation/upgrade_validations/ci3sample/002.php
--8<--
```

### CodeIgniter Version 4.x

Path: **app/Views**:

    <html>
    <head>
        <title>My Form</title>
    </head>
    <body>

        <?= validation_list_errors() ?>

        <?= form_open('form') ?>

        <h5>Username</h5>
        <input type="text" name="username" value="" size="50" />

        <h5>Password</h5>
        <input type="text" name="password" value="" size="50" />

        <h5>Password Confirm</h5>
        <input type="text" name="passconf" value="" size="50" />

        <h5>Email Address</h5>
        <input type="text" name="email" value="" size="50" />

        <div><input type="submit" value="Submit" /></div>

        </form>

    </body>
    </html>

Path: **app/Controllers**:

```php
--8<--
installation/upgrade_validations/002.php
--8<--
```
