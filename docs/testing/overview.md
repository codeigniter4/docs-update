# Testing

CodeIgniter has been built to make testing both the framework and your
application as simple as possible. Support for
[PHPUnit](https://phpunit.de/) is built in, and the framework provides a
number of convenient helper methods to make testing every aspect of your
application as painless as possible.

<div class="contents" local="" depth="3">

</div>

## System Set Up

### Installing PHPUnit

CodeIgniter uses [PHPUnit](https://phpunit.de/) as the basis for all of
its testing. There are two ways to install PHPUnit to use within your
system.

#### Composer

The recommended method is to install it in your project using
[Composer](https://getcomposer.org/). While it's possible to install it
globally we do not recommend it, since it can cause compatibility issues
with other projects on your system as time goes on.

Ensure that you have Composer installed on your system. From the project
root (the directory that contains the application and system
directories) type the following from the command line:

``` console
composer require --dev phpunit/phpunit
```

This will install the correct version for your current PHP version. Once
that is done, you can run all of the tests for this project by typing:

``` console
vendor/bin/phpunit
```

If you are using Windows, use the following command:

``` console
vendor\bin\phpunit
```

#### Phar

The other option is to download the .phar file from the
[PHPUnit](https://phpunit.de/getting-started/phpunit-9.html) site. This
is a standalone file that should be placed within your project root.

## Testing Your Application

### PHPUnit Configuration

The framework has a `phpunit.xml.dist` file in the project root. This
controls unit testing of the framework itself. If you provide your own
`phpunit.xml`, it will over-ride this.

Your `phpunit.xml` should exclude the `system` folder, as well as any
`vendor` or `ThirdParty` folders, if you are unit testing your
application.

### The Test Class

In order to take advantage of the additional tools provided, your tests
must extend `CIUnitTestCase`. All tests are expected to be located in
the **tests/app** directory by default.

To test a new library, **Foo**, you would create a new file at
**tests/app/Libraries/FooTest.php**:

<div class="literalinclude">

overview/001.php

</div>

To test one of your models, you might end up with something like this in
**tests/app/Models/OneOfMyModelsTest.php**:

<div class="literalinclude">

overview/002.php

</div>

You can create any directory structure that fits your testing
style/needs. When namespacing the test classes, remember that the
**app** directory is the root of the `App` namespace, so any classes you
use must have the correct namespace relative to `App`.

> [!NOTE]
> Namespaces are not strictly required for test classes, but they are
> helpful to ensure no class names collide.

When testing database results, you must use the
`DatabaseTestTrait <database>` in your class.

#### Staging

Most tests require some preparation in order to run correctly. PHPUnit's
`TestCase` provides four methods to help with staging and clean up:

    public static function setUpBeforeClass(): void
    public static function tearDownAfterClass(): void

    protected function setUp(): void
    protected function tearDown(): void

The static methods `setUpBeforeClass()` and `tearDownAfterClass()` run
before and after the entire test case, whereas the protected methods
`setUp()` and `tearDown()` run between each test.

If you implement any of these special functions make sure you run their
parent as well so extended test cases do not interfere with staging:

<div class="literalinclude">

overview/003.php

</div>

#### Traits

A common way to enhance your tests is by using traits to consolidate
staging across different test cases. `CIUnitTestCase` will detect any
class traits and look for staging methods to run named for the trait
itself (i.e. <span class="title-ref">setUp{NameOfTrait}()</span> and
<span class="title-ref">tearDown{NameOfTrait}()</span>).

For example, if you needed to add authentication to some of your test
cases you could create an authentication trait with a set up method to
fake a logged in user:

<div class="literalinclude">

overview/006.php

</div>

#### Additional Assertions

`CIUnitTestCase` provides additional unit testing assertions that you
might find useful.

##### assertLogged(\$level, \$expectedMessage)

Ensure that something you expected to be logged was actually logged:

##### assertLogContains(\$level, \$logMessage)

Ensure that there's a record in the logs which contains a message part.

<div class="literalinclude">

overview/007.php

</div>

##### assertEventTriggered(\$eventName)

Ensure that an event you expected to be triggered actually was:

<div class="literalinclude">

overview/008.php

</div>

##### assertHeaderEmitted(\$header, \$ignoreCase = false)

Ensure that a header or cookie was actually emitted:

<div class="literalinclude">

overview/009.php

</div>

> [!NOTE]
> the test case with this should be [run as a separate process in
> PHPunit](https://docs.phpunit.de/en/9.6/annotations.html#runinseparateprocess).

##### assertHeaderNotEmitted(\$header, \$ignoreCase = false)

Ensure that a header or cookie was not emitted:

<div class="literalinclude">

overview/010.php

</div>

> [!NOTE]
> the test case with this should be [run as a separate process in
> PHPunit](https://docs.phpunit.de/en/9.6/annotations.html#runinseparateprocess).

##### assertCloseEnough(\$expected, \$actual, \$message = '', \$tolerance = 1)

For extended execution time testing, tests that the absolute difference
between expected and actual time is within the prescribed tolerance:

<div class="literalinclude">

overview/011.php

</div>

The above test will allow the actual time to be either 660 or 661
seconds.

##### assertCloseEnoughString(\$expected, \$actual, \$message = '', \$tolerance = 1)

For extended execution time testing, tests that the absolute difference
between expected and actual time, formatted as strings, is within the
prescribed tolerance:

<div class="literalinclude">

overview/012.php

</div>

The above test will allow the actual time to be either 660 or 661
seconds.

#### Accessing Protected/Private Properties

When testing, you can use the following setter and getter methods to
access protected and private methods and properties in the classes that
you are testing.

##### getPrivateMethodInvoker(\$instance, \$method)

Enables you to call private methods from outside the class. This returns
a function that can be called. The first parameter is an instance of the
class to test. The second parameter is the name of the method you want
to call.

<div class="literalinclude">

overview/013.php

</div>

##### getPrivateProperty(\$instance, \$property)

Retrieves the value of a private/protected class property from an
instance of a class. The first parameter is an instance of the class to
test. The second parameter is the name of the property.

<div class="literalinclude">

overview/014.php

</div>

##### setPrivateProperty(\$instance, \$property, \$value)

Set a protected value within a class instance. The first parameter is an
instance of the class to test. The second parameter is the name of the
property to set the value of. The third parameter is the value to set it
to:

<div class="literalinclude">

overview/015.php

</div>

### Mocking Services

You will often find that you need to mock one of the services defined in
**app/Config/Services.php** to limit your tests to only the code in
question, while simulating various responses from the services. This is
especially true when testing controllers and other integration testing.
The **Services** class provides the following methods to simplify this.

#### Services::injectMock()

This method allows you to define the exact instance that will be
returned by the Services class. You can use this to set properties of a
service so that it behaves in a certain way, or replace a service with a
mocked class.

<div class="literalinclude">

overview/016.php

</div>

The first parameter is the service that you are replacing. The name must
match the function name in the Services class exactly. The second
parameter is the instance to replace it with.

#### Services::reset()

Removes all mocked classes from the Services class, bringing it back to
its original state.

You can also use the `$this->resetServices()` method that
`CIUnitTestCase` provides.

> [!NOTE]
> This method resets the all states of Services, and the
> `RouteCollection` will have no routes. If you want to use your routes
> to be loaded, you need to call the `loadRoutes()` method like
> `Services::routes()->loadRoutes()`.

#### Services::resetSingle(string \$name)

Removes any mock and shared instances for a single service, by its name.

> [!NOTE]
> The `Cache`, `Email` and `Session` services are mocked by default to
> prevent intrusive testing behavior. To prevent these from mocking
> remove their method callback from the class property:
> `$setUpMethods = ['mockEmail', 'mockSession'];`

### Mocking Factory Instances

Similar to Services, you may find yourself needing to supply a
pre-configured class instance during testing that will be used with
`Factories`. Use the same `Factories::injectMock()` and
`Factories::reset()` static methods like **Services**, but they take an
additional preceding parameter for the component name:

<div class="literalinclude">

overview/017.php

</div>

> [!NOTE]
> All component Factories are reset by default between each test. Modify
> your test case's `$setUpMethods` if you need instances to persist.

### Testing and Time

Testing time-dependent code can be challenging. However, when using the
`Time <../libraries/time>` class, the current time can be fixed or
changed at will during testing.

Below is a sample test code that fixes the current time:

<div class="literalinclude">

overview/021.php

</div>

You can fix the current time with the `Time::setTestNow()` method.
Optionally, you can specify a locale as the second parameter.

Don't forget to reset the current time after the test with calling it
without parameters.
