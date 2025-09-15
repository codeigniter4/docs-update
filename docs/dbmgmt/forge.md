# Database Forge Class

The Database Forge Class contains methods that help you manage your database.

- [Initializing the Forge Class](#initializing-the-forge-class)
- [Creating and Dropping Databases](#creating-and-dropping-databases)
    - [$forge-\>createDatabase('db_name')](#forge-createdatabasedbname)
    - [$forge-\>dropDatabase('db_name')](#forge-dropdatabasedbname)
    - [Creating Databases in the Command Line](#creating-databases-in-the-command-line)
- [Creating Tables](#creating-tables)
    - [Adding Fields](#adding-fields)
    - [Adding Keys](#adding-keys)
    - [Adding Foreign Keys](#adding-foreign-keys)
    - [Creating a Table](#creating-a-table)
- [Dropping Tables](#dropping-tables)
    - [Dropping a Table](#dropping-a-table)
- [Modifying Tables](#modifying-tables)
    - [Adding a Field to a Table](#adding-a-field-to-a-table)
    - [Dropping Fields From a Table](#dropping-fields-from-a-table)
    - [Modifying a Field in a Table](#modifying-a-field-in-a-table)
    - [Adding Keys to a Table](#adding-keys-to-a-table)
    - [Dropping a Primary Key](#dropping-a-primary-key)
    - [Dropping a Key](#dropping-a-key)
    - [Dropping a Foreign Key](#dropping-a-foreign-key)
    - [Renaming a Table](#renaming-a-table)
- [Class Reference](#class-reference)
- [CodeIgniter\Database](#codeigniterdatabase)
    - [Forge](#forge)

## Initializing the Forge Class

!!! important "Important"
    In order to initialize the Forge class, your database driver must already be running, since the Forge class relies on it.

Load the Forge Class as follows:

```php
--8<--
dbmgmt/forge/001.php
--8<--
```

You can also pass another database group name to the DB Forge loader, in case the database you want to manage isn't the default one:

```php
--8<--
dbmgmt/forge/002.php
--8<--
```

In the above example, we're passing the name of a different database group to connect to as the first parameter.

## Creating and Dropping Databases

### $forge-\>createDatabase('db_name')

Permits you to create the database specified in the first parameter. Returns true/false based on success or failure:

```php
--8<--
dbmgmt/forge/003.php
--8<--
```

An optional second parameter set to true will add `IF EXISTS` statement or will check if a database exists before create it (depending on DBMS).

```php
--8<--
dbmgmt/forge/004.php
--8<--
```

### $forge-\>dropDatabase('db_name')

Permits you to drop the database specified in the first parameter. Returns true/false based on success or failure:

```php
--8<--
dbmgmt/forge/005.php
--8<--
```

### Creating Databases in the Command Line

CodeIgniter supports creating databases straight from your favorite terminal using the dedicated `db:create` command. By using this command it is assumed that the database is not yet existing. Otherwise, CodeIgniter will complain that the database creation has failed.

To start, just type the command and the name of the database (e.g., `foo`):

``` console
php spark db:create foo
```

If everything went fine, you should expect the `Database "foo" successfully created.` message displayed.

If you are on a testing environment or you are using the SQLite3 driver, you may pass in the file extension for the file where the database will be created using the `--ext` option. Valid values are `db` and `sqlite` and defaults to `db`. Remember that these should not be preceded by a period. :

``` console
php spark db:create foo --ext sqlite
```

The above command will create the db file in **WRITEPATH/foo.sqlite**.

!!! note "Note"
    When using the special SQLite3 database name `:memory:`, expect that the command will still produce a success message but no database file is created. This is because SQLite3 will just use an in-memory database.

## Creating Tables

There are several things you may wish to do when creating tables. Add fields, add keys to the table, alter columns. CodeIgniter provides a mechanism for this.

### Adding Fields

Fields are normally created via an associative array. Within the array, you must include a `type` key that relates to the datatype of the field. For example, INT, VARCHAR, TEXT, etc. Many datatypes (for example VARCHAR) also require a `constraint` key.

```php
--8<--
dbmgmt/forge/006.php
--8<--
```

Additionally, the following key/values can be used:

- `unsigned`/true : to generate "UNSIGNED" in the field definition.

- `default`/value : to generate a default value in the field definition.

- `null`/true : to generate "null" in the field definition. Without this, the field will default to "NOT null".

- `auto_increment`/true : generates an auto_increment flag on the field. Note that the field type must be a type that supports this, such as integer.

- `unique`/true : to generate a unique key for the field definition.

```php
--8<--
dbmgmt/forge/007.php
--8<--
```

After the fields have been defined, they can be added using `$forge->addField($fields)` followed by a call to the `createTable()` method.

#### $forge-\>addField()

The `addField()` method will accept the above array.

#### Raw Sql Strings as Default Values

!!! success "Available from version 4.2.0"

Since v4.2.0, `$forge->addField()` accepts a `CodeIgniter\Database\RawSql` instance, which expresses raw SQL strings.

```php
--8<--
dbmgmt/forge/027.php
--8<--
```

!!! warning "Warning"
    When you use `RawSql`, you MUST escape the data manually. Failure to do so could result in SQL injections.

#### Passing Strings as Fields

If you know exactly how you want a field to be created, you can pass the string into the field definitions with `addField()`:

```php
--8<--
dbmgmt/forge/008.php
--8<--
```

!!! note "Note"
    Passing raw strings as fields cannot be followed by `addKey()` calls on those fields.

!!! note "Note"
    Multiple calls to `addField()` are cumulative.

#### Creating an id Field

There is a special exception for creating id fields. A field with type id will automatically be assigned as an INT(9) auto_incrementing Primary Key.

```php
--8<--
dbmgmt/forge/009.php
--8<--
```

### Adding Keys

#### $forge-\>addKey()

Generally speaking, you'll want your table to have Keys. This is accomplished with `$forge->addKey('field')`. The optional second parameter set to true will make it a primary key and the third parameter set to true will make it a unique key. You may specify a name with the fourth parameter. Note that `addKey()` must be followed by a call to `createTable()` or `processIndexes()` when the table already exists.

Multiple column non-primary keys must be sent as an array. Sample output below is for MySQL.

```php
--8<--
dbmgmt/forge/010.php
--8<--
```

#### $forge-\>addPrimaryKey()

#### $forge-\>addUniqueKey()

To make code reading more objective it is also possible to add primary and unique keys with specific methods:

```php
--8<--
dbmgmt/forge/011.php
--8<--
```

!!! note "Note"
    When you add a primary key, MySQL and SQLite will assume the name `PRIMARY` even if a name is provided.

### Adding Foreign Keys

Foreign Keys help to enforce relationships and actions across your tables. For tables that support Foreign Keys, you may add them directly in forge:

```php
--8<--
dbmgmt/forge/012.php
--8<--
```

You can specify the desired action for the "on update" and "on delete" properties of the constraint as well as the name:

```php
--8<--
dbmgmt/forge/013.php
--8<--
```

!!! note "Note"
    SQLite3 does not support the naming of foreign keys. CodeIgniter will refer to them by `prefix_table_column_foreign`.

### Creating a Table

After fields and keys have been declared, you can create a new table with

```php
--8<--
dbmgmt/forge/014.php
--8<--
```

An optional second parameter set to true will create the table only if it doesn't already exist.

```php
--8<--
dbmgmt/forge/015.php
--8<--
```

You could also pass optional table attributes, such as MySQL's `ENGINE`:

```php
--8<--
dbmgmt/forge/016.php
--8<--
```

!!! note "Note"
    Unless you specify the `CHARACTER SET` and/or `COLLATE` attributes, `createTable()` will always add them with your configured *charset* and *DBCollat* values, as long as they are not empty (MySQL only).

## Dropping Tables

### Dropping a Table

Execute a `DROP TABLE` statement and optionally add an `IF EXISTS` clause.

```php
--8<--
dbmgmt/forge/017.php
--8<--
```

A third parameter can be passed to add a `CASCADE` option, which might be required for some drivers to handle removal of tables with foreign keys.

```php
--8<--
dbmgmt/forge/018.php
--8<--
```

## Modifying Tables

### Adding a Field to a Table

#### $forge-\>addColumn()

The `addColumn()` method is used to modify an existing table. It accepts the same field array as [Creating Tables](#adding-fields), and can be used to add additional fields.

!!! note "Note"
    Unlike when creating a table, if `null` is not specified, the column will be `NULL`, not `NOT NULL`.

```php
--8<--
dbmgmt/forge/022.php
--8<--
```

If you are using MySQL or CUBIRD, then you can take advantage of their `AFTER` and `FIRST` clauses to position the new column.

Examples:

```php
--8<--
dbmgmt/forge/023.php
--8<--
```

### Dropping Fields From a Table

#### $forge-\>dropColumn()

Used to remove a column from a table.

```php
--8<--
dbmgmt/forge/024.php
--8<--
```

Used to remove multiple columns from a table.

```php
--8<--
dbmgmt/forge/025.php
--8<--
```

### Modifying a Field in a Table

#### $forge-\>modifyColumn()

The usage of this method is identical to `addColumn()`, except it alters an existing column rather than adding a new one. In order to change the name, you can add a "name" key into the field defining array.

```php
--8<--
dbmgmt/forge/026.php
--8<--
```

!!! note "Note"
    The `modifyColumn()` may unexpectedly change `NULL`/`NOT NULL`. So it is recommended to always specify the value for `null` key. Unlike when creating a table, if `null` is not specified, the column will be `NULL`, not `NOT NULL`.

!!! note "Note"
    Due to a bug, prior v4.3.3, SQLite3 may not set `NOT NULL` even if you specify `'null' => false`.

!!! note "Note"
    Due to a bug, prior v4.3.3, Postgres and SQLSRV set `NOT NULL` even if you specify `'null' => false`.

### Adding Keys to a Table

!!! success "Available from version 4.3.0"

You may add keys to an existing table by using `addKey()`, `addPrimaryKey()`, `addUniqueKey()` or `addForeignKey()` and `processIndexes()`:

```php
--8<--
dbmgmt/forge/029.php
--8<--
```

### Dropping a Primary Key

!!! success "Available from version 4.3.0"

Execute a DROP PRIMARY KEY.

```php
--8<--
dbmgmt/forge/028.php
--8<--
```

### Dropping a Key

Execute a DROP KEY.

```php
--8<--
dbmgmt/forge/020.php
--8<--
```

### Dropping a Foreign Key

Execute a DROP FOREIGN KEY.

```php
--8<--
dbmgmt/forge/019.php
--8<--
```

### Renaming a Table

Executes a TABLE rename

```php
--8<--
dbmgmt/forge/021.php
--8<--
```

## Class Reference

## CodeIgniter\Database

### Forge

#### addColumn($table\[, $field = \[]])

- **Parameters**  

- **$table** `string` Table name to add the column to

- **$field** `array` Column definition(s)

- **Returns**: true on success, false on failure

- **Return type**: `bool`

Adds a column to an existing table. Usage: See [Adding a Field to a Table](#adding-a-field-to-a-table).

#### addField($field)

- **Parameters**  

- **$field** `array` Field definition to add

- **Returns**: `\CodeIgniter\Database\Forge` instance (method chaining)

- **Return type**: ``CodeIgniterDatabaseForge``

Adds a field to the set that will be used to create a table. Usage: See [Adding Fields](#adding-fields).

#### addForeignKey($fieldName, $tableName, $tableField\[, $onUpdate = '', $onDelete = '', $fkName = ''])

- **Parameters**  

- string|string\[] `$fieldName` Name of a key field or an array of fields

- **$tableName** `string` Name of a parent table

- string|string\[] `$tableField` Name of a parent table field or an array of fields

- **$onUpdate** `string` Desired action for the "on update"

- **$onDelete** `string` Desired action for the "on delete"

- **$fkName** `string` Name of foreign key. This does not work with SQLite3

- **Returns**: `\CodeIgniter\Database\Forge` instance (method chaining)

- **Return type**: ``CodeIgniterDatabaseForge``

Adds a foreign key to the set that will be used to create a table. Usage: See [Adding Foreign Keys](#adding-foreign-keys).

!!! note "Note"
    `$fkName` can be used since v4.3.0.

#### addKey($key\[, $primary = false\[, $unique = false\[, $keyName = '']]])

- **Parameters**  

- **$key** `mixed` Name of a key field or an array of fields

- **$primary** `bool` Set to true if it should be a primary key or a regular one

- **$unique** `bool` Set to true if it should be a unique key or a regular one

- **$keyName** `string` Name of key to be added

- **Returns**: `\CodeIgniter\Database\Forge` instance (method chaining)

- **Return type**: ``CodeIgniterDatabaseForge``

Adds a key to the set that will be used to create a table. Usage: See [Adding Keys](#adding-keys).

!!! note "Note"
    `$keyName` can be used since v4.3.0.

#### addPrimaryKey($key\[, $keyName = ''])

- **Parameters**  

- **$key** `mixed` Name of a key field or an array of fields

- **$keyName** `string` Name of key to be added

- **Returns**: `\CodeIgniter\Database\Forge` instance (method chaining)

- **Return type**: ``CodeIgniterDatabaseForge``

Adds a primary key to the set that will be used to create a table. Usage: See [Adding Keys](#adding-keys).

!!! note "Note"
    `$keyName` can be used since v4.3.0.

#### addUniqueKey($key\[, $keyName = ''])

- **Parameters**  

- **$key** `mixed` Name of a key field or an array of fields

- **$keyName** `string` Name of key to be added

- **Returns**: `\CodeIgniter\Database\Forge` instance (method chaining)

- **Return type**: ``CodeIgniterDatabaseForge``

Adds a unique key to the set that will be used to create a table. Usage: See [Adding Keys](#adding-keys).

!!! note "Note"
    `$keyName` can be used since v4.3.0.

#### createDatabase($dbName\[, $ifNotExists = false])

- **Parameters**  

- **$db_name** `string` Name of the database to create

- **$ifNotExists** `string` Set to true to add an `IF NOT EXISTS` clause or check if database exists

- **Returns**: true on success, false on failure

- **Return type**: `bool`

Creates a new database. Usage: See [Creating and Dropping Databases](#creating-and-dropping-databases).

#### createTable($table\[, $if_not_exists = false\[, array $attributes = \[]]])

- **Parameters**  

- **$table** `string` Name of the table to create

- **$if_not_exists** `string` Set to true to add an `IF NOT EXISTS` clause

- **$attributes** `string` An associative array of table attributes

- **Returns**: Query object on success, false on failure

- **Return type**: `mixed`

Creates a new table. Usage: See [Creating a Table](#creating-a-table).

#### dropColumn($table, $column_name)

- **Parameters**  

- **$table** `string` Table name

- **$column_names** `mixed` Comma-delimited string or an array of column names

- **Returns**: true on success, false on failure

- **Return type**: `bool`

Drops single or multiple columns from a table. Usage: See [Dropping Fields From a Table](#dropping-fields-from-a-table).

#### dropDatabase($dbName)

- **Parameters**  

- **$dbName** `string` Name of the database to drop

- **Returns**: true on success, false on failure

- **Return type**: `bool`

Drops a database. Usage: See [Creating and Dropping Databases](#creating-and-dropping-databases).

#### dropKey($table, $keyName\[, $prefixKeyName = true])

- **Parameters**  

- **$table** `string` Name of table that has key

- **$keyName** `string` Name of key to be dropped

- **$prefixKeyName** `string` If database prefix should be added to `$keyName`

- **Returns**: true on success, false on failure

- **Return type**: `bool`

Drops an index or unique index.

!!! note "Note"
    `$keyName` and `$prefixKeyName` can be used since v4.3.0.

#### dropPrimaryKey($table\[, $keyName = ''])

- **Parameters**  

- **$table** `string` Name of table to drop primary key

- **$keyName** `string` Name of primary key to be dropped

- **Returns**: true on success, false on failure

- **Return type**: `bool`

Drops a primary key from a table.

!!! note "Note"
    `$keyName` can be used since v4.3.0.

#### dropTable($table_name\[, $if_exists = false])

- **Parameters**  

- **$table** `string` Name of the table to drop

- **$if_exists** `string` Set to true to add an `IF EXISTS` clause

- **Returns**: true on success, false on failure

- **Return type**: `bool`

Drops a table. Usage: See [Dropping a Table](#dropping-a-table).

#### processIndexes($table)

!!! success "Available from version 4.3.0"

- **Parameters**  

- **$table** `string` Name of the table to add indexes to

- **Returns**: true on success, false on failure

- **Return type**: `bool`

Used following `addKey()`, `addPrimaryKey()`, `addUniqueKey()`, and `addForeignKey()` to add indexes to an existing table. See [Adding Keys to a Table](#adding-keys-to-a-table).

#### modifyColumn($table, $field)

- **Parameters**  

- **$table** `string` Table name

- **$field** `array` Column definition(s)

- **Returns**: true on success, false on failure

- **Return type**: `bool`

Modifies a table column. Usage: See [Modifying a Field in a Table](#modifying-a-field-in-a-table).

#### renameTable($table_name, $new_table_name)

- **Parameters**  

- **$table** `string` Current of the table

- **$new_table_name** `string` New name of the table

- **Returns**: Query object on success, false on failure

- **Return type**: `mixed`

Renames a table. Usage: See [Renaming a Table](#renaming-a-table).
