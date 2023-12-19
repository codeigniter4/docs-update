# Database Utility Class

The Database Utility Class contains methods that help you manage your
database.

<div class="contents" local="" depth="2">

</div>

## Initializing the Utility Class

Load the Utility Class as follows:

<div class="literalinclude" lines="2-">

utilities/002.php

</div>

You can also pass another database group to the DB Utility loader, in
case the database you want to manage isn't the default one:

<div class="literalinclude" lines="2-">

utilities/003.php

</div>

In the above example, we're passing a database group name as the first
parameter.

## Using the Database Utilities

### Retrieve List of Database Names

Returns an array of database names:

<div class="literalinclude" lines="2-">

utilities/004.php

</div>

### Determine If a Database Exists

Sometimes it's helpful to know whether a particular database exists.
Returns a boolean `true`/`false`. Usage example:

<div class="literalinclude" lines="2-">

utilities/005.php

</div>

> [!NOTE]
> Replace `database_name` with the name of the database you are looking
> for. This method is case sensitive.

### Optimize a Table

Permits you to optimize a table using the table name specified in the
first parameter. Returns `true`/`false` based on success or failure:

<div class="literalinclude" lines="2-">

utilities/006.php

</div>

> [!NOTE]
> Not all database platforms support table optimization. It is mostly
> for use with MySQL.

### Optimize a Database

Permits you to optimize the database your DB class is currently
connected to. Returns an array containing the DB status messages or
`false` on failure:

<div class="literalinclude" lines="2-">

utilities/008.php

</div>

> [!NOTE]
> Not all database platforms support database optimization. It it is
> mostly for use with MySQL.

### Export a Query Result as a CSV File

Permits you to generate a CSV file from a query result. The first
parameter of the method must contain the result object from your query.
Example:

<div class="literalinclude" lines="2-">

utilities/009.php

</div>

The second, third, and fourth parameters allow you to set the delimiter
newline, and enclosure characters respectively. By default commas are
used as the delimiter, `"\n"` is used as a new line, and a double-quote
is used as the enclosure. Example:

<div class="literalinclude" lines="2-">

utilities/010.php

</div>

> [!IMPORTANT]
> This method will NOT write the CSV file for you. It simply creates the
> CSV layout. If you need to write the file use the `write_file()`
> helper.

### Export a Query Result as an XML Document

Permits you to generate an XML file from a query result. The first
parameter expects a query result object, the second may contain an
optional array of config parameters. Example:

<div class="literalinclude">

utilities/001.php

</div>

and it will get the following xml result when the `mytable` has columns
`id` and `name`:

    <root>
        <element>
            <id>1</id>
            <name>bar</name>
        </element>
    </root>

> [!IMPORTANT]
> This method will NOT write the XML file for you. It simply creates the
> XML layout. If you need to write the file use the `write_file()`
> helper.
