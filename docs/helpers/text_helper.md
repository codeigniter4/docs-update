# Text Helper

The Text Helper file contains functions that assist in working with Text.

- [Loading this Helper](#loading-this-helper)
- [Available Functions](#available-functions)

## Loading this Helper

This helper is loaded using the following code:

```php
--8<--
helpers/text_helper/001.php
--8<--
```

## Available Functions

The following functions are available:

#### random_string(\[$type = 'alnum'\[, $len = 8]])

- **Parameters**  

- **$type** `string` Randomization type

- **$len** `int` Output string length

- **Returns**: A random string

- **Return type**: `string`

Generates a random string based on the type and length you specify. Useful for creating passwords or generating random hashes.

!!! warning "Warning"
    For types: **basic**, **md5**, and **sha1**, generated strings

are not cryptographically secure. Therefore, these types cannot be used for cryptographic purposes or purposes requiring unguessable return values. Since v4.3.3, these types are deprecated.

The first parameter specifies the type of string, the second parameter specifies the length. The following choices are available:

- **alpha**: A string with lower and uppercase letters only.

- **alnum**: Alphanumeric string with lower and uppercase characters.

- **basic**: \[deprecated] A random number based on `mt_rand()` (length ignored).

- **numeric**: Numeric string.

- **nozero**: Numeric string with no zeros.

- **md5**: \[deprecated] An encrypted random number based on `md5()` (fixed length of 32).

- **sha1**: \[deprecated] An encrypted random number based on `sha1()` (fixed length of 40).

- **crypto**: A random string based on `random_bytes()`.

!!! note "Note"
    When you use **crypto**, you must set an even number to the second parameter.

Since v4.2.2, if you set an odd number, `InvalidArgumentException` will be thrown.

!!! note "Note"
    Since v4.3.3, **alpha**, **alnum** and **nozero** use `random_byte()`,

and **numeric** uses `random_int()`. In the previous versions, it used `str_shuffle()` that is not cryptographically secure.

Usage example:

```php
--8<--
helpers/text_helper/002.php
--8<--
```

#### increment_string($str\[, $separator = '\_'\[, $first = 1]])

- **Parameters**  

- **$str** `string` Input string

- **$separator** `string` Separator to append a duplicate number with

- **$first** `int` Starting number

- **Returns**: An incremented string

- **Return type**: `string`

Increments a string by appending a number to it or increasing the number. Useful for creating "copies" or a file or duplicating database content which has unique titles or slugs.

Usage example:

```php
--8<--
helpers/text_helper/003.php
--8<--
```

#### alternator($args)

- **Parameters**  

- **$args** `mixed` A variable number of arguments

- **Returns**: Alternated string(s)

- **Return type**: `mixed`

Allows two or more items to be alternated between, when cycling through a loop. Example:

```php
--8<--
helpers/text_helper/004.php
--8<--
```

You can add as many parameters as you want, and with each iteration of your loop the next item will be returned.

```php
--8<--
helpers/text_helper/005.php
--8<--
```

!!! note "Note"
    To use multiple separate calls to this function simply call the

function with no arguments to re-initialize.

#### reduce_double_slashes($str)

- **Parameters**  

- **$str** `string` Input string

- **Returns**: A string with normalized slashes

- **Return type**: `string`

Converts double slashes in a string to a single slash, except those found in URL protocol prefixes (e.g., http&#58;//).

Example:

```php
--8<--
helpers/text_helper/006.php
--8<--
```

#### strip_slashes($data)

- **Parameters**  

- **$data** `mixed` Input string or an array of strings

- **Returns**: String(s) with stripped slashes

- **Return type**: `mixed`

Removes any slashes from an array of strings.

Example:

```php
--8<--
helpers/text_helper/007.php
--8<--
```

The above will return the following array:

```php
--8<--
helpers/text_helper/008.php
--8<--
```

!!! note "Note"
    For historical reasons, this function will also accept

and handle string inputs. This however makes it just an alias for `stripslashes()`.

#### reduce_multiples($str\[, $character = ''\[, $trim = false]])

- **Parameters**  

- **$str** `string` Text to search in

- **$character** `string` Character to reduce

- **$trim** `bool` Whether to also trim the specified character

- **Returns**: Reduced string

- **Return type**: `string`

Reduces multiple instances of a particular character occurring directly after each other. Example:

```php
--8<--
helpers/text_helper/009.php
--8<--
```

If the third parameter is set to true it will remove occurrences of the character at the beginning and the end of the string. Example:

```php
--8<--
helpers/text_helper/010.php
--8<--
```

#### quotes_to_entities($str)

- **Parameters**  

- **$str** `string` Input string

- **Returns**: String with quotes converted to HTML entities

- **Return type**: `string`

Converts single and double quotes in a string to the corresponding HTML entities. Example:

```php
--8<--
helpers/text_helper/011.php
--8<--
```

#### strip_quotes($str)

- **Parameters**  

- **$str** `string` Input string

- **Returns**: String with quotes stripped

- **Return type**: `string`

Removes single and double quotes from a string. Example:

```php
--8<--
helpers/text_helper/012.php
--8<--
```

#### word_limiter($str\[, $limit = 100\[, $end_char = '&#8230;']])

- **Parameters**  

- **$str** `string` Input string

- **$limit** `int` Limit

- **$end_char** `string` End character (usually an ellipsis)

- **Returns**: Word-limited string

- **Return type**: `string`

Truncates a string to the number of *words* specified. Example:

```php
--8<--
helpers/text_helper/013.php
--8<--
```

The third parameter is an optional suffix added to the string. By default it adds an ellipsis.

#### character_limiter($str\[, $n = 500\[, $end_char = '&#8230;']])

- **Parameters**  

- **$str** `string` Input string

- **$n** `int` Number of characters

- **$end_char** `string` End character (usually an ellipsis)

- **Returns**: Character-limited string

- **Return type**: `string`

Truncates a string to the number of *characters* specified. It maintains the integrity of words so the character count may be slightly more or less than what you specify.

Example:

```php
--8<--
helpers/text_helper/014.php
--8<--
```

The third parameter is an optional suffix added to the string, if undeclared this helper uses an ellipsis.

!!! note "Note"
    If you need to truncate to an exact number of characters, please

see the `ellipsize()` function below.

#### ascii_to_entities($str)

- **Parameters**  

- **$str** `string` Input string

- **Returns**: A string with ASCII values converted to entities

- **Return type**: `string`

Converts ASCII values to character entities, including high ASCII and MS Word characters that can cause problems when used in a web page, so that they can be shown consistently regardless of browser settings or stored reliably in a database. There is some dependence on your server's supported character sets, so it may not be 100% reliable in all cases, but for the most part, it should correctly identify characters outside the normal range (like accented characters).

Example:

```php
--8<--
helpers/text_helper/015.php
--8<--
```

#### entities_to_ascii($str\[, $all = true])

- **Parameters**  

- **$str** `string` Input string

- **$all** `bool` Whether to convert unsafe entities as well

- **Returns**: A string with HTML entities converted to ASCII characters

- **Return type**: `string`

This function does the opposite of `ascii_to_entities()`. It turns character entities back into ASCII.

#### convert_accented_characters($str)

- **Parameters**  

- **$str** `string` Input string

- **Returns**: A string with accented characters converted

- **Return type**: `string`

Transliterates high ASCII characters to low ASCII equivalents. Useful when non-English characters need to be used where only standard ASCII characters are safely used, for instance, in URLs.

Example:

```php
--8<--
helpers/text_helper/016.php
--8<--
```

!!! note "Note"
    This function uses a companion config file

**app/Config/ForeignCharacters.php** to define the to and from array for transliteration.

#### word_censor($str, $censored\[, $replacement = ''])

- **Parameters**  

- **$str** `string` Input string

- **$censored** `array` List of bad words to censor

- **$replacement** `string` What to replace bad words with

- **Returns**: Censored string

- **Return type**: `string`

Enables you to censor words within a text string. The first parameter will contain the original string. The second will contain an array of words which you disallow. The third (optional) parameter can contain a replacement value for the words. If not specified they are replaced with pound signs: ####.

Example:

```php
--8<--
helpers/text_helper/017.php
--8<--
```

#### highlight_code($str)

- **Parameters**  

- **$str** `string` Input string

- **Returns**: String with code highlighted via HTML

- **Return type**: `string`

Colorizes a string of code (PHP, HTML, etc.). Example:

```php
--8<--
helpers/text_helper/018.php
--8<--
```

The function uses PHP's `highlight_string()` function, so the colors used are the ones specified in your php.ini file.

#### highlight_phrase($str, $phrase\[, $tag_open = '\<mark\>'\[, $tag_close = '\</mark\>']])

- **Parameters**  

- **$str** `string` Input string

- **$phrase** `string` Phrase to highlight

- **$tag_open** `string` Opening tag used for the highlight

- **$tag_close** `string` Closing tag for the highlight

- **Returns**: String with a phrase highlighted via HTML

- **Return type**: `string`

Will highlight a phrase within a text string. The first parameter will contain the original string, the second will contain the phrase you wish to highlight. The third and fourth parameters will contain the opening/closing HTML tags you would like the phrase wrapped in.

Example:

```php
--8<--
helpers/text_helper/019.php
--8<--
```

The above code prints:

Here is a \<span style="color:#990000;"\>nice text\</span\> string about nothing in particular.

!!! note "Note"
    This function used to use the `<strong>` tag by default. Older browsers

might not support the new HTML5 mark tag, so it is recommended that you insert the following CSS code into your stylesheet if you need to support such browsers:

mark { background: #ff0; color: #000; };

#### word_wrap($str\[, $charlim = 76])

- **Parameters**  

- **$str** `string` Input string

- **$charlim** `int` Character limit

- **Returns**: Word-wrapped string

- **Return type**: `string`

Wraps text at the specified *character* count while maintaining complete words.

Example:

```php
--8<--
helpers/text_helper/020.php
--8<--
```

#### ellipsize($str, $max_length\[, $position = 1\[, $ellipsis = '&hellip;']])

- **Parameters**  

- **$str** `string` Input string

- **$max_length** `int` String length limit

- **$position** `mixed` Position to split at (int or float)

- **$ellipsis** `string` What to use as the ellipsis character

- **Returns**: Ellipsized string

- **Return type**: `string`

This function will strip tags from a string, split it at a defined maximum length, and insert an ellipsis.

The first parameter is the string to ellipsize, the second is the number of characters in the final string. The third parameter is where in the string the ellipsis should appear from 0 - 1, left to right. For example. a value of 1 will place the ellipsis at the right of the string, .5 in the middle, and 0 at the left.

An optional fourth parameter is the kind of ellipsis. By default, &hellip; will be inserted.

Example:

```php
--8<--
helpers/text_helper/021.php
--8<--
```

Produces:

this_string_is_e&hellip;ak_my_design.jpg

#### excerpt($text, $phrase = false, $radius = 100, $ellipsis = '...')

- **Parameters**  

- **$text** `string` Text to extract an excerpt

- **$phrase** `string` Phrase or word to extract the text around

- **$radius** `int` Number of characters before and after $phrase

- **$ellipsis** `string` What to use as the ellipsis character

- **Returns**: Excerpt.

- **Return type**: `string`

This function will extract $radius number of characters before and after the central $phrase with an ellipsis before and after.

The first parameter is the text to extract an excerpt from, the second is the central word or phrase to count before and after. The third parameter is the number of characters to count before and after the central phrase. If no phrase passed, the excerpt will include the first $radius characters with the ellipsis at the end.

Example:

```php
--8<--
helpers/text_helper/022.php
--8<--
```

Produces:

    ... non mauris lectus. Phasellus eu sodales sem. Integer dictum purus ac

enim hendrerit gravida. Donec ac magna vel nunc tincidunt molestie sed vitae nisl. Cras sed auctor mauris, non dictum tortor. ...
