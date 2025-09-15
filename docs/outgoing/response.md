# HTTP Responses

The Response class extends the [HTTP Message Class](#/incoming/message) with methods only appropriate for a server responding to the client that called it.

- [Working with the Response](#working-with-the-response)
    - [Setting the Output](#setting-the-output)
    - [Setting Headers](#setting-headers)
- [Redirect](#redirect)
    - [Redirect to a URI path](#redirect-to-a-uri-path)
    - [Redirect to a Defined Route](#redirect-to-a-defined-route)
    - [Redirect Back](#redirect-back)
    - [Redirect with Cookies](#redirect-with-cookies)
    - [Redirect with Headers](#redirect-with-headers)
    - [Redirect Status Code](#redirect-status-code)
- [Force File Download](#force-file-download)
    - [Open File in Browser](#open-file-in-browser)
- [HTTP Caching](#http-caching)
- [Class Reference](#class-reference)
- [CodeIgniter\HTTP](#codeigniterhttp)
    - [Response](#response)

## Working with the Response

A Response class is instantiated for you and passed into your controllers. It can be accessed through `$this->response`. It is the same instance that `Services::response()` returns. We call it the global response instance.

Many times you will not need to touch the class directly, since CodeIgniter takes care of sending the headers and the body for you. This is great if the page successfully created the content it was asked to. When things go wrong, or you need to send very specific status codes back, or even take advantage of the powerful HTTP caching, it's there for you.

### Setting the Output

When you need to set the output of the script directly, and not rely on CodeIgniter to automatically get it, you do it manually with the `setBody` method. This is usually used in conjunction with setting the status code of the response:

```php
--8<--
outgoing/response/001.php
--8<--
```

The reason phrase ('OK', 'Created', 'Moved Permanently') will be automatically added, but you can add custom reasons as the second parameter of the `setStatusCode()` method:

```php
--8<--
outgoing/response/002.php
--8<--
```

You can set format an array into either JSON or XML and set the content type header to the appropriate mime with the `setJSON()` and `setXML()` methods. Typically, you will send an array of data to be converted:

```php
--8<--
outgoing/response/003.php
--8<--
```

### Setting Headers

#### setHeader()

Often, you will need to set headers to be set for the response. The Response class makes this very simple to do, with the `setHeader()` method.

The first parameter is the name of the header. The second parameter is the value, which can be either a string or an array of values that will be combined correctly when sent to the client.

```php
--8<--
outgoing/response/004.php
--8<--
```

Using these functions instead of using the native PHP functions allows you to ensure that no headers are sent prematurely, causing errors, and makes testing possible.

!!! note "Note"
    This method just sets headers to the response instance. So, if you create and return another response instance (e.g., if you call `redirect()`), the headers set here will not be sent automatically.

#### appendHeader()

If the header exists and can have more than one value, you may use the `appendHeader()` and `prependHeader()` methods to add the value to the end or beginning of the values list, respectively. The first parameter is the name of the header, while the second is the value to append or prepend.

```php
--8<--
outgoing/response/005.php
--8<--
```

#### removeHeader()

Headers can be removed from the response with the `removeHeader()` method, which takes the header name as the only parameter. This is not case-sensitive.

```php
--8<--
outgoing/response/006.php
--8<--
```

## Redirect

If you want to create a redirect, use the `redirect()` function.

It returns a `RedirectResponse` instance. It is a different instance from the global response instance that `Services::response()` returns.

!!! warning "Warning"
    If you set Cookies or Response Headers before you call `redirect()`, they are set to the global response instance, and they are not automatically copied to the `RedirectResponse` instance. To send them, you need to call the `withCookies()` or `withHeaders()` method manually.

!!! important "Important"
    If you want to redirect, an instance of `RedirectResponse` must be returned in a method of the [Controller](#../incoming/controllers) or the [Controller Filter](#../incoming/filters). Note that the `__construct()` or the `initController()` method cannot return any value. If you forget to return `RedirectResponse`, no redirection will occur.

### Redirect to a URI path

When you want to pass a URI path (relative to baseURL), use `redirect()->to()`:

```php
--8<--
outgoing/./response/028.php:2:
--8<--
```

!!! note "Note"
    If there is a fragment in your URL that you want to remove, you can use the refresh parameter in the method. Like `return redirect()->to('admin/home', null, 'refresh');`.

### Redirect to a Defined Route

When you want to pass a [route name](#using-named-routes) or Controller::method for [reverse routing](#reverse-routing), use `redirect()->route()`:

```php
--8<--
outgoing/./response/029.php:2:
--8<--
```

When passing an argument into the function, it is treated as a route name or Controller::method for reverse routing, not a relative/full URI, treating it the same as using `redirect()->route()`:

```php
--8<--
outgoing/./response/030.php:2:
--8<--
```

### Redirect Back

When you want to redirect back, use `redirect()->back()`:

```php
--8<--
outgoing/./response/031.php:2:
--8<--
```

!!! note "Note"
    `redirect()->back()` is not the same as browser "back" button. It takes a visitor to "the last page viewed during the Session" when the Session is available. If the Session hasn't been loaded, or is otherwise unavailable, then a sanitized version of HTTP_REFERER will be used.

### Redirect with Cookies

If you set Cookies before you call `redirect()`, they are set to the global response instance, and they are not automatically copied to the `RedirectResponse` instance.

To send the Cookies, you need to call the `withCookies()` method manually.

```php
--8<--
outgoing/./response/034.php:2:
--8<--
```

### Redirect with Headers

If you set Response Headers before you call `redirect()`, they are set to the global response instance, and they are not automatically copied to the `RedirectResponse` instance.

To send the Headers, you need to call the `withHeaders()` method manually.

```php
--8<--
outgoing/./response/035.php:2:
--8<--
```

### Redirect Status Code

The default HTTP status code for GET requests is 302. However, when using HTTP/1.1 or later, 303 is used for POST/PUT/DELETE requests and 307 for all other requests.

You can specify the status code:

```php
--8<--
outgoing/./response/032.php:2:
--8<--
```

!!! note "Note"
    Due to a bug, in v4.3.3 or previous versions, the status code of the actual redirect response might be changed even if a status code was specified. See [ChangeLog v4.3.4](#v434-redirect-status-code).

If you don't know HTTP status code for redirection, it is recommended to read [Redirections in HTTP](https://developer.mozilla.org/en-US/docs/Web/HTTP/Redirections).

## Force File Download

The Response class provides a simple way to send a file to the client, prompting the browser to download the data to your computer. This sets the appropriate headers to make it happen.

The first parameter is the **name you want the downloaded file to be named**, the second parameter is the file data.

If you set the second parameter to null and `$filename` is an existing, readable file path, then its content will be read instead.

If you set the third parameter to boolean true, then the actual file MIME type (based on the filename extension) will be sent, so that if your browser has a handler for that type - it can use it.

Example:

```php
--8<--
outgoing/response/007.php
--8<--
```

If you want to download an existing file from your server you'll need to pass `null` explicitly for the second parameter:

```php
--8<--
outgoing/response/008.php
--8<--
```

Use the optional `setFileName()` method to change the filename as it is sent to the client's browser:

```php
--8<--
outgoing/response/009.php
--8<--
```

!!! note "Note"
    The response object MUST be returned for the download to be sent to the client. This allows the response to be passed through all **after** filters before being sent to the client.

### Open File in Browser

Some browsers can display files such as PDF. To tell the browser to display the file instead of saving it, call the `DownloadResponse::inline()` method.

```php
--8<--
outgoing/response/033.php
--8<--
```

## HTTP Caching

Built into the HTTP specification are tools help the client (often the web browser) cache the results. Used correctly, this can lead to a huge performance boost to your application because it will tell the client that they don't need to contact the server at all since nothing has changed. And you can't get faster than that.

This are handled through the `Cache-Control` and `ETag` headers. This guide is not the proper place for a thorough introduction to all of the cache headers power, but you can get a good understanding over at [Google Developers](https://developers.google.com/web/fundamentals/performance/optimizing-content-efficiency/http-caching).

By default, all response objects sent through CodeIgniter have HTTP caching turned off. The options and exact circumstances are too varied for us to be able to create a good default other than turning it off. It's simple to set the Cache values to what you need, through the `setCache()` method:

```php
--8<--
outgoing/response/010.php
--8<--
```

The `$options` array simply takes an array of key/value pairs that are, with a couple of exceptions, assigned to the `Cache-Control` header. You are free to set all of the options exactly as you need for your specific situation. While most of the options are applied to the `Cache-Control` header, it intelligently handles the `etag` and `last-modified` options to their appropriate header.

## Class Reference

!!! note "Note"
    In addition to the methods listed here, this class inherits the methods from the [Message Class](#/incoming/message).

The methods provided by the parent class that are available are:

- `CodeIgniter\\HTTP\\Message::body`

- `CodeIgniter\\HTTP\\Message::setBody`

- `CodeIgniter\\HTTP\\Message::populateHeaders`

- `CodeIgniter\\HTTP\\Message::headers`

- `CodeIgniter\\HTTP\\Message::header`

- `CodeIgniter\\HTTP\\Message::headerLine`

- `CodeIgniter\\HTTP\\Message::setHeader`

- `CodeIgniter\\HTTP\\Message::removeHeader`

- `CodeIgniter\\HTTP\\Message::appendHeader`

- `CodeIgniter\\HTTP\\Message::protocolVersion`

- `CodeIgniter\\HTTP\\Message::setProtocolVersion`

- `CodeIgniter\\HTTP\\Message::negotiateMedia`

- `CodeIgniter\\HTTP\\Message::negotiateCharset`

- `CodeIgniter\\HTTP\\Message::negotiateEncoding`

- `CodeIgniter\\HTTP\\Message::negotiateLanguage`

- `CodeIgniter\\HTTP\\Message::negotiateLanguage`

## CodeIgniter\HTTP

### Response

#### getStatusCode()

- **Returns**: The current HTTP status code for this response

- **Return type**: `int`

Returns the currently status code for this response. If no status code has been set, a BadMethodCallException will be thrown:

```php
--8<--
outgoing/response/014.php
--8<--
```

#### setStatusCode($code\[, $reason=''])

- **Parameters**  

- **$code** `int` The HTTP status code

- **$reason** `string` An optional reason phrase.

- **Returns**: The current Response instance

- **Return type**: ``CodeIgniterHTTPResponse``

Sets the HTTP status code that should be sent with this response:

```php
--8<--
outgoing/response/015.php
--8<--
```

The reason phrase will be automatically generated based upon the official lists. If you need to set your own for a custom status code, you can pass the reason phrase as the second parameter:

```php
--8<--
outgoing/response/016.php
--8<--
```

#### getReasonPhrase()

- **Returns**: The current reason phrase.

- **Return type**: `string`

Returns the current status code for this response. If not status has been set, will return an empty string:

```php
--8<--
outgoing/response/017.php
--8<--
```

#### setDate($date)

- **Parameters**  

- **$date** `DateTime` A DateTime instance with the time to set for this response.

- **Returns**: The current response instance.

- **Return type**: ``CodeIgniterHTTPResponse``

Sets the date used for this response. The `$date` argument must be an instance of `DateTime`.

#### setContentType($mime\[, $charset='UTF-8'])

- **Parameters**  

- **$mime** `string` The content type this response represents.

- **$charset** `string` The character set this response uses.

- **Returns**: The current response instance.

- **Return type**: ``CodeIgniterHTTPResponse``

Sets the content type this response represents:

```php
--8<--
outgoing/response/019.php
--8<--
```

By default, the method sets the character set to `UTF-8`. If you need to change this, you can pass the character set as the second parameter:

```php
--8<--
outgoing/response/020.php
--8<--
```

#### noCache()

- **Returns**: The current response instance.

- **Return type**: ``CodeIgniterHTTPResponse``

Sets the `Cache-Control` header to turn off all HTTP caching. This is the default setting of all response messages:

```php
--8<--
outgoing/response/021.php
--8<--
```

#### setCache($options)

- **Parameters**  

- **$options** `array` An array of key/value cache control settings

- **Returns**: The current response instance.

- **Return type**: ``CodeIgniterHTTPResponse``

Sets the `Cache-Control` headers, including `ETags` and `Last-Modified`. Typical keys are:

- etag

- last-modified

- max-age

- s-maxage

- private

- public

- must-revalidate

- proxy-revalidate

- no-transform

When passing the last-modified option, it can be either a date string, or a DateTime object.

#### setLastModified($date)

- **Parameters**  

- **$date** `string|DateTime` The date to set the Last-Modified header to

- **Returns**: The current response instance.

- **Return type**: ``CodeIgniterHTTPResponse``

Sets the `Last-Modified` header. The `$date` object can be either a string or a `DateTime` instance:

```php
--8<--
outgoing/response/022.php
--8<--
```

#### send(): Response

- **Returns**: The current response instance.

- **Return type**: ``CodeIgniterHTTPResponse``

Tells the response to send everything back to the client. This will first send the headers, followed by the response body. For the main application response, you do not need to call this as it is handled automatically by CodeIgniter.

#### setCookie($name = ''\[, $value = ''\[, $expire = 0\[, $domain = ''\[, $path = '/'\[, $prefix = ''\[, $secure = false\[, $httponly = false\[, $samesite = null]]]]]]]])

- **Parameters**  

- **$name** `array|Cookie|string` Cookie name *or* associative array of all of the parameters available to this method *or* an instance of `CodeIgniter\Cookie\Cookie`

- **$value** `string` Cookie value

- **$expire** `int` Cookie expiration time in seconds. If set to `0` the cookie will only last as long as the browser is open

- **$domain** `string` Cookie domain

- **$path** `string` Cookie path

- **$prefix** `string` Cookie name prefix. If set to `''`, the default value from **app/Config/Cookie.php** will be used

- **$secure** `bool` Whether to only transfer the cookie through HTTPS. If set to `null`, the default value from **app/Config/Cookie.php** will be used

- **$httponly** `bool` Whether to only make the cookie accessible for HTTP requests (no JavaScript). If set to `null`, the default value from **app/Config/Cookie.php** will be used

- **$samesite** `string` The value for the SameSite cookie parameter. If set to `''`, no SameSite attribute will be set on the cookie. If set to `null`, the default value from **app/Config/Cookie.php** will be used

- **Return type**: `void`

!!! note "Note"
    Prior to v4.2.7, the default values of `$secure` and `$httponly` were `false`

due to a bug, and these values from **app/Config/Cookie.php** were never used.

Sets a cookie containing the values you specify to the Response instance.

There are two ways to pass information to this method so that a cookie can be set: Array Method, and Discrete Parameters:

**Array Method**

Using this method, an associative array is passed as the first parameter:

```php
--8<--
outgoing/response/023.php
--8<--
```

Only the `name` and `value` are required. To delete a cookie set it with the `value` blank.

The `expire` is set in **seconds**, which will be added to the current time. Do not include the time, but rather only the number of seconds from *now* that you wish the cookie to be valid. If the `expire` is set to zero the cookie will only last as long as the browser is open.

!!! note "Note"
    But if the `value` is set to empty string and the `expire` is set to `0`,

the cookie will be deleted.

For site-wide cookies regardless of how your site is requested, add your URL to the `domain` starting with a period, like this: .your-domain.com

The `path` is usually not needed since the method sets a root path.

The `prefix` is only needed if you need to avoid name collisions with other identically named cookies for your server.

The `secure` flag is only needed if you want to make it a secure cookie by setting it to `true`.

The `samesite` value controls how cookies are shared between domains and sub-domains. Allowed values are `'None'`, `'Lax'`, `'Strict'` or a blank string `''`. If set to blank string, default SameSite attribute will be set.

**Discrete Parameters**

If you prefer, you can set the cookie by passing data using individual parameters:

```php
--8<--
outgoing/response/024.php
--8<--
```

#### deleteCookie($name = ''\[, $domain = ''\[, $path = '/'\[, $prefix = '']]])

- **Parameters**  

- **$name** `mixed` Cookie name or an array of parameters

- **$domain** `string` Cookie domain

- **$path** `string` Cookie path

- **$prefix** `string` Cookie name prefix

- **Return type**: `void`

Delete an existing cookie.

!!! note "Note"
    This also just sets browser cookie for deleting the cookie.

Only the `name` is required.

The `prefix` is only needed if you need to avoid name collisions with other identically named cookies for your server.

Provide a `prefix` if cookies should only be deleted for that subset. Provide a `domain` name if cookies should only be deleted for that domain. Provide a `path` name if cookies should only be deleted for that path.

If any of the optional parameters are empty, then the same-named cookie will be deleted across all that apply.

Example:

```php
--8<--
outgoing/response/025.php
--8<--
```

#### hasCookie($name = ''\[, $value = null\[, $prefix = '']])

- **Parameters**  

- **$name** `mixed` Cookie name or an array of parameters

- **$value** `string` cookie value

- **$prefix** `string` Cookie name prefix

- **Return type**: `bool`

Checks to see if the Response has a specified cookie or not.

**Notes**

Only the `name` is required. If a `prefix` is specified, it will be prepended to the cookie name.

If no `value` is given, the method just checks for the existence of the named cookie. If a `value` is given, then the method checks that the cookie exists, and that it has the prescribed value.

Example:

```php
--8<--
outgoing/response/026.php
--8<--
```

#### getCookie($name = ''\[, $prefix = ''])

- **Parameters**  

- **$name** `string` Cookie name

- **$prefix** `string` Cookie name prefix

- **Return type**: ``Cookie[|Cookie\[]|](##SUBST##|Cookie[]|)null``

Returns the named cookie, if found, or `null`. If no `name` is given, returns the array of `Cookie` objects.

Example:

```php
--8<--
outgoing/response/027.php
--8<--
```

#### getCookies()

- **Return type**: ``Cookie\[]``

Returns all cookies currently set within the Response instance. These are any cookies that you have specifically specified to set during the current request only.
