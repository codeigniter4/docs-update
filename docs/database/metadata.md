# Database Metadata

- [Table MetaData](#table-metadata)
    - [List the Tables in Your Database](#list-the-tables-in-your-database)
    - [Determine If a Table Exists](#determine-if-a-table-exists)
- [Field MetaData](#field-metadata)
    - [List the Fields in a Table](#list-the-fields-in-a-table)
    - [Determine If a Field is Present in a Table](#determine-if-a-field-is-present-in-a-table)
    - [Retrieve Field Metadata](#retrieve-field-metadata)
    - [List the Indexes in a Table](#list-the-indexes-in-a-table)

## Table MetaData

These functions let you fetch table information.

### List the Tables in Your Database

#### $db-\>listTables()

Returns an array containing the names of all the tables in the database you are currently connected to. Example:

```php
--8<--
database/metadata/001.php
--8<--
```

!!! note "Note"
    Some drivers have additional system tables that are excluded from this return.

### Determine If a Table Exists

#### $db-\>tableExists()

Sometimes it's helpful to know whether a particular table exists before running an operation on it. Returns a boolean true/false. Usage example:

```php
--8<--
database/metadata/002.php
--8<--
```

!!! note "Note"
    Replace *table_name* with the name of the table you are looking for.

## Field MetaData

### List the Fields in a Table

#### $db-\>getFieldNames()

Returns an array containing the field names. This query can be called two ways:

1.  You can supply the table name and call it from the `$db->object`:

> ```php
--8<--
database/>
> metadata/003.php
>
>
--8<--
```

2\. You can gather the field names associated with any query you run by calling the function from your query result object:

```php
--8<--
database/metadata/004.php
--8<--
```

### Determine If a Field is Present in a Table

#### $db-\>fieldExists()

Sometimes it's helpful to know whether a particular field exists before performing an action. Returns a boolean true/false. Usage example:

```php
--8<--
database/metadata/005.php
--8<--
```

!!! note "Note"
    Replace *field_name* with the name of the column you are looking for, and replace *table_name* with the name of the table you are looking for.

### Retrieve Field Metadata

#### $db-\>getFieldData()

Returns an array of objects containing field information.

Sometimes it's helpful to gather the field names or other metadata, like the column type, max length, etc.

!!! note "Note"
    Not all databases provide meta-data.

Usage example:

```php
--8<--
database/metadata/006.php
--8<--
```

If you have run a query already you can use the result object instead of supplying the table name:

```php
--8<--
database/metadata/007.php
--8<--
```

The following data is available from this function if supported by your database:

- name - column name

- type - the type of the column

- max_length - maximum length of the column

- primary_key - integer `1` if the column is a primary key (all integer `1`, even if there are multiple primary keys), otherwise integer `0` (This field is currently only available for MySQL and SQLite3)

- nullable - boolean `true` if the column is nullable, otherwise boolean `false`

- default - the default value

!!! note "Note"
    Since v4.4.0, SQLSRV supported `nullable`.

### List the Indexes in a Table

#### $db-\>getIndexData()

Returns an array of objects containing index information.

Usage example:

```php
--8<--
database/metadata/008.php
--8<--
```

The key types may be unique to the database you are using. For instance, MySQL will return one of primary, fulltext, spatial, index or unique for each key associated with a table.

SQLite3 returns a pseudo index named `PRIMARY`. But it is a special index, and you can't use it in your SQL commands.

#### $db-\>getForeignKeyData()

Returns an array of objects containing foreign key information.

Usage example:

```php
--8<--
database/metadata/009.php
--8<--
```

Foreign keys use the naming convention `tableprefix_table_column1_column2_foreign`. Oracle uses a slightly different suffix of `_fk`.
