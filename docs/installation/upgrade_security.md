# Upgrade Security

<div class="contents" local="" depth="2">

</div>

## Documentations

- [Security Class Documentation CodeIgniter
  3.X](http://codeigniter.com/userguide3/libraries/security.html)
- `Security Documentation CodeIgniter 4.X </libraries/security>`

> [!NOTE]
> If you use the `../helpers/form_helper` and enable the CSRF filter
> globally, then `form_open()` will automatically insert a hidden CSRF
> field in your forms. So you do not have to upgrade this by yourself.

## What has been changed

- The method to implement CSRF tokens to HTML forms has been changed.

## Upgrade Guide

1.  To enable CSRF protection in CI4 you have to enable it in
    **app/Config/Filters.php**:

    <div class="literalinclude">

    upgrade_security/001.php

    </div>

2.  Within your HTML forms you have to remove the CSRF input field which
    looks similar to
    `<input type="hidden" name="<?= $csrf['name'] ?>" value="<?= $csrf['hash'] ?>" />`.

3.  Now, within your HTML forms you have to add `<?= csrf_field() ?>`
    somewhere in the form body, unless you are using `form_open()`.

## Code Example

### CodeIgniter Version 3.x

<div class="literalinclude">

upgrade_security/ci3sample/002.php

</div>

### CodeIgniter Version 4.x

<div class="literalinclude">

upgrade_security/002.php

</div>
