# IncomingRequest Class

The IncomingRequest class provides an object-oriented representation of
an HTTP request from a client, like a browser. It extends from, and has
access to all the methods of the `Request </incoming/request>` and
`Message </incoming/message>` classes, in addition to the methods listed
below.

<div class="contents" local="" depth="2">

</div>

## Accessing the Request

An instance of the request class already populated for you if the
current class is a descendant of `CodeIgniter\Controller` and can be
accessed as a class property:

<div class="literalinclude">

incomingrequest/001.php

</div>

If you are not within a controller, but still need access to the
application's Request object, you can get a copy of it through the
`Services class </concepts/services>`:

<div class="literalinclude">

incomingrequest/002.php

</div>

It's preferable, though, to pass the request in as a dependency if the
class is anything other than the controller, where you can save it as a
class property:

<div class="literalinclude">

incomingrequest/003.php

</div>

## Determining Request Type

A request could be of several types, including an AJAX request or a
request from the command line. This can be checked with the `isAJAX()`
and `isCLI()` methods:

<div class="literalinclude">

incomingrequest/004.php

</div>

> [!NOTE]
> The `isAJAX()` method depends on the `X-Requested-With` header, which
> in some cases is not sent by default in XHR requests via JavaScript
> (i.e., fetch). See the `AJAX Requests </general/ajax>` section on how
> to avoid this problem.

### is()

<div class="versionadded">

4.3.0

</div>

Since v4.3.0, you can use the `is()` method. It accepts a HTTP method,
`'ajax'`, or `'json'`, and returns boolean.

> [!NOTE]
> HTTP method should be case-sensitive, but the parameter is
> case-insensitive.

<div class="literalinclude">

incomingrequest/040.php

</div>

### getMethod()

You can check the HTTP method that this request represents with the
`getMethod()` method:

<div class="literalinclude">

incomingrequest/005.php

</div>

The HTTP method is case-sensitive, and by convention, standardized
methods are defined in all-uppercase US-ASCII letters.

> [!NOTE]
> Prior to v4.5.0, by default, the method was returned as a lower-case
> string (i.e., `'get'`, `'post'`, etc). But it was a bug.

You can get an lowercase version by wrapping the call in `strtolower()`:

    // Returns 'get'
    $method = strtolower($request->getMethod());

You can also check if the request was made through and HTTPS connection
with the `isSecure()` method:

<div class="literalinclude">

incomingrequest/006.php

</div>

## Retrieving Input

You can retrieve input from `$_GET`, `$_POST`, `$_COOKIE`, `$_SERVER`
and `$_ENV` through the Request object. The data is not automatically
filtered and returns the raw input data as passed in the request.

> [!NOTE]
> It is bad practice to use global variables. Basically, it should be
> avoided and it is recommended to use methods of the Request object.

The main advantages to using these methods instead of accessing them
directly (`$_POST['something']`), is that they will return null if the
item doesn't exist, and you can have the data filtered. This lets you
conveniently use data without having to test whether an item exists
first. In other words, normally you might do something like this:

<div class="literalinclude">

incomingrequest/007.php

</div>

With CodeIgniter's built-in methods you can simply do this:

<div class="literalinclude">

incomingrequest/008.php

</div>

### Getting Data

#### getGet()

The `getGet()` method will pull from `$_GET`.

- `$request->getGet()`

#### getPost()

The `getPost()` method will pull from `$_POST`.

- `$request->getPost()`

#### getCookie()

The `getCookie()` method will pull from `$_COOKIE`.

- `$request->getCookie()`

#### getServer()

The `getServer()` method will pull from `$_SERVER`.

- `$request->getServer()`

#### getEnv()

<div class="deprecated">

4.4.4 This method does not work from the beginning. Use `env()` instead.

</div>

The `getEnv()` method will pull from `$_ENV`.

- `$request->getEnv()`

#### getPostGet()

In addition, there are a few utility methods for retrieving information
from either `$_GET` or `$_POST`, while maintaining the ability to
control the order you look for it:

- `$request->getPostGet()` - checks `$_POST` first, then `$_GET`

#### getGetPost()

- `$request->getGetPost()` - checks `$_GET` first, then `$_POST`

