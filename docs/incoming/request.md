# Request Class

The request class is an object-oriented representation of an HTTP request. This is meant to work for both incoming, such as a request to the application from a browser, and outgoing requests, like would be used to send a request from the application to a third-party application.

This class provides the common functionality they both need, but both cases have custom classes that extend from the Request class to add specific functionality. In practice, you will need to use these classes.

See the documentation for the [IncomingRequest Class](#./incomingrequest) and [CURLRequest Class](../general/../libraries/curlrequest.md) for more usage details.

## Class Reference

## CodeIgniter\HTTP

### Request

#### getIPAddress()

\* **Returns**: The user's IP Address, if it can be detected. If the IP address is not a valid IP address, then will return `0.0.0.0`. \* **Return type**: `string`

Returns the IP address for the current user. If the IP address is not valid, the method will return `0.0.0.0`:

```php
--8<--
incoming/request/001.php
--8<--
```

!!! important "Important"
    This method takes into account the `Config\App::$proxyIPs` setting and will

return the reported client IP address by the HTTP header for the allowed IP address.

#### isValidIP($ip\[, $which = ''])

!!! danger "Deprecated since version 4.0.5"

Use [Validation](../libraries/validation.md) instead.

!!! important "Important"
    This method is deprecated. It will be removed in future releases.

- **Parameters**  

- **$ip** `string` IP address

- **$which** `string` IP protocol (`ipv4` or `ipv6`)

- **Returns**: true if the address is valid, false if not

- **Return type**: `bool`

Takes an IP address as input and returns true or false (boolean) depending on whether it is valid or not.

!!! note "Note"
    The $request-\>getIPAddress() method above automatically validates the IP address.

```php
--8<--
incoming/request/002.php
--8<--
```

Accepts an optional second string parameter of `ipv4` or `ipv6` to specify an IP format. The default checks for both formats.

#### getMethod()

- **Returns**: HTTP request method

- **Return type**: `string`

Returns the `$_SERVER['REQUEST_METHOD']`.

```php
--8<--
incoming/request/003.php
--8<--
```

#### setMethod($method)

!!! danger "Deprecated since version 4.0.5"

Use `CodeIgniter\\HTTP\\Request::withMethod()` instead.

- **Parameters**  

- **$method** `string` Sets the request method. Used when spoofing the request.

- **Returns**: This request

- **Return type**: `Request`

#### withMethod($method)

!!! success "Available from version 4.0.5"

- **Parameters**  

- **$method** `string` Sets the request method.

- **Returns**: New request instance

- **Return type**: `Request`

#### getServer(\[$index = null\[, $filter = null\[, $flags = null]]])

- **Parameters**  

- **$index** `mixed` Value name

- **$filter** `int` The type of filter to apply. A list of filters can be found in [PHP manual](https://www.php.net/manual/en/filter.filters.php).

- **$flags** `int|array` Flags to apply. A list of flags can be found in [PHP manual](https://www.php.net/manual/en/filter.filters.flags.php).

- **Returns**: `$_SERVER` item value if found, null if not

- **Return type**: `mixed`

This method is identical to the `getPost()`, `getGet()` and `getCookie()` methods from the [IncomingRequest Class](#./incomingrequest), only it fetches server data (`$_SERVER`):

```php
--8<--
incoming/request/004.php
--8<--
```

To return an array of multiple `$_SERVER` values, pass all the required keys as an array.

```php
--8<--
incoming/request/005.php
--8<--
```

#### getEnv(\[$index = null\[, $filter = null\[, $flags = null]]])

<div class="deprecated">

4.4.4 This method does not work from the beginning. Use

</div>

`env()` instead.

- **Parameters**  

- **$index** `mixed` Value name

- **$filter** `int` The type of filter to apply. A list of filters can be found in [PHP manual](https://www.php.net/manual/en/filter.filters.php).

- **$flags** `int|array` Flags to apply. A list of flags can be found in [PHP manual](https://www.php.net/manual/en/filter.filters.flags.php).

- **Returns**: `$_ENV` item value if found, null if not

- **Return type**: `mixed`

This method is identical to the `getPost()`, `getGet()` and `getCookie()` methods from the [IncomingRequest Class](#./incomingrequest), only it fetches env data (`$_ENV`):

```php
--8<--
incoming/request/006.php
--8<--
```

To return an array of multiple `$_ENV` values, pass all the required keys as an array.

```php
--8<--
incoming/request/007.php
--8<--
```

#### setGlobal($method, $value)

- **Parameters**  

- **$method** `string` Method name

- **$value** `mixed` Data to be added

- **Returns**: This request

- **Return type**: `Request`

Allows manually setting the value of PHP global, like `$_GET`, `$_POST`, etc.

#### fetchGlobal($method \[, $index = null\[, $filter = null\[, $flags = null]]])

- **Parameters**  

- **$method** `string` Input filter constant

- **$index** `mixed` Value name

- **$filter** `int` The type of filter to apply. A list of filters can be found in [PHP manual](https://www.php.net/manual/en/filter.filters.php).

- **$flags** `int|array` Flags to apply. A list of flags can be found in [PHP manual](https://www.php.net/manual/en/filter.filters.flags.php).

- **Return type**: `mixed`

Fetches one or more items from a global, like cookies, get, post, etc. Can optionally filter the input when you retrieve it by passing in a filter.
