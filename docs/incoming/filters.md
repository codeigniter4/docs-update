# Controller Filters

<div class="contents" local="" depth="3">

</div>

Controller Filters allow you to perform actions either before or after
the controllers execute. Unlike `events <../extending/events>`, you can
choose the specific URIs or routes in which the filters will be applied
to. Before filters may modify the Request while after filters can act on
and even modify the Response, allowing for a lot of flexibility and
power.

Some common examples of tasks that might be performed with filters are:

- Performing CSRF protection on the incoming requests
- Restricting areas of your site based upon their Role
- Perform rate limiting on certain endpoints
- Display a "Down for Maintenance" page
- Perform automatic content negotiation
- and more...

## Creating a Filter

Filters are simple classes that implement
`CodeIgniter\Filters\FilterInterface`. They contain two methods:
`before()` and `after()` which hold the code that will run before and
after the controller respectively. Your class must contain both methods
but may leave the methods empty if they are not needed. A skeleton
filter class looks like:

<div class="literalinclude">

filters/001.php

</div>

### Before Filters

#### Replacing Request

From any filter, you can return the `$request` object and it will
replace the current Request, allowing you to make changes that will
still be present when the controller executes.

#### Stopping Later Filters

Also, when you have a series of filters you may also want to stop the
execution of the later filters after a certain filter. You can easily do
this by returning **any non-empty** result. If the before filter returns
an empty result, the controller actions or the later filters will still
be executed.

An exception to the non-empty result rule is the `Request` instance.
Returning it in the before filter will not stop the execution but only
replace the current `$request` object.

#### Returning Response

Since before filters are executed prior to your controller being
executed, you may at times want to stop the actions in the controller
from happening.

This is typically used to perform redirects, like in this example:

<div class="literalinclude">

filters/002.php

</div>

If a `Response` instance is returned, the Response will be sent back to
the client and script execution will stop. This can be useful for
implementing rate limiting for APIs. See
`Throttler <../libraries/throttler>` for an example.

### After Filters

After filters are nearly identical to before filters, except that you
can only return the `$response` object, and you cannot stop script
execution. This does allow you to modify the final output, or simply do
something with the final output. This could be used to ensure certain
security headers were set the correct way, or to cache the final output,
or even to filter the final output with a bad words filter.

## Configuring Filters

There are two ways to configure filters when they get run. One is done
in **app/Config/Filters.php**, the other is done in
**app/Config/Routes.php**.

If you want to specify filter to a specific route, use
**app/Config/Routes.php** and see `URI Routing <applying-filters>`.

> [!NOTE]
> The safest way to apply filters is to
> `disable auto-routing <use-defined-routes-only>`, and
> `set filters to routes <applying-filters>`.

### app/Config/Filters.php

The **app/Config/Filters.php** file contains four properties that allow
you to configure exactly when the filters run.

> [!WARNING]
> It is recommended that you should always add `*` at the end of a URI
> in the filter settings. Because a controller method might be
> accessible by different URLs than you think. For example, when
> `auto-routing-legacy` is enabled, if you have `Blog::index`, it can be
> accessible with `blog`, `blog/index`, and `blog/index/1`, etc.

#### \$aliases

The `$aliases` array is used to associate a simple name with one or more
fully-qualified class names that are the filters to run:

<div class="literalinclude">

filters/003.php

</div>

Aliases are mandatory and if you try to use a full class name later, the
system will throw an error. Defining them in this way makes it simple to
switch out the class used. Great for when you decided you need to change
to a different authentication system since you only change the filter's
class and you're done.

You can combine multiple filters into one alias, making complex sets of
filters simple to apply:

<div class="literalinclude">

filters/004.php

</div>

You should define as many aliases as you need.

#### \$required

<div class="versionadded">

4.5.0

</div>

The second section allows you to define **Required Filters**. They are
special filters that are applied to every request made by the framework.
They are applied before and after other kinds of filters that are
explained below.

> [!NOTE]
> The Required Filters are always executed even if a route does not
> exist.

You should take care with how many you use here, since it could have
performance implications to have too many run on every request. But the
filters set by default provide framework functionality. If removed,
those functions will no longer work. See `provided-filters` for details.

Filters can be specified by adding their alias to either the `before` or
`after` array:

<div class="literalinclude">

filters/013.php

</div>

#### \$globals

The third section allows you to define any filters that should be
applied to every valid request made by the framework.

You should take care with how many you use here, since it could have
performance implications to have too many run on every request.

Filters can be specified by adding their alias to either the `before` or
`after` array:

<div class="literalinclude">

filters/005.php

</div>

##### Except for a Few URIs

There are times where you want to apply a filter to almost every
request, but have a few that should be left alone. One common example is
if you need to exclude a few URI's from the CSRF protection filter to
allow requests from third-party websites to hit one or two specific
URI's, while keeping the rest of them protected.

To do this, add an array with the `except` key and a URI path (relative
to BaseURL) to match as the value alongside the alias:

<div class="literalinclude">

filters/006.php

</div>

Any place you can use a URI path (relative to BaseURL) in the filter
settings, you can use a regular expression or, like in this example, use
an asterisk (`*`) for a wildcard that will match all characters after
that. In this example, any URI path starting with `api/` would be
exempted from CSRF protection, but the site's forms would all be
protected.

