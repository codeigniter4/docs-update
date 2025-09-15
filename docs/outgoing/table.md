# HTML Table Class

The Table Class provides methods that enable you to auto-generate HTML tables from arrays or database result sets.

- [Using the Table Class](#using-the-table-class)
    - [Initializing the Class](#initializing-the-class)
    - [Examples](#examples)
    - [Changing the Look of Your Table](#changing-the-look-of-your-table)
    - [Synchronizing Rows with Headings](#synchronizing-rows-with-headings)
- [Class Reference](#class-reference)
- [CodeIgniter\View](#codeigniterview)
    - [Table](#table)

## Using the Table Class

### Initializing the Class

The Table class is not provided as a service, and should be instantiated "normally", for instance:

```php
--8<--
outgoing/table/001.php
--8<--
```

### Examples

Here is an example showing how you can create a table from a multi-dimensional array. Note that the first array index will become the table heading (or you can set your own headings using the `setHeading()` method described in the function reference below).

```php
--8<--
outgoing/table/002.php
--8<--
```

Here is an example of a table created from a database query result. The table class will automatically generate the headings based on the table names (or you can set your own headings using the `setHeading()` method described in the class reference below).

```php
--8<--
outgoing/table/003.php
--8<--
```

Here is an example showing how you might create a table using discrete parameters:

```php
--8<--
outgoing/table/004.php
--8<--
```

Here is the same example, except instead of individual parameters, arrays are used:

```php
--8<--
outgoing/table/005.php
--8<--
```

### Changing the Look of Your Table

The Table Class permits you to set a table template with which you can specify the design of your layout. Here is the template prototype:

```php
--8<--
outgoing/table/006.php
--8<--
```

!!! note "Note"
    You'll notice there are two sets of "row" blocks in the template. These permit you to create alternating row colors or design elements that alternate with each iteration of the row data.

You are NOT required to submit a complete template. If you only need to change parts of the layout you can simply submit those elements. In this example, only the table opening tag is being changed:

```php
--8<--
outgoing/table/007.php
--8<--
```

You can also set defaults for these by passing an array of template settings to the Table constructor:

```php
--8<--
outgoing/table/008.php
--8<--
```

### Synchronizing Rows with Headings

!!! success "Available from version 4.4.0"

The `setSyncRowsWithHeading(true)` method enables that each data value is placed in the same column as defined in `setHeading()` if an associative array was used as parameter. This is especially useful when dealing with data loaded via REST API where the order is not to your liking, or if the API returned too much data.

If a data row contains a key that is not present in the heading, its value is filtered. Conversely, if a data row does not have a key listed in the heading, an empty cell will be placed in its place.

```php
--8<--
outgoing/table/019.php
--8<--
```

!!! important "Important"
    You must call `setSyncRowsWithHeading(true)` and `setHeading([...])` before adding any rows via `addRow([...])` where the rearrangement of columns takes place.

Using an array as input to `generate()` produces the same result:

```php
--8<--
outgoing/table/020.php
--8<--
```

## Class Reference

## CodeIgniter\View

### Table

> <div class="attribute">
>
> $function = null
>
> Allows you to specify a native PHP function or a valid function array object to be applied to all cell data.
>
> ```php
--8<--
outgoing/>
> table/009.php
>
>
--8<--
```
>
> In the above example, all cell data would be run through PHP's `htmlspecialchars()` function, resulting in:
>
>     <td>Fred</td><td>&lt;strong&gt;Blue&lt;/strong&gt;</td><td>Small</td>
>
> </div>

#### generate(\[$tableData = null])

- **Parameters**  

- **$tableData** `mixed` Data to populate the table rows with

- **Returns**: HTML table

- **Return type**: `string`

Returns a string containing the generated table. Accepts an optional parameter which can be an array or a database result object.

#### setCaption($caption)

- **Parameters**  

- **$caption** `string` Table caption

- **Returns**: Table instance (method chaining)

- **Return type**: `Table`

Permits you to add a caption to the table.

```php
--8<--
outgoing/table/010.php
--8<--
```

#### setHeading(\[$args = \[] \[, ...]])

- **Parameters**  

- **$args** `mixed` An array or multiple strings containing the table column titles

- **Returns**: Table instance (method chaining)

- **Return type**: `Table`

Permits you to set the table heading. You can submit an array or discrete params:

```php
--8<--
outgoing/table/011.php
--8<--
```

#### setFooting(\[$args = \[] \[, ...]])

- **Parameters**  

- **$args** `mixed` An array or multiple strings containing the table footing values

- **Returns**: Table instance (method chaining)

- **Return type**: `Table`

Permits you to set the table footing. You can submit an array or discrete params:

```php
--8<--
outgoing/table/012.php
--8<--
```

#### addRow(\[$args = \[] \[, ...]])

- **Parameters**  

- **$args** `mixed` An array or multiple strings containing the row values

- **Returns**: Table instance (method chaining)

- **Return type**: `Table`

Permits you to add a row to your table. You can submit an array or discrete params:

```php
--8<--
outgoing/table/013.php
--8<--
```

If you would like to set an individual cell's tag attributes, you can use an associative array for that cell. The associative key **data** defines the cell's data. Any other key =\> val pairs are added as key='val' attributes to the tag:

```php
--8<--
outgoing/table/014.php
--8<--
```

#### makeColumns(\[$array = \[] \[, $columnLimit = 0]])

- **Parameters**  

- **$array** `array` An array containing multiple rows' data

- **$columnLimit** `int` Count of columns in the table

- **Returns**: An array of HTML table columns

- **Return type**: `array`

This method takes a one-dimensional array as input and creates a multi-dimensional array with a depth equal to the number of columns desired. This allows a single array with many elements to be displayed in a table that has a fixed column count. Consider this example:

```php
--8<--
outgoing/table/015.php
--8<--
```

#### setTemplate($template)

- **Parameters**  

- **$template** `array` An associative array containing template values

- **Returns**: true on success, false on failure

- **Return type**: `bool`

Permits you to set your template. You can submit a full or partial template.

```php
--8<--
outgoing/table/016.php
--8<--
```

#### setEmpty($value)

- **Parameters**  

- **$value** `mixed` Value to put in empty cells

- **Returns**: Table instance (method chaining)

- **Return type**: `Table`

Lets you set a default value for use in any table cells that are empty. You might, for example, set a non-breaking space:

```php
--8<--
outgoing/table/017.php
--8<--
```

#### clear()

- **Returns**: Table instance (method chaining)

- **Return type**: `Table`

Lets you clear the table heading, row data and caption. If you need to show multiple tables with different data you should to call this method after each table has been generated to clear the previous table information.

Example

```php
--8<--
outgoing/table/018.php
--8<--
```

#### setSyncRowsWithHeading(bool $orderByKey)

- **Returns**: Table instance (method chaining)

- **Return type**: `Table`

Enables each row data key to be ordered by heading keys. This gives more control of data being displaced in the correct column. Make sure to set this value before calling the first `addRow()` method.
