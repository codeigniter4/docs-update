# Upgrade Migrations

<div class="contents" local="" depth="2">

</div>

## Documentations

- [Database Migrations Documentation CodeIgniter
  3.X](http://codeigniter.com/userguide3/libraries/migration.html)
- `Database Migrations Documentation CodeIgniter 4.X </dbmgmt/migration>`

## What has been changed

- First of all, the sequential naming (`001_create_users`,
  `002_create_posts`) of migrations is not longer supported. Version 4
  of CodeIgniter only supports the timestamp scheme
  (`20121031100537_create_users`, `20121031500638_create_posts`) . If
  you have used sequential naming you have to rename each migration
  file.
- The migration table definition was changed. If you upgrade from CI3 to
  CI4 and use the same database, You need to upgrade the migration table
  definition and its data.
- The migration procedure has been also changed. You can now migrate the
  database with a simple CLI command:

``` console
php spark migrate
```

## Upgrade Guide

1.  If your v3 project uses sequential migration names you have to
    change those to timestamp names.

2.  You have to move all migration files to the new folder
    **app/Database/Migrations**.

3.  Remove the line
    `defined('BASEPATH') OR exit('No direct script access allowed');` if
    it exists.

4.  Add this line just after the opening php tag:
    `namespace App\Database\Migrations;`.

5.  Below the `namespace App\Database\Migrations;` line add this line:
    `use CodeIgniter\Database\Migration;`

6.  Replace `extends CI_Migration` with `extends Migration`.

7.  The method names within the `Forge` class has been changed to use
    camelCase. For example:

    > - `$this->dbforge->add_field` to `$this->forge->addField`
    > - `$this->dbforge->add_key` to `$this->forge->addKey`
    > - `$this->dbforge->create_table` to `$this->forge->addTable`
    > - `$this->dbforge->drop_table` to `$this->forge->addTable`

8.  (optional) You can change the array syntax from `array(...)` to
    `[...]`

9.  Upgrade the migration table, if you use the same database.

    > - **(development)** Run the CI4 migration in the development
    >   environment or so with brand new database, to create the new
    >   migration table.
    > - **(development)** Export the migration table.
    > - **(production)** Drop (or rename) the existing CI3 migration
    >   table.
    > - **(production)** Import the new migration table and the data.

## Code Example

### CodeIgniter Version 3.x

Path: **application/migrations**:

<div class="literalinclude">

upgrade_migrations/ci3sample/001.php

</div>

### CodeIgniter Version 4.x

Path: **app/Database/Migrations**:

<div class="literalinclude">

upgrade_migrations/001.php

</div>

## Search & Replace

You can use to following table to search & replace your old CI3 files.

<table style="width:83%;">
<colgroup>
<col style="width: 43%" />
<col style="width: 40%" />
</colgroup>
<thead>
<tr class="header">
<th><blockquote>
<p>Search</p>
</blockquote></th>
<th>Replace</th>
</tr>
</thead>
<tbody>
<tr class="odd">
<td>extends CI_Migration</td>
<td>extends Migration</td>
</tr>
<tr class="even">
<td>$this-&gt;dbforge-&gt;add_field</td>
<td>$this-&gt;forge-&gt;addField</td>
</tr>
<tr class="odd">
<td>$this-&gt;dbforge-&gt;add_key</td>
<td>$this-&gt;forge-&gt;addKey</td>
</tr>
<tr class="even">
<td>$this-&gt;dbforge-&gt;create_table</td>
<td>$this-&gt;forge-&gt;createTable</td>
</tr>
<tr class="odd">
<td>$this-&gt;dbforge-&gt;drop_table</td>
<td>$this-&gt;forge-&gt;dropTable</td>
</tr>
</tbody>
</table>
