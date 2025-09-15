# Working with URIs

CodeIgniter provides an object oriented solution for working with URI's in your application. Using this makes it simple to ensure that the structure is always correct, no matter how complex the URI might be, as well as adding relative URI to an existing one and have it resolved safely and correctly.

- [Creating URI instances](#creating-uri-instances)
    - [The Current URI](#the-current-uri)
- [URI Strings](#uri-strings)
- [The URI Parts](#the-uri-parts)
    - [Scheme](#scheme)
    - [Authority](#authority)
    - [UserInfo](#userinfo)
    - [Host](#host)
    - [Port](#port)
    - [Path](#path)
    - [Query](#query)
    - [Fragment](#fragment)
- [URI Segments](#uri-segments)
- [Disable Throwing Exceptions](#disable-throwing-exceptions)

## Creating URI instances

Creating a URI instance is as simple as creating a new class instance.

When you create the new instance, you can pass a full or partial URL in the constructor and it will be parsed into its appropriate sections:

```php
--8<--
libraries/uri/001.php:2:
--8<--
```

Alternatively, you can use the `service()` function to return an instance for you:

```php
--8<--
libraries/uri/003.php:2:
--8<--
```

Since v4.4.0, if you don't pass a URL, it returns the current URI:

```php
--8<--
libraries/uri/002.php:2:
--8<--
```

!!! note "Note"
    The above code returns the `SiteURI` instance, that extends the `URI` class. The `URI` class is for general URIs, but the `SiteURI` class is for your site URIs.

### The Current URI

Many times, all you really want is an object representing the current URL of this request. You can use the `current_url()` function available in the [Url Helper](../helpers/url_helper.md):

```php
--8<--
libraries/uri/004.php:2:
--8<--
```

You must pass `true` as the first parameter, otherwise, it will return the string representation of the current URL.

This URI is based on the path (relative to your `baseURL`) as determined by the current request object and your settings in `Config\App` (`baseURL`, `indexPage`, and `forceGlobalSecureRequests`).

Assuming that you're in a controller that extends `CodeIgniter\Controller`, you can also get the current SiteURI instance:

```php
--8<--
libraries/uri/005.php:2:
--8<--
```

## URI Strings

Many times, all you really want is to get a string representation of a URI. This is easy to do by simply casting the URI as a string:

```php
--8<--
libraries/uri/006.php
--8<--
```

If you know the pieces of the URI and just want to ensure it's all formatted correctly, you can generate a string using the URI class' static `createURIString()` method:

```php
--8<--
libraries/uri/007.php
--8<--
```

!!! important "Important"
    When `URI` is cast to a string, it will attempt to adjust project URLs to the settings defined in `Config\App`. If you need the exact, unaltered string representation then use `URI::createURIString()` instead.

## The URI Parts

Once you have a URI instance, you can set or retrieve any of the various parts of the URI. This section will provide details on what those parts are, and how to work with them.

### Scheme

The scheme is frequently 'http' or 'https', but any scheme is supported, including 'file', 'mailto', etc.

```php
--8<--
libraries/uri/008.php
--8<--
```

### Authority

Many URIs contain several elements that are collectively known as the 'authority'. This includes any user info, the host and the port number. You can retrieve all of these pieces as one single string with the `getAuthority()` method, or you can manipulate the individual parts.

```php
--8<--
libraries/uri/009.php
--8<--
```

By default, this will not display the password portion since you wouldn't want to show that to anyone. If you want to show the password, you can use the `showPassword()` method. This URI instance will continue to show that password until you turn it off again, so always make sure that you turn it off as soon as you are finished with it:

```php
--8<--
libraries/uri/010.php
--8<--
```

If you do not want to display the port, pass in `true` as the only parameter:

```php
--8<--
libraries/uri/011.php
--8<--
```

!!! note "Note"
    If the current port is the default port for the scheme it will never be displayed.

### UserInfo

The userinfo section is simply the username and password that you might see with an FTP URI. While you can get this as part of the Authority, you can also retrieve it yourself:

```php
--8<--
libraries/uri/012.php
--8<--
```

By default, it will not display the password, but you can override that with the `showPassword()` method:

```php
--8<--
libraries/uri/013.php
--8<--
```

### Host

The host portion of the URI is typically the domain name of the URL. This can be easily set and retrieved with the `getHost()` and `setHost()` methods:

```php
--8<--
libraries/uri/014.php
--8<--
```

### Port

The port is an integer number between 0 and 65535. Each scheme has a default value associated with it.

```php
--8<--
libraries/uri/015.php
--8<--
```

When using the `setPort()` method, the port will be checked that it is within the valid range and assigned.

### Path

The path are all of the segments within the site itself. As expected, the `getPath()` and `setPath()` methods can be used to manipulate it:

```php
--8<--
libraries/uri/016.php
--8<--
```

!!! note "Note"
    When setting the path this way, or any other way the class allows, it is sanitized to encode any dangerous characters, and remove dot segments for safety.

!!! note "Note"
    Since v4.4.0, the `SiteURI::getRoutePath()` method, returns the URI path relative to baseURL, and the `SiteURI::getPath()` method always returns the full URI path with leading `/`.

### Query

The query data can be manipulated through the class using simple string representations.

#### Getting/Setting Query

Query values can only be set as a string currently.

```php
--8<--
libraries/uri/017.php
--8<--
```

The `setQuery()` method overwrite any existing query variables.

!!! note "Note"
    Query values cannot contain fragments. An InvalidArgumentException will be thrown if it does.

#### Setting Query from Array

You can set query values using an array:

```php
--8<--
libraries/uri/018.php
--8<--
```

The `setQueryArray()` method overwrite any existing query variables.

#### Adding Query Value

You can add a value to the query variables collection without destroying the existing query variables with the `addQuery()` method. The first parameter is the name of the variable, and the second parameter is the value:

```php
--8<--
libraries/uri/019.php
--8<--
```

#### Filtering Query Values

You can filter the query values returned by passing an options array to the `getQuery()` method, with either an *only* or an *except* key:

```php
--8<--
libraries/uri/020.php
--8<--
```

This only changes the values returned during this one call. If you need to modify the URI's query values more permanently,

#### Changing Query Values

you can use the `stripQuery()` and `keepQuery()` methods to change the actual object's query variable collection:

```php
--8<--
libraries/uri/021.php
--8<--
```

!!! note "Note"
    By default `setQuery()` and `setQueryArray()` methods uses native `parse_str()` function to prepare data. If you want to use more liberal rules (which allow key names to contain dots), you can use a special method `useRawQueryString()` beforehand.

### Fragment

Fragments are the portion at the end of the URL, preceded by the pound-sign (`#`). In HTML URLs these are links to an on-page anchor. Media URI's can make use of them in various other ways.

```php
--8<--
libraries/uri/022.php
--8<--
```

## URI Segments

Each section of the path between the slashes is a single segment.

!!! note "Note"
    In the case of your site URI, URI Segments mean only the URI path part relative to the baseURL. If your baseURL contains sub folders, the values will be different from the current URI path.

The URI class provides a simple way to determine what the values of the segments are. The segments start at 1 being the furthest left of the path.

```php
--8<--
libraries/uri/023.php
--8<--
```

You can also set a different default value for a particular segment by using the second parameter of the `getSegment()` method. The default is empty string.

```php
--8<--
libraries/uri/024.php
--8<--
```

!!! note "Note"
    You can get the last +1 segment. When you try to get the last +2 or more segment, an exception will be thrown by default. You could prevent throwing exceptions with the `setSilent()` method.

You can get a count of the total segments:

```php
--8<--
libraries/uri/025.php
--8<--
```

Finally, you can retrieve an array of all of the segments:

```php
--8<--
libraries/uri/026.php
--8<--
```

## Disable Throwing Exceptions

By default, some methods of this class may throw an exception. If you want to disable it, you can set a special flag that will prevent throwing exceptions.

```php
--8<--
libraries/uri/027.php
--8<--
```
