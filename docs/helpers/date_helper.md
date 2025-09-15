# Date Helper

The Date Helper file contains functions that assist in working with dates.

- [Loading this Helper](#loading-this-helper)
- [Available Functions](#available-functions)

!!! note "Note"
    Many functions previously found in the CodeIgniter 3 `date_helper` have been moved to the [Time](../general/../libraries/time.md) class in CodeIgniter 4.

## Loading this Helper

This helper is loaded using the following code:

```php
--8<--
helpers/date_helper/001.php
--8<--
```

## Available Functions

The following functions are available:

#### now(\[$timezone = null])

- **Parameters**  

- **$timezone** `string` Timezone

- **Returns**: UNIX timestamp

- **Return type**: `int`

!!! note "Note"
    It is recommended to use the [Time](../general/../libraries/time.md) class instead. Use `Time::now()->getTimestamp()` to get the current UNIX timestamp.

If a timezone is not provided, it will return the current UNIX timestamp by `time()`.

```php
--8<--
helpers/date_helper/002.php
--8<--
```

If any PHP supported timezone is provided, it will return a timestamp that is offset by the time difference. It is not the same as the current UNIX timestamp.

If you do not intend to set your master time reference to any other PHP supported timezone (which you'll typically do if you run a site that lets each user set their own timezone settings) there is no benefit to using this function over PHP's `time()` function.

#### timezone_select(\[$class = '', $default = '', $what = DateTimeZone::ALL, $country = null])

- **Parameters**  

- **$class** `string` Optional class to apply to the select field

- **$default** `string` Default value for initial selection

- **$what** `int` DateTimeZone class constants (see \[listIdentifiers](#https://www.php.net/manual/en/datetimezone.listidentifiers.php)\_)

- **$country** `string` A two-letter ISO 3166-1 compatible country code (see \[listIdentifiers](#https://www.php.net/manual/en/datetimezone.listidentifiers.php)\_)

- **Returns**: Preformatted HTML select field

- **Return type**: `string`

Generates a <span class="title-ref">select</span> form field of available timezones (optionally filtered by `$what` and `$country`). You can supply an option class to apply to the field to make formatting easier, as well as a default selected value.

```php
--8<--
helpers/date_helper/003.php
--8<--
```
