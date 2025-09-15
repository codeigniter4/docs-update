# Inflector Helper

The Inflector Helper file contains functions that permit you to change **English** words to plural, singular, camel case, etc.

- [Loading this Helper](#loading-this-helper)
- [Available Functions](#available-functions)

## Loading this Helper

This helper is loaded using the following code:

```php
--8<--
helpers/inflector_helper/001.php
--8<--
```

## Available Functions

The following functions are available:

#### singular($string)

- **Parameters**  

- **$string** `string` Input string

- **Returns**: A singular word

- **Return type**: `string`

Changes a plural word to singular. Example:

```php
--8<--
helpers/inflector_helper/002.php
--8<--
```

#### plural($string)

- **Parameters**  

- **$string** `string` Input string

- **Returns**: A plural word

- **Return type**: `string`

Changes a singular word to plural. Example:

```php
--8<--
helpers/inflector_helper/003.php
--8<--
```

#### counted($count, $string)

- **Parameters**  

- **$count** `int` Number of items

- **$string** `string` Input string

- **Returns**: A singular or plural phrase

- **Return type**: `string`

Changes a word and its count to a phrase. Example:

```php
--8<--
helpers/inflector_helper/004.php
--8<--
```

#### camelize($string)

- **Parameters**  

- **$string** `string` Input string

- **Returns**: Camel case string

- **Return type**: `string`

Changes a string of words separated by spaces or underscores to camel case. Example:

```php
--8<--
helpers/inflector_helper/005.php
--8<--
```

#### pascalize($string)

- **Parameters**  

- **$string** `string` Input string

- **Returns**: Pascal case string

- **Return type**: `string`

Changes a string of words separated by spaces or underscores to Pascal case, which is camel case with the first letter capitalized. Example:

```php
--8<--
helpers/inflector_helper/006.php
--8<--
```

#### underscore($string)

- **Parameters**  

- **$string** `string` Input string

- **Returns**: String containing underscores instead of spaces

- **Return type**: `string`

Takes multiple words separated by spaces and underscores them. Example:

```php
--8<--
helpers/inflector_helper/007.php
--8<--
```

#### decamelize($string)

- **Parameters**  

- **$string** `string` Input string

- **Returns**: String containing underscores between words

- **Return type**: `string`

Takes multiple words in camelCase or PascalCase and converts them to snake_case. Example:

```php
--8<--
helpers/inflector_helper/014.php
--8<--
```

#### humanize($string\[, $separator = '\_'])

- **Parameters**  

- **$string** `string` Input string

- **$separator** `string` Input separator

- **Returns**: Humanized string

- **Return type**: `string`

Takes multiple words separated by underscores and adds spaces between them. Each word is capitalized.

Example:

```php
--8<--
helpers/inflector_helper/008.php
--8<--
```

To use dashes instead of underscores:

```php
--8<--
helpers/inflector_helper/009.php
--8<--
```

#### is_pluralizable($word)

- **Parameters**  

- **$word** `string` Input string

- **Returns**: true if the word is countable or false if not

- **Return type**: `bool`

Checks if the given word has a plural version. Example:

```php
--8<--
helpers/inflector_helper/010.php
--8<--
```

#### dasherize($string)

- **Parameters**  

- **$string** `string` Input string

- **Returns**: Dasherized string

- **Return type**: `string`

Replaces underscores with dashes in the string. Example:

```php
--8<--
helpers/inflector_helper/011.php
--8<--
```

#### ordinal($integer)

- **Parameters**  

- **$integer** `int` The integer to determine the suffix

- **Returns**: Ordinal suffix

- **Return type**: `string`

Returns the suffix that should be added to a number to denote the position such as 1st, 2nd, 3rd, 4th. Example:

```php
--8<--
helpers/inflector_helper/012.php
--8<--
```

#### ordinalize($integer)

- **Parameters**  

- **$integer** `int` The integer to ordinalize

- **Returns**: Ordinalized integer

- **Return type**: `string`

Turns a number into an ordinal string used to denote the position such as 1st, 2nd, 3rd, 4th. Example:

```php
--8<--
helpers/inflector_helper/013.php
--8<--
```
