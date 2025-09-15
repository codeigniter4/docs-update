# Database Configuration

- [Config File](#config-file)
    - [Setting Default Database](#setting-default-database)
        - [DSN](#dsn)
        - [Failovers](#failovers)
    - [Setting Multiple Databases](#setting-multiple-databases)
    - [Changing Databases Automatically](#changing-databases-automatically)
- [Configuring with .env File](#configuring-with-env-file)
- [Explanation of Values:](#explanation-of-values)

!!! note "Note"
    See `requirements-supported-databases` for currently supported database drivers.

## Config File

CodeIgniter has a config file that lets you store your database connection values (username, password, database name, etc.). The config file is located at **app/Config/Database.php**. You can also set database connection values in the **.env** file. See below for more details.

### Setting Default Database

The config settings are stored in a class property that is an array with this prototype:

```php
--8<--
database/configuration/001.php
--8<--
```

The name of the class property is the connection name, and can be used while connecting to specify a group name.

!!! note "Note"
    The default location of the SQLite3 database is in the **writable** folder. If you want to change the location, you must set the full path to the new folder.

#### DSN

Some database drivers (such as Postgre, OCI8) requires a full DSN string to connect. But if you do not specify a DSN string for a driver that requires it, CodeIgniter will try to build it with the rest of the provided settings.

If you specify a DSN, you should use the `'DSN'` configuration setting, as if you're using the driver's underlying native PHP extension, like this:

```php
--8<--
database/configuration/002.php:11:15
--8<--
```

##### DSN in Universal Manner

You can also set a Data Source Name in universal manner (URL like). In that case DSNs must have this prototype:

```php
--8<--
database/configuration/003.php:11:14
--8<--
```

To override default config values when connecting with a universal version of the DSN string, add the config variables as a query string:

```php
--8<--
database/configuration/004.php:11:15
--8<--
```

```php
--8<--
database/configuration/010.php:11:15
--8<--
```

!!! note "Note"
    If you provide a DSN string and it is missing some valid settings (e.g., the database character set), which are present in the rest of the configuration fields, CodeIgniter will append them.

#### Failovers

You can also specify failovers for the situation when the main connection cannot connect for some reason. These failovers can be specified by setting the failover for a connection like this:

```php
--8<--
database/configuration/005.php
--8<--
```

You can specify as many failovers as you like.

### Setting Multiple Databases

You may optionally store multiple sets of connection values. If, for example, you run multiple environments (development, production, test, etc.) under a single installation, you can set up a connection group for each, then switch between groups as needed. For example, to set up a "test" environment you would do this:

```php
--8<--
database/configuration/006.php
--8<--
```

Then, to globally tell the system to use that group you would set this variable located in the config file:

```php
--8<--
database/configuration/007.php
--8<--
```

!!! note "Note"
    The name `test` is arbitrary. It can be anything you want. By default we've used the word `default` for the primary connection, but it too can be renamed to something more relevant to your project.

### Changing Databases Automatically

You could modify the config file to detect the environment and automatically update the `defaultGroup` value to the correct one by adding the required logic within the class' constructor:

```php
--8<--
database/configuration/008.php
--8<--
```

## Configuring with .env File

You can also save your configuration values within a **.env** file with the current server's database settings. You only need to enter the values that change from what is in the default group's configuration settings. The values should be name following this format, where `default` is the group name:

    database.default.username = 'root';
    database.default.password = '';
    database.default.database = 'ci4';

But you cannot add a new property by setting environment variables, nor change a scalar value to an array. See `env-var-replacements-for-data` for details.

So if you want to use SSL with MySQL, you need a hack. For example, set the array values as a JSON string in your **.env** file:

    database.default.encrypt = {"ssl_verify":true,"ssl_ca":"/var/www/html/BaltimoreCyberTrustRoot.crt.pem"}

and decode it in the constructor in the Config class:

```php
--8<--
database/configuration/009.php
--8<--
```

## Explanation of Values:

<table>
<thead>
<tr>
<th>Name Config</th>
<th>Description</th>
</tr>
</thead>
<tbody>
<tr>
<td><strong>DSN</strong></td>
<td>The DSN connect string (an all-in-one configuration sequence).</td>
</tr>
<tr>
<td><strong>hostname</strong></td>
<td>The hostname of your database server. Often this is 'localhost'.</td>
</tr>
<tr>
<td><strong>username</strong></td>
<td>The username used to connect to the database. (<code>SQLite3</code> does not use this.)</td>
</tr>
<tr>
<td><strong>password</strong></td>
<td>The password used to connect to the database. (<code>SQLite3</code> does not use this.)</td>
</tr>
<tr>
<td><strong>database</strong></td>
<td><p>The name of the database you want to connect to.</p>
!!! note "<p>Note</p>"
    <p>CodeIgniter doesn't support dots (<code>.</code>) in the database, table, and column names.</p></td>
</tr>
<tr>
<td><strong>DBDriver</strong></td>
<td>The database driver name. The case must match the driver name. You can set a fully qualified classname to use your custom driver. Supported drivers: <code>MySQLi</code>, <code>Postgre</code>, <code>SQLite3</code>, <code>SQLSRV</code>, and <code>OCI8</code>.</td>
</tr>
<tr>
<td><strong>DBPrefix</strong></td>
<td>An optional table prefix which will added to the table name when running [Query Builder](#query-builder) queries. This permits multiple CodeIgniter installations to share one database.</td>
</tr>
<tr>
<td><strong>pConnect</strong></td>
<td>true/false (boolean) - Whether to use a persistent connection.</td>
</tr>
<tr>
<td><strong>DBDebug</strong></td>
<td>true/false (boolean) - Whether to throw exceptions or not when database errors occur.</td>
</tr>
<tr>
<td><strong>charset</strong></td>
<td>The character set used in communicating with the database.</td>
</tr>
<tr>
<td><strong>DBCollat</strong></td>
<td>The character collation used in communicating with the database (<code>MySQLi</code> only).</td>
</tr>
<tr>
<td><strong>swapPre</strong></td>
<td>A default table prefix that should be swapped with <code>DBPrefix</code>. This is useful for distributed applications where you might run manually written queries, and need the prefix to still be customizable by the end user.</td>
</tr>
<tr>
<td><strong>schema</strong></td>
<td>The database schema, default value varies by driver. (Used by <code>Postgre</code> and <code>SQLSRV</code>.)</td>
</tr>
<tr>
<td><strong>encrypt</strong></td>
<td>Whether or not to use an encrypted connection. <code>SQLSRV</code> driver accepts true/false <code>MySQLi</code> driver accepts an array with the following options: * <code>ssl_key</code> - Path to the private key file * <code>ssl_cert</code> - Path to the public key certificate file * <code>ssl_ca</code> - Path to the certificate authority file * <code>ssl_capath</code> - Path to a directory containing trusted CA certificates in PEM format * <code>ssl_cipher</code> - List of <em>allowed</em> ciphers to be used for the encryption, separated by colons (<code>:</code>) * <code>ssl_verify</code> - true/false; Whether to verify the server certificate or not (<code>MySQLi</code> only)</td>
</tr>
<tr>
<td><strong>compress</strong></td>
<td>Whether or not to use client compression (<code>MySQLi</code> only).</td>
</tr>
<tr>
<td><strong>strictOn</strong></td>
<td>true/false (boolean) - Whether to force "Strict Mode" connections, good for ensuring strict SQL while developing an application (<code>MySQLi</code> only).</td>
</tr>
<tr>
<td><strong>port</strong></td>
<td>The database port number - Empty string <code>''</code> for default port (or dynamic port with <code>SQLSRV</code>).</td>
</tr>
<tr>
<td><strong>foreignKeys</strong></td>
<td><p>true/false (boolean) - Whether or not to enable Foreign Key constraint (<code>SQLite3</code> only).</p>
!!! important "<p>Important</p>"
    <p>SQLite3 Foreign Key constraint is disabled by default. See <a href="https://www.sqlite.org/pragma.html#pragma_foreign_keys">SQLite documentation</a>. To enforce Foreign Key constraint, set this config item to true.</p></td>
</tr>
<tr>
<td><strong>busyTimeout</strong></td>
<td>milliseconds (int) - Sleeps for a specified amount of time when a table is locked (<code>SQLite3</code> only).</td>
</tr>
<tr>
<td><strong>numberNative</strong></td>
<td>true/false (boolean) - Whether or not to enable MYSQLI_OPT_INT_AND_FLOAT_NATIVE (<code>MySQLi</code> only).</td>
</tr>
</tbody>
</table>

!!! note "Note"
    Depending on what database driver you are using (`MySQLi`, `Postgre`, etc.) not all values will be needed. For example, when using `SQLite3` you will not need to supply a username or password, and the database name will be the path to your database file.
