# Generating Test Data

Often you will need sample data for your application to run its tests. The `Fabricator` class uses fzaninotto's \[Faker](#https://github.com/FakerPHP/Faker)\_ to turn models into generators of random data. Use fabricators in your seeds or test cases to stage fake data for your unit tests.

- [Supported Models](#supported-models)
- [Loading Fabricators](#loading-fabricators)
- [Defining Formatters](#defining-formatters)
    - [Advanced Formatting](#advanced-formatting)
- [Localization](#localization)
- [Faking the Data](#faking-the-data)
- [Specifying Test Data](#specifying-test-data)
- [Test Helper](#test-helper)
- [Table Counts](#table-counts)
    - [Methods](#methods)

## Supported Models

`Fabricator` supports any model that extends the framework's core model, `CodeIgniter\Model`. You may use your own custom models by ensuring they implement `CodeIgniter\Test\Interfaces\FabricatorModel`:

```php
--8<--
testing/fabricator/001.php
--8<--
```

!!! note "Note"
    In addition to methods, the interface outlines some necessary properties for the target model. Please see the interface code for details.

## Loading Fabricators

At its most basic a fabricator takes the model to act on:

```php
--8<--
testing/fabricator/002.php
--8<--
```

The parameter can be a string specifying the name of the model, or an instance of the model itself:

```php
--8<--
testing/fabricator/003.php
--8<--
```

## Defining Formatters

Faker generates data by requesting it from a formatter. With no formatters defined, `Fabricator` will attempt to guess at the most appropriate fit based on the field name and properties of the model it represents, falling back on `$fabricator->defaultFormatter`. This may be fine if your field names correspond with common formatters, or if you don't care much about the content of the fields, but most of the time you will want to specify the formatters to use as the second parameter to the constructor:

```php
--8<--
testing/fabricator/004.php
--8<--
```

You can also change the formatters after a fabricator is initialized by using the `setFormatters()` method.

### Advanced Formatting

Sometimes the default return of a formatter is not enough. Faker providers allow parameters to most formatters to further limit the scope of random data. A fabricator will check its representative model for the `fake()` method where you can define exactly what the faked data should look like:

```php
--8<--
testing/fabricator/005.php
--8<--
```

Notice in this example how the first three values are equivalent to the formatters from before. However for `avatar` we have requested an image size other than the default and `login` uses a conditional based on app configuration, neither of which are possible using the `$formatters` parameter. You may want to keep your test data separate from your production models, so it is a good practice to define a child class in your test support folder:

```php
--8<--
testing/fabricator/006.php
--8<--
```

## Localization

Faker supports a lot of different locales. Check their documentation to determine which providers support your locale. Specify a locale in the third parameter while initiating a fabricator:

```php
--8<--
testing/fabricator/007.php
--8<--
```

If no locale is specified it will use the one defined in **app/Config/App.php** as `defaultLocale`. You can check the locale of an existing fabricator using its `getLocale()` method.

## Faking the Data

Once you have a properly-initialized fabricator it is easy to generate test data with the `make()` command:

```php
--8<--
testing/fabricator/008.php
--8<--
```

You might get back something like this:

```php
--8<--
testing/fabricator/009.php
--8<--
```

You can also get a lot of them back by supplying a count:

```php
--8<--
testing/fabricator/010.php
--8<--
```

The return type of `make()` mimics what is defined in the representative model, but you can force a type using the methods directly:

```php
--8<--
testing/fabricator/011.php
--8<--
```

The return from `make()` is ready to be used in tests or inserted into the database. Alternatively `Fabricator` includes the `create()` command to insert it for you, and return the result. Due to model callbacks, database formatting, and special keys like primary and timestamps the return from `create()` can differ from `make()`. You might get back something like this:

```php
--8<--
testing/fabricator/012.php
--8<--
```

Similar to `make()` you can supply a count to insert and return an array of objects:

```php
--8<--
testing/fabricator/013.php
--8<--
```

Finally, there may be times you want to test with the full database object but you are not actually using a database. `create()` takes a second parameter to allowing mocking the object, returning the object with extra database fields above without actually touching the database:

```php
--8<--
testing/fabricator/014.php
--8<--
```

## Specifying Test Data

Generated data is great, but sometimes you may want to supply a specific field for a test without compromising your formatters configuration. Rather then creating a new fabricator for each variant you can use `setOverrides()` to specify the value for any fields:

```php
--8<--
testing/fabricator/015.php
--8<--
```

Now any data generated with `make()` or `create()` will always use "Bobby" for the `first` field:

```php
--8<--
testing/fabricator/016.php
--8<--
```

`setOverrides()` can take a second parameter to indicate whether this should be a persistent override or only for a single action:

```php
--8<--
testing/fabricator/017.php
--8<--
```

Notice after the first return the fabricator stops using the overrides:

```php
--8<--
testing/fabricator/018.php
--8<--
```

If no second parameter is supplied then passed values will persist by default.

## Test Helper

Often all you will need is a one-and-done fake object for testing. The Test Helper provides the `fake($model, $overrides, $persist = true)` function to do just this:

```php
--8<--
testing/fabricator/019.php
--8<--
```

This is equivalent to:

```php
--8<--
testing/fabricator/020.php
--8<--
```

If you just need a fake object without saving it to the database you can pass false into the persist parameter.

## Table Counts

Frequently your faked data will depend on other faked data. `Fabricator` provides a static count of the number of faked items you have created for each table. Consider the following example:

Your project has users and groups. In your test case you want to create various scenarios with groups of different sizes, so you use `Fabricator` to create a bunch of groups. Now you want to create fake users but don't want to assign them to a non-existent group ID. Your model's fake method could look like this:

```php
--8<--
testing/fabricator/021.php
--8<--
```

Now creating a new user will ensure it is a part of a valid group: `$user = fake(UserModel::class);`

### Methods

`Fabricator` handles the counts internally but you can also access these static methods to assist with using them:

#### getCount(string $table): int

Return the current value for a specific table (default: 0).

#### setCount(string $table, int $count): int

Set the value for a specific table manually, for example if you create some test items without using a fabricator that you still wanted factored into the final counts.

#### upCount(string $table): int

Increment the value for a specific table by one and return the new value. (This is what is used internally with `Fabricator::create()`).

#### downCount(string $table): int

Decrement the value for a specific table by one and return the new value, for example if you deleted a fake item but wanted to track the change.

#### resetCounts()

Resets all counts. Good idea to call this between test cases (though using `CIUnitTestCase::$refresh = true` does it automatically).
