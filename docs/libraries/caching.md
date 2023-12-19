# Caching Driver

CodeIgniter features wrappers around some of the most popular forms of
fast and dynamic caching. All but file-based caching require specific
server requirements, and a Fatal Exception will be thrown if server
requirements are not met.

<div class="contents" local="" depth="2">

</div>

## Example Usage

The following example shows a common usage pattern within your
controllers.

<div class="literalinclude">

caching/001.php

</div>

You can grab an instance of the cache engine directly through the
Services class:

<div class="literalinclude">

caching/002.php

</div>

## Configuring the Cache

All configuration for the cache engine is done in
**app/Config/Cache.php**. In that file, the following items are
available.

### \$handler

The is the name of the handler that should be used as the primary
handler when starting up the engine. Available names are: dummy, file,
memcached, redis, predis, wincache.

### \$backupHandler

In the case that the first choice `$handler` is not available, this is
the next cache handler to load. This is commonly the `File` handler
since the file system is always available, but may not fit more complex,
multi-server setups.

### \$prefix

If you have more than one application using the same cache storage, you
can add a custom prefix string here that is prepended to all key names.

### \$ttl

The default number of seconds to save items when none is specified.

WARNING: This is not used by framework handlers where 60 seconds is
hard-coded, but may be useful to projects and modules. This will replace
the hard-coded value in a future release.

### \$file

This is an array of settings specific to the `File` handler to determine
how it should save the cache files.

### \$memcached

This is an array of servers that will be used when using the
`Memcache(d)` handler.

### \$redis

The settings for the Redis server that you wish to use when using the
`Redis` and `Predis` handler.

## Command-Line Tools

CodeIgniter ships with several `commands </cli/spark_commands>` that are
available from the command line to help you work with Cache. These tools
are not required to use Cache driver but might help you.

### cache:clear

Clears the current system caches:

``` console
php spark cache:clear
```

### cache:info

Shows file cache information in the current system:

``` console
php spark cache:info
```

> [!NOTE]
> This command only supports the File cache handler.

## Class Reference

