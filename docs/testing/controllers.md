# Testing Controllers

Testing your controllers is made convenient with a couple of new helper
classes and traits. When testing controllers, you can execute the code
within a controller, without first running through the entire
application bootstrap process. Often times, using the
`Feature Testing tools <feature>` will be simpler, but this
functionality is here in case you need it.

> [!NOTE]
> Because the entire framework has not been bootstrapped, there will be
> times when you cannot test a controller this way.

<div class="contents" local="" depth="2">

</div>

## The Helper Trait

To enable Controller Testing you need to use the `ControllerTestTrait`
trait within your tests:

<div class="literalinclude">

controllers/001.php

</div>

Once the trait has been included, you can start setting up the
environment, including the request and response classes, the request
body, URI, and more. You specify the controller to use with the
`controller()` method, passing in the fully qualified class name of your
controller. Finally, call the `execute()` method with the name of the
method to run as the parameter:

<div class="literalinclude">

controllers/002.php

</div>

## Helper Methods

### controller(\$class)

Specifies the class name of the controller to test. The first parameter
must be a fully qualified class name (i.e., include the namespace):

<div class="literalinclude">

controllers/003.php

</div>

### execute(string \$method, ...\$params)

Executes the specified method within the controller. The first parameter
is the name of the method to run:

<div class="literalinclude">

controllers/004.php

</div>

By specifying the second and subsequent parameters, you can pass them to
the controller method.

This returns a new helper class that provides a number of routines for
checking the response itself. See below for details.

### withConfig(\$config)

Allows you to pass in a modified version of **app/Config/App.php** to
test with different settings:

<div class="literalinclude">

controllers/005.php

</div>

If you do not provide one, the application's App config file will be
used.

### withRequest(\$request)

Allows you to provide an **IncomingRequest** instance tailored to your
testing needs:

<div class="literalinclude">

controllers/006.php

</div>

If you do not provide one, a new IncomingRequest instance with the
default application values will be passed into your controller.

### withResponse(\$response)

Allows you to provide a **Response** instance:

<div class="literalinclude">

controllers/007.php

</div>

If you do not provide one, a new Response instance with the default
application values will be passed into your controller.

### withLogger(\$logger)

Allows you to provide a **Logger** instance:

<div class="literalinclude">

controllers/008.php

</div>

If you do not provide one, a new Logger instance with the default
configuration values will be passed into your controller.

### withURI(string \$uri)

Allows you to provide a new URI that simulates the URL the client was
visiting when this controller was run. This is helpful if you need to
check URI segments within your controller. The only parameter is a
string representing a valid URI:

<div class="literalinclude">

controllers/009.php

</div>

It is a good practice to always provide the URI during testing to avoid
surprises.

> [!NOTE]
> Since v4.4.0, this method creates a new Request instance with the URI.
> Because the Request instance should have the URI instance. Also if the
> hostname in the URI string is invalid with `Config\App`, the valid
> hostname will be set.

### withBody(\$body)

Allows you to provide a custom body for the request. This can be helpful
when testing API controllers where you need to set a JSON value as the
body. The only parameter is a string that represents the body of the
request:

<div class="literalinclude">

controllers/010.php

</div>

## Checking the Response

`ControllerTestTrait::execute()` returns an instance of a
`TestResponse`. See `Testing Responses <response>` on how to use this
class to perform additional assertions and verification in your test
cases.

## Filter Testing

Similar to Controller Testing, the framework provides tools to help with
creating tests for custom `Filters </incoming/filters>` and your
projects use of them in routing.

### The Helper Trait

Just like with the Controller Tester you need to include the
`FilterTestTrait` in your test cases to enable these features:

<div class="literalinclude">

controllers/011.php

</div>

### Configuration

Because of the logical overlap with Controller Testing `FilterTestTrait`
is designed to work together with `ControllerTestTrait` should you need
both on the same class. Once the trait has been included
`CIUnitTestCase` will detect its `setUp` method and prepare all the
components needed for your tests. Should you need a special
configuration you can alter any of the properties before calling the
support methods:

- `$request` A prepared version of the default `IncomingRequest` service
- `$response` A prepared version of the default `ResponseInterface`
  service
- `$filtersConfig` The default `Config\Filters` configuration (note:
  discovery is handle by `Filters` so this will not include module
  aliases)
- `$filters` An instance of `CodeIgniter\Filters\Filters` using the
  three components above
- `$collection` A prepared version of `RouteCollection` which includes
  the discovery of `Config\Routes`

The default configuration will usually be best for your testing since it
most closely emulates a "live" project, but (for example) if you wanted
to simulate a filter triggering accidentally on an unfiltered route you
could add it to the Config:

<div class="literalinclude">

controllers/012.php

</div>

### Checking Routes

The first helper method is `getFiltersForRoute()` which will simulate
the provided route and return a list of all Filters (by their alias)
that would have run for the given position ("before" or "after"),
without actually executing any controller or routing code. This has a
large performance advantage over Controller and HTTP Testing.

> param string \$route  
> The URI to check
>
> param string \$position  
> The filter method to check, "before" or "after"
>
> returns  
> Aliases for each filter that would have run
>
> rtype  
> string\[\]
>
> Usage example:
>
> <div class="literalinclude">
>
> controllers/013.php
>
> </div>

### Calling Filter Methods

The properties describe in Configuration are all set up to ensure
maximum performance without interfering or interference from other
tests. The next helper method will return a callable method using these
properties to test your Filter code safely and check the results.

> param FilterInterface\|string \$filter  
> The filter instance, class, or alias
>
> param string \$position  
> The filter method to run, "before" or "after"
>
> returns  
> A callable method to run the simulated Filter event
>
> rtype  
> Closure
>
> Usage example:
>
> <div class="literalinclude">
>
> controllers/014.php
>
> </div>
>
> Notice how the `Closure` can take input parameters which are passed to
> your filter method.

### Assertions

In addition to the helper methods above `FilterTestTrait` also comes
with some assertions to streamline your test methods.

#### assertFilter()

The `assertFilter()` method checks that the given route at position uses
the filter (by its alias):

<div class="literalinclude">

controllers/015.php

</div>

#### assertNotFilter()

The `assertNotFilter()` method checks that the given route at position
does not use the filter (by its alias):

<div class="literalinclude">

controllers/016.php

</div>

#### assertHasFilters()

The `assertHasFilters()` method checks that the given route at position
has at least one filter set:

<div class="literalinclude">

controllers/017.php

</div>

#### assertNotHasFilters()

The `assertNotHasFilters()` method checks that the given route at
position has no filters set:

<div class="literalinclude">

controllers/018.php

</div>
