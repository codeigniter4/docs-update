# Test Helper

The Test Helper file contains functions that assist in testing your project.

- [Loading this Helper](#loading-this-helper)
- [Available Functions](#available-functions)

## Loading this Helper

This helper is loaded using the following code:

```php
--8<--
helpers/test_helper/001.php
--8<--
```

## Available Functions

The following functions are available:

#### fake($model, array $overrides = null)

- **Parameters**  

- **$model** `Model|object|string` Instance or name of the model to use with Fabricator

- **$overrides** `array|null` Overriding data to pass to Fabricator::setOverrides()

- **Returns**: A random fake item created and added to the database by Fabricator

- **Return type**: `object|array`

Uses `CodeIgniter\Test\Fabricator` to create a random item and add it to the database.

Usage example:

```php
--8<--
helpers/test_helper/002.php
--8<--
```
