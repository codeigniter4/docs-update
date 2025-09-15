# Array Helper

The array helper provides several functions to simplify more complex usages of arrays. It is not intended to duplicate any of the existing functionality that PHP provides - unless it is to vastly simplify their usage.

- [Loading this Helper](#loading-this-helper)
- [Available Functions](#available-functions)

## Loading this Helper

This helper is loaded using the following code:

```php
--8<--
helpers/array_helper/001.php
--8<--
```

## Available Functions

The following functions are available:

#### dot_array_search(string $search, array $values)

- **Parameters**  

- **$search** `string` The dot-notation string describing how to search the array

- **$values** `array` The array to search

- **Returns**: The value found within the array, or null

- **Return type**: `mixed`

This method allows you to use dot-notation to search through an array for a specific-key, and allows the use of a the '\*' wildcard. Given the following array:

```php
--8<--
helpers/array_helper/002.php
--8<--
```

We can locate the value of 'fizz' by using the search string "foo.buzz.fizz". Likewise, the value of baz can be found with "foo.bar.baz":

```php
--8<--
helpers/array_helper/003.php
--8<--
```

You can use the asterisk as a wildcard to replace any of the segments. When found, it will search through all of the child nodes until it finds it. This is handy if you don't know the values, or if your values have a numeric index:

```php
--8<--
helpers/array_helper/004.php
--8<--
```

If the array key contains a dot, then the key can be escaped with a backslash:

```php
--8<--
helpers/array_helper/005.php
--8<--
```

!!! note "Note"
    Prior to v4.2.0, `dot_array_search('foo.bar.baz', ['foo' => ['bar' => 23]])` returned `23` due to a bug. v4.2.0 and later returns `null`.

#### array_deep_search($key, array $array)

- **Parameters**  

- **$key** `mixed` The target key

- **$array** `array` The array to search

- **Returns**: The value found within the array, or null

- **Return type**: `mixed`

Returns the value of an element with a key value in an array of uncertain depth

#### array_sort_by_multiple_keys(array &$array, array $sortColumns)

\* **Parameters**  

- **$array** `array` The array to be sorted (passed by reference).

- **$sortColumns** `array` The array keys to sort after and the respective PHP

sort flags as an associative array. \* **Returns**: Whether sorting was successful or not. \* **Return type**: `bool`

This method sorts the elements of a multidimensional array by the values of one or more keys in a hierarchical way. Take the following array, that might be returned from, e.g., the `find()` function of a model:

```php
--8<--
helpers/array_helper/006.php
--8<--
```

Now sort this array by two keys. Note that the method supports the dot-notation to access values in deeper array levels, but does not support wildcards:

```php
--8<--
helpers/array_helper/007.php
--8<--
```

The `$players` array is now sorted by the 'order' value in each players' 'team' subarray. If this value is equal for several players, these players will be ordered by their 'position'. The resulting array is:

```php
--8<--
helpers/array_helper/008.php
--8<--
```

In the same way, the method can also handle an array of objects. In the example above it is further possible that each 'player' is represented by an array, while the 'teams' are objects. The method will detect the type of elements in each nesting level and handle it accordingly.

#### array_flatten_with_dots(iterable $array\[, string $id = '']): array

- **Parameters**  

- **$array** `iterable` The multidimensional array to flatten

- **$id** `string` Optional ID to prepend to the outer keys. Used internally for flattening keys.

- **Return type**: `array`

- **Returns**: The flattened array

This function flattens a multidimensional array to a single key-value array by using dots as separators for the keys.

```php
--8<--
helpers/array_helper/009.php
--8<--
```

On inspection, `$flattened` is equal to:

```php
--8<--
helpers/array_helper/010.php
--8<--
```

Users may use the `$id` parameter on their own, but are not required to do so. The function uses this parameter internally to track the flattened keys. If users will be supplying an initial `$id`, it will be prepended to all keys.

```php
--8<--
helpers/array_helper/011.php
--8<--
```

#### array_group_by(array $array, array $indexes\[, bool $includeEmpty = false]): array

- **Parameters**  

- **$array** `array` Data rows (most likely from query results)

- **$indexes** `array` Indexes to group values. Follows dot syntax

- **$includeEmpty** `bool` If true, `null` and `''` values are not filtered out

- **Return type**: `array`

- **Returns**: An array grouped by indexes values

This function allows you to group data rows together by index values. The depth of returned array equals the number of indexes passed as parameter.

The example shows some data (i.e. loaded from an API) with nested arrays.

```php
--8<--
helpers/array_helper/012.php
--8<--
```

We want to group them first by "gender", then by "hr.department" (max depth = 2). First the result when excluding empty values:

```php
--8<--
helpers/array_helper/013.php
--8<--
```

And here the same code, but this time we want to include empty values:

```php
--8<--
helpers/array_helper/014.php
--8<--
```
