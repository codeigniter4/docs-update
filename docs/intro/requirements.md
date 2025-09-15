# Server Requirements

- [PHP and Required Extensions](#php-and-required-extensions)
- [Optional PHP Extensions](#optional-php-extensions)
- [Supported Databases](#supported-databases)

## PHP and Required Extensions

<a href="https://www.php.net/" target="_blank">PHP</a> version 8.1 or newer is required, with the following PHP extensions are enabled:

> - <a href="https://www.php.net/manual/en/intl.requirements.php" target="_blank">intl</a>
> - <a href="https://www.php.net/manual/en/mbstring.requirements.php" target="_blank">mbstring</a>
> - <a href="https://www.php.net/manual/en/json.requirements.php" target="_blank">json</a>

!!! warning "Warning"
    The end of life date for PHP 7.4 was November 28, 2022. If you are still using PHP 7.4, you should upgrade immediately. The end of life date for PHP 8.0 will be November 26, 2023.

## Optional PHP Extensions

The following PHP extensions should be enabled on your server:

> - <a href="https://www.php.net/manual/en/mysqlnd.install.php" target="_blank">mysqlnd</a> (if you use MySQL)
> - <a href="https://www.php.net/manual/en/curl.requirements.php" target="_blank">curl</a> (if you use [CURLRequest](#/libraries/curlrequest))
> - <a href="https://www.php.net/manual/en/imagick.requirements.php" target="_blank">imagick</a> (if you use [Image](#/libraries/images) class ImageMagickHandler)
> - <a href="https://www.php.net/manual/en/image.requirements.php" target="_blank">gd</a> (if you use [Image](#/libraries/images) class GDHandler)
> - <a href="https://www.php.net/manual/en/simplexml.requirements.php" target="_blank">simplexml</a> (if you format XML)

The following PHP extensions are required when you use a Cache server:

> - <a href="https://www.php.net/manual/en/memcache.requirements.php" target="_blank">memcache</a> (if you use [Cache](#/libraries/caching) class MemcachedHandler with Memcache)
> - <a href="https://www.php.net/manual/en/memcached.requirements.php" target="_blank">memcached</a> (if you use [Cache](#/libraries/caching) class MemcachedHandler with Memcached)
> - <a href="https://github.com/phpredis/phpredis" target="_blank">redis</a> (if you use [Cache](#/libraries/caching) class RedisHandler)

The following PHP extensions are required when you use PHPUnit:

> - <a href="https://www.php.net/manual/en/dom.requirements.php" target="_blank">dom</a> (if you use [TestResponse](#/testing/response) class)
> - <a href="https://www.php.net/manual/en/libxml.requirements.php" target="_blank">libxml</a> (if you use [TestResponse](#/testing/response) class)
> - <a href="https://xdebug.org/docs/install" target="_blank">xdebug</a> (if you use `CIUnitTestCase::assertHeaderEmitted()`)

## Supported Databases

A database is required for most web application programming. Currently supported databases are:

> - MySQL via the `MySQLi` driver (version 5.1 and above only)
> - PostgreSQL via the `Postgre` driver (version 7.4 and above only)
> - SQLite3 via the `SQLite3` driver
> - Microsoft SQL Server via the `SQLSRV` driver (version 2005 and above only)
> - Oracle Database via the `OCI8` driver (version 12.1 and above only)

Not all of the drivers have been converted/rewritten for CodeIgniter4. The list below shows the outstanding ones.

> - MySQL (5.1+) via the *pdo* driver
> - Oracle via the *pdo* drivers
> - PostgreSQL via the *pdo* driver
> - MSSQL via the *pdo* driver
> - SQLite via the *sqlite* (version 2) and *pdo* drivers
> - CUBRID via the *cubrid* and *pdo* drivers
> - Interbase/Firebird via the *ibase* and *pdo* drivers
> - ODBC via the *odbc* and *pdo* drivers (you should know that ODBC is actually an abstraction layer)
