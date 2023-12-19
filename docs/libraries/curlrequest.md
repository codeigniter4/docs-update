# CURLRequest Class

The `CURLRequest` class is a lightweight HTTP client based on CURL that
allows you to talk to other web sites and servers. It can be used to get
the contents of a Google search, retrieve a web page or image, or
communicate with an API, among many other things.

<div class="contents" local="" depth="2">

</div>

This class is modeled after the [Guzzle HTTP
Client](http://docs.guzzlephp.org/en/latest/) library since it is one of
the more widely used libraries. Where possible, the syntax has been kept
the same so that if your application needs something a little more
powerful than what this library provides, you will have to change very
little to move over to use Guzzle.

> [!NOTE]
> This class requires the [cURL
> Library](https://www.php.net/manual/en/book.curl.php) to be installed
> in your version of PHP. This is a very common library that is
> typically available but not all hosts will provide it, so please check
> with your host to verify if you run into problems.

## Config for CURLRequest

### Sharing Options

> [!IMPORTANT]
> This setting exists only for backward compatibility. Do not use it in
> new projects. Even if you are already using it, we recommend that you
> disable it.

> [!NOTE]
> Since v4.4.0, the default value has been changed to `false`.

If you want to share all the options between requests, set
`$shareOptions` to `true` in **app/Config/CURLRequest.php**:

<div class="literalinclude">

curlrequest/001.php

</div>

If you send more than one request with an instance of the class, this
behavior may cause an error request with unnecessary headers and body.

> [!NOTE]
> Before v4.2.0, the request body is not reset even if `$shareOptions`
> is false due to a bug.

## Loading the Library

The library can be loaded either manually or through the
`Services class </concepts/services>`.

To load with the Services class call the `curlrequest()` method:

<div class="literalinclude">

curlrequest/002.php

</div>

You can pass in an array of default options as the first parameter to
modify how cURL will handle the request. The options are described later
in this document:

<div class="literalinclude">

curlrequest/003.php

</div>

> [!NOTE]
> When `$shareOptions` is false, the default options passed to the class
> constructor will be used for all requests. Other options will be reset
> after sending a request.

When creating the class manually, you need to pass a few dependencies
in. The first parameter is an instance of the `Config\App` class. The
second parameter is a URI instance. The third parameter is a Response
object. The fourth parameter is the optional default `$options` array:

<div class="literalinclude">

curlrequest/004.php

</div>

## Working with the Library

Working with CURL requests is simply a matter of creating the Request
and getting a `Response object </outgoing/response>` back. It is meant
to handle the communications. After that you have complete control over
how the information is handled.

### Making Requests

Most communication is done through the `request()` method, which fires
off the request, and then returns a Response instance to you. This takes
the HTTP method, the url and an array of options as the parameters.

<div class="literalinclude">

curlrequest/005.php

</div>

> [!IMPORTANT]
> By default, CURLRequest will throw `HTTPException` if the HTTP code
> returned is greater than or equal to 400. If you want to get the
> response, see the [http_errors](#http_errors) option.

> [!NOTE]
> When `$shareOptions` is false, the options passed to the method will
> be used for the request. After sending the request, they will be
> cleared. If you want to use the options to all requests, pass the
> options in the constructor.

Since the response is an instance of `CodeIgniter\HTTP\Response` you
have all of the normal information available to you:

<div class="literalinclude">

curlrequest/006.php

</div>

While the `request()` method is the most flexible, you can also use the
following shortcut methods. They each take the URL as the first
parameter and an array of options as the second:

<div class="literalinclude">

curlrequest/007.php

</div>

#### Base URI

A `baseURI` can be set as one of the options during the instantiation of
the class. This allows you to set a base URI, and then make all requests
with that client using relative URLs. This is especially handy when
working with APIs:

<div class="literalinclude">

curlrequest/008.php

</div>

When a relative URI is provided to the `request()` method or any of the
shortcut methods, it will be combined with the baseURI according to the
rules described by [RFC 2986, section
2](https://tools.ietf.org/html/rfc3986#section-5.2). To save you some
time, here are some examples of how the combinations are resolved.

> | baseURI             | URI            | Result                 |
> |---------------------|----------------|------------------------|
> | http://foo.com      | /bar           | http://foo.com/bar     |
> | http://foo.com/foo  | /bar           | http://foo.com/bar     |
> | http://foo.com/foo  | bar            | http://foo.com/bar     |
> | http://foo.com/foo/ | bar            | http://foo.com/foo/bar |
> | http://foo.com      | http://baz.com | http://baz.com         |
> | http://foo.com/?bar | bar            | http://foo.com/bar     |

### Using Responses

Each `request()` call returns a Response object that contains a lot of
useful information and some helpful methods. The most commonly used
methods let you determine the response itself.

You can get the status code and reason phrase of the response:

<div class="literalinclude">

curlrequest/009.php

</div>

You can retrieve headers from the response:

<div class="literalinclude">

curlrequest/010.php

</div>

The body can be retrieved using the `getBody()` method:

<div class="literalinclude">

curlrequest/011.php

</div>

The body is the raw body provided by the remote server. If the content
type requires formatting, you will need to ensure that your script
handles that:

<div class="literalinclude">

curlrequest/012.php

</div>

## Request Options

This section describes all of the available options you may pass into
the constructor, the `request()` method, or any of the shortcut methods.

### allow_redirects

By default, cURL will follow all "Location:" headers the remote servers
send back. The `allow_redirects` option allows you to modify how that
works.

If you set the value to `false`, then it will not follow any redirects
at all:

<div class="literalinclude">

curlrequest/013.php

</div>

Setting it to `true` will apply the default settings to the request:

<div class="literalinclude">

curlrequest/014.php

</div>

You can pass in array as the value of the `allow_redirects` option to
specify new settings in place of the defaults:

<div class="literalinclude">

curlrequest/015.php

</div>

> [!NOTE]
> Following redirects does not work when PHP is in safe_mode or
> open_basedir is enabled.

### auth

Allows you to provide Authentication details for [HTTP
Basic](https://www.ietf.org/rfc/rfc2069.txt) and
[Digest](https://www.ietf.org/rfc/rfc2069.txt) and authentication. Your
script may have to do extra to support Digest authentication - this
simply passes the username and password along for you. The value must be
an array where the first element is the username, and the second is the
password. The third parameter should be the type of authentication to
use, either `basic` or `digest`:

<div class="literalinclude">

curlrequest/016.php

</div>

### body

There are two ways to set the body of the request for request types that
support them, like PUT, OR POST. The first way is to use the `setBody()`
method:

<div class="literalinclude">

curlrequest/017.php

</div>

The second method is by passing a `body` option in. This is provided to
maintain Guzzle API compatibility, and functions the exact same way as
the previous example. The value must be a string:

<div class="literalinclude">

curlrequest/018.php

</div>

### cert

To specify the location of a PEM formatted client-side certificate, pass
a string with the full path to the file as the `cert` option. If a
password is required, set the value to an array with the first element
as the path to the certificate, and the second as the password:

<div class="literalinclude">

curlrequest/019.php

</div>

### connect_timeout

By default, CodeIgniter does not impose a limit for cURL to attempt to
connect to a website. If you need to modify this value, you can do so by
passing the amount of time in seconds with the `connect_timeout` option.
You can pass 0 to wait indefinitely:

<div class="literalinclude">

curlrequest/020.php

</div>

### cookie

This specifies the filename that CURL should use to read cookie values
from, and to save cookie values to. This is done using the
`CURL_COOKIEJAR` and `CURL_COOKIEFILE` options. An example:

<div class="literalinclude">

curlrequest/021.php

</div>

### debug

When `debug` is passed and set to `true`, this will enable additional
debugging to echo to STDERR during the script execution.

This is done by passing `CURLOPT_VERBOSE` and echoing the output. So,
when you're running a built-in server via `spark serve`, you will see
the output in the console. Otherwise, the output will be written to the
server's error log.

<div class="literalinclude">

curlrequest/034.php

</div>

You can pass a filename as the value for debug to have the output
written to a file:

<div class="literalinclude">

curlrequest/022.php

</div>

### delay

Allows you to pause a number of milliseconds before sending the request:

<div class="literalinclude">

curlrequest/023.php

</div>

### form_params

You can send form data in an application/x-www-form-urlencoded POST
request by passing an associative array in the `form_params` option.
This will set the `Content-Type` header to
`application/x-www-form-urlencoded` if it's not already set:

<div class="literalinclude">

curlrequest/024.php

</div>

> [!NOTE]
> `form_params` cannot be used with the `multipart` option. You will
> need to use one or the other. Use `form_params` for
> `application/x-www-form-urlencoded` request, and `multipart` for
> `multipart/form-data` requests.

### headers

While you can set any headers this request needs by using the
`setHeader()` method, you can also pass an associative array of headers
in as an option. Each key is the name of a header, and each value is a
string or array of strings representing the header field values:

<div class="literalinclude">

curlrequest/025.php

</div>

If headers are passed into the constructor they are treated as default
values that will be overridden later by any further headers arrays or
calls to `setHeader()`.

### http_errors

By default, CURLRequest will throw `HTTPException` if the HTTP code
returned is greater than or equal to 400.

If you want to see the response body, you can set `http_errors` to
`false` to return the content instead:

<div class="literalinclude">

curlrequest/026.php

</div>

### json

The `json` option is used to easily upload JSON encoded data as the body
of a request. A Content-Type header of `application/json` is added,
overwriting any Content-Type that might be already set. The data
provided to this option can be any value that `json_encode()` accepts:

<div class="literalinclude">

curlrequest/027.php

</div>

> [!NOTE]
> This option does not allow for any customization of the
> `json_encode()` function, or the Content-Type header. If you need that
> ability, you will need to encode the data manually, passing it through
> the `setBody()` method of CURLRequest, and set the Content-Type header
> with the `setHeader()` method.

### multipart

When you need to send files and other data via a POST request, you can
use the `multipart` option, along with the [CURLFile
Class](https://www.php.net/manual/en/class.curlfile.php). The values
should be an associative array of POST data to send. For safer usage,
the legacy method of uploading files by prefixing their name with an
<span class="title-ref">@</span> has been disabled. Any files that you
want to send must be passed as instances of CURLFile:

<div class="literalinclude">

curlrequest/028.php

</div>

> [!NOTE]
> `multipart` cannot be used with the `form_params` option. You can only
> use one or the other. Use `form_params` for
> `application/x-www-form-urlencoded` requests, and `multipart` for
> `multipart/form-data` requests.

### proxy

<div class="versionadded">

4.4.0

</div>

You can set a proxy by passing an associative array as the `proxy`
option:

<div class="literalinclude">

curlrequest/035.php

</div>

### query

You can pass along data to send as query string variables by passing an
associative array as the `query` option:

<div class="literalinclude">

curlrequest/029.php

</div>

### timeout

By default, cURL functions are allowed to run as long as they take, with
no time limit. You can modify this with the `timeout` option. The value
should be the number of seconds you want the functions to execute for.
Use 0 to wait indefinitely:

<div class="literalinclude">

curlrequest/030.php

</div>

### user_agent

Allows specifying the User Agent for requests:

<div class="literalinclude">

curlrequest/031.php

</div>

### verify

This option describes the SSL certificate verification behavior. If the
`verify` option is `true`, it enables the SSL certificate verification
and uses the default CA bundle provided by the operating system. If set
to `false` it will disable the certificate verification (this is
insecure, and allows man-in-the-middle attacks!). You can set it to a
string that contains the path to a CA bundle to enable verification with
a custom certificate. The default value is true:

<div class="literalinclude">

curlrequest/032.php

</div>

### version

To set the HTTP protocol to use, you can pass a string or float with the
version number (typically either `1.0` or `1.1`, `2.0` is supported
since v4.3.0.):

<div class="literalinclude">

curlrequest/033.php

</div>
