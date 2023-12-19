# Database Metadata

<div class="contents" local="" depth="2">

</div>

## Table MetaData

These functions let you fetch table information.

### List the Tables in Your Database

#### \$db-\>listTables()

Returns an array containing the names of all the tables in the database
you are currently connected to. Example:

<div class="literalinclude">

metadata/001.php

</div>

> [!NOTE]
> Some drivers have additional system tables that are excluded from this
> return.

### Determine If a Table Exists

#### \$db-\>tableExists()

Sometimes it's helpful to know whether a particular table exists before
running an operation on it. Returns a boolean true/false. Usage example:

<div class="literalinclude">

metadata/002.php

</div>

> [!NOTE]
> Replace *table_name* with the name of the table you are looking for.

## Field MetaData

### List the Fields in a Table

#### \$db-\>getFieldNames()

Returns an array containing the field names. This query can be called
two ways:

1.  You can supply the table name and call it from the `$db->object`:

    <div class="literalinclude">

    metadata/003.php

    </div>

2\. You can gather the field names associated with any query you run by
calling the function from your query result object:

<div class="literalinclude">

metadata/004.php

</div>

### Determine If a Field is Present in a Table

#### \$db-\>fieldExists()

Sometimes it's helpful to know whether a particular field exists before
performing an action. Returns a boolean true/false. Usage example:

<div class="literalinclude">

metadata/005.php

</div>

> [!NOTE]
> Replace *field_name* with the name of the column you are looking for,
> and replace *table_name* with the name of the table you are looking
> for.

### Retrieve Field Metadata

#### \$db-\>getFieldData()

Returns an array of objects containing field information.

Sometimes it's helpful to gather the field names or other metadata, like
the column type, max length, etc.

> [!NOTE]
> Not all databases provide meta-data.

Usage example:

<div class="literalinclude">

metadata/006.php

</div>

If you have run a query already you can use the result object instead of
supplying the table name:

<div class="literalinclude">

metadata/007.php

</div>

The following data is available from this function if supported by your
database:

- name - column name
- type - the type of the column
- max_length - maximum length of the column
- primary_key - integer `1` if the column is a primary key (all integer
  `1`, even if there are multiple primary keys), otherwise integer `0`
  (This field is currently only available for MySQL and SQLite3)
- nullable - boolean `true` if the column is nullable, otherwise boolean
  `false`
- default - the default value

> [!NOTE]
> Since v4.4.0, SQLSRV supported `nullable`.

### List the Indexes in a Table

#### \$db-\>getIndexData()

Returns an array of objects containing index information.

Usage example:

<div class="literalinclude">

metadata/008.php

</div>

The key types may be unique to the database you are using. For instance,
MySQL will return one of primary, fulltext, spatial, index or unique for
each key associated with a table.

SQLite3 returns a pseudo index named `PRIMARY`. But it is a special
index, and you can't use it in your SQL commands.

#### \$db-\>getForeignKeyData()

Returns an array of objects containing foreign key information.

Usage example:

<div class="literalinclude">

metadata/009.php

</div>

Foreign keys use the naming convention
`tableprefix_table_column1_column2_foreign`. Oracle uses a slightly
different suffix of `_fk`.