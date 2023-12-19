# Controllers

Controllers are the heart of your application, as they determine how
HTTP requests should be handled.

<div class="contents" local="" depth="2">

</div>

## What is a Controller?

A Controller is simply a class file that handles a HTTP request.
`URI Routing <routing>` associates a URI with a controller. It returns a
view string or `Response` object.

Every controller you create should extend `BaseController` class. This
class provides several features that are available to all of your
controllers.

## Constructor

The CodeIgniter's Controller has a special constructor
`initController()`. It will be called by the framework after PHP's
constructor `__construct()` execution.

If you want to override the `initController()`, don't forget to add
`parent::initController($request, $response, $logger);` in the method:

<div class="literalinclude">

controllers/023.php

</div>

> [!IMPORTANT]
> You cannot use `return` in the constructor. So
> `return redirect()->to('route');` does not work.

The `initController()` method sets the following three properties.

## Included Properties

The CodeIgniter's Controller provides these properties.

### Request Object

The application's main `Request Instance </incoming/incomingrequest>` is
always available as a class property, `$this->request`.

### Response Object

The application's main `Response Instance </outgoing/response>` is
always available as a class property, `$this->response`.

### Logger Object

An instance of the `Logger <../general/logging>` class is available as a
class property, `$this->logger`.

### Helpers

You can define an array of helper files as a class property. Whenever
the controller is loaded these helper files will be automatically loaded
into memory so that you can use their methods anywhere inside the
controller:

<div class="literalinclude">

controllers/001.php

</div>

## forceHTTPS

A convenience method for forcing a method to be accessed via HTTPS is
available within all controllers:

<div class="literalinclude">

controllers/002.php

</div>

By default, and in modern browsers that support the HTTP Strict
Transport Security header, this call should force the browser to convert
non-HTTPS calls to HTTPS calls for one year. You can modify this by
passing the duration (in seconds) as the first parameter:

<div class="literalinclude">

controllers/003.php

</div>

> [!NOTE]
> A number of `time-based constants </general/common_functions>` are
> always available for you to use, including `YEAR`, `MONTH`, and more.

## Validating Data

### \$this-\>validateData()

<div class="versionadded">

4.2.0

</div>

To simplify data checking, the controller also provides the convenience
method `validateData()`.

The method accepts (1) an array of data to validate, (2) an array of
rules, (3) an optional array of custom error messages to display if the
items are not valid, (4) an optional database group to use.

The `Validation Library docs </libraries/validation>` have details on
rule and message array formats, as well as available rules:

<div class="literalinclude">

controllers/006.php

</div>

### \$this-\>validate()

> [!IMPORTANT]
> This method exists only for backward compatibility. Do not use it in
> new projects. Even if you are already using it, we recommend that you
> use the `validateData()` method instead.

The controller also provides the convenience method `validate()`.