#### getVar()

> [!IMPORTANT]
> This method exists only for backward compatibility. Do not use it in
> new projects. Even if you are already using it, we recommend that you
> use another, more appropriate method.

The `getVar()` method will pull from `$_REQUEST`, so will return any
data from `$_GET`, `$POST`, or `$_COOKIE` (depending on php.ini
[request-order](https://www.php.net/manual/en/ini.core.php#ini.request-order)).

> [!WARNING]
> If you want to validate POST data only, don't use `getVar()`. Newer
> values override older values. POST values may be overridden by the
> cookies if they have the same name, and you set "C" after "P" in
> [request-order](https://www.php.net/manual/en/ini.core.php#ini.request-order).

> [!NOTE]
> If the incoming request has a `Content-Type` header set to
> `application/json`, the `getVar()` method returns the JSON data
> instead of `$_REQUEST` data.

### Getting JSON Data

You can grab the contents of `php://input` as a JSON stream with
`getJSON()`.

> [!NOTE]
> This has no way of checking if the incoming data is valid JSON or not,
> you should only use this method if you know that you're expecting
> JSON.

<div class="literalinclude">

incomingrequest/009.php

</div>

By default, this will return any objects in the JSON data as objects. If
you want that converted to associative arrays, pass in `true` as the
first parameter.

The second and third parameters match up to the `$depth` and `$flags`
arguments of the
[json_decode()](https://www.php.net/manual/en/function.json-decode.php)
PHP function.

### Getting Specific Data from JSON

You can get a specific piece of data from a JSON stream by passing a
variable name into `getJsonVar()` for the data that you want or you can
use "dot" notation to dig into the JSON to get data that is not on the
root level.

<div class="literalinclude">

incomingrequest/010.php

</div>

If you want the result to be an associative array instead of an object,
you can pass true in the second parameter:

<div class="literalinclude">

incomingrequest/011.php

</div>

> [!NOTE]
> See the documentation for `dot_array_search()` in the `Array` helper
> for more information on "dot" notation.

### Retrieving Raw Data (PUT, PATCH, DELETE)

Finally, you can grab the contents of `php://input` as a raw stream with
`getRawInput()`:

<div class="literalinclude">

incomingrequest/012.php

</div>

This will retrieve data and convert it to an array. Like this:

<div class="literalinclude">

incomingrequest/013.php

</div>

You can also use `getRawInputVar()`, to get the specified variable from
raw stream and filter it.

<div class="literalinclude">

incomingrequest/039.php

</div>

### Filtering Input Data

To maintain security of your application, you will want to filter all
input as you access it. You can pass the type of filter to use as the
second parameter of any of these methods. The native `filter_var()`
function is used for the filtering. Head over to the PHP manual for a
list of [valid filter
types](https://www.php.net/manual/en/filter.filters.php).

Filtering a POST variable would look like this:

<div class="literalinclude">

incomingrequest/014.php

</div>

All of the methods mentioned above support the filter type passed in as
the second parameter, with the exception of `getJSON()` and
`getRawInput()`.

## Retrieving Headers

You can get access to any header that was sent with the request with the
`headers()` method, which returns an array of all headers, with the key
as the name of the header, and the value is an instance of
`CodeIgniter\HTTP\Header`:

<div class="literalinclude">

incomingrequest/015.php

</div>

If you only need a single header, you can pass the name into the
`header()` method. This will grab the specified header object in a
case-insensitive manner if it exists. If not, then it will return
`null`:

<div class="literalinclude">

incomingrequest/016.php

</div>

You can always use `hasHeader()` to see if the header existed in this
request:

<div class="literalinclude">

incomingrequest/017.php

</div>

If you need the value of header as a string with all values on one line,
you can use the `getHeaderLine()` method:

<div class="literalinclude">

incomingrequest/018.php

</div>

If you need the entire header, with the name and values in a single
string, simply cast the header as a string:

<div class="literalinclude">

incomingrequest/019.php

</div>

## The Request URL

You can retrieve a `URI </libraries/uri>` object that represents the
current URI for this request through the `$request->getUri()` method.
You can cast this object as a string to get a full URL for the current
request:

<div class="literalinclude">

incomingrequest/020.php

</div>

The object gives you full abilities to grab any part of the request on
it's own:

<div class="literalinclude">

incomingrequest/021.php

</div>

You can work with the current URI string (the path relative to your
baseURL) using the `getRoutePath()`.

> [!NOTE]
> The `getRoutePath()` method can be used since v4.4.0. Prior to v4.4.0,
> the `getPath()` method returned the path relative to your baseURL.

## Uploaded Files

Information about all uploaded files can be retrieved through
`$request->getFiles()`, which returns an array of
`CodeIgniter\HTTP\Files\UploadedFile` instance. This helps to ease the
pain of working with uploaded files, and uses best practices to minimize
any security risks.

<div class="literalinclude">

incomingrequest/023.php

</div>

See `Working with Uploaded Files <uploaded-files-accessing-files>` for
the details.

You can retrieve a single file uploaded on its own, based on the
filename given in the HTML file input:

<div class="literalinclude">

incomingrequest/024.php

</div>

You can retrieve an array of same-named files uploaded as part of a
multi-file upload, based on the filename given in the HTML file input:

<div class="literalinclude">

incomingrequest/025.php

</div>

> [!NOTE]
> The files here correspond to `$_FILES`. Even if a user just clicks
> submit button of a form and does not upload any file, the file will
> still exist. You can check that the file was actually uploaded by the
> `isValid()` method in UploadedFile. See `verify-a-file` for more
> details.

## Content Negotiation

You can easily negotiate content types with the request through the
`negotiate()` method:

<div class="literalinclude">

incomingrequest/026.php

</div>

See the `Content Negotiation </incoming/content_negotiation>` page for
more details.

## Class Reference

> [!NOTE]
> In addition to the methods listed here, this class inherits the
> methods from the `Request Class </incoming/request>` and the
> `Message Class </incoming/message>`.

The methods provided by the parent classes that are available are:

- `CodeIgniter\\HTTP\\Request::getIPAddress`
- `CodeIgniter\\HTTP\\Request::isValidIP`
- `CodeIgniter\\HTTP\\Request::getMethod`
- `CodeIgniter\\HTTP\\Request::setMethod`
- `CodeIgniter\\HTTP\\Request::getServer`
- `CodeIgniter\\HTTP\\Request::getEnv`
- `CodeIgniter\\HTTP\\Request::setGlobal`
- `CodeIgniter\\HTTP\\Request::fetchGlobal`
- `CodeIgniter\\HTTP\\Message::getBody`
- `CodeIgniter\\HTTP\\Message::setBody`
- `CodeIgniter\\HTTP\\Message::appendBody`
- `CodeIgniter\\HTTP\\Message::populateHeaders`
- `CodeIgniter\\HTTP\\Message::headers`
- `CodeIgniter\\HTTP\\Message::header`
- `CodeIgniter\\HTTP\\Message::hasHeader`
- `CodeIgniter\\HTTP\\Message::getHeaderLine`
- `CodeIgniter\\HTTP\\Message::setHeader`
- `CodeIgniter\\HTTP\\Message::removeHeader`
- `CodeIgniter\\HTTP\\Message::appendHeader`
- `CodeIgniter\\HTTP\\Message::prependHeader`
- `CodeIgniter\\HTTP\\Message::getProtocolVersion`
- `CodeIgniter\\HTTP\\Message::setProtocolVersion`

> > returns  
> > True if the request was initiated from the command line, otherwise
> > false.
> >
> > rtype  
> > bool
>
> > returns  
> > True if the request is an AJAX request, otherwise false.
> >
> > rtype  
> > bool
>
> > returns  
> > True if the request is an HTTPS request, otherwise false.
> >
> > rtype  
> > bool
>
> > param string \$index  
> > The name of the variable/key to look for.
> >
> > param int \$filter  
> > The type of filter to apply. A list of filters can be found in
> > [Types of
> > filters](https://www.php.net/manual/en/filter.filters.php).
> >
> > param int \$flags  
> > Flags to apply. A list of flags can be found in [Filter
> > flags](https://www.php.net/manual/en/filter.filters.flags.php).
> >
> > returns  
> > `$_REQUEST` if no parameters supplied, otherwise the REQUEST value
> > if found, or null if not
> >
> > rtype  
> > arrayfloatobjectnull
> >
> > > [!IMPORTANT]
> > > This method exists only for backward compatibility. Do not use it
> > > in new projects. Even if you are already using it, we recommend
> > > that you use another, more appropriate method.
> >
> > This method is identical to `getGet()`, only it fetches REQUEST
> > data.
>
> > param string \$index  
> > The name of the variable/key to look for.
> >
> > param int \$filter  
> > The type of filter to apply. A list of filters can be found in
> > [Types of
> > filters](https://www.php.net/manual/en/filter.filters.php).
> >
> > param int \$flags  
> > Flags to apply. A list of flags can be found in [Filter
> > flags](https://www.php.net/manual/en/filter.filters.flags.php).
> >
> > returns  
> > `$_GET` if no parameters supplied, otherwise the GET value if found,
> > or null if not
> >
> > rtype  
> > arrayfloatobjectnull
> >
> > The first parameter will contain the name of the GET item you are
> > looking for:
> >
> > <div class="literalinclude">
> >
> > incomingrequest/041.php
> >
> > </div>
> >
> > The method returns null if the item you are attempting to retrieve
> > does not exist.
> >
> > The second optional parameter lets you run the data through the
> > PHP's filters. Pass in the desired filter type as the second
> > parameter:
> >
> > <div class="literalinclude">
> >
> > incomingrequest/042.php
> >
> > </div>
> >
> > To return an array of all GET items call without any parameters.
> >
> > To return all GET items and pass them through the filter, set the
> > first parameter to null while setting the second parameter to the
> > filter you want to use:
> >
> > <div class="literalinclude">
> >
> > incomingrequest/043.php
> >
> > </div>
> >
> > To return an array of multiple GET parameters, pass all the required
> > keys as an array:
> >
> > <div class="literalinclude">
> >
> > incomingrequest/044.php
> >
> > </div>
> >
> > Same rule applied here, to retrieve the parameters with filtering,
> > set the second parameter to the filter type to apply:
> >
> > <div class="literalinclude">
> >
> > incomingrequest/045.php
> >
> > </div>
>
> > param string \$index  
> > The name of the variable/key to look for.
> >
> > param int \$filter  
> > The type of filter to apply. A list of filters can be found
> > [here](https://www.php.net/manual/en/filter.filters.php).
> >
> > param int \$flags  
> > Flags to apply. A list of flags can be found
> > [here](https://www.php.net/manual/en/filter.filters.flags.php).
> >
> > returns  
> > `$_POST` if no parameters supplied, otherwise the POST value if
> > found, or null if not
> >
> > rtype  
> > arrayfloatobjectnull
> >
> > This method is identical to `getGet()`, only it fetches POST data.
>
> > param string \$index  
> > The name of the variable/key to look for.
> >
> > param int \$filter  
> > The type of filter to apply. A list of filters can be found in
> > [Types of
> > filters](https://www.php.net/manual/en/filter.filters.php).
> >
> > param int \$flags  
> > Flags to apply. A list of flags can be found in [Filter
> > flags](https://www.php.net/manual/en/filter.filters.flags.php).
> >
> > returns  
> > `$_POST` and `$_GET` combined if no parameters specified (prefer
> > POST value on conflict), otherwise looks for POST value, if nothing
> > found looks for GET value, if no value found returns null
> >
> > rtype  
> > arrayfloatobjectnull
> >
> > This method works pretty much the same way as `getPost()` and
> > `getGet()`, only combined. It will search through both POST and GET
> > streams for data, looking first in POST, and then in GET:
> >
> > <div class="literalinclude">
> >
> > incomingrequest/032.php
> >
> > </div>
> >
> > If no index is specified, it will return both POST and GET streams
> > combined. Although POST data will be preferred in case of name
> > conflict.
>
> > param string \$index  
> > The name of the variable/key to look for.
> >
> > param int \$filter  
> > The type of filter to apply. A list of filters can be found in
> > [Types of
> > filters](https://www.php.net/manual/en/filter.filters.php).
> >
> > param int \$flags  
> > Flags to apply. A list of flags can be found in [Filter
> > flags](https://www.php.net/manual/en/filter.filters.flags.php).
> >
> > returns  
> > `$_GET` and `$_POST` combined if no parameters specified (prefer GET
> > value on conflict), otherwise looks for GET value, if nothing found
> > looks for POST value, if no value found returns null
> >
> > rtype  
> > arrayfloatobjectnull
> >
> > This method works pretty much the same way as `getPost()` and
> > `getGet()`, only combined. It will search through both GET and POST
> > streams for data, looking first in GET, and then in POST:
> >
> > <div class="literalinclude">
> >
> > incomingrequest/033.php
> >
> > </div>
> >
> > If no index is specified, it will return both GET and POST streams
> > combined. Although GET data will be preferred in case of name
> > conflict.
>
> > param arraynull \$index  
> > COOKIE name
> >
> > param int \$filter  
> > The type of filter to apply. A list of filters can be found in
> > [Types of
> > filters](https://www.php.net/manual/en/filter.filters.php).
> >
> > param int \$flags  
> > Flags to apply. A list of flags can be found in [Filter
> > flags](https://www.php.net/manual/en/filter.filters.flags.php).
> >
> > returns  
> > `$_COOKIE` if no parameters supplied, otherwise the COOKIE value if
> > found or null if not
> >
> > rtype  
> > arrayfloatobjectnull
> >
> > This method is identical to `getPost()` and `getGet()`, only it
> > fetches cookie data:
> >
> > <div class="literalinclude">
> >
> > incomingrequest/034.php
> >
> > </div>
> >
> > To return an array of multiple cookie values, pass all the required
> > keys as an array:
> >
> > <div class="literalinclude">
> >
> > incomingrequest/035.php
> >
> > </div>
> >
> > > [!NOTE]
> > > Unlike the `Cookie Helper <../helpers/cookie_helper>` function
> > > `get_cookie()`, this method does NOT prepend your configured
> > > `Config\Cookie::$prefix` value.
>
> > param arraynull \$index  
> > Value name
> >
> > param int \$filter  
> > The type of filter to apply. A list of filters can be found in
> > [Types of
> > filters](https://www.php.net/manual/en/filter.filters.php).
> >
> > param int \$flags  
> > Flags to apply. A list of flags can be found in [Filter
> > flags](https://www.php.net/manual/en/filter.filters.flags.php).
> >
> > returns  
> > `$_SERVER` item value if found, null if not
> >
> > rtype  
> > arrayfloatobjectnull
> >
> > This method is identical to the `getPost()`, `getGet()` and
> > `getCookie()` methods, only it fetches Server data (`$_SERVER`):
> >
> > <div class="literalinclude">
> >
> > incomingrequest/036.php
> >
> > </div>
> >
> > To return an array of multiple `$_SERVER` values, pass all the
> > required keys as an array.
> >
> > <div class="literalinclude">
> >
> > incomingrequest/037.php
> >
> > </div>
>
> > returns  
> > The User Agent string, as found in the SERVER data, or null if not
> > found.
> >
> > rtype  
> > CodeIgniter\HTTP\UserAgent
> >
> > This method returns the User Agent instance from the SERVER data:
> >
> > <div class="literalinclude">
> >
> > incomingrequest/038.php
> >
> > </div>
>
> > returns  
> > The current URI path relative to baseURL
> >
> > rtype  
> > string
> >
> > This method returns the current URI path relative to baseURL.
> >
> > > [!NOTE]
> > > Prior to v4.4.0, this was the safest method to determine the
> > > "current URI", since `IncomingRequest::$uri` might not be aware of
> > > the complete App configuration for base URLs.
>
> > <div class="deprecated">
> >
> > 4.4.0
> >
> > </div>
> >
> > param string \$path  
> > The relative path to use as the current URI
> >
> > returns  
> > This Incoming Request
> >
> > rtype  
> > IncomingRequest
> >
> > > [!NOTE]
> > > Prior to v4.4.0, used mostly just for testing purposes, this
> > > allowed you to set the relative path value for the current request
> > > instead of relying on URI detection. This also updated the
> > > underlying `URI` instance with the new path.
