# Database Quick Start: Example Code

The following page contains example code showing how the database class is used. For complete details please read the individual pages describing each function.

!!! note "Note"
    CodeIgniter doesn't support dots (`.`) in the database, table, and column names.

- [Initializing the Database Class](#initializing-the-database-class)
- [Standard Query With Multiple Results (Object Version)](#standard-query-with-multiple-results-object-version)
- [Standard Query With Multiple Results (Array Version)](#standard-query-with-multiple-results-array-version)
- [Standard Query With Single Result](#standard-query-with-single-result)
- [Standard Query With Single Result (Array version)](#standard-query-with-single-result-array-version)
- [Standard Insert](#standard-insert)
- [Query Builder Query](#query-builder-query)
- [Query Builder Insert](#query-builder-insert)

## Initializing the Database Class

The following code loads and initializes the database class based on your [configuration](#configuration) settings:

```php
--8<--
database/examples/001.php
--8<--
```

Once loaded the class is ready to be used as described below.

!!! note "Note"
    If all your pages require database access you can connect automatically. See the [connecting](#connecting) page for details.

## Standard Query With Multiple Results (Object Version)

```php
--8<--
database/examples/002.php
--8<--
```

The above `getResult()` function returns an array of **objects**.  
Example: `$row->title`

## Standard Query With Multiple Results (Array Version)

```php
--8<--
database/examples/003.php
--8<--
```

The above `getResultArray()` function returns an array of standard array indexes.  
Example: `$row['title']`

## Standard Query With Single Result

```php
--8<--
database/examples/004.php
--8<--
```

The above `getRow()` function returns an **object**. Example: `$row->name`

## Standard Query With Single Result (Array version)

```php
--8<--
database/examples/005.php
--8<--
```

The above `getRowArray()` function returns an **array**. Example: `$row['name']`.

## Standard Insert

```php
--8<--
database/examples/006.php
--8<--
```

## Query Builder Query

The [Query Builder Pattern](#query-builder) gives you a simplified means of retrieving data:

```php
--8<--
database/examples/007.php
--8<--
```

The above `get()` function retrieves all the results from the supplied table. The [Query Builder](#query-builder) class contains a full complement of functions for working with data.

## Query Builder Insert

```php
--8<--
database/examples/008.php
--8<--
```