> [!WARNING]
> Instead of `validate()`, use `validateData()` to validate POST data
> only. `validate()` uses `$request->getVar()` which returns `$_GET`,
> `$_POST` or `$_COOKIE` data in that order (depending on php.ini
> [request-order](https://www.php.net/manual/en/ini.core.php#ini.request-order)).
> Newer values override older values. POST values may be overridden by
> the cookies if they have the same name.

The method accepts an array of rules in the first parameter, and in the
optional second parameter, an array of custom error messages to display
if the items are not valid.

Internally, this uses the controller's `$this->request` instance to get
the data to be validated.

The `Validation Library docs </libraries/validation>` have details on
rule and message array formats, as well as available rules:

<div class="literalinclude">

controllers/004.php

</div>

> [!WARNING]
> When you use the `validate()` method, you should use the
> `getValidated() <validation-getting-validated-data>` method to get the
> validated data. Because the `validate()` method uses the
> `Validation::withRequest() <validation-withrequest>` method
> internally, and it validates data from
> `$request->getJSON() <incomingrequest-getting-json-data>` or
> `$request->getRawInput() <incomingrequest-retrieving-raw-data>` or
> `$request->getVar() <incomingrequest-getting-data>`, and an attacker
> could change what data is validated.

> [!NOTE]
> The
> `$this->validator->getValidated() <validation-getting-validated-data>`
> method can be used since v4.4.0.

If you find it simpler to keep the rules in the configuration file, you
can replace the `$rules` array with the name of the group as defined in
**app/Config/Validation.php**:

<div class="literalinclude">

controllers/005.php

</div>

> [!NOTE]
> Validation can also be handled automatically in the model, but
> sometimes it's easier to do it in the controller. Where is up to you.

## Protecting Methods

In some cases, you may want certain methods hidden from public access.
To achieve this, simply declare the method as `private` or `protected`.
That will prevent it from being served by a URL request.

For example, if you were to define a method like this for the
`Helloworld` controller:

<div class="literalinclude">

controllers/007.php

</div>

and to define a route (`helloworld/utitilty`) for the method. Then
trying to access it using the following URL will not work:

    example.com/index.php/helloworld/utility

Auto-routing also will not work.

## Auto Routing (Improved)

<div class="versionadded">

4.2.0

</div>

Since v4.2.0, the new more secure Auto Routing has been introduced.

> [!NOTE]
> If you are familiar with Auto Routing, which was enabled by default
> from CodeIgniter 3 through 4.1.x, you can see the differences in
> `ChangeLog v4.2.0 <v420-new-improved-auto-routing>`.

This section describes the functionality of the new auto-routing. It
automatically routes an HTTP request, and executes the corresponding
controller method without route definitions.

Since v4.2.0, the auto-routing is disabled by default. To use it, see
`enabled-auto-routing-improved`.

Consider this URI:

    example.com/index.php/helloworld/

In the above example, CodeIgniter would attempt to find a controller
named `App\Controllers\Helloworld` and load it, when auto-routing is
enabled.

> [!NOTE]
> When a controller's short name matches the first segment of a URI, it
> will be loaded.

### Let's try it: Hello World!

Let's create a simple controller so you can see it in action. Using your
text editor, create a file called **Helloworld.php**, and put the
following code in it. You will notice that the `Helloworld` Controller
is extending the `BaseController`. you can also extend the
`CodeIgniter\Controller` if you do not need the functionality of the
BaseController.

The BaseController provides a convenient place for loading components
and performing functions that are needed by all your controllers. You
can extend this class in any new controller.

<div class="literalinclude">

controllers/020.php

</div>

Then save the file to your **app/Controllers** directory.

> [!IMPORTANT]
> The file must be called **Helloworld.php**, with a capital `H`. When
> you use Auto Routing, Controller class names MUST start with an
> uppercase letter and ONLY the first character can be uppercase.
>
> Since v4.5.0, if you enable the `$translateUriToCamelCase` option, you
> can use CamelCase classnames. See
> `controller-translate-uri-to-camelcase` for details.

> [!IMPORTANT]
> A controller method that will be executed by Auto Routing (Improved)
> needs HTTP verb (`get`, `post`, `put`, etc.) prefix like `getIndex()`,
> `postCreate()`.

Now visit your site using a URL similar to this:

    example.com/index.php/helloworld

If you did it right you should see:

    Hello World!

This is valid:

<div class="literalinclude">

controllers/009.php

</div>

This is **not** valid:

<div class="literalinclude">

controllers/010.php

</div>

This is **not** valid:

<div class="literalinclude">

controllers/011.php

</div>

> [!NOTE]
> Since v4.5.0, if you enable the `$translateUriToCamelCase` option, you
> can use CamelCase classnames like above. See
> `controller-translate-uri-to-camelcase` for details.

Also, always make sure your controller extends the parent controller
class so that it can inherit all its methods.

> [!NOTE]
> The system will attempt to match the URI against Controllers by
> matching each segment against directories/files in
> **app/Controllers**, when a match wasn't found against defined routes.
> That's why your directories/files MUST start with a capital letter and
> the rest MUST be lowercase.
>
> If you want another naming convention you need to manually define it
> using the `Defined Route Routing <defined-route-routing>`. Here is an
> example based on PSR-4 Autoloader:
>
> <div class="literalinclude">
>
> controllers/012.php
>
> </div>

### Methods

#### Method Visibility

When you define a method that is executable via HTTP request, the method
must be declared as `public`.

> [!WARNING]
> For security reasons be sure to declare any new utility methods as
> `protected` or `private`.

#### Default Method

In the above example, the method name is `getIndex()`. The method (HTTP
verb + `Index()`) is called the **default method**, and is loaded if the
**second segment** of the URI is empty.

#### Normal Methods

The second segment of the URI determines which method in the controller
gets called.

Let's try it. Add a new method to your controller:

<div class="literalinclude">

controllers/021.php

</div>

Now load the following URL to see the `getComment()` method:

    example.com/index.php/helloworld/comment/

You should see your new message.

### Passing URI Segments to Your Methods

If your URI contains more than two segments they will be passed to your
method as parameters.

For example, let's say you have a URI like this:

    example.com/index.php/products/shoes/sandals/123

Your method will be passed URI segments 3 and 4 (`'sandals'` and
`'123'`):

<div class="literalinclude">

controllers/022.php

</div>

### Default Controller

The Default Controller is a special controller that is used when a URI
ends with a directory name or when a URI is not present, as will be the
case when only your site root URL is requested.

#### Defining a Default Controller

Let's try it with the `Helloworld` controller.

To specify a default controller open your **app/Config/Routes.php** file
and set this variable:

<div class="literalinclude">

controllers/015.php

</div>

Where `Helloworld` is the name of the controller class you want to be
used.

A few lines further down **Routes.php** in the "Route Definitions"
section, comment out the line:

<div class="literalinclude">

controllers/016.php

</div>

If you now browse to your site without specifying any URI segments
you'll see the "Hello World" message.

> [!IMPORTANT]
> When you use Auto Routing (Improved), you must remove the line
> `$routes->get('/', 'Home::index');`. Because defined routes take
> precedence over Auto Routing, and controllers defined in the defined
> routes are denied access by Auto Routing (Improved) for security
> reasons.

For more information, please refer to the `routes-configuration-options`
section of the
`URI Routing <routing-auto-routing-improved-configuration-options>`
documentation.

### Default Method Fallback

<div class="versionadded">

4.4.0

</div>

If the controller method corresponding to the URI segment of the method
name does not exist, and if the default method is defined, the remaining
URI segments are passed to the default method for execution.

<div class="literalinclude">

controllers/024.php

</div>

Load the following URL:

    example.com/index.php/product/15/edit

The method will be passed URI segments 2 and 3 (`'15'` and `'edit'`):

> [!IMPORTANT]
> If there are more parameters in the URI than the method parameters,
> Auto Routing (Improved) does not execute the method, and it results in
> 404 Not Found.

#### Fallback to Default Controller

If the controller corresponding to the URI segment of the controller
name does not exist, and if the default controller (`Home` by default)
exists in the directory, the remaining URI segments are passed to the
default controller's default method.

For example, when you have the following default controller `Home` in
the **app/Controllers/News** directory:

<div class="literalinclude">

controllers/025.php

</div>

Load the following URL:

    example.com/index.php/news/101

The `News\Home` controller and the default `getIndex()` method will be
found. So the default method will be passed URI segments 2 (`'101'`):

> [!NOTE]
> If there is `App\Controllers\News` controller, it takes precedence.
> The URI segments are searched sequentially and the first controller
> found is used.

> [!NOTE]
> If there are more parameters in the URI than the method parameters,
> Auto Routing (Improved) does not execute the method, and it results in
> 404 Not Found.

### Organizing Your Controllers into Sub-directories

If you are building a large application you might want to hierarchically
organize or structure your controllers into sub-directories. CodeIgniter
permits you to do this.

Simply create sub-directories under the main **app/Controllers**, and
place your controller classes within them.

> [!IMPORTANT]
> Directory names MUST start with an uppercase letter and ONLY the first
> character can be uppercase.
>
> Since v4.5.0, if you enable the `$translateUriToCamelCase` option, you
> can use CamelCase directory names. See
> `controller-translate-uri-to-camelcase` for details.

When using this feature the first segment of your URI must specify the
directory. For example, let's say you have a controller located here:

    app/Controllers/Products/Shoes.php

To call the above controller your URI will look something like this:

    example.com/index.php/products/shoes/show/123

> [!NOTE]
> You cannot have directories with the same name in **app/Controllers**
> and **public**. This is because if there is a directory, the web
> server will search for it and it will not be routed to CodeIgniter.

Each of your sub-directories may contain a default controller which will
be called if the URL contains *only* the sub-directory. Simply put a
controller in there that matches the name of your default controller as
specified in your **app/Config/Routes.php** file.

CodeIgniter also permits you to map your URIs using its
`Defined Route Routing <defined-route-routing>`..

### Translate URI To CamelCase

<div class="versionadded">

4.5.0

</div>

Since v4.5.0, the `$translateUriToCamelCase` option has been
implemented, which works well with the current CodeIgniter's coding
standards.

This option enables you to automatically translate URI with dashes (`-`)
to CamelCase in the controller and method URI segments.

For example, the URI `sub-dir/hello-controller/some-method` will execute
the `SubDir\HelloController::getSomeMethod()` method.

> [!NOTE]
> When this option is enabled, the `$translateURIDashes` option is
> ignored.

#### Enable Translate URI To CamelCase

To enable it, you need to change the setting `$translateUriToCamelCase`
option to `true` in **app/Config/Routing.php**:

    public bool $translateUriToCamelCase = true;

## Auto Routing (Legacy)

> [!IMPORTANT]
> This feature exists only for backward compatibility. Do not use it in
> new projects. Even if you are already using it, we recommend that you
> use the `auto-routing-improved` instead.

This section describes the functionality of Auto Routing (Legacy) that
is a routing system from CodeIgniter 3. It automatically routes an HTTP
request, and executes the corresponding controller method without route
definitions. The auto-routing is disabled by default.

> [!WARNING]
> To prevent misconfiguration and miscoding, we recommend that you do
> not use Auto Routing (Legacy). It is easy to create vulnerable apps
> where controller filters or CSRF protection are bypassed.

> [!IMPORTANT]
> Auto Routing (Legacy) routes a HTTP request with **any** HTTP method
> to a controller method.

> [!IMPORTANT]
> Since v4.5.0, if Auto Routing (Legacy) doesn't find the controller, it
> will throw `PageNotFoundException` exception before the Controller
> Filters execute.

Consider this URI:

    example.com/index.php/helloworld/

In the above example, CodeIgniter would attempt to find a controller
named **Helloworld.php** and load it.

> [!NOTE]
> When a controller's short name matches the first segment of a URI, it
> will be loaded.

### Let's try it: Hello World! (Legacy)

Let's create a simple controller so you can see it in action. Using your
text editor, create a file called **Helloworld.php**, and put the
following code in it. You will notice that the `Helloworld` Controller
is extending the `BaseController`. you can also extend the
`CodeIgniter\Controller` if you do not need the functionality of the
BaseController.

The BaseController provides a convenient place for loading components
and performing functions that are needed by all your controllers. You
can extend this class in any new controller.

For security reasons be sure to declare any new utility methods as
`protected` or `private`:

<div class="literalinclude">

controllers/008.php

</div>

Then save the file to your **app/Controllers** directory.

> [!IMPORTANT]
> The file must be called **Helloworld.php**, with a capital `H`. When
> you use Auto Routing, Controller class names MUST start with an
> uppercase letter and ONLY the first character can be uppercase.

Now visit your site using a URL similar to this:

    example.com/index.php/helloworld

If you did it right you should see:

    Hello World!

This is valid:

<div class="literalinclude">

controllers/009.php

</div>

This is **not** valid:

<div class="literalinclude">

controllers/010.php

</div>

This is **not** valid:

<div class="literalinclude">

controllers/011.php

</div>

Also, always make sure your controller extends the parent controller
class so that it can inherit all its methods.

> [!NOTE]
> The system will attempt to match the URI against Controllers by
> matching each segment against directories/files in
> **app/Controllers**, when a match wasn't found against defined routes.
> That's why your directories/files MUST start with a capital letter and
> the rest MUST be lowercase.
>
> If you want another naming convention you need to manually define it
> using the `Defined Route Routing <defined-route-routing>`. Here is an
> example based on PSR-4 Autoloader:
>
> <div class="literalinclude">
>
> controllers/012.php
>
> </div>

### Methods (Legacy)

In the above example, the method name is `index()`. The `index()` method
is always loaded by default if the **second segment** of the URI is
empty. Another way to show your "Hello World" message would be this:

    example.com/index.php/helloworld/index/

**The second segment of the URI determines which method in the
controller gets called.**

Let's try it. Add a new method to your controller:

<div class="literalinclude">

controllers/013.php

</div>

Now load the following URL to see the comment method:

    example.com/index.php/helloworld/comment/

You should see your new message.

### Passing URI Segments to Your Methods (Legacy)

If your URI contains more than two segments they will be passed to your
method as parameters.

For example, let's say you have a URI like this:

    example.com/index.php/products/shoes/sandals/123

Your method will be passed URI segments 3 and 4 (`'sandals'` and
`'123'`):

<div class="literalinclude">

controllers/014.php

</div>

### Default Controller (Legacy)

The Default Controller is a special controller that is used when a URI
end with a directory name or when a URI is not present, as will be the
case when only your site root URL is requested.

#### Defining a Default Controller (Legacy)

Let's try it with the `Helloworld` controller.

To specify a default controller open your **app/Config/Routes.php** file
and set this variable:

<div class="literalinclude">

controllers/015.php

</div>

Where `Helloworld` is the name of the controller class you want to be
used.

A few lines further down **Routes.php** in the "Route Definitions"
section, comment out the line:

<div class="literalinclude">

controllers/016.php

</div>

If you now browse to your site without specifying any URI segments
you'll see the "Hello World" message.

> [!NOTE]
> The line `$routes->get('/', 'Home::index');` is an optimization that
> you will want to use in a "real-world" app. But for demonstration
> purposes we don't want to use that feature. `$routes->get()` is
> explained in `URI Routing <routing>`

For more information, please refer to the `routes-configuration-options`
section of the
`URI Routing <routing-auto-routing-legacy-configuration-options>`
documentation.

### Organizing Your Controllers into Sub-directories (Legacy)

If you are building a large application you might want to hierarchically
organize or structure your controllers into sub-directories. CodeIgniter
permits you to do this.

Simply create sub-directories under the main **app/Controllers**, and
place your controller classes within them.

> [!IMPORTANT]
> Directory names MUST start with an uppercase letter and ONLY the first
> character can be uppercase.

When using this feature the first segment of your URI must specify the
directory. For example, let's say you have a controller located here:

    app/Controllers/Products/Shoes.php

To call the above controller your URI will look something like this:

    example.com/index.php/products/shoes/show/123

> [!NOTE]
> You cannot have directories with the same name in **app/Controllers**
> and **public/**. This is because if there is a directory, the web
> server will search for it and it will not be routed to CodeIgniter.

Each of your sub-directories may contain a default controller which will
be called if the URL contains *only* the sub-directory. Simply put a
controller in there that matches the name of your default controller as
specified in your **app/Config/Routes.php** file.

CodeIgniter also permits you to map your URIs using its
`Defined Route Routing <defined-route-routing>`..

## Remapping Method Calls

> [!NOTE]
> **Auto Routing (Improved)** does not support this feature
> intentionally.

As noted above, the second segment of the URI typically determines which
method in the controller gets called. CodeIgniter permits you to
override this behavior through the use of the `_remap()` method:

<div class="literalinclude">

controllers/017.php

</div>

> [!IMPORTANT]
> If your controller contains a method named `_remap()`, it will
> **always** get called regardless of what your URI contains. It
> overrides the normal behavior in which the URI determines which method
> is called, allowing you to define your own method routing rules.

The overridden method call (typically the second segment of the URI)
will be passed as a parameter to the `_remap()` method:

<div class="literalinclude">

controllers/018.php

</div>

Any extra segments after the method name are passed into `_remap()`.
These parameters can be passed to the method to emulate CodeIgniter's
default behavior.

Example:

<div class="literalinclude">

controllers/019.php

</div>

## Extending the Controller

If you want to extend the controller, see `../extending/basecontroller`.

## That's it!

That, in a nutshell, is all there is to know about controllers.