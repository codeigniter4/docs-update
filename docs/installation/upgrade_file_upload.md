# Upgrade Working with Uploaded Files

- [Documentations](#documentations)
- [What has been changed](#what-has-been-changed)
- [Upgrade Guide](#upgrade-guide)
- [Code Example](#code-example)
    - [CodeIgniter Version 3.x](#codeigniter-version-3x)
    - [CodeIgniter Version 4.x](#codeigniter-version-4x)

## Documentations

- [File Uploading Class Documentation CodeIgniter 3.X](http://codeigniter.com/userguide3/libraries/file_uploading.html)

- [Working with Uploaded Files Documentation CodeIgniter 4.X](#/libraries/uploaded-files)

## What has been changed

- The functionality of the file upload has changed a lot. You can now check if the file got uploaded without errors and moving / storing files is simpler.

## Upgrade Guide

In CI4 you access uploaded files with `$file = $this->request->getFile('userfile')`. From there you can validate if the file got uploaded successfully with `$file->isValid()`. To store the file you could use `$path = $this->request->getFile('userfile')->store('head_img/', 'user_name.jpg');`. This will store the file in **writable/uploads/head_img/user_name.jpg**.

You have to change your file uploading code to match the new methods.

## Code Example

### CodeIgniter Version 3.x

```php
--8<--
installation/upgrade_file_upload/ci3sample/001.php
--8<--
```

### CodeIgniter Version 4.x

```php
--8<--
installation/upgrade_file_upload/001.php
--8<--
```
