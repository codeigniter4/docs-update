# Generating Query Results

There are several ways to generate query results:

- [Result Arrays](#result-arrays)
    - [getResult()](#getresult)
        - [Getting an Array of stdClass](#getting-an-array-of-stdclass)
        - [Getting an Array of Array](#getting-an-array-of-array)
        - [Getting an Array of Custom Object](#getting-an-array-of-custom-object)
    - [getResultArray()](#getresultarray)
- [Result Rows](#result-rows)
    - [getRow()](#getrow)
    - [getRowArray()](#getrowarray)
    - [getUnbufferedRow()](#getunbufferedrow)
- [Custom Result Objects](#custom-result-objects)
    - [getCustomResultObject()](#getcustomresultobject)
    - [getCustomRowObject()](#getcustomrowobject)
- [Result Helper Methods](#result-helper-methods)
    - [getFieldCount()](#getfieldcount)
    - [getFieldNames()](#getfieldnames)
    - [getNumRows()](#getnumrows)
    - [freeResult()](#freeresult)
    - [dataSeek()](#dataseek)
- [Class Reference](#class-reference)
- [CodeIgniter\Database](#codeigniterdatabase)
    - [BaseResult](#baseresult)
        - [getResult(\[$type = 'object'])](#getresulttype-object)
        - [getResultArray()](#getresultarray)
        - [getResultObject()](#getresultobject)
        - [getCustomResultObject($class_name)](#getcustomresultobjectclassname)
        - [getRow(\[$n = 0\[, $type = 'object']])](#getrown-0-type-object)
        - [getUnbufferedRow(\[$type = 'object'])](#getunbufferedrowtype-object)
        - [getRowArray(\[$n = 0])](#getrowarrayn-0)
        - [getRowObject(\[$n = 0])](#getrowobjectn-0)
        - [getCustomRowObject($n, $type)](#getcustomrowobjectn-type)
        - [dataSeek(\[$n = 0])](#dataseekn-0)
        - [setRow($key\[, $value = null])](#setrowkey-value-null)
        - [getNextRow(\[$type = 'object'])](#getnextrowtype-object)
        - [getPreviousRow(\[$type = 'object'])](#getpreviousrowtype-object)
        - [getFirstRow(\[$type = 'object'])](#getfirstrowtype-object)
        - [getLastRow(\[$type = 'object'])](#getlastrowtype-object)
        - [getFieldCount()](#getfieldcount)
        - [getFieldNames()](#getfieldnames)
        - [getFieldData()](#getfielddata)
        - [getNumRows()](#getnumrows)
        - [freeResult()](#freeresult)

## Result Arrays

### getResult()

This method returns the query result as an array of **objects**, or **an empty array** on failure.

#### Getting an Array of stdClass

Typically you'll use this in a foreach loop, like this:

```php
--8<--
database/results/001.php
--8<--
```

The above method is an alias of `CodeIgniter\\Database\\BaseResult::getResultObject()`.

#### Getting an Array of Array

You can pass in the string 'array' if you wish to get your results as an array of arrays:

```php
--8<--
database/results/002.php
--8<--
```

The above usage is an alias of [getResultArray()](#getresultarray).

#### Getting an Array of Custom Object

You can also pass a string to `getResult()` which represents a class to instantiate for each result object

```php
--8<--
database/results/003.php
--8<--
```

The above method is an alias of [getCustomResultObject()](#getcustomresultobject).

### getResultArray()

This method returns the query result as a pure array, or an empty array when no result is produced. Typically you'll use this in a foreach loop, like this:

```php
--8<--
database/results/004.php
--8<--
```

## Result Rows

### getRow()

This method returns a single result row. If your query has more than one row, it returns only the first row. The result is returned as an **object**. Here's a usage example:

```php
--8<--
database/results/005.php
--8<--
```

If you want a specific row returned you can submit the row number as a digit in the first parameter:

```php
--8<--
database/results/006.php
--8<--
```

You can also add a second String parameter, which is the name of a class to instantiate the row with:

```php
--8<--
database/results/007.php
--8<--
```

### getRowArray()

Identical to the above `row()` method, except it returns an array. Example:

```php
--8<--
database/results/008.php
--8<--
```

If you want a specific row returned you can submit the row number as a digit in the first parameter:

```php
--8<--
database/results/009.php
--8<--
```

In addition, you can walk forward/backwards/first/last through your results using these variations:

> `$row = $query->getFirstRow()`  
> `$row = $query->getLastRow()`  
> `$row = $query->getNextRow()`  
> `$row = $query->getPreviousRow()`

By default they return an object unless you put the word "array" in the parameter:

> `$row = $query->getFirstRow('array')`  
> `$row = $query->getLastRow('array')`  
> `$row = $query->getNextRow('array')`  
> `$row = $query->getPreviousRow('array')`

!!! note "Note"
    All the methods above will load the whole result into memory (prefetching). Use `getUnbufferedRow()` for processing large result sets.

### getUnbufferedRow()

This method returns a single result row without prefetching the whole result in memory as `row()` does. If your query has more than one row, it returns the current row and moves the internal data pointer ahead.

```php
--8<--
database/results/010.php
--8<--
```

For use with MySQLi you may set MySQLi's result mode to `MYSQLI_USE_RESULT` for maximum memory savings. Use of this is not generally recommended but it can be beneficial in some circumstances such as writing large queries to csv. If you change the result mode be aware of the tradeoffs associated with it.

```php
--8<--
database/results/011.php
--8<--
```

!!! note "Note"
    When using `MYSQLI_USE_RESULT` all subsequent calls on the same connection will result in error until all records have been fetched or a `freeResult()` call has been made. The `getNumRows()` method will only return the number of rows based on the current position of the data pointer. MyISAM tables will remain locked until all the records have been fetched or a `freeResult()` call has been made.

You can optionally pass 'object' (default) or 'array' in order to specify the returned value's type:

```php
--8<--
database/results/012.php
--8<--
```

## Custom Result Objects

You can have the results returned as an instance of a custom class instead of a `stdClass` or array, as the `getResult()` and `getResultArray()[ methods allow. If the class is not already loaded into memory, the Autoloader will attempt to load it. The object will have all values returned from the database set as properties. If these have been declared and are non-public then you should provide a ]_set()` method to allow them to be set.

Example:

```php
--8<--
database/results/013.php
--8<--
```

In addition to the two methods listed below, the following methods also can take a class name to return the results as: `getFirstRow()`, `getLastRow()`, `getNextRow()`, and `getPreviousRow()`.

### getCustomResultObject()

Returns the entire result set as an array of instances of the class requested. The only parameter is the name of the class to instantiate.

Example:

```php
--8<--
database/results/014.php
--8<--
```

### getCustomRowObject()

Returns a single row from your query results. The first parameter is the row number of the results. The second parameter is the class name to instantiate.

Example:

```php
--8<--
database/results/015.php
--8<--
```

You can also use the `getRow()` method in exactly the same way.

Example:

```php
--8<--
database/results/016.php
--8<--
```

## Result Helper Methods

### getFieldCount()

The number of FIELDS (columns) returned by the query. Make sure to call the method using your query result object:

```php
--8<--
database/results/017.php
--8<--
```

### getFieldNames()

Returns an array with the names of the FIELDS (columns) returned by the query. Make sure to call the method using your query result object:

```php
--8<--
database/results/018.php
--8<--
```

### getNumRows()

The number of records returned by the query. Make sure to call the method using your query result object:

```php
--8<--
database/results/019.php
--8<--
```

!!! note "Note"
    Because SQLite3 lacks an efficient method returning a record count, CodeIgniter will fetch and buffer the query result records internally and return a count of the resulting record array, which can be inefficient.

### freeResult()

It frees the memory associated with the result and deletes the result resource ID. Normally PHP frees its memory automatically at the end of script execution. However, if you are running a lot of queries in a particular script you might want to free the result after each query result has been generated in order to cut down on memory consumption.

Example:

```php
--8<--
database/results/020.php
--8<--
```

### dataSeek()

This method sets the internal pointer for the next result row to be fetched. It is only useful in combination with `getUnbufferedRow()`.

It accepts a positive integer value, which defaults to 0 and returns true on success or false on failure.

```php
--8<--
database/results/021.php
--8<--
```

!!! note "Note"
    Not all database drivers support this feature and will return false. Most notably - you won't be able to use it with PDO.

## Class Reference

## CodeIgniter\Database

### BaseResult

#### getResult(\[$type = 'object'])

- **Parameters**  

- **$type** `string` Type of requested results - array, object, or class name

- **Returns**: Array containing the fetched rows

- **Return type**: `array`

A wrapper for the `getResultArray()`, `getResultObject()` and `getCustomResultObject()` methods.

Usage: see [Result Arrays](#result-arrays).

#### getResultArray()

- **Returns**: Array containing the fetched rows

- **Return type**: `array`

Returns the query results as an array of rows, where each row is itself an associative array.

Usage: see [Result Arrays](#result-arrays).

#### getResultObject()

- **Returns**: Array containing the fetched rows

- **Return type**: `array`

Returns the query results as an array of rows, where each row is an object of type `stdClass`.

Usage: see [Getting an Array of stdClass](#getting-an-array-of-stdclass).

#### getCustomResultObject($class_name)

- **Parameters**  

- **$class_name** `string` Class name for the resulting rows

- **Returns**: Array containing the fetched rows

- **Return type**: `array`

Returns the query results as an array of rows, where each row is an instance of the specified class.

#### getRow(\[$n = 0\[, $type = 'object']])

- **Parameters**  

- **$n** `int` Index of the query results row to be returned

- **$type** `string` Type of the requested result - array, object, or class name

- **Returns**: The requested row or null if it doesn't exist

- **Return type**: `mixed`

A wrapper for the `getRowArray()`, `getRowObject()` and `getCustomRowObject()` methods.

Usage: see [Result Rows](#result-rows).

#### getUnbufferedRow(\[$type = 'object'])

- **Parameters**  

- **$type** `string` Type of the requested result - array, object, or class name

- **Returns**: Next row from the result set or null if it doesn't exist

- **Return type**: `mixed`

Fetches the next result row and returns it in the requested form.

Usage: see [Result Rows](#result-rows).

#### getRowArray(\[$n = 0])

- **Parameters**  

- **$n** `int` Index of the query results row to be returned

- **Returns**: The requested row or null if it doesn't exist

- **Return type**: `array`

Returns the requested result row as an associative array.

Usage: see [Result Rows](#result-rows).

#### getRowObject(\[$n = 0])

- **Parameters**  

- **$n** `int` Index of the query results row to be returned

- **Returns**: The requested row or null if it doesn't exist

- **Return type**: `stdClass`

Returns the requested result row as an object of type `stdClass`.

Usage: see [Result Rows](#result-rows).

#### getCustomRowObject($n, $type)

- **Parameters**  

- **$n** `int` Index of the results row to return

- **$class_name** `string` Class name for the resulting row

- **Returns**: The requested row or null if it doesn't exist

- **Return type**: `$type`

Returns the requested result row as an instance of the requested class.

#### dataSeek(\[$n = 0])

- **Parameters**  

- **$n** `int` Index of the results row to be returned next

- **Returns**: true on success, false on failure

- **Return type**: `bool`

Moves the internal results row pointer to the desired offset.

Usage: see [Result Helper Methods](#result-helper-methods).

#### setRow($key\[, $value = null])

- **Parameters**  

- **$key** `mixed` Column name or array of key/value pairs

- **$value** `mixed` Value to assign to the column, $key is a single field name

- **Return type**: `void`

Assigns a value to a particular column.

#### getNextRow(\[$type = 'object'])

- **Parameters**  

- **$type** `string` Type of the requested result - array, object, or class name

- **Returns**: Next row of result set, or null if it doesn't exist

- **Return type**: `mixed`

Returns the next row from the result set.

#### getPreviousRow(\[$type = 'object'])

- **Parameters**  

- **$type** `string` Type of the requested result - array, object, or class name

- **Returns**: Previous row of result set, or null if it doesn't exist

- **Return type**: `mixed`

Returns the previous row from the result set.

#### getFirstRow(\[$type = 'object'])

- **Parameters**  

- **$type** `string` Type of the requested result - array, object, or class name

- **Returns**: First row of result set, or null if it doesn't exist

- **Return type**: `mixed`

Returns the first row from the result set.

#### getLastRow(\[$type = 'object'])

- **Parameters**  

- **$type** `string` Type of the requested result - array, object, or class name

- **Returns**: Last row of result set, or null if it doesn't exist

- **Return type**: `mixed`

Returns the last row from the result set.

#### getFieldCount()

- **Returns**: Number of fields in the result set

- **Return type**: `int`

Returns the number of fields in the result set.

Usage: see [Result Helper Methods](#result-helper-methods).

#### getFieldNames()

- **Returns**: Array of column names

- **Return type**: `array`

Returns an array containing the field names in the result set.

#### getFieldData()

- **Returns**: Array containing field meta-data

- **Return type**: `array`

Generates an array of `stdClass` objects containing field meta-data.

#### getNumRows()

- **Returns**: Number of rows in result set

- **Return type**: `int`

Returns number of rows returned by the query

#### freeResult()

- **Return type**: `void`

Frees a result set.

Usage: see [Result Helper Methods](#result-helper-methods).
