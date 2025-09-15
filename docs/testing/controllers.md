# Testing Controllers

Testing your controllers is made convenient with a couple of new helper classes and traits. When testing controllers, you can execute the code within a controller, without first running through the entire application bootstrap process. Often times, using the [Feature Testing tools](#feature) will be simpler, but this functionality is here in case you need it.

!!! note "Note"
    Because the entire framework has not been bootstrapped, there will be times when you cannot test a controller this way.

- [The Helper Trait](#the-helper-trait)
- [Helper Methods](#helper-methods)
    - [controller($class)](#controllerclass)
    - [execute(string $method, ...$params)](#executestring-method-params)
    - [withConfig($config)](#withconfigconfig)
    - [withRequest($request)](#withrequestrequest)
    - [withResponse($response)](#withresponseresponse)
    - [withLogger($logger)](#withloggerlogger)
    - [withURI(string $uri)](#withuristring-uri)
    - [withBody($body)](#withbodybody)
- [Checking the Response](#checking-the-response)
- [Filter Testing](#filter-testing)
    - [The Helper Trait](#the-helper-trait)
    - [Configuration](#configuration)
    - [Checking Routes](#checking-routes)
    - [Calling Filter Methods](#calling-filter-methods)
    - [Assertions](#assertions)

## The Helper Trait

To enable Controller Testing you need to use the `ControllerTestTrait` trait within your tests:

```php
--8<--
testing/controllers/001.php
--8<--
```

Once the trait has been included, you can start setting up the environment, including the request and response classes, the request body, URI, and more. You specify the controller to use with the `controller()` method, passing in the fully qualified class name of your controller. Finally, call the `execute()` method with the name of the method to run as the parameter:

```php
--8<--
testing/controllers/002.php
--8<--
```

## Helper Methods

### controller($class)

Specifies the class name of the controller to test. The first parameter must be a fully qualified class name (i.e., include the namespace):

```php
--8<--
testing/controllers/003.php
--8<--
```

### execute(string $method, ...$params)

Executes the specified method within the controller. The first parameter is the name of the method to run:

```php
--8<--
testing/controllers/004.php
--8<--
```

By specifying the second and subsequent parameters, you can pass them to the controller method.

This returns a new helper class that provides a number of routines for checking the response itself. See below for details.

### withConfig($config)

Allows you to pass in a modified version of **app/Config/App.php** to test with different settings:

```php
--8<--
testing/controllers/005.php
--8<--
```

If you do not provide one, the application's App config file will be used.

### withRequest($request)

Allows you to provide an **IncomingRequest** instance tailored to your testing needs:

```php
--8<--
testing/controllers/006.php
--8<--
```

If you do not provide one, a new IncomingRequest instance with the default application values will be passed into your controller.

### withResponse($response)

Allows you to provide a **Response** instance:

```php
--8<--
testing/controllers/007.php
--8<--
```

If you do not provide one, a new Response instance with the default application values will be passed into your controller.

### withLogger($logger)

Allows you to provide a **Logger** instance:

```php
--8<--
testing/controllers/008.php
--8<--
```

If you do not provide one, a new Logger instance with the default configuration values will be passed into your controller.

### withURI(string $uri)

Allows you to provide a new URI that simulates the URL the client was visiting when this controller was run. This is helpful if you need to check URI segments within your controller. The only parameter is a string representing a valid URI:

```php
--8<--
testing/controllers/009.php
--8<--
```

It is a good practice to always provide the URI during testing to avoid surprises.

!!! note "Note"
    Since v4.4.0, this method creates a new Request instance with the URI. Because the Request instance should have the URI instance. Also if the hostname in the URI string is invalid with `Config\App`, the valid hostname will be set.

### withBody($body)

Allows you to provide a custom body for the request. This can be helpful when testing API controllers where you need to set a JSON value as the body. The only parameter is a string that represents the body of the request:

```php
--8<--
testing/controllers/010.php
--8<--
```

## Checking the Response

`ControllerTestTrait::execute()` returns an instance of a `TestResponse`. See [Testing Responses](#response) on how to use this class to perform additional assertions and verification in your test cases.

## Filter Testing

Similar to Controller Testing, the framework provides tools to help with creating tests for custom [Filters](#/incoming/filters) and your projects use of them in routing.

### The Helper Trait

Just like with the Controller Tester you need to include the `FilterTestTrait` in your test cases to enable these features:

```php
--8<--
testing/controllers/011.php
--8<--
```

### Configuration

Because of the logical overlap with Controller Testing `FilterTestTrait` is designed to work together with `ControllerTestTrait` should you need both on the same class. Once the trait has been included `CIUnitTestCase` will detect its `setUp` method and prepare all the components needed for your tests. Should you need a special configuration you can alter any of the properties before calling the support methods:

- `$request` A prepared version of the default `IncomingRequest` service

- `$response` A prepared version of the default `ResponseInterface` service

- `$filtersConfig` The default `Config\Filters` configuration (note: discovery is handle by `Filters` so this will not include module aliases)

- `$filters` An instance of `CodeIgniter\Filters\Filters` using the three components above

- `$collection` A prepared version of `RouteCollection` which includes the discovery of `Config\Routes`

The default configuration will usually be best for your testing since it most closely emulates a "live" project, but (for example) if you wanted to simulate a filter triggering accidentally on an unfiltered route you could add it to the Config:

```php
--8<--
testing/controllers/012.php
--8<--
```

### Checking Routes

The first helper method is `getFiltersForRoute()` which will simulate the provided route and return a list of all Filters (by their alias) that would have run for the given position ("before" or "after"), without actually executing any controller or routing code. This has a large performance advantage over Controller and HTTP Testing.

#### getFiltersForRoute($route, $position)

- **Parameters**  

- **$route** `string` The URI to check

- **$position** `string` The filter method to check, "before" or "after"

- **Returns**: Aliases for each filter that would have run

- **Return type**: `string[]`

Usage example:

```php
--8<--
testing/controllers/013.php
--8<--
```

### Calling Filter Methods

The properties describe in Configuration are all set up to ensure maximum performance without interfering or interference from other tests. The next helper method will return a callable method using these properties to test your Filter code safely and check the results.

#### getFilterCaller($filter, $position)

- **Parameters**  

- **$filter** `FilterInterface|string` The filter instance, class, or alias

- **$position** `string` The filter method to run, "before" or "after"

- **Returns**: A callable method to run the simulated Filter event

- **Return type**: `Closure`

Usage example:

```php
--8<--
testing/controllers/014.php
--8<--
```

Notice how the `Closure` can take input parameters which are passed to your filter method.

### Assertions

In addition to the helper methods above `FilterTestTrait` also comes with some assertions to streamline your test methods.

#### assertFilter()

The `assertFilter()` method checks that the given route at position uses the filter (by its alias):

```php
--8<--
testing/controllers/015.php
--8<--
```

#### assertNotFilter()

The `assertNotFilter()` method checks that the given route at position does not use the filter (by its alias):

```php
--8<--
testing/controllers/016.php
--8<--
```

#### assertHasFilters()

The `assertHasFilters()` method checks that the given route at position has at least one filter set:

```php
--8<--
testing/controllers/017.php
--8<--
```

#### assertNotHasFilters()

The `assertNotHasFilters()` method checks that the given route at position has no filters set:

```php
--8<--
testing/controllers/018.php
--8<--
```
