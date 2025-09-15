# Security Helper

The Security Helper file contains security related functions.

- [Loading this Helper](#loading-this-helper)
- [Available Functions](#available-functions)

## Loading this Helper

This helper is loaded using the following code:

```php
--8<--
helpers/security_helper/001.php
--8<--
```

## Available Functions

The following functions are available:

#### sanitize_filename($filename)

- **Parameters**  

- **$filename** `string` Filename

- **Returns**: Sanitized file name

- **Return type**: `string`

Provides protection against directory traversal.

This function is an alias for `\CodeIgniter\Security::sanitizeFilename()`. For more info, please see the [Security Library](../general/../libraries/security.md) documentation.

#### strip_image_tags($str)

- **Parameters**  

- **$str** `string` Input string

- **Returns**: The input string with no image tags

- **Return type**: `string`

This is a security function that will strip image tags from a string. It leaves the image URL as plain text.

Example:

```php
--8<--
helpers/security_helper/002.php
--8<--
```

#### encode_php_tags($str)

- **Parameters**  

- **$str** `string` Input string

- **Returns**: Safely formatted string

- **Return type**: `string`

This is a security function that converts PHP tags to entities.

Example:

```php
--8<--
helpers/security_helper/003.php
--8<--
```