> > returns  
> > `true` if supported, `false` if not
> >
> > rtype  
> > bool
>
> > param string \$key  
> > Cache item name
> >
> > returns  
> > Item value or `null` if not found
> >
> > rtype  
> > mixed
> >
> > This method will attempt to fetch an item from the cache store. If
> > the item does not exist, the method will return null.
> >
> > Example:
> >
> > <div class="literalinclude">
> >
> > caching/003.php
> >
> > </div>
>
> > param string \$key  
> > Cache item name
> >
> > param int \$ttl  
> > Time to live in seconds
> >
> > param Closure \$callback  
> > Callback to invoke when the cache item returns null
> >
> > returns  
> > The value of the cache item
> >
> > rtype  
> > mixed
> >
> > Gets an item from the cache. If `null` was returned, this will
> > invoke the callback and save the result. Either way, this will
> > return the value.
>
> > param string \$key  
> > Cache item name
> >
> > param mixed \$data  
> > the data to save
> >
> > param int \$ttl  
> > Time To Live, in seconds (default 60)
> >
> > param bool \$raw  
> > Whether to store the raw value
> >
> > returns  
> > `true` on success, `false` on failure
> >
> > rtype  
> > bool
> >
> > This method will save an item to the cache store. If saving fails,
> > the method will return `false`.
> >
> > Example:
> >
> > <div class="literalinclude">
> >
> > caching/004.php
> >
> > </div>
> >
> > > [!NOTE]
> > > The `$raw` parameter is only utilized by Memcache, in order to
> > > allow usage of `increment()` and `decrement()`.
>
> > param string \$key  
> > name of cached item
> >
> > returns  
> > `true` on success, `false` on failure
> >
> > rtype  
> > bool
> >
> > This method will delete a specific item from the cache store. If
> > item deletion fails, the method will return false.
> >
> > Example:
> >
> > <div class="literalinclude">
> >
> > caching/005.php
> >
> > </div>
>
> > param string \$pattern  
> > glob-style pattern to match cached items keys
> >
> > returns  
> > number of deleted items
> >
> > rtype  
> > integer
> >
> > This method will delete multiple items from the cache store at once
> > by matching their keys against a glob-style pattern. It will return
> > the total number of deleted items.
> >
> > > [!IMPORTANT]
> > > This method is only implemented for File, Redis and Predis
> > > handlers. Due to limitations, it couldn't be implemented for
> > > Memcached and Wincache handlers.
> >
> > Example:
> >
> > <div class="literalinclude">
> >
> > caching/006.php
> >
> > </div>
> >
> > For more information on glob-style syntax, please see [Glob
> > (programming)](https://en.wikipedia.org/wiki/Glob_(programming)#Syntax).
>
> > param string \$key  
> > Cache ID
> >
> > param int \$offset  
> > Step/value to add
> >
> > returns  
> > New value on success, `false` on failure
> >
> > rtype  
> > mixed
> >
> > Performs atomic incrementation of a raw stored value.
> >
> > Example:
> >
> > <div class="literalinclude">
> >
> > caching/007.php
> >
> > </div>
>
> > param string \$key  
> > Cache ID
> >
> > param int \$offset  
> > Step/value to reduce by
> >
> > returns  
> > New value on success, `false` on failure
> >
> > rtype  
> > mixed
> >
> > Performs atomic decrementation of a raw stored value.
> >
> > Example:
> >
> > <div class="literalinclude">
> >
> > caching/008.php
> >
> > </div>
>
> > returns  
> > `true` on success, `false` on failure
> >
> > rtype  
> > bool
> >
> > This method will 'clean' the entire cache. If the deletion of the
> > cache files fails, the method will return false.
> >
> > Example:
> >
> > <div class="literalinclude">
> >
> > caching/009.php
> >
> > </div>
>
> > returns  
> > Information on the entire cache database
> >
> > rtype  
> > mixed
> >
> > This method will return information on the entire cache.
> >
> > Example:
> >
> > <div class="literalinclude">
> >
> > caching/010.php
> >
> > </div>
> >
> > > [!NOTE]
> > > The information returned and the structure of the data is
> > > dependent on which adapter is being used.
>
> > param string \$key  
> > Cache item name
> >
> > returns  
> > Metadata for the cached item. `null` for missing items, or an array
> > with at least the "expire" key for absolute epoch expiry (`null` for
> > never expires).
> >
> > rtype  
> > array\|null
> >
> > This method will return detailed information on a specific item in
> > the cache.
> >
> > Example:
> >
> > <div class="literalinclude">
> >
> > caching/011.php
> >
> > </div>
> >
> > > [!NOTE]
> > > The information returned and the structure of the data is
> > > dependent on which adapter is being used. Some adapters (File,
> > > Memcached, Wincache) still return `false` for missing items.
>
> > param string \$key  
> > Potential cache key
> >
> > param string \$prefix  
> > Optional prefix
> >
> > returns  
> > The verified and prefixed key. If the key exceeds the driver's max
> > key length it will be hashed.
> >
> > rtype  
> > string
> >
> > This method is used by handler methods to check that keys are valid.
> > It will throw an `InvalidArgumentException` for non-strings, invalid
> > characters, and empty lengths.
> >
> > Example:
> >
> > <div class="literalinclude">
> >
> > caching/012.php
> >
> > </div>

## Drivers

### File-based Caching

Unlike caching from the Output Class, the driver file-based caching
allows for pieces of view files to be cached. Use this with care, and
make sure to benchmark your application, as a point can come where disk
I/O will negate positive gains by caching. This requires a cache
directory to be really writable by the application.

### Memcached Caching

Memcached servers can be specified in the cache configuration file.
Available options are:

<div class="literalinclude">

caching/013.php

</div>

For more information on Memcached, please see
<https://www.php.net/memcached>.

### WinCache Caching

Under Windows, you can also utilize the WinCache driver.

For more information on WinCache, please see
<https://www.php.net/wincache>.

### Redis Caching

Redis is an in-memory key-value store which can operate in LRU cache
mode. To use it, you need [Redis server and phpredis PHP
extension](https://github.com/phpredis/phpredis).

Config options to connect to redis server stored in the cache
configuration file. Available options are:

<div class="literalinclude">

caching/014.php

</div>

For more information on Redis, please see <https://redis.io>.

### Predis Caching

Predis is a flexible and feature-complete PHP client library for the
Redis key-value store. To use it, from the command line inside your
project root:

``` console
composer require predis/predis
```

For more information on Redis, please see
<https://github.com/nrk/predis>.

### Dummy Cache

This is a caching backend that will always 'miss.' It stores no data,
but lets you keep your caching code in place in environments that don't
support your chosen cache.