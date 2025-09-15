# Database Utility Class

The Database Utility Class contains methods that help you manage your database.

- [Initializing the Utility Class](#initializing-the-utility-class)
- [Using the Database Utilities](#using-the-database-utilities)
    - [Retrieve List of Database Names](#retrieve-list-of-database-names)
    - [Determine If a Database Exists](#determine-if-a-database-exists)
    - [Optimize a Table](#optimize-a-table)
    - [Optimize a Database](#optimize-a-database)
    - [Export a Query Result as a CSV File](#export-a-query-result-as-a-csv-file)
    - [Export a Query Result as an XML Document](#export-a-query-result-as-an-xml-document)

## Initializing the Utility Class

Load the Utility Class as follows:

```php
--8<--
database/utilities/002.php:2:
--8<--
```

You can also pass another database group to the DB Utility loader, in case the database you want to manage isn't the default one:

```php
--8<--
database/utilities/003.php:2:
--8<--
```

In the above example, we're passing a database group name as the first parameter.

## Using the Database Utilities

### Retrieve List of Database Names

Returns an array of database names:

```php
--8<--
database/utilities/004.php:2:
--8<--
```

### Determine If a Database Exists

Sometimes it's helpful to know whether a particular database exists. Returns a boolean `true`/`false`. Usage example:

```php
--8<--
database/utilities/005.php:2:
--8<--
```

!!! note "Note"
    Replace `database_name` with the name of the database you are looking for. This method is case sensitive.

### Optimize a Table

Permits you to optimize a table using the table name specified in the first parameter. Returns `true`/`false` based on success or failure:

```php
--8<--
database/utilities/006.php:2:
--8<--
```

!!! note "Note"
    Not all database platforms support table optimization. It is mostly for use with MySQL.

### Optimize a Database

Permits you to optimize the database your DB class is currently connected to. Returns an array containing the DB status messages or `false` on failure:

```php
--8<--
database/utilities/008.php:2:
--8<--
```

!!! note "Note"
    Not all database platforms support database optimization. It it is mostly for use with MySQL.

### Export a Query Result as a CSV File

Permits you to generate a CSV file from a query result. The first parameter of the method must contain the result object from your query. Example:

```php
--8<--
database/utilities/009.php:2:
--8<--
```

The second, third, and fourth parameters allow you to set the delimiter newline, and enclosure characters respectively. By default commas are used as the delimiter, `"\n"` is used as a new line, and a double-quote is used as the enclosure. Example:

```php
--8<--
database/utilities/010.php:2:
--8<--
```

!!! important "Important"
    This method will NOT write the CSV file for you. It simply creates the CSV layout. If you need to write the file use the `write_file()` helper.

### Export a Query Result as an XML Document

Permits you to generate an XML file from a query result. The first parameter expects a query result object, the second may contain an optional array of config parameters. Example:

```php
--8<--
database/utilities/001.php
--8<--
```

and it will get the following xml result when the `mytable` has columns `id` and `name`:

    <root>
    <element>
        <id>1</id>
        <name>bar</name>
    </element>
    </root>

!!! important "Important"
    This method will NOT write the XML file for you. It simply creates the XML layout. If you need to write the file use the `write_file()` helper.
