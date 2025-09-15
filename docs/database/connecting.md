# Connecting to your Database

- [Connecting to a Database](#connecting-to-a-database)
    - [Connecting to the Default Group](#connecting-to-the-default-group)
    - [Available Parameters](#available-parameters)
    - [Connecting to Specific Group](#connecting-to-specific-group)
    - [Multiple Connections to Same Database](#multiple-connections-to-same-database)
- [Connecting to Multiple Databases](#connecting-to-multiple-databases)
- [Connecting with Custom Settings](#connecting-with-custom-settings)
- [Reconnecting / Keeping the Connection Alive](#reconnecting-keeping-the-connection-alive)
- [Manually Closing the Connection](#manually-closing-the-connection)

## Connecting to a Database

### Connecting to the Default Group

You can connect to your database by adding this line of code in any function where it is needed, or in your class constructor to make the database available globally in that class.

```php
--8<--
database/connecting/001.php:2:
--8<--
```

If the above function does **not** contain any information in the first parameter, it will connect to the default group specified in your database config file. For most people, this is the preferred method of use.

A convenience method exists that is purely a wrapper around the above line and is provided for your convenience:

```php
--8<--
database/connecting/002.php:2:
--8<--
```

### Available Parameters

**\Config\Database::connect($group = null, bool $getShared = true): BaseConnection**

1.  `$group`: The database group name, a string that must match the config class' property name. Default value is `Config\Database::$defaultGroup`.
2.  `$getShared`: true/false (boolean). Whether to return the shared connection (see Connecting to Multiple Databases below).

### Connecting to Specific Group

The first parameter of this function can **optionally** be used to specify a particular database group from your config file. Examples:

To choose a specific group from your config file you can do this:

```php
--8<--
database/connecting/003.php:2:
--8<--
```

Where `group_name` is the name of the connection group from your config file.

### Multiple Connections to Same Database

By default, the `connect()` method will return the same instance of the database connection every time. If you need to have a separate connection to the same database, send `false` as the second parameter:

```php
--8<--
database/connecting/004.php:2:
--8<--
```

## Connecting to Multiple Databases

If you need to connect to more than one database simultaneously you can do so as follows:

```php
--8<--
database/connecting/005.php:2:
--8<--
```

Note: Change the words `group_one` and `group_two` to the specific group names you are connecting to.

!!! note "Note"
    You don't need to create separate database configurations if you only need to use a different database on the same connection. You can switch to a different database when you need to, like this: `$db->setDatabase($database2_name);`

## Connecting with Custom Settings

You can pass in an array of database settings instead of a group name to get a connection that uses your custom settings. The array passed in must be the same format as the groups are defined in the configuration file:

```php
--8<--
database/connecting/006.php:2:
--8<--
```

## Reconnecting / Keeping the Connection Alive

If the database server's idle timeout is exceeded while you're doing some heavy PHP lifting (processing an image, for instance), you should consider pinging the server by using the `reconnect()` method before sending further queries, which can gracefully keep the connection alive or re-establish it.

!!! important "Important"
    If you are using MySQLi database driver, the `reconnect()` method does not ping the server but it closes the connection then connects again.

```php
--8<--
database/connecting/007.php:2:
--8<--
```

## Manually Closing the Connection

While CodeIgniter intelligently takes care of closing your database connections, you can explicitly close the connection.

```php
--8<--
database/connecting/008.php:2:
--8<--
```
