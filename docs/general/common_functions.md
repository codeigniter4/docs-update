# Global Functions and Constants

CodeIgniter provides a few functions and variables that are globally defined, and are available to you at any point. These do not require loading any additional libraries or helpers.

- [Global Functions](#global-functions)
    - [Service Accessors](#service-accessors)
    - [Miscellaneous Functions](#miscellaneous-functions)
- [Global Constants](#global-constants)
    - [Core Constants](#core-constants)
    - [Time Constants](#time-constants)

## Global Functions

### Service Accessors

#### cache(\[$key])

- **Parameters**  

- **$key** `string` The cache name of the item to retrieve from cache (Optional)

- **Returns**: Either the cache object, or the item retrieved from the cache

- **Return type**: `mixed`

If no $key is provided, will return the Cache engine instance. If a $key is provided, will return the value of $key as stored in the cache currently, or null if no value is found.

Examples:

```php
--8<--
general/common_functions/001.php
--8<--
```

#### config(string $name\[, bool $getShared = true])

- **Parameters**  

- **$name** `string` The config classname.

- **$getShared** `bool` Whether to return a shared instance.

- **Returns**: The config instances.

- **Return type**: `object|null`

More simple way of getting config instances from Factories.

See [Configuration](#configuration-config) and [Factories](#factories-config) for details.

The `config()` uses `Factories::config()` internally. See `factories-loading-class` for details on the first parameter `$name`.

#### cookie(string $name\[, string $value = ''\[, array $options = \[]]])

- **Parameters**  

- **$name** `string` Cookie name

- **$value** `string` Cookie value

- **$options** `array` Cookie options

- **Return type**: ``Cookie``

- **Returns**: `Cookie` instance

- **Throws**: `CookieException`

Simpler way to create a new Cookie instance.

#### cookies(\[array $cookies = \[]\[, bool $getGlobal = true]])

- **Parameters**  

- **$cookies** `array` If `getGlobal` is `false`, this is passed to `CookieStore`'s constructor.

- **$getGlobal** `bool` If `false`, creates a new instance of `CookieStore`.

- **Return type**: ``CookieStore``

- **Returns**: Instance of `CookieStore` saved in the current `Response`, or a new `CookieStore` instance.

Fetches the global `CookieStore` instance held by `Response`.

#### env($key\[, $default = null])

- **Parameters**  

- **$key** `string` The name of the environment variable to retrieve

- **$default** `mixed` The default value to return if no value is found.

- **Returns**: The environment variable, the default value, or null.

- **Return type**: `mixed`

Used to retrieve values that have previously been set to the environment, or return a default value if it is not found. Will format boolean values to actual booleans instead of string representations.

Especially useful when used in conjunction with **.env** files for setting values that are specific to the environment itself, like database settings, API keys, etc.

#### esc($data\[, $context = 'html'\[, $encoding]])

- **Parameters**  

- **$data** `string|array` The information to be escaped.

- **$context** `string` The escaping context. Default is 'html'.

- **$encoding** `string` The character encoding of the string.

- **Returns**: The escaped data.

- **Return type**: `mixed`

Escapes data for inclusion in web pages, to help prevent XSS attacks. This uses the Laminas Escaper library to handle the actual filtering of the data.

If $data is a string, then it simply escapes and returns it. If $data is an array, then it loops over it, escaping each 'value' of the key/value pairs.

Valid context values: `html`, `js`, `css`, `url`, `attr`, `raw`

#### helper($filename)

- **Parameters**  

- **$filename** `string|array` The name of the helper file to load, or an array of names.

Loads a helper file.

For full details, see the [Helpers](helpers.md) page.

#### lang($line\[, $args\[, $locale]])

- **Parameters**  

- **$line** `string` The line of text to retrieve

- **$args** `array` An array of data to substitute for placeholders.

- **$locale** `string` Specify a different locale to be used instead of default one.

Retrieves a locale-specific file based on an alias string.

For more information, see the [Localization](#/outgoing/localization) page.

#### model($name\[, $getShared = true\[, &$conn = null]])

- **Parameters**  

- **$name** `string` The model classname.

- **$getShared** `boolean` Whether to return a shared instance.

- **$conn** `ConnectionInterface|null` The database connection.

- **Returns**: The model instances

- **Return type**: `object`

More simple way of getting model instances.

The `model()` uses `Factories::models()` internally. See `factories-loading-class` for details on the first parameter `$name`.

See also the [Using CodeIgniter's Model](#accessing-models).

#### old($key\[, $default = null,\[, $escape = 'html']])

- **Parameters**  

- **$key** `string` The name of the old form data to check for.

- **$default** `string|null` The default value to return if $key doesn't exist.

- **$escape** `false|string` An \[escape](##esc) context or false to disable it.

- **Returns**: The value of the defined key, or the default value.

- **Return type**: `array|string|null`

Provides a simple way to access "old input data" from submitting a form.

Example:

```php
--8<--
general/common_functions/002.php
--8<--
```

!!! note "Note"
    If you are using the `set_value()`, `set_select()`, `set_checkbox()`, and `set_radio()` functions in [form helper](#/helpers/form-helper), this feature is built-in. You only need to use this function when not using the form helper.

#### session(\[$key])

- **Parameters**  

- **$key** `string` The name of the session item to check for.

- **Returns**: An instance of the Session object if no $key, the value found in the session for $key, or null.

- **Return type**: `mixed`

Provides a convenient way to access the session class and to retrieve a stored value. For more information, see the [Sessions](#/libraries/sessions) page.

#### timer(\[$name])

- **Parameters**  

- **$name** `string` The name of the benchmark point.

- **Returns**: The Timer instance

- **Return type**: `CodeIgniter\Debug\Timer`

A convenience method that provides quick access to the Timer class. You can pass in the name of a benchmark point as the only parameter. This will start timing from this point, or stop timing if a timer with this name is already running.

Example:

```php
--8<--
general/common_functions/003.php
--8<--
```

#### view($name\[, $data\[, $options]])

- **Parameters**  

- **$name** `string` The name of the file to load

- **$data** `array` An array of key/value pairs to make available within the view.

- **$options** `array` An array of options that will be passed to the rendering class.

- **Returns**: The output from the view.

- **Return type**: `string`

Grabs the current RendererInterface-compatible class and tells it to render the specified view. Simply provides a convenience method that can be used in Controllers, libraries, and routed closures.

Currently, these options are available for use within the `$options` array:

- `saveData` specifies that data will persistent between multiple calls to `view()` within the same request. If you do not want the data to be persisted, specify false.

- `cache` specifies the number of seconds to cache the view for. See `caching-views` for the details.

- `debug` can be set to false to disable the addition of debug code for [Debug Toolbar](#the-debug-toolbar).

The `$option` array is provided primarily to facilitate third-party integrations with libraries like Twig.

Example:

```php
--8<--
general/common_functions/004.php
--8<--
```

For more details, see the [Views](#/outgoing/views) page.

#### view_cell($library\[, $params = null\[, $ttl = 0\[, $cacheName = null]]])

- **Parameters**  

- string `$library`

- null `$params`

- integer `$ttl`

- string|null `$cacheName`

- **Returns**: View cells are used within views to insert HTML chunks that are managed by other classes.

- **Return type**: `string`

For more details, see the [View Cells](#/outgoing/view-cells) page.

### Miscellaneous Functions

#### app_timezone()

- **Returns**: The timezone the application has been set to display dates in.

- **Return type**: `string`

Returns the timezone the application has been set to display dates in.

#### csp_script_nonce()

- **Returns**: The CSP nonce attribute for script tag.

- **Return type**: `string`

Returns the nonce attribute for a script tag. For example: `nonce="Eskdikejidojdk978Ad8jf"`. See `content-security-policy`.

#### csp_style_nonce()

- **Returns**: The CSP nonce attribute for style tag.

- **Return type**: `string`

Returns the nonce attribute for a style tag. For example: `nonce="Eskdikejidojdk978Ad8jf"`. See `content-security-policy`.

#### csrf_token()

- **Returns**: The name of the current CSRF token.

- **Return type**: `string`

Returns the name of the current CSRF token.

#### csrf_header()

- **Returns**: The name of the header for current CSRF token.

- **Return type**: `string`

The name of the header for current CSRF token.

#### csrf_hash()

- **Returns**: The current value of the CSRF hash.

- **Return type**: `string`

Returns the current CSRF hash value.

#### csrf_field()

- **Returns**: A string with the HTML for hidden input with all required CSRF information.

- **Return type**: `string`

Returns a hidden input with the CSRF information already inserted:

    <input type="hidden" name="{csrf_token}" value="{csrf_hash}">

#### csrf_meta()

- **Returns**: A string with the HTML for meta tag with all required CSRF information.

- **Return type**: `string`

Returns a meta tag with the CSRF information already inserted:

    <meta name="{csrf_header}" content="{csrf_hash}">

#### force_https($duration = 31536000\[, $request = null\[, $response = null]])

- **Parameters**  

- **$duration** `int` The number of seconds browsers should convert links to this resource to HTTPS.

- **$request** `RequestInterface` An instance of the current Request object.

- **$response** `ResponseInterface` An instance of the current Response object.

Checks to see if the page is currently being accessed via HTTPS. If it is, then nothing happens. If it is not, then the user is redirected back to the current URI but through HTTPS. Will set the HTTP Strict Transport Security (HTST) header, which instructs modern browsers to automatically modify any HTTP requests to HTTPS requests for the `$duration`.

!!! note "Note"
    This function is also used when you set

`Config\App:$forceGlobalSecureRequests` to true.

#### function_usable($function_name)

- **Parameters**  

- **$function_name** `string` Function to check for

- **Returns**: true if the function exists and is safe to call, false otherwise.

- **Return type**: `bool`

#### is_cli()

- **Returns**: true if the script is being executed from the command line or false otherwise.

- **Return type**: `bool`

#### is_really_writable($file)

- **Parameters**  

- **$file** `string` The filename being checked.

- **Returns**: true if you can write to the file, false otherwise.

- **Return type**: `bool`

#### is_windows(\[$mock = null])

- **Parameters**  

- **$mock** `bool|null` If given and is a boolean then it will be used as the return value.

- **Return type**: `bool`

Detect if platform is running in Windows.

!!! note "Note"
    The boolean value provided to $mock will persist in subsequent calls. To reset this

mock value, the user must pass an explicit `null` to the function call. This will refresh the function to use auto-detection.

```php
--8<--
general/common_functions/012.php
--8<--
```

#### log_message($level, $message \[, $context])

- **Parameters**  

- **$level** `string` The level of severity

- **$message** `string` The message that is to be logged.

- **$context** `array` An associative array of tags and their values that should be replaced in $message

- **Returns**: void

- **Return type**: `bool`

!!! note "Note"
    Since v4.5.0, the return value is fixed to be compatible with PSR

Log. In previous versions, it returned `true` if was logged successfully or `false` if there was a problem logging it.

Logs a message using the Log Handlers defined in **app/Config/Logger.php**.

Level can be one of the following values: `emergency`, `alert`, `critical`, `error`, `warning`, `notice`, `info`, or `debug`.

Context can be used to substitute values in the message string. For full details, see the [Logging Information](../general/logging.md) page.

#### redirect(string $route)

- **Parameters**  

- **$route** `string` The route name or Controller::method to redirect the user to.

- **Return type**: `RedirectResponse`

Returns a RedirectResponse instance allowing you to easily create redirects. See `response-redirect` for details.

#### remove_invisible_characters($str\[, $urlEncoded = true])

- **Parameters**  

- **$str** `string` Input string

- **$urlEncoded** `bool` Whether to remove URL-encoded characters as well

- **Returns**: Sanitized string

- **Return type**: `string`

This function prevents inserting null characters between ASCII characters, like Java\0script.

Example:

```php
--8<--
general/common_functions/007.php
--8<--
```

#### request()

!!! success "Available from version 4.3.0"

- **Returns**: The shared Request object.

- **Return type**: `IncomingRequest|CLIRequest`

This function is a wrapper for `Services::request()`.

#### response()

!!! success "Available from version 4.3.0"

- **Returns**: The shared Response object.

- **Return type**: `Response`

This function is a wrapper for `Services::response()`.

#### route_to($method\[, ...$params])

- **Parameters**  

- **$method** `string` Route name or Controller::method

- int|string ...`$params` One or more parameters to be passed to the route. The last parameter allows you to set the locale.

- **Returns**: a route path (URI path relative to baseURL)

- **Return type**: `string`

!!! note "Note"
    This function requires the controller/method to have a route defined in **app/Config/routes.php**.

!!! important "Important"
    `route_to()` returns a *route* path, not a full URI path for your site.

If your **baseURL** contains sub folders, the return value is not the same as the URI to link. In that case, just use `url_to()` instead. See also `urls-url-structure`.

Generates a route for you based on a controller::method combination. Will take parameters into effect, if provided.

```php
--8<--
general/common_functions/009.php
--8<--
```

Generates a route for you based on a route name.

```php
--8<--
general/common_functions/010.php
--8<--
```

Since v4.3.0, when you use `{locale}` in your route, you can optionally specify the locale value as the last parameter.

```php
--8<--
general/common_functions/011.php
--8<--
```

#### service($name\[, ...$params])

- **Parameters**  

- **$name** `string` The name of the service to load

- **$params** `mixed` One or more parameters to pass to the service method.

- **Returns**: An instance of the service class specified.

- **Return type**: `mixed`

Provides easy access to any of the [Services](#../concepts/services) defined in the system. This will always return a shared instance of the class, so no matter how many times this is called during a single request, only one class instance will be created.

Example:

```php
--8<--
general/common_functions/008.php
--8<--
```

#### single_service($name \[, ...$params])

- **Parameters**  

- **$name** `string` The name of the service to load

- **$params** `mixed` One or more parameters to pass to the service method.

- **Returns**: An instance of the service class specified.

- **Return type**: `mixed`

Identical to the **service()** function described above, except that all calls to this function will return a new instance of the class, where **service** returns the same instance every time.

#### slash_item ( $item )

- **Parameters**  

- **$item** `string` Config item name

- **Returns**: The configuration item or null if the item doesn't exist

- **Return type**: `string|null`

Fetch a config file item with slash appended (if not empty)

#### stringify_attributes($attributes \[, $js])

- **Parameters**  

- **$attributes** `mixed` string, array of key value pairs, or object

- **$js** `boolean` true if values do not need quotes (Javascript-style)

- **Returns**: String containing the attribute key/value pairs, comma-separated

- **Return type**: `string`

Helper function used to convert a string, array, or object of attributes to a string.

## Global Constants

The following constants are always available anywhere within your application.

### Core Constants

> The path to the **app** directory.

> The path to the project root directory. Just above `APPPATH`.

> The path to the **system** directory.

> The path to the directory that holds the front controller.

> The path to the **writable** directory.

### Time Constants

> Equals 1.

> Equals 60.

> Equals 3600.

> Equals 86400.

> Equals 604800.

> Equals 2592000.

> Equals 31536000.

> Equals 315360000.
