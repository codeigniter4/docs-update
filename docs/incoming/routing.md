# URI Routing

- [What is URI Routing?](#what-is-uri-routing)
- [Setting Routing Rules](#setting-routing-rules)
    - [Examples](#examples)
    - [HTTP verb Routes](#http-verb-routes)
    - [Specifying Route Handlers](#specifying-route-handlers)
        - [Controller's Namespace](#controllers-namespace)
        - [Array Callable Syntax](#array-callable-syntax)
        - [Using Closures](#using-closures)
    - [Specifying Route Paths](#specifying-route-paths)
        - [Placeholders](#placeholders)
        - [Custom Placeholders](#custom-placeholders)
        - [Regular Expressions](#regular-expressions)
    - [View Routes](#view-routes)
    - [Redirecting Routes](#redirecting-routes)
    - [Environment Restrictions](#environment-restrictions)
    - [Routes with any HTTP verbs](#routes-with-any-http-verbs)
    - [Mapping Multiple Routes](#mapping-multiple-routes)
    - [Command-Line Only Routes](#command-line-only-routes)
- [Global Options](#global-options)
    - [Applying Filters](#applying-filters)
        - [Alias Filter](#alias-filter)
        - [Classname Filter](#classname-filter)
        - [Multiple Filters](#multiple-filters)
    - [Assigning Namespace](#assigning-namespace)
    - [Limit to Hostname](#limit-to-hostname)
    - [Limit to Subdomains](#limit-to-subdomains)
    - [Offsetting the Matched Parameters](#offsetting-the-matched-parameters)
- [Reverse Routing](#reverse-routing)
- [Named Routes](#named-routes)
- [Grouping Routes](#grouping-routes)
    - [Setting Namespace](#setting-namespace)
    - [Setting Filters](#setting-filters)
    - [Setting Other Options](#setting-other-options)
    - [Nesting Groups](#nesting-groups)
- [Route Priority](#route-priority)
    - [Changing Route Priority](#changing-route-priority)
- [Routes Configuration Options](#routes-configuration-options)
    - [Default Namespace](#default-namespace)
    - [Translate URI Dashes](#translate-uri-dashes)
    - [Use Defined Routes Only](#use-defined-routes-only)
    - [404 Override](#404-override)
    - [Route Processing by Priority](#route-processing-by-priority)
- [Auto Routing (Improved)](#auto-routing-improved)
    - [Enable Auto Routing](#enable-auto-routing)
    - [URI Segments](#uri-segments)
    - [Configuration Options](#configuration-options)
        - [Default Controller](#default-controller)
        - [Default Method](#default-method)
    - [Module Routing](#module-routing)
- [Auto Routing (Legacy)](#auto-routing-legacy)
    - [Enable Auto Routing (Legacy)](#enable-auto-routing-legacy)
    - [URI Segments (Legacy)](#uri-segments-legacy)
    - [Configuration Options (Legacy)](#configuration-options-legacy)
        - [Default Controller (Legacy)](#default-controller-legacy)
        - [Default Method (Legacy)](#default-method-legacy)
- [Confirming Routes](#confirming-routes)
    - [spark routes](#spark-routes)
        - [Auto Routing (Improved)](#auto-routing-improved)
        - [Auto Routing (Legacy)](#auto-routing-legacy)
        - [Sort by Handler](#sort-by-handler)
        - [Specify Host](#specify-host)

## What is URI Routing?

URI Routing associates a URI with a controller's method.

CodeIgniter has two kinds of routing. One is **Defined Route Routing**, and the other is **Auto Routing**. With Defined Route Routing, you can define routes manually. It allows flexible URL. Auto Routing automatically routes HTTP requests based on conventions and execute the corresponding controller methods. There is no need to define routes manually.

First, let's look at Defined Route Routing. If you want to use Auto Routing, see `auto-routing-improved`.

## Setting Routing Rules

Routing rules are defined in the **app/Config/Routes.php** file. In it you'll see that it creates an instance of the RouteCollection class (`$routes`) that permits you to specify your own routing criteria. Routes can be specified using placeholders or Regular Expressions.

When you specify a route, you choose a method to corresponding to HTTP verbs (request method). If you expect a GET request, you use the `get()` method:

```php
--8<--
incoming/routing/001.php
--8<--
```

A route takes the **Route Path** (URI path relative to the BaseURL. `/`) on the left, and maps it to the **Route Handler** (controller and method `Home::index`) on the right, along with any parameters that should be passed to the controller.

The controller and method should be listed in the same way that you would use a static method, by separating the class and its method with a double-colon, like `Users::list`.

If that method requires parameters to be passed to it, then they would be listed after the method name, separated by forward-slashes:

```php
--8<--
incoming/routing/002.php
--8<--
```

### Examples

Here are a few basic routing examples.

A URL containing the word **journals** in the first segment will be mapped to the `\App\Controllers\Blogs` class, and the default method, which is usually `index()`:

```php
--8<--
incoming/routing/006.php
--8<--
```

A URL containing the segments **blog/joe** will be mapped to the `\App\Controllers\Blogs` class and the `users()` method. The ID will be set to `34`:

```php
--8<--
incoming/routing/007.php
--8<--
```

A URL with **product** as the first segment, and anything in the second will be mapped to the `\App\Controllers\Catalog` class and the `productLookup()` method:

```php
--8<--
incoming/routing/008.php
--8<--
```

A URL with **product** as the first segment, and a number in the second will be mapped to the `\App\Controllers\Catalog` class and the `productLookupByID()` method passing in the match as a variable to the method:

```php
--8<--
incoming/routing/009.php
--8<--
```

### HTTP verb Routes

You can use any standard HTTP verb (GET, POST, PUT, DELETE, OPTIONS, etc):

```php
--8<--
incoming/routing/003.php
--8<--
```

You can supply multiple verbs that a route should match by passing them in as an array to the `match()` method:

```php
--8<--
incoming/routing/004.php
--8<--
```

### Specifying Route Handlers

#### Controller's Namespace

When you specify a controller and method name as a string, if a controller is written without a leading `\`, the `routing-default-namespace` will be prepended:

```php
--8<--
incoming/routing/063.php
--8<--
```

If you put `\` at the beginning, it is treated as a fully qualified class name:

```php
--8<--
incoming/routing/064.php
--8<--
```

You can also specify the namespace with the `namespace` option:

```php
--8<--
incoming/routing/038.php
--8<--
```

See `assigning-namespace` for details.

#### Array Callable Syntax

!!! success "Available from version 4.2.0"

Since v4.2.0, you can use array callable syntax to specify the controller:

```php
--8<--
incoming/routing/013.php:2:
--8<--
```

Or using `use` keyword:

```php
--8<--
incoming/routing/014.php:2:
--8<--
```

If you forget to add `use App\Controllers\Home;`, the controller classname is interpreted as `Config\Home`, not `App\Controllers\Home` because **app/Config/Routes.php** has `namespace Config;` at the top.

!!! note "Note"
    When you use Array Callable Syntax, the classname is always interpreted as a fully qualified classname. So `routing-default-namespace` and [namespace option](#assigning-namespace) have no effect.

##### Array Callable Syntax and Placeholders

If there are placeholders, it will automatically set the parameters in the specified order:

```php
--8<--
incoming/routing/015.php:2:
--8<--
```

But the auto-configured parameters may not be correct if you use regular expressions in routes. In such a case, you can specify the parameters manually:

```php
--8<--
incoming/routing/016.php:2:
--8<--
```

#### Using Closures

You can use an anonymous function, or Closure, as the destination that a route maps to. This function will be executed when the user visits that URI. This is handy for quickly executing small tasks, or even just showing a simple view:

```php
--8<--
incoming/routing/020.php
--8<--
```

### Specifying Route Paths

#### Placeholders

A typical route might look something like this:

```php
--8<--
incoming/routing/005.php
--8<--
```

In a route, the first parameter contains the URI to be matched, while the second parameter contains the destination it should be routed to. In the above example, if the literal word "product" is found in the first segment of the URL path, and a number is found in the second segment, the `Catalog` class and the `productLookup` method are used instead.

Placeholders are simply strings that represent a Regular Expression pattern. During the routing process, these placeholders are replaced with the value of the Regular Expression. They are primarily used for readability.

The following placeholders are available for you to use in your routes:

<table>
<thead>
<tr>
<th>Placeholders</th>
<th>Description</th>
</tr>
</thead>
<tbody>
<tr>
<td>(:any)</td>
<td>will match all characters from that point to the end of the URI. This may include multiple URI segments.</td>
</tr>
<tr>
<td>(:segment)</td>
<td>will match any character except for a forward slash (<code>/</code>) restricting the result to a single segment.</td>
</tr>
<tr>
<td>(:num)</td>
<td>will match any integer.</td>
</tr>
<tr>
<td>(:alpha)</td>
<td>will match any string of alphabetic characters</td>
</tr>
<tr>
<td>(:alphanum)</td>
<td>will match any string of alphabetic characters or integers, or any combination of the two.</td>
</tr>
<tr>
<td>(:hash)</td>
<td>is the same as <code>(:segment)</code>, but can be used to easily see which routes use hashed ids.</td>
</tr>
</tbody>
</table>

!!! note "Note"
    `{locale}` cannot be used as a placeholder or other part of the route, as it is reserved for use in [localization](#/outgoing/localization).

##### The Behavior of (:any)

Note that a single `(:any)` will match multiple segments in the URL if present.

For example the route:

```php
--8<--
incoming/routing/010.php
--8<--
```

will match **product/123**, **product/123/456**, **product/123/456/789** and so on.

The implementation in the Controller should take into account the maximum parameters:

```php
--8<--
incoming/routing/011.php
--8<--
```

!!! important "Important"
    Do not put any placeholder after `(:any)`. Because the number of parameters passed to the controller method may change.

If matching multiple segments is not the intended behavior, `(:segment)` should be used when defining the routes. With the examples URLs from above:

```php
--8<--
incoming/routing/012.php
--8<--
```

will only match **product/123** and generate 404 errors for other example.

#### Custom Placeholders

You can create your own placeholders that can be used in your routes file to fully customize the experience and readability.

You add new placeholders with the `addPlaceholder()` method. The first parameter is the string to be used as the placeholder. The second parameter is the Regular Expression pattern it should be replaced with. This must be called before you add the route:

```php
--8<--
incoming/routing/017.php
--8<--
```

#### Regular Expressions

If you prefer you can use regular expressions to define your routing rules. Any valid regular expression is allowed, as are back-references.

!!! important "Important"
    Note: If you use back-references you must use the dollar syntax rather than the double backslash syntax. A typical RegEx route might look something like this:
    
    ```php
--8<--
incoming/routing/018.php
--8<--
```

In the above example, a URI similar to **products/shirts/123** would instead call the `show()` method of the `Products` controller class, with the original first and second segment passed as arguments to it.

With regular expressions, you can also catch a segment containing a forward slash (`/`), which would usually represent the delimiter between multiple segments.

For example, if a user accesses a password protected area of your web application and you wish to be able to redirect them back to the same page after they log in, you may find this example useful:

```php
--8<--
incoming/routing/019.php
--8<--
```

For those of you who don't know regular expressions and want to learn more about them, \[regular-expressions.info](#https://www.regular-expressions.info/)\_ might be a good starting point.

!!! note "Note"
    You can also mix and match placeholders with regular expressions.

### View Routes

!!! success "Available from version 4.3.0"

If you just want to render a view out that has no logic associated with it, you can use the `view()` method. This is always treated as GET request. This method accepts the name of the view to load as the second parameter.

```php
--8<--
incoming/routing/065.php
--8<--
```

If you use placeholders within your route, you can access them within the view in a special variable, `$segments`. They are available as an array, indexed in the order they appear in the route.

```php
--8<--
incoming/routing/066.php
--8<--
```

### Redirecting Routes

Any site that lives long enough is bound to have pages that move. You can specify routes that should redirect to other routes with the `addRedirect()` method. The first parameter is the URI pattern for the old route. The second parameter is either the new URI to redirect to, or the name of a named route. The third parameter is the HTTP status code that should be sent along with the redirect. The default value is `302` which is a temporary redirect and is recommended in most cases:

```php
--8<--
incoming/routing/022.php
--8<--
```

!!! note "Note"
    Since v4.2.0, `addRedirect()` can use placeholders.

If a redirect route is matched during a page load, the user will be immediately redirected to the new page before a controller can be loaded.

### Environment Restrictions

You can create a set of routes that will only be viewable in a certain environment. This allows you to create tools that only the developer can use on their local machines that are not reachable on testing or production servers. This can be done with the `environment()` method. The first parameter is the name of the environment. Any routes defined within this closure are only accessible from the given environment:

```php
--8<--
incoming/routing/028.php
--8<--
```

### Routes with any HTTP verbs

!!! important "Important"
    This method exists only for backward compatibility. Do not use it in new projects. Even if you are already using it, we recommend that you use another, more appropriate method.

!!! warning "Warning"
    While the `add()` method seems to be convenient, it is recommended to always use the HTTP-verb-based routes, described above, as it is more secure. If you use the [CSRF protection](#/libraries/security), it does not protect **GET** requests. If the URI specified in the `add()` method is accessible by the GET method, the CSRF protection will not work.

It is possible to define a route with any HTTP verbs. You can use the `add()` method:

```php
--8<--
incoming/routing/031.php
--8<--
```

!!! note "Note"
    Using the HTTP-verb-based routes will also provide a slight performance increase, since only routes that match the current request method are stored, resulting in fewer routes to scan through when trying to find a match.

### Mapping Multiple Routes

!!! important "Important"
    This method exists only for backward compatibility. Do not use it in new projects. Even if you are already using it, we recommend that you use another, more appropriate method.

!!! warning "Warning"
    The `map()` method is not recommended as well as `add()` because it calls `add()` internally.

While the `add()` method is simple to use, it is often handier to work with multiple routes at once, using the `map()` method. Instead of calling the `add()` method for each route that you need to add, you can define an array of routes and then pass it as the first parameter to the `map()` method:

```php
--8<--
incoming/routing/021.php
--8<--
```

### Command-Line Only Routes

!!! note "Note"
    It is recommended to use Spark Commands for CLI scripts instead of calling controllers via CLI. See the [Cli Commands](../cli/cli_commands.md) page for detailed information.

Any route created by any of the HTTP-verb-based route methods will also be inaccessible from the CLI, but routes created by the `add()` method will still be available from the command line.

You can create routes that work only from the command-line, and are inaccessible from the web browser, with the `cli()` method:

```php
--8<--
incoming/routing/032.php
--8<--
```

!!! warning "Warning"
    If you enable `auto-routing-legacy` and place the command file in **app/Controllers**, anyone could access the command with the help of Auto Routing (Legacy) via HTTP.

## Global Options

All of the methods for creating a route (`get()`, `post()`, [resource()](#restful) etc) can take an array of options that can modify the generated routes, or further restrict them. The `$options` array is always the last parameter:

```php
--8<--
incoming/routing/033.php
--8<--
```

### Applying Filters

You can alter the behavior of specific routes by supplying filters to run before or after the controller. This is especially handy during authentication or api logging. The value for the filter can be a string or an array of strings:

- matching the aliases defined in **app/Config/Filters.php**.

- filter classnames

See [Controller Filters](#filters) for more information on setting up filters.

!!! warning "Warning"
    If you set filters to routes in **app/Config/Routes.php** (not in **app/Config/Filters.php**), it is recommended to disable Auto Routing (Legacy). When `auto-routing-legacy` is enabled, it may be possible that a controller can be accessed via a different URL than the configured route, in which case the filter you specified to the route will not be applied. See `use-defined-routes-only` to disable auto-routing.

#### Alias Filter

You specify an alias defined in **app/Config/Filters.php** for the filter value:

```php
--8<--
incoming/routing/034.php
--8<--
```

You may also supply arguments to be passed to the alias filter's `before()` and `after()` methods:

```php
--8<--
incoming/routing/035.php
--8<--
```

#### Classname Filter

!!! success "Available from version 4.1.5"

You specify a filter classname for the filter value:

```php
--8<--
incoming/routing/036.php
--8<--
```

#### Multiple Filters

!!! success "Available from version 4.1.5"

!!! important "Important"
    Since v4.5.0, *Multiple Filters* are always enabled. Prior to v4.5.0, *Multiple Filters* were disabled by default. If you want to use with prior to v4.5.0, See [Upgrading from 4.1.4 to 4.1.5](#upgrade-415-multiple-filters-for-a-route) for the details.

You specify an array for the filter value:

```php
--8<--
incoming/routing/037.php
--8<--
```

##### Filter Arguments

Additional arguments may be passed to a filter:

```php
--8<--
incoming/routing/067.php
--8<--
```

In this example, the array `['dual', 'noreturn']` will be passed in `$arguments` to the filter's `before()` and `after()` implementation methods.

### Assigning Namespace

While a `routing-default-namespace` will be prepended to the generated controllers, you can also specify a different namespace to be used in any options array, with the `namespace` option. The value should be the namespace you want modified:

```php
--8<--
incoming/routing/038.php
--8<--
```

The new namespace is only applied during that call for any methods that create a single route, like get, post, etc. For any methods that create multiple routes, the new namespace is attached to all routes generated by that function or, in the case of `group()`, all routes generated while in the closure.

### Limit to Hostname

You can restrict groups of routes to function only in certain domain or sub-domains of your application by passing the "hostname" option along with the desired domain to allow it on as part of the options array:

```php
--8<--
incoming/routing/039.php
--8<--
```

This example would only allow the specified hosts to work if the domain exactly matched **accounts.example.com**. It would not work under the main site at **example.com**.

### Limit to Subdomains

When the `subdomain` option is present, the system will restrict the routes to only be available on that sub-domain. The route will only be matched if the subdomain is the one the application is being viewed through:

```php
--8<--
incoming/routing/040.php
--8<--
```

You can restrict it to any subdomain by setting the value to an asterisk, (`*`). If you are viewing from a URL that does not have any subdomain present, this will not be matched:

```php
--8<--
incoming/routing/041.php
--8<--
```

!!! important "Important"
    The system is not perfect and should be tested for your specific domain before being used in production. Most domains should work fine but some edge case ones, especially with a period in the domain itself (not used to separate suffixes or www) can potentially lead to false positives.

### Offsetting the Matched Parameters

You can offset the matched parameters in your route by any numeric value with the `offset` option, with the value being the number of segments to offset.

This can be beneficial when developing APIs with the first URI segment being the version number. It can also be used when the first parameter is a language string:

```php
--8<--
incoming/routing/042.php
--8<--
```

## Reverse Routing

Reverse routing allows you to define the controller and method, as well as any parameters, that a link should go to, and have the router lookup the current route to it. This allows route definitions to change without you having to update your application code. This is typically used within views to create links.

For example, if you have a route to a photo gallery that you want to link to, you can use the `url_to()` helper function to get the route that should be used. The first parameter is the fully qualified Controller and method, separated by a double colon (`::`), much like you would use when writing the initial route itself. Any parameters that should be passed to the route are passed in next:

```php
--8<--
incoming/routing/029.php
--8<--
```

## Named Routes

You can name routes to make your application less fragile. This applies a name to a route that can be called later, and even if the route definition changes, all of the links in your application built with `url_to()` will still work without you having to make any changes. A route is named by passing in the `as` option with the name of the route:

```php
--8<--
incoming/routing/030.php
--8<--
```

This has the added benefit of making the views more readable, too.

## Grouping Routes

You can group your routes under a common name with the `group()` method. The group name becomes a segment that appears prior to the routes defined inside of the group. This allows you to reduce the typing needed to build out an extensive set of routes that all share the opening string, like when building an admin area:

```php
--8<--
incoming/routing/023.php
--8<--
```

This would prefix the **users** and **blog** URIs with **admin**, handling URLs like **admin/users** and **admin/blog**.

### Setting Namespace

If you need to assign options to a group, like a `assigning-namespace`, do it before the callback:

```php
--8<--
incoming/routing/024.php
--8<--
```

This would handle a resource route to the `App\API\v1\Users` controller with the **api/users** URI.

### Setting Filters

You can also use a specific [filter](#filters) for a group of routes. This will always run the filter before or after the controller. This is especially handy during authentication or api logging:

```php
--8<--
incoming/routing/025.php
--8<--
```

The value for the filter must match one of the aliases defined within **app/Config/Filters.php**.

### Setting Other Options

At some point, you may want to group routes for the purpose of applying filters or other route config options like namespace, subdomain, etc. Without necessarily needing to add a prefix to the group, you can pass an empty string in place of the prefix and the routes in the group will be routed as though the group never existed but with the given route config options:

```php
--8<--
incoming/routing/027.php
--8<--
```

### Nesting Groups

It is possible to nest groups within groups for finer organization if you need it:

```php
--8<--
incoming/routing/026.php
--8<--
```

This would handle the URL at **admin/users/list**.

**Filter** option passed to the outer `group()` are merged with the inner `group()` filter option. The above code runs `myfilter:config` for the route `admin`, and `myfilter:config` and `myfilter:region` for the route `admin/users/list`.

Any other overlapping options passed to the inner <span class="title-ref">group()</span> will overwrite their values.

!!! note "Note"
    Prior to v4.5.0, due to a bug, options passed to the outer `group()` are not merged with the inner `group()` options.

## Route Priority

Routes are registered in the routing table in the order in which they are defined. This means that when a URI is accessed, the first matching route will be executed.

!!! warning "Warning"
    If a route path is defined more than once with different handlers, only the first defined route is registered.

You can check registered routes in the routing table by running the [spark routes](#routing-spark-routes) command.

### Changing Route Priority

When working with modules, it can be a problem if the routes in the application contain wildcards. Then the module routes will not be processed correctly. You can solve this problem by lowering the priority of route processing using the `priority` option. The parameter accepts positive integers and zero. The higher the number specified in the `priority`, the lower route priority in the processing queue:

```php
--8<--
incoming/routing/043.php
--8<--
```

To disable this functionality, you must call the method with the parameter `false`:

```php
--8<--
incoming/routing/044.php
--8<--
```

!!! note "Note"
    By default, all routes have a priority of 0. Negative integers will be cast to the absolute value.

## Routes Configuration Options

The RoutesCollection class provides several options that affect all routes, and can be modified to meet your application's needs. These options are available in **app/Config/Routing.php**.

!!! note "Note"
    The config file **app/Config/Routing.php** has been added since v4.4.0. In previous versions, the setter methods were used in **app/Config/Routes.php** to change settings.

### Default Namespace

When matching a controller to a route, the router will add the default namespace value to the front of the controller specified by the route. By default, this value is `App\Controllers`.

If you set the value empty string (`''`), it leaves each route to specify the fully namespaced controller:

```php
--8<--
incoming/routing/045.php
--8<--
```

If your controllers are not explicitly namespaced, there is no need to change this. If you namespace your controllers, then you can change this value to save typing:

```php
--8<--
incoming/routing/046.php
--8<--
```

### Translate URI Dashes

This option enables you to automatically replace dashes (`-`) with underscores in the controller and method URI segments when used in Auto Routing, thus saving you additional route entries if you need to do that. This is required because the dash isn't a valid class or method name character and would cause a fatal error if you try to use it:

```php
--8<--
incoming/routing/049.php
--8<--
```

!!! note "Note"
    When using Auto Routing (Improved), prior to v4.4.0, if `$translateURIDashes` is true, two URIs correspond to a single controller method, one URI for dashes (e.g., **foo-bar**) and one URI for underscores (e.g., **foo_bar**). This was incorrect behavior. Since v4.4.0, the URI for underscores (**foo_bar**) is not accessible.

### Use Defined Routes Only

Since v4.2.0, the auto-routing is disabled by default.

When no defined route is found that matches the URI, the system will attempt to match that URI against the controllers and methods when Auto Routing is enabled.

You can disable this automatic matching, and restrict routes to only those defined by you, by setting the `$autoRoute` property to false:

```php
--8<--
incoming/routing/050.php
--8<--
```

!!! warning "Warning"
    If you use the [CSRF protection](#/libraries/security), it does not protect **GET** requests. If the URI is accessible by the GET method, the CSRF protection will not work.

### 404 Override

When a page is not found that matches the current URI, the system will show a generic 404 view. You can change what happens by specifying an action to happen with the `set404Override()` method. The value can be either a valid class/method pair, just like you would show in any route, or a Closure:

```php
--8<--
incoming/routing/051.php
--8<--
```

Using the `$override404` property within the routing config file, you can use closures. Defining the override in the Routing file is restricted to class/method pairs.

!!! note "Note"
    The `set404Override()` method does not change the Response status code to `404`. If you don't set the status code in the controller you set, the default status code `200` will be returned. See `CodeIgniter\\HTTP\\Response::setStatusCode()` for information on how to set the status code.

### Route Processing by Priority

Enables or disables processing of the routes queue by priority. Lowering the priority is defined in the route option. Disabled by default. This functionality affects all routes. For an example use of lowering the priority see `routing-priority`:

```php
--8<--
incoming/routing/052.php
--8<--
```

## Auto Routing (Improved)

!!! success "Available from version 4.2.0"

Since v4.2.0, the new more secure Auto Routing has been introduced.

!!! note "Note"
    If you are familiar with Auto Routing, which was enabled by default from CodeIgniter 3 through 4.1.x, you can see the differences in [ChangeLog v4.2.0](#v420-new-improved-auto-routing).

When no defined route is found that matches the URI, the system will attempt to match that URI against the controllers and methods when Auto Routing is enabled.

!!! important "Important"
    For security reasons, if a controller is used in the defined routes, Auto Routing (Improved) does not route to the controller.

Auto Routing can automatically route HTTP requests based on conventions and execute the corresponding controller methods.

!!! note "Note"
    Auto Routing (Improved) is disabled by default. To use it, see below.

### Enable Auto Routing

To use it, you need to change the setting `$autoRoute` option to `true` in **app/Config/Routing.php**:

    public bool $autoRoute = true;

And you need to change the property `$autoRoutesImproved` to `true` in **app/Config/Feature.php**:

    public bool $autoRoutesImproved = true;

### URI Segments

The segments in the URL, in following with the Model-View-Controller approach, usually represent:

    example.com/class/method/ID

1.  The first segment represents the controller **class** that should be invoked.
2.  The second segment represents the class **method** that should be called.
3.  The third, and any additional segments, represent the ID and any variables that will be passed to the controller.

Consider this URI:

    example.com/index.php/helloworld/hello/1

In the above example, when you send a HTTP request with **GET** method, Auto Routing would attempt to find a controller named `App\Controllers\Helloworld` and executes `getHello()` method with passing `'1'` as the first argument.

!!! note "Note"
    A controller method that will be executed by Auto Routing (Improved) needs HTTP verb (`get`, `post`, `put`, etc.) prefix like `getIndex()`, `postCreate()`.

See [Auto Routing in Controllers](#controller-auto-routing-improved) for more info.

### Configuration Options

These options are available in the **app/Config/Routing.php** file.

#### Default Controller

##### For Site Root URI

When a user visits the root of your site (i.e., **example.com**) the controller to use is determined by the value set to the `$defaultController` property, unless a route exists for it explicitly.

The default value for this is `Home` which matches the controller at **app/Controllers/Home.php**:

    public string $defaultController = 'Home';

##### For Directory URI

The default controller is also used when no matching route has been found, and the URI would point to a directory in the controllers directory. For example, if the user visits **example.com/admin**, if a controller was found at **app/Controllers/Admin/Home.php**, it would be used.

!!! important "Important"
    You cannot access the default controller with the URI of the controller name. When the default controller is `Home`, you can access **example.com/**, but if you access **example.com/home**, it will be not found.

See [Auto Routing in Controllers](#controller-auto-routing-improved) for more info.

#### Default Method

This works similar to the default controller setting, but is used to determine the default method that is used when a controller is found that matches the URI, but no segment exists for the method. The default value is `index`.

In this example, if the user were to visit **example.com/products**, and a `Products` controller existed, the `Products::getListAll()` method would be executed:

    public string $defaultMethod = 'listAll';

!!! important "Important"
    You cannot access the controller with the URI of the default method name. In the example above, you can access **example.com/products**, but if you access **example.com/products/listall**, it will be not found.

### Module Routing

!!! success "Available from version 4.4.0"

You can use auto routing even if you use [Modules](../general/modules.md) and place the controllers in a different namespace.

To route to a module, the `$moduleRoutes` property in **app/Config/Routing.php** must be set:

    public array $moduleRoutes = [
    'blog' => 'Acme\Blog\Controllers',
    ];

The key is the first URI segment for the module, and the value is the controller namespace. In the above configuration, **http://localhost:8080/blog/foo/bar** will be routed to `Acme\Blog\Controllers\Foo::getBar()`.

!!! note "Note"
    If you define `$moduleRoutes`, the routing for the module takes precedence. In the above example, even if you have the `App\Controllers\Blog` controller, **http://localhost:8080/blog** will be routed to the default controller `Acme\Blog\Controllers\Home`.

## Auto Routing (Legacy)

!!! important "Important"
    This feature exists only for backward compatibility. Do not use it in new projects. Even if you are already using it, we recommend that you use the `auto-routing-improved` instead.

Auto Routing (Legacy) is a routing system from CodeIgniter 3. It can automatically route HTTP requests based on conventions and execute the corresponding controller methods.

It is recommended that all routes are defined in the **app/Config/Routes.php** file, or to use `auto-routing-improved`,

!!! warning "Warning"
    To prevent misconfiguration and miscoding, we recommend that you do not use Auto Routing (Legacy) feature. It is easy to create vulnerable apps where controller filters or CSRF protection are bypassed.

!!! important "Important"
    Auto Routing (Legacy) routes a HTTP request with **any** HTTP method to a controller method.

### Enable Auto Routing (Legacy)

Since v4.2.0, the auto-routing is disabled by default.

To use it, you need to change the setting `$autoRoute` option to `true` in **app/Config/Routing.php**:

    public bool $autoRoute = true;

And set the property `$autoRoutesImproved` to `false` in **app/Config/Feature.php**:

    public bool $autoRoutesImproved = false;

### URI Segments (Legacy)

The segments in the URL, in following with the Model-View-Controller approach, usually represent:

    example.com/class/method/ID

1.  The first segment represents the controller **class** that should be invoked.
2.  The second segment represents the class **method** that should be called.
3.  The third, and any additional segments, represent the ID and any variables that will be passed to the controller.

Consider this URI:

    example.com/index.php/helloworld/index/1

In the above example, CodeIgniter would attempt to find a controller named **Helloworld.php** and executes `index()` method with passing `'1'` as the first argument.

See [Auto Routing (Legacy) in Controllers](#controller-auto-routing-legacy) for more info.

### Configuration Options (Legacy)

These options are available in the **app/Config/Routing.php** file.

#### Default Controller (Legacy)

##### For Site Root URI (Legacy)

When a user visits the root of your site (i.e., **example.com**) the controller to use is determined by the value set to the `$defaultController` property, unless a route exists for it explicitly.

The default value for this is `Home` which matches the controller at **app/Controllers/Home.php**:

    public string $defaultController = 'Home';

##### For Directory URI (Legacy)

The default controller is also used when no matching route has been found, and the URI would point to a directory in the controllers directory. For example, if the user visits **example.com/admin**, if a controller was found at **app/Controllers/Admin/Home.php**, it would be used.

See [Auto Routing (Legacy) in Controllers](#controller-auto-routing-legacy) for more info.

#### Default Method (Legacy)

This works similar to the default controller setting, but is used to determine the default method that is used when a controller is found that matches the URI, but no segment exists for the method. The default value is `index`.

In this example, if the user were to visit **example.com/products**, and a `Products` controller existed, the `Products::listAll()` method would be executed:

    public string $defaultMethod = 'listAll';

## Confirming Routes

CodeIgniter has the following [command](#/cli/spark-commands) to display all routes.

### spark routes

Displays all routes and filters:

``` console
php spark routes
```

The output is like the following:

``` none
+---------+---------+---------------+-------------------------------+----------------+---------------+
| Method  | Route   | Name          | Handler                       | Before Filters | After Filters |
+---------+---------+---------------+-------------------------------+----------------+---------------+
| GET     | /       | »             | \App\Controllers\Home::index  |                | toolbar       |
| GET     | feed    | »             | (Closure)                     |                | toolbar       |
+---------+---------+---------------+-------------------------------+----------------+---------------+
```

The *Method* column shows the HTTP method that the route is listening for.

The *Route* column shows the route path to match. The route of a defined route is expressed as a regular expression.

Since v4.3.0, the *Name* column shows the route name. `»` indicates the name is the same as the route path.

!!! important "Important"
    The system is not perfect. For routes containing regular expression patterns like `([^/]+)` or `{locale}`, the *Filters* displayed might not be correct (if you set complicated URI pattern for the filters in **app/Config/Filters.php**), or it is displayed as `<unknown>`.
    
    The [spark filter:check](#spark-filter-check) command can be used to check for 100% accurate filters.

#### Auto Routing (Improved)

When you use Auto Routing (Improved), the output is like the following:

``` none
+-----------+-------------------------+---------------+-----------------------------------+----------------+---------------+
| Method    | Route                   | Name          | Handler                           | Before Filters | After Filters |
+-----------+-------------------------+---------------+-----------------------------------+----------------+---------------+
| GET(auto) | product/list/../..[/..] |               | \App\Controllers\Product::getList |                | toolbar       |
+-----------+-------------------------+---------------+-----------------------------------+----------------+---------------+
```

The *Method* will be like `GET(auto)`.

`/..` in the *Route* column indicates one segment. `[/..]` indicates it is optional.

!!! note "Note"
    When auto-routing is enabled and you have the route `home`, it can be also accessed by `Home`, or maybe by `hOme`, `hoMe`, `HOME`, etc. but the command will show only `home`.

If you see a route starting with `x` like the following, it indicates an invalid route that won't be routed, but the controller has a public method for routing.

``` none
+-----------+----------------+------+-------------------------------------+----------------+---------------+
| Method    | Route          | Name | Handler                             | Before Filters | After Filters |
+-----------+----------------+------+-------------------------------------+----------------+---------------+
| GET(auto) | x home/foo     |      | \App\Controllers\Home::getFoo       | <unknown>      | <unknown>     |
+-----------+----------------+------+-------------------------------------+----------------+---------------+
```

The above example shows you have the `\App\Controllers\Home::getFoo()` method, but it is not routed because it is the default controller (`Home` by default) and the default controller name must be omitted in the URI. You should delete the `getFoo()` method.

!!! note "Note"
    Prior to v4.3.4, the invalid route is displayed as a normal route due to a bug.

#### Auto Routing (Legacy)

When you use Auto Routing (Legacy), the output is like the following:

``` none
+--------+--------------------+---------------+-----------------------------------+----------------+---------------+
| Method | Route              | Name          | Handler                           | Before Filters | After Filters |
+--------+--------------------+---------------+-----------------------------------+----------------+---------------+
| auto   | product/list[/...] |               | \App\Controllers\Product::getList |                | toolbar       |
+--------+--------------------+---------------+-----------------------------------+----------------+---------------+
```

The *Method* will be `auto`.

`[/...]` in the *Route* column indicates any number of segments.

!!! note "Note"
    When auto-routing is enabled and you have the route `home`, it can be also accessed by `Home`, or maybe by `hOme`, `hoMe`, `HOME`, etc. but the command will show only `home`.

#### Sort by Handler

!!! success "Available from version 4.3.0"

You can sort the routes by *Handler*:

``` console
php spark routes -h
```

#### Specify Host

!!! success "Available from version 4.4.0"

You can specify the host in the request URL with the `--host` option:

``` console
php spark routes --host accounts.example.com
```
