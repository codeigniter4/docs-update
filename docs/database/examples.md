# Database Quick Start: Example Code

The following page contains example code showing how the database class
is used. For complete details please read the individual pages
describing each function.

> [!NOTE]
> CodeIgniter doesn't support dots (`.`) in the database, table, and
> column names.

<div class="contents" local="" depth="2">

</div>

## Initializing the Database Class

The following code loads and initializes the database class based on
your `configuration <configuration>` settings:

<div class="literalinclude">

examples/001.php

</div>

Once loaded the class is ready to be used as described below.

> [!NOTE]
> If all your pages require database access you can connect
> automatically. See the `connecting <connecting>` page for details.

## Standard Query With Multiple Results (Object Version)

<div class="literalinclude">

examples/002.php

</div>

The above `getResult()` function returns an array of **objects**.  
Example: `$row->title`

## Standard Query With Multiple Results (Array Version)

<div class="literalinclude">

examples/003.php

</div>

The above `getResultArray()` function returns an array of standard array
indexes.  
Example: `$row['title']`

## Standard Query With Single Result

<div class="literalinclude">

examples/004.php

</div>

The above `getRow()` function returns an **object**. Example:
`$row->name`

## Standard Query With Single Result (Array version)

<div class="literalinclude">

examples/005.php

</div>

The above `getRowArray()` function returns an **array**. Example:
`$row['name']`.

## Standard Insert

<div class="literalinclude">

examples/006.php

</div>

## Query Builder Query

The `Query Builder Pattern <query_builder>` gives you a simplified means
of retrieving data:

<div class="literalinclude">

examples/007.php

</div>

The above `get()` function retrieves all the results from the supplied
table. The `Query Builder <query_builder>` class contains a full
complement of functions for working with data.

## Query Builder Insert

<div class="literalinclude">

examples/008.php

</div>