If you need to specify multiple URI paths, you can use an array of URI
path patterns:

<div class="literalinclude">

filters/007.php

</div>

#### \$methods

> [!WARNING]
> If you use `$methods` filters, you should
> `disable Auto Routing (Legacy) <use-defined-routes-only>` because
> `auto-routing-legacy` permits any HTTP method to access a controller.
> Accessing the controller with a method you don't expect could bypass
> the filter.

You can apply filters to all requests of a certain HTTP method, like
POST, GET, PUT, etc. In this array, you would specify the method name in
**lowercase**. It's value would be an array of filters to run:

<div class="literalinclude">

filters/008.php

</div>

> [!NOTE]
> Unlike the `$globals` or the `$filters` properties, these will only
> run as before filters.

In addition to the standard HTTP methods, this also supports one special
case: `cli`. The `cli` method would apply to all requests that were run
from the command line.

#### \$filters

This property is an array of filter aliases. For each alias, you can
specify `before` and `after` arrays that contain a list of URI path
(relative to BaseURL) patterns that filter should apply to:

<div class="literalinclude">

filters/009.php

</div>

##### Filter Arguments

<div class="versionadded">

4.4.0

</div>

When configuring `$filters`, additional arguments may be passed to a
filter:

<div class="literalinclude">

filters/012.php

</div>

In this example, when the URI matches `admin/*'`, the array
`['admin', 'superadmin']` will be passed in `$arguments` to the `group`
filter's `before()` methods. When the URI matches `admin/users/*'`, the
array `['users.manage']` will be passed in `$arguments` to the
`permission` filter's `before()` methods.

### Filter Execution Order

> [!IMPORTANT]
> Starting with v4.5.0, the order in which filters are executed has
> changed. If you wish to maintain the same execution order as in
> previous versions, you must set `true` to
> `Config\Feature::$oldFilterOrder`.

Filters are executed in the following order:

- **Before Filters**: required → globals → methods → filters → route
- **After Filters**: route → filters → globals → required

> [!NOTE]
> The *required* filters can be used since v4.5.0.

> [!NOTE]
> Prior to v4.5.0, the filters that are specified to a route (in
> **app/Config/Routes.php**) are executed before the filters specified
> in **app/Config/Filters.php**. And the After Filters in Route filters
> and Filters filters execution order were not reversed. See
> `Upgrading Guide <upgrade-450-filter-execution-order>` for details.

## Confirming Filters

CodeIgniter has the following `command <../cli/spark_commands>` to check
the filters for a route.

### filter:check

<div class="versionadded">

4.3.0

</div>

For example, check the filters for the route `/` with **GET** method:

``` console
php spark filter:check get /
```

The output is like the following:

``` none
+--------+-------+----------------+---------------+
| Method | Route | Before Filters | After Filters |
+--------+-------+----------------+---------------+
| GET    | /     |                | toolbar       |
+--------+-------+----------------+---------------+
```

You can also see the routes and filters by the `spark routes` command,
but it might not show accurate filters when you use regular expressions
for routes. See `URI Routing <routing-spark-routes>` for details.

## Provided Filters

The filters bundled with CodeIgniter4 are:

- `csrf` =\> `CSRF <cross-site-request-forgery>`
- `toolbar` =\> `DebugToolbar <the-debug-toolbar>`
- `honeypot` =\> `Honeypot <../libraries/honeypot>`
- `invalidchars` =\> `invalidchars`
- `secureheaders` =\> `secureheaders`
- `forcehttps` =\> `forcehttps`
- `pagecache` =\> `PageCache <../general/caching>`
- `performance` =\> `performancemetrics`

> [!NOTE]
> The filters are executed in the order defined in the config file.
> However, if enabled, `DebugToolbar` is always executed last because it
> should be able to capture everything that happens in the other
> filters.

### ForceHTTPS

<div class="versionadded">

4.5.0

</div>

This filter provides the "Force Global Secure Requests" feature.

If you set `Config\App:$forceGlobalSecureRequests` to true, this will
force every request made to this application to be made via a secure
connection (HTTPS). If the incoming request is not secure, the user will
be redirected to a secure version of the page and the HTTP Strict
Transport Security (HSTS) header will be set.

### PerformanceMetrics

<div class="versionadded">

4.5.0

</div>

This filter provides the pseudo-variables for performance metrics.

If you would like to display the total elapsed time from the moment
CodeIgniter starts to the moment right before the final output is sent
to the browser, simply place this pseudo-variable in one of your views:

    {elapsed_time}

If you would like to show your memory usage in your view files, use this
pseudo-variable:

    {memory_usage}

If you don't need this feature, remove `'performance'` from
`$required['after']`.

### InvalidChars

This filter prohibits user input data (`$_GET`, `$_POST`, `$_COOKIE`,
`php://input`) from containing the following characters:

- invalid UTF-8 characters
- control characters except line break and tab code

### SecureHeaders

This filter adds HTTP response headers that your application can use to
increase the security of your application.

If you want to customize the headers, extend
`CodeIgniter\Filters\SecureHeaders` and override the `$headers`
property. And change the `$aliases` property in
**app/Config/Filters.php**:

<div class="literalinclude">

filters/011.php

</div>

If you want to know about secure headers, see [OWASP Secure Headers
Project](https://owasp.org/www-project-secure-headers/).
