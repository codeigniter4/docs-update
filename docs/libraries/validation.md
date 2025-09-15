# Validation

CodeIgniter provides a comprehensive data validation class that helps minimize the amount of code you'll write.

- [Overview](#overview)
- [Form Validation Tutorial](#form-validation-tutorial)
    - [The Form](#the-form)
    - [The Success Page](#the-success-page)
    - [The Controller](#the-controller)
    - [The Routes](#the-routes)
    - [Try it!](#try-it)
    - [Explanation](#explanation)
    - [Add Validation Rules](#add-validation-rules)
- [Config for Validation](#config-for-validation)
    - [Traditional and Strict Rules](#traditional-and-strict-rules)
- [Loading the Library](#loading-the-library)
- [How Validation Works](#how-validation-works)
- [Setting Validation Rules](#setting-validation-rules)
    - [Setting a Single Rule](#setting-a-single-rule)
    - [Setting Multiple Rules](#setting-multiple-rules)
    - [Setting Rules for Array Data](#setting-rules-for-array-data)
    - [withRequest()](#withrequest)
- [Working with Validation](#working-with-validation)
    - [Running Validation](#running-validation)
    - [Running Multiple Validations](#running-multiple-validations)
    - [Validating 1 Value](#validating-1-value)
    - [Getting Validated Data](#getting-validated-data)
    - [Saving Sets of Validation Rules to the Config File](#saving-sets-of-validation-rules-to-the-config-file)
    - [Validation Placeholders](#validation-placeholders)
- [Working with Errors](#working-with-errors)
    - [Setting Custom Error Messages](#setting-custom-error-messages)
    - [Translation of Messages and Validation Labels](#translation-of-messages-and-validation-labels)
    - [Getting All Errors](#getting-all-errors)
    - [Getting a Single Error](#getting-a-single-error)
    - [Check If Error Exists](#check-if-error-exists)
    - [Redirect and Validation Errors](#redirect-and-validation-errors)
- [Customizing Error Display](#customizing-error-display)
    - [Creating the Views](#creating-the-views)
    - [Configuration](#configuration)
    - [Specifying the Template](#specifying-the-template)
- [Creating Custom Rules](#creating-custom-rules)
    - [Using Rule Classes](#using-rule-classes)
    - [Using Closure Rule](#using-closure-rule)
    - [Using Callable Rule](#using-callable-rule)
- [Available Rules](#available-rules)
    - [Rules for General Use](#rules-for-general-use)
    - [Rules for File Uploads](#rules-for-file-uploads)

## Overview

Before explaining CodeIgniter's approach to data validation, let's describe the ideal scenario:

1.  A form is displayed.
2.  You fill it in and submit it.
3.  If you submitted something invalid, or perhaps missed a required item, the form is redisplayed containing your data along with an error message describing the problem.
4.  This process continues until you have submitted a valid form.

On the receiving end, the script must:

1.  Check for required data.
2.  Verify that the data is of the correct type, and meets the correct criteria. For example, if a username is submitted it must be validated to contain only permitted characters. It must be of a minimum length, and not exceed a maximum length. The username can't be someone else's existing username, or perhaps even a reserved word. Etc.
3.  Sanitize the data for security.
4.  Pre-format the data if needed.
5.  Prep the data for insertion in the database.

Although there is nothing terribly complex about the above process, it usually requires a significant amount of code, and to display error messages, various control structures are usually placed within the form HTML. Form validation, while simple to create, is generally very messy and tedious to implement.

## Form Validation Tutorial

What follows is a "hands on" tutorial for implementing CodeIgniter's Form Validation.

In order to implement form validation you'll need three things:

1.  A [View](#/outgoing/views) file containing a form.
2.  A View file containing a "success" message to be displayed upon successful submission.
3.  A [controller](#/incoming/controllers) method to receive and process the submitted data.

Let's create those three things, using a member sign-up form as the example.

### The Form

Using a text editor, create a form called **signup.php**. In it, place this code and save it to your **app/Views/** folder:

    <html>
    <head>
    <title>My Form</title>
    </head>
    <body>

    <?= validation_list_errors() ?>

    <?= form_open('form') ?>

        <h5>Username</h5>
        <input type="text" name="username" value="<?= set_value('username') ?>" size="50">

        <h5>Password</h5>
        <input type="text" name="password" value="<?= set_value('password') ?>" size="50">

        <h5>Password Confirm</h5>
        <input type="text" name="passconf" value="<?= set_value('passconf') ?>" size="50">

        <h5>Email Address</h5>
        <input type="text" name="email" value="<?= set_value('email') ?>" size="50">

        <div><input type="submit" value="Submit"></div>

    <?= form_close() ?>

    </body>
    </html>

### The Success Page

Using a text editor, create a form called **success.php**. In it, place this code and save it to your **app/Views/** folder:

    <html>
    <head>
    <title>My Form</title>
    </head>
    <body>

    <h3>Your form was successfully submitted!</h3>

    <p><?= anchor('form', 'Try it again!') ?></p>

    </body>
    </html>

### The Controller

Using a text editor, create a controller called **Form.php**. In it, place this code and save it to your **app/Controllers/** folder:

```php
--8<--
libraries/validation/001.php
--8<--
```

!!! note "Note"
    The [$this-\>request-\>is()](#incomingrequest-is) method can be used since v4.3.0. In previous versions, you need to use `if (strtolower($this->request->getMethod()) !== 'post')`.

!!! note "Note"
    The [$this-\>validator-\>getValidated()](#validation-getting-validated-data) method can be used since v4.4.0.

### The Routes

Then add routes for the controller in **app/Config/Routes.php**:

```php
--8<--
libraries/validation/039.php:2:
--8<--
```

### Try it!

To try your form, visit your site using a URL similar to this one:

    example.com/index.php/form/

If you submit the form you should simply see the form reload. That's because you haven't set up any validation rules in `controller-validatedata` yet.

The `validateData()` method is a method in the Controller. It uses the **Validation class** inside. See `controller-validatedata`.

!!! note "Note"
    Since you haven't told the `validateData()` method to validate anything yet, it **returns false** (boolean false) **by default**. The `validateData()` method only returns true if it has successfully applied your rules without any of them failing.

### Explanation

You'll notice several things about the above pages.

#### signup.php

The form (**signup.php**) is a standard web form with a couple of exceptions:

1.  It uses a [form helper](#/helpers/form-helper) to create the form opening and closing. Technically, this isn't necessary. You could create the form using standard HTML. However, the benefit of using the helper is that it generates the action URL for you, based on the URL in your config file. This makes your application more portable in the event your URLs change.

2.  At the top of the form you'll notice the following function call: :

    <?= validation_list_errors() ?>

    This function will return any error messages sent back by the validator. If there are no messages it returns an empty string.

#### Form.php

The controller (**Form.php**) has one property: `$helpers`. It loads the form helper used by your view files.

The controller has one method: `index()`. This method returns the **signup** view to show the form when a non-POST request comes. Otherwise, it uses the Controller-provided `controller-validatedata` method. It also runs the validation routine. Based on whether the validation was successful it either presents the form or the success page.

### Add Validation Rules

Then add validation rules in the controller (**Form.php**):

```php
--8<--
libraries/validation/002.php:2:
--8<--
```

If you submit the form you should see the success page or the form with error messages.

## Config for Validation

### Traditional and Strict Rules

CodeIgniter 4 has two kinds of Validation rule classes.

The default rule classes (**Strict Rules**) have the namespace `CodeIgniter\Validation\StrictRules`, and they provide strict validation.

The traditional rule classes (**Traditional Rules**) have the namespace `CodeIgniter\Validation`. They are provided for backward compatibility only. They may not validate non-string values correctly and need not be used in new projects.

!!! note "Note"
    Since v4.3.0, **Strict Rules** are used by default for better security.

#### Strict Rules

!!! success "Available from version 4.2.0"

The **Strict Rules** don't use implicit type conversion.

#### Traditional Rules

!!! important "Important"
    Traditional Rules exist only for backward compatibility. Do not use them in new projects. Even if you are already using them, we recommend switching to Strict Rules.

!!! warning "Warning"
    When validating data that contains non-string values, such as JSON data, you should use **Strict Rules**.

The **Traditional Rules** implicitly assume that string values are validated, and the input value may be converted implicitly to a string value. It works for most basic cases like validating POST data.

However, for example, if you use JSON input data, it may be a type of bool/null/array. When you validate the boolean `true`, it is converted to string `'1'` with the Traditional rule classes. If you validate it with the `integer` rule, `'1'` passes the validation.

#### Using Traditional Rules

!!! warning "Warning"
    The **Traditional Rules** are provided for backward compatibility only. They may not validate non-string values correctly and need not be used in new projects.

If you want to use traditional rules, you need to change the rule classes in **app/Config/Validation.php**:

```php
--8<--
libraries/validation/003.php
--8<--
```

## Loading the Library

The library is loaded as a service named **validation**:

```php
--8<--
libraries/validation/004.php:2:
--8<--
```

This automatically loads the `Config\Validation` file which contains settings for including multiple Rulesets, and collections of rules that can be easily reused.

!!! note "Note"
    You may never need to use this method, as both the [Controller](#/incoming/controllers) and the [Model](#/models/model) provide methods to make validation even easier.

## How Validation Works

- The validation never changes data to be validated.

- The validation checks each field in turn according to the Validation Rules you set. If any rule returns false, the check for that field ends there.

- The Format Rules do not permit empty string. If you want to permit empty string, add the `permit_empty` rule.

- If a field does not exist in the data to be validated, the value is interpreted as `null`. If you want to check that the field exists, add the `field_exists` rule.

!!! note "Note"
    The `field_exists` rule can be used since v4.5.0.

## Setting Validation Rules

CodeIgniter lets you set as many validation rules as you need for a given field, cascading them in order. To set validation rules you will use the `setRule()`, `setRules()`, or `withRequest()` methods.

### Setting a Single Rule

#### setRule()

This method sets a single rule. It has the method signature:

    setRule(string $field, ?string $label, array|string $rules[, array $errors = []])

The `$rules` either takes in a pipe-delimited list of rules or an array collection of rules:

```php
--8<--
libraries/validation/005.php:2:
--8<--
```

The value you pass to `$field` must match the key of any data array that is sent in. If the data is taken directly from `$_POST`, then it must be an exact match for the form input name.

!!! warning "Warning"
    Prior to v4.2.0, this method's third parameter, `$rules`, was typehinted to accept `string`. In v4.2.0 and after, the typehint was removed to allow arrays, too. To avoid LSP being broken in extending classes overriding this method, the child class's method should also be modified to remove the typehint.

### Setting Multiple Rules

#### setRules()

Like `setRule()`, but accepts an array of field names and their rules:

```php
--8<--
libraries/validation/006.php:2:
--8<--
```

To give a labeled error message you can set up as:

```php
--8<--
libraries/validation/007.php:2:
--8<--
```

!!! note "Note"
    `setRules()` will overwrite any rules that were set previously. To add more than one rule to an existing set of rules, use `setRule()` multiple times.

### Setting Rules for Array Data

If your data is in a nested associative array, you can use "dot array syntax" to easily validate your data:

```php
--8<--
libraries/validation/009.php:2:
--8<--
```

You can use the `*` wildcard symbol to match any one level of the array:

```php
--8<--
libraries/validation/010.php:2:
--8<--
```

!!! note "Note"
    Prior to v4.4.4, due to a bug, the wildcard `*` validated data in incorrect dimensions. See [Upgrading](#upgrade-444-validation-with-dot-array-syntax) for details.

"dot array syntax" can also be useful when you have single dimension array data. For example, data returned by multi select dropdown:

```php
--8<--
libraries/validation/011.php:2:
--8<--
```

### withRequest()

!!! important "Important"
    This method exists only for backward compatibility. Do not use it in new projects. Even if you are already using it, we recommend that you use another, more appropriate method.

!!! warning "Warning"
    If you want to validate POST data only, don't use `withRequest()`. This method uses [$request-\>getVar()](#incomingrequest-getting-data) which returns `$_GET`, `$_POST` or `$_COOKIE` data in that order (depending on php.ini <a href="https://www.php.net/manual/en/ini.core.php#ini.request-order" target="_blank">request-order</a>). Newer values override older values. POST values may be overridden by the cookies if they have the same name.

One of the most common times you will use the validation library is when validating data that was input from an HTTP Request. If desired, you can pass an instance of the current Request object and it will take all of the input data and set it as the data to be validated:

```php
--8<--
libraries/validation/008.php:2:
--8<--
```

!!! warning "Warning"
    When you use this method, you should use the [getValidated()](#validation-getting-validated-data) method to get the validated data. Because this method gets JSON data from [$request-\>getJSON()](#incomingrequest-getting-json-data) when the request is a JSON request (`Content-Type: application/json`), or gets Raw data from [$request-\>getRawInput()](#incomingrequest-retrieving-raw-data) when the request is a PUT, PATCH, DELETE request and is not HTML form post (`Content-Type: multipart/form-data`), or gets data from [$request-\>getVar()](#incomingrequest-getting-data), and an attacker could change what data is validated.

!!! note "Note"
    The [getValidated()](#validation-getting-validated-data) method can be used since v4.4.0.

## Working with Validation

### Running Validation

The `run()` method runs validation. It has the method signature:

    run(?array $data = null, ?string $group = null, ?string $dbGroup = null): bool

The `$data` is an array of data to validate. The optional second parameter `$group` is the [predefined group of rules](#validation-array) to apply. The optional third parameter `$dbGroup` is the database group to use.

This method returns true if the validation is successful.

```php
--8<--
libraries/validation/043.php:2:
--8<--
```

### Running Multiple Validations

!!! note "Note"
    `run()` method will not reset error state. Should a previous run fail, `run()` will always return false and `getErrors()` will return all previous errors until explicitly reset.

If you intend to run multiple validations, for instance on different data sets or with different rules after one another, you might need to call `$validation->reset()` before each run to get rid of errors from previous run. Be aware that `reset()` will invalidate any data, rule or custom error you previously set, so `setRules()`, `setRuleGroup()` etc. need to be repeated:

```php
--8<--
libraries/validation/019.php:2:
--8<--
```

### Validating 1 Value

The `check()` method validates one value against the rules. The first parameter `$value` is the value to validate. The second parameter `$rule` is the validation rules. The optional third parameter `$errors` is the the custom error message.

```php
--8<--
libraries/validation/012.php:2:
--8<--
```

!!! note "Note"
    Prior to v4.4.0, this method's second parameter, `$rule`, was typehinted to accept `string`. In v4.4.0 and after, the typehint was removed to allow arrays, too.

!!! note "Note"
    This method calls the `setRule()` method to set the rules internally.

### Getting Validated Data

!!! success "Available from version 4.4.0"

The actual validated data can be retrieved with the `getValidated()` method. This method returns an array of only those elements that have been validated by the validation rules.

```php
--8<--
libraries/validation/044.php:2:
--8<--
```

```php
--8<--
libraries/validation/045.php:2:
--8<--
```

### Saving Sets of Validation Rules to the Config File

A nice feature of the Validation class is that it permits you to store all your validation rules for your entire application in a config file. You organize the rules into "groups". You can specify a different group every time you run the validation.

#### How to Save Your Rules

To store your validation rules, simply create a new public property in the `Config\Validation` class with the name of your group. This element will hold an array with your validation rules. As shown earlier, the validation array will have this prototype:

```php
--8<--
libraries/validation/013.php
--8<--
```

#### How to Specify Rule Group

You can specify the group to use when you call the `run()` method:

```php
--8<--
libraries/validation/014.php:2:
--8<--
``[

#### How to Save Error Messages

You can also store custom error messages in this configuration file by naming the property the same as the group, and appended with ]errors`. These will automatically be used for any errors when this group is used:

```php
--8<--
libraries/validation/015.php
--8<--
```

Or pass all settings in an array:

```php
--8<--
libraries/validation/016.php
--8<--
```

See `validation-custom-errors` for details on the formatting of the array.

#### Getting & Setting Rule Groups

##### Get Rule Group

This method gets a rule group from the validation configuration:

```php
--8<--
libraries/validation/017.php:2:
--8<--
```

##### Set Rule Group

This method sets a rule group from the validation configuration to the validation service:

```php
--8<--
libraries/validation/018.php:2:
--8<--
```

### Validation Placeholders

The Validation class provides a simple method to replace parts of your rules based on data that's being passed into it. This sounds fairly obscure but can be especially handy with the `is_unique` validation rule. Placeholders are simply the name of the field (or array key) that was passed in as `$data` surrounded by curly brackets. It will be replaced by the **value** of the matched incoming field. An example should clarify this:

```php
--8<--
libraries/validation/020.php:2:
--8<--
```

!!! note "Note"
    Since v4.3.5, you must set the validation rules for the placeholder field (the `id` field in the sample code above) for security.

In this set of rules, it states that the email address should be unique in the database, except for the row that has an id matching the placeholder's value. Assuming that the form POST data had the following:

```php
--8<--
libraries/validation/021.php:2:
--8<--
```

then the `{id}` placeholder would be replaced with the number **4**, giving this revised rule:

```php
--8<--
libraries/validation/022.php:2:
--8<--
```

So it will ignore the row in the database that has `id=4` when it verifies the email is unique.

!!! note "Note"
    Since v4.3.5, if the placeholder (`id`) value does not pass the validation, the placeholder would not be replaced.

This can also be used to create more dynamic rules at runtime, as long as you take care that any dynamic keys passed in don't conflict with your form data.

## Working with Errors

The Validation library provides several methods to help you set error messages, provide custom error messages, and retrieve one or more errors to display.

By default, error messages are derived from language strings in **system/Language/en/Validation.php**, where each rule has an entry.

### Setting Custom Error Messages

Both the `setRule()` and `setRules()` methods can accept an array of custom messages that will be used as errors specific to each field as their last parameter. This allows for a very pleasant experience for the user since the errors are tailored to each instance. If not custom error message is provided, the default value will be used.

These are two ways to provide custom error messages.

As the last parameter:

```php
--8<--
libraries/validation/023.php:2:
--8<--
```

Or as a labeled style:

```php
--8<--
libraries/validation/024.php:2:
--8<--
```

If you'd like to include a field's "human" name, or the optional parameter some rules allow for (such as max_length), or the value that was validated you can add the `{field}`, `{param}` and `{value}` tags to your message, respectively:

    'min_length' => 'Supplied value ({value}) for {field} must have at least {param} characters.'

On a field with the human name Username and a rule of `min_length[6]` with a value of "Pizza", an error would display: "Supplied value (Pizza) for Username must have at least 6 characters."

!!! warning "Warning"
    If you get the error messages with `getErrors()` or `getError()`, the messages are not HTML escaped. If you use user input data like `({value})` to make the error message, it might contain HTML tags. If you don't escape the messages before displaying them, XSS attacks are possible.

!!! note "Note"
    When using label-style error messages, if you pass the second parameter to `setRules()`, it will be overwritten with the value of the first parameter.

### Translation of Messages and Validation Labels

To use translated strings from language files, we can simply use the dot syntax. Let's say we have a file with translations located here: **app/Languages/en/Rules.php**. We can simply use the language lines defined in this file, like this:

```php
--8<--
libraries/validation/025.php:2:
--8<--
```

### Getting All Errors

If you need to retrieve all error messages for failed fields, you can use the `getErrors()` method:

```php
--8<--
libraries/validation/026.php:2:
--8<--
```

If no errors exist, an empty array will be returned.

When using a wildcard (`*`), the error will point to a specific field, replacing the asterisk with the appropriate key/keys:

    // for data
    'contacts' => [
    'friends' => [
        [
            'name' => 'Fred Flinstone',
        ],
        [
            'name' => '',
        ],
    ]
    ]

    // rule
    'contacts.friends.*.name' => 'required'

    // error will be
    'contacts.friends.1.name' => 'The contacts.friends.*.name field is required.'

### Getting a Single Error

You can retrieve the error for a single field with the `getError()` method. The only parameter is the field name:

```php
--8<--
libraries/validation/027.php:2:
--8<--
```

If no error exists, an empty string will be returned.

!!! note "Note"
    When using a wildcard, all found errors that match the mask will be combined into one line separated by the EOL character.

### Check If Error Exists

You can check to see if an error exists with the `hasError()` method. The only parameter is the field name:

```php
--8<--
libraries/validation/028.php:2:
--8<--
```

When specifying a field with a wildcard, all errors matching the mask will be checked:

```php
--8<--
libraries/validation/029.php:2:
--8<--
```

### Redirect and Validation Errors

PHP shares nothing between requests. So when you redirect if a validation fails, there will be no validation errors in the redirected request because the validation has run in the previous request.

In that case, you need to use Form helper function `validation_errors()`, `validation_list_errors()` and `validation_show_error()`. These functions check the validation errors that are stored in the session.

To store the validation errors in the session, you need to use `withInput()` with :php:func:\[redirect()](#redirect):

```php
--8<--
libraries/validation/042.php:2:
--8<--
```

## Customizing Error Display

When you call `$validation->listErrors()` or `$validation->showError()`, it loads a view file in the background that determines how the errors are displayed. By default, they display with a class of `errors` on the wrapping div. You can easily create new views and use them throughout your application.

### Creating the Views

The first step is to create custom views. These can be placed anywhere that the `view()` method can locate them, which means the standard View directory, or any namespaced View folder will work. For example, you could create a new view at **app/Views/\_errors_list.php**:

```php
--8<--
libraries/validation/030.php
--8<--
```

An array named `$errors` is available within the view that contains a list of the errors, where the key is the name of the field that had the error, and the value is the error message, like this:

```php
--8<--
libraries/validation/031.php:2:
--8<--
```

There are actually two types of views that you can create. The first has an array of all of the errors, and is what we just looked at. The other type is simpler, and only contains a single variable, `$error` that contains the error message. This is used with the `showError()` method where a field must be specified:

    <span class="help-block"><?= esc($error) ?></span>

### Configuration

Once you have your views created, you need to let the Validation library know about them. Open **app/Config/Validation.php**. Inside, you'll find the `$templates` property where you can list as many custom views as you want, and provide an short alias they can be referenced by. If we were to add our example file from above, it would look something like:

```php
--8<--
libraries/validation/032.php
--8<--
```

### Specifying the Template

You can specify the template to use by passing it's alias as the first parameter in `listErrors()`:

    <?= $validation->listErrors('my_list') ?>

When showing field-specific errors, you can pass the alias as the second parameter to the `showError()` method, right after the name of the field the error should belong to:

    <?= $validation->showError('username', 'my_single') ?>

## Creating Custom Rules

### Using Rule Classes

Rules are stored within simple, namespaced classes. They can be stored any location you would like, as long as the autoloader can find it. These files are called RuleSets.

#### Adding a RuleSet

To add a new RuleSet, edit **app/Config/Validation.php** and add the new file to the `$ruleSets` array:

```php
--8<--
libraries/validation/033.php
--8<--
```

You can add it as either a simple string with the fully qualified class name, or using the `::class` suffix as shown above. The primary benefit here is that it provides some extra navigation capabilities in more advanced IDEs.

#### Creating a Rule Class

Within the file itself, each method is a rule and must accept a value to validate as the first parameter, and must return a boolean true or false value signifying true if it passed the test or false if it did not:

```php
--8<--
libraries/validation/034.php
--8<--
```

By default, the system will look within **system/Language/en/Validation.php** for the language strings used within errors. In custom rules, you may provide error messages by accepting a `&$error` variable by reference in the second parameter:

```php
--8<--
libraries/validation/035.php
--8<--
```

#### Using a Custom Rule

Your new custom rule could now be used just like any other rule:

```php
--8<--
libraries/validation/036.php:2:
--8<--
```

#### Allowing Parameters

If your method needs to work with parameters, the function will need a minimum of three parameters:

1.  the value to validate (`$value`)
2.  the parameter string (`$params`)
3.  an array with all of the data that was submitted the form (`$data`)
4.  (optional) a custom error string (`&$error`), just as described above.

!!! warning "Warning"
    The field values in `$data` are unvalidated (or may be invalid). Using unvalidated input data is a source of vulnerability. You must perform the necessary validation within your custom rules before using the data in `$data`.

The `$data` array is especially handy for rules like `required_with` that needs to check the value of another submitted field to base its result on:

```php
--8<--
libraries/validation/037.php
--8<--
```

### Using Closure Rule

!!! success "Available from version 4.3.0"

If you only need the functionality of a custom rule once throughout your application, you may use a closure instead of a rule class.

You need to use an array for validation rules:

```php
--8<--
libraries/validation/040.php:2:
--8<--
```

You must set the error message for the closure rule. When you specify the error message, set the array key for the closure rule. In the above code, the `required` rule has the key `0`, and the closure has `1`.

Or you can use the following parameters:

```php
--8<--
libraries/validation/041.php:2:
--8<--
```

### Using Callable Rule

!!! success "Available from version 4.5.0"

If you like to use an array callback as a rule, you may use it instead of a Closure Rule.

You need to use an array for validation rules:

```php
--8<--
libraries/validation/046.php:2:
--8<--
```

You must set the error message for the callable rule. When you specify the error message, set the array key for the callable rule. In the above code, the `required` rule has the key `0`, and the callable has `1`.

Or you can use the following parameters:

```php
--8<--
libraries/validation/047.php:2:
--8<--
```

## Available Rules

!!! note "Note"
    Rule is a string; there must be **no spaces** between the parameters, especially the `is_unique` rule. There can be no spaces before and after `ignore_value`.

```php
--8<--
libraries/validation/038.php:2:
--8<--
```

### Rules for General Use

The following is a list of all the native rules that are available to use:

<table>
<thead>
<tr>
<th>Rule</th>
<th>Parameter</th>
<th>Description</th>
<th>Example</th>
</tr>
</thead>
<tbody>
<tr>
<td>alpha</td>
<td>No</td>
<td>Fails if field has anything other than alphabetic characters in ASCII.</td>
<td></td>
</tr>
<tr>
<td>alpha_space</td>
<td>No</td>
<td>Fails if field contains anything other than alphabetic characters or spaces in ASCII.</td>
<td></td>
</tr>
<tr>
<td>alpha_dash</td>
<td>No</td>
<td>Fails if field contains anything other than alphanumeric characters, underscores or dashes in ASCII.</td>
<td></td>
</tr>
<tr>
<td>alpha_numeric</td>
<td>No</td>
<td>Fails if field contains anything other than alphanumeric characters in ASCII.</td>
<td></td>
</tr>
<tr>
<td>alpha_numeric_space</td>
<td>No</td>
<td>Fails if field contains anything other than alphanumeric or space characters in ASCII.</td>
<td></td>
</tr>
<tr>
<td>alpha_numeric_punct</td>
<td>No</td>
<td>Fails if field contains anything other than alphanumeric, space, or this limited set of punctuation characters: <code>~</code> (tilde), <code>!</code> (exclamation), <code>#</code> (number), <code>$</code> (dollar), <code>%</code> (percent), <code>&amp;</code> (ampersand), <code>*</code> (asterisk), <code>-</code> (dash), <code>_</code> (underscore), <code>+</code> (plus), <code>=</code> (equals), <code>|</code> (vertical bar), <code>:</code> (colon), <code>.</code> (period).</td>
<td></td>
</tr>
<tr>
<td>decimal</td>
<td>No</td>
<td>Fails if field contains anything other than a decimal number. Also accepts a <code>+</code> or <code>-</code> sign for the number.</td>
<td></td>
</tr>
<tr>
<td>differs</td>
<td>Yes</td>
<td>Fails if field does not differ from the one in the parameter.</td>
<td><code>differs[field_name]</code></td>
</tr>
<tr>
<td>exact_length</td>
<td>Yes</td>
<td>Fails if field is not exactly the parameter value. One or more comma-separated values.</td>
<td><code>exact_length[5]</code> or <code>exact_length[5,8,12]</code></td>
</tr>
<tr>
<td>field_exists</td>
<td>Yes</td>
<td>Fails if field does not exist. (This rule was added in v4.5.0.)</td>
<td></td>
</tr>
<tr>
<td>greater_than</td>
<td>Yes</td>
<td>Fails if field is less than or equal to the parameter value or not numeric.</td>
<td><code>greater_than[8]</code></td>
</tr>
<tr>
<td>greater_than_equal_to</td>
<td>Yes</td>
<td>Fails if field is less than the parameter value, or not numeric.</td>
<td><code>greater_than_equal_to[5]</code></td>
</tr>
<tr>
<td>hex</td>
<td>No</td>
<td>Fails if field contains anything other than hexadecimal characters.</td>
<td></td>
</tr>
<tr>
<td>if_exist</td>
<td>No</td>
<td>If this rule is present, validation will check the field only when the field key exists in the data to validate.</td>
<td></td>
</tr>
<tr>
<td>in_list</td>
<td>Yes</td>
<td>Fails if field is not within a predetermined list.</td>
<td><code>in_list[red,blue,green]</code></td>
</tr>
<tr>
<td>integer</td>
<td>No</td>
<td>Fails if field contains anything other than an integer.</td>
<td></td>
</tr>
<tr>
<td>is_natural</td>
<td>No</td>
<td>Fails if field contains anything other than a natural number: 0, 1, 2, 3, etc.</td>
<td></td>
</tr>
<tr>
<td>is_natural_no_zero</td>
<td>No</td>
<td>Fails if field contains anything other than a natural number, except zero: 1, 2, 3, etc.</td>
<td></td>
</tr>
<tr>
<td>is_not_unique</td>
<td>Yes</td>
<td>Checks the database to see if the given value exist. Can ignore records by field/value to filter (currently accept only one filter).</td>
<td><code>is_not_unique[table.field,where_field,where_value]</code></td>
</tr>
<tr>
<td>is_unique</td>
<td>Yes</td>
<td>Checks if this field value exists in the database. Optionally set a column and value to ignore, useful when updating records to ignore itself.</td>
<td><code>is_unique[table.field,ignore_field,ignore_value]</code></td>
</tr>
<tr>
<td>less_than</td>
<td>Yes</td>
<td>Fails if field is greater than or equal to the parameter value or not numeric.</td>
<td><code>less_than[8]</code></td>
</tr>
<tr>
<td>less_than_equal_to</td>
<td>Yes</td>
<td>Fails if field is greater than the parameter value or not numeric.</td>
<td><code>less_than_equal_to[8]</code></td>
</tr>
<tr>
<td>matches</td>
<td>Yes</td>
<td>The value must match the value of the field in the parameter.</td>
<td><code>matches[field]</code></td>
</tr>
<tr>
<td>max_length</td>
<td>Yes</td>
<td>Fails if field is longer than the parameter value.</td>
<td><code>max_length[8]</code></td>
</tr>
<tr>
<td>min_length</td>
<td>Yes</td>
<td>Fails if field is shorter than the parameter value.</td>
<td><code>min_length[3]</code></td>
</tr>
<tr>
<td>not_in_list</td>
<td>Yes</td>
<td>Fails if field is within a predetermined list.</td>
<td><code>not_in_list[red,blue,green]</code></td>
</tr>
<tr>
<td>numeric</td>
<td>No</td>
<td>Fails if field contains anything other than numeric characters.</td>
<td></td>
</tr>
<tr>
<td>regex_match</td>
<td>Yes</td>
<td>Fails if field does not match the regular expression.</td>
<td><code>regex_match[/regex/]</code></td>
</tr>
<tr>
<td>permit_empty</td>
<td>No</td>
<td>Allows the field to receive an empty array, empty string, null or false.</td>
<td></td>
</tr>
<tr>
<td>required</td>
<td>No</td>
<td>Fails if the field is an empty array, empty string, null or false.</td>
<td></td>
</tr>
<tr>
<td>required_with</td>
<td>Yes</td>
<td>The field is required when any of the other fields is not <a href="https://www.php.net/manual/en/function.empty.php">empty()</a> in the data.</td>
<td><code>required_with[field1,field2]</code></td>
</tr>
<tr>
<td>required_without</td>
<td>Yes</td>
<td>The field is required when any of the other fields is <a href="https://www.php.net/manual/en/function.empty.php">empty()</a> in the data.</td>
<td><code>required_without[field1,field2]</code></td>
</tr>
<tr>
<td>string</td>
<td>No</td>
<td>A generic alternative to the alpha* rules that confirms the element is a string</td>
<td></td>
</tr>
<tr>
<td>timezone</td>
<td>No</td>
<td>Fails if field does match a timezone per <a href="https://www.php.net/manual/en/function.timezone-identifiers-list.php">timezone_identifiers_list()</a></td>
<td></td>
</tr>
<tr>
<td>valid_base64</td>
<td>No</td>
<td>Fails if field contains anything other than valid Base64 characters.</td>
<td></td>
</tr>
<tr>
<td>valid_json</td>
<td>No</td>
<td>Fails if field does not contain a valid JSON string.</td>
<td></td>
</tr>
<tr>
<td>valid_email</td>
<td>No</td>
<td>Fails if field does not contain a valid email address.</td>
<td></td>
</tr>
<tr>
<td>valid_emails</td>
<td>No</td>
<td>Fails if any value provided in a comma separated list is not a valid email.</td>
<td></td>
</tr>
<tr>
<td>valid_ip</td>
<td>Yes</td>
<td>Fails if the supplied IP is not valid. Accepts an optional parameter of <code>ipv4</code> or <code>ipv6</code> to specify an IP format.</td>
<td><code>valid_ip[ipv6]</code></td>
</tr>
<tr>
<td>valid_url</td>
<td>No</td>
<td>Fails if field does not contain (loosely) a URL. Includes simple strings that could be hostnames, like "codeigniter". <strong>Normally,</strong> <code>valid_url_strict</code> <strong>should be used.</strong></td>
<td></td>
</tr>
<tr>
<td>valid_url_strict</td>
<td>Yes</td>
<td>Fails if field does not contain a valid URL. You can optionally specify a list of valid schemas. If not specified, <code>http,https</code> are valid. This rule uses PHP's <code>FILTER_VALIDATE_URL</code>.</td>
<td><code>valid_url_strict[https]</code></td>
</tr>
<tr>
<td>valid_date</td>
<td>Yes</td>
<td>Fails if field does not contain a valid date. Any string that <a href="https://www.php.net/manual/en/function.strtotime.php">strtotime()</a> accepts is valid if you don't specify an optional parameter to matches a date format. <strong>So it is usually necessary to specify the parameter.</strong></td>
<td><code>valid_date[d/m/Y]</code></td>
</tr>
<tr>
<td>valid_cc_number</td>
<td>Yes</td>
<td>Verifies that the credit card number matches the format used by the specified provider. Current supported providers are: American Express (<code>amex</code>), China Unionpay (<code>unionpay</code>), Diners Club CarteBlance (<code>carteblanche</code>), Diners Club (<code>dinersclub</code>), Discover Card (<code>discover</code>), Interpayment (<code>interpayment</code>), JCB (<code>jcb</code>), Maestro (<code>maestro</code>), Dankort (<code>dankort</code>), NSPK MIR (<code>mir</code>), Troy (<code>troy</code>), MasterCard (<code>mastercard</code>), Visa (<code>visa</code>), UATP (<code>uatp</code>), Verve (<code>verve</code>), CIBC Convenience Card (<code>cibc</code>), Royal Bank of Canada Client Card (<code>rbc</code>), TD Canada Trust Access Card (<code>tdtrust</code>), Scotiabank Scotia Card (<code>scotia</code>), BMO ABM Card (<code>bmoabm</code>), HSBC Canada Card (<code>hsbc</code>)</td>
<td><code>valid_cc_number[amex]</code></td>
</tr>
</tbody>
</table>

!!! note "Note"
    You can also use any native PHP functions that return boolean and permit at least one parameter, the field data to validate. The Validation library **never alters the data** to validate.

### Rules for File Uploads

When you validate uploaded files, you must use the rules specifically created for file validation.

!!! important "Important"
    Only rules that listed in the table below can be used to validate files. Therefore, adding any general rules, like `permit_empty`, to file validation rules array or string, the file validation will not work correctly.

Since the value of a file upload HTML field doesn't exist, and is stored in the `$_FILES` global, the name of the input field will need to be used twice. Once to specify the field name as you would for any other rule, but again as the first parameter of all file upload related rules:

    // In the HTML
    <input type="file" name="avatar">

    // In the controller
    $this->validate([
    'avatar' => 'uploaded[avatar]|max_size[avatar,1024]',
    ]);

<table>
<thead>
<tr>
<th>Rule</th>
<th>Parameter</th>
<th>Description</th>
<th>Example</th>
</tr>
</thead>
<tbody>
<tr>
<td>uploaded</td>
<td>Yes</td>
<td><blockquote>
<p>Fails if the name of the parameter does not match the name of any uploaded files. If you want the file upload to be optional (not required), do not define this rule.</p>
</blockquote></td>
<td><code>uploaded[field_name]</code></td>
</tr>
<tr>
<td>max_size</td>
<td>Yes</td>
<td><blockquote>
<p>Fails if the uploaded file named in the parameter is larger than the second parameter in kilobytes (kb). Or if the file is larger than allowed maximum size declared in php.ini config file -<code>upload_max_filesize</code> directive.</p>
</blockquote></td>
<td><code>max_size[field_name,2048]</code></td>
</tr>
<tr>
<td>max_dims</td>
<td>Yes</td>
<td><blockquote>
<p>Fails if the maximum width and height of an uploaded image exceed values. The first parameter is the field name. The second is the width, and the third is the height. Will also fail if the file cannot be determined to be an image.</p>
</blockquote></td>
<td><code>max_dims[field_name,300,150]</code></td>
</tr>
<tr>
<td>mime_in</td>
<td>Yes</td>
<td><blockquote>
<p>Fails if the file's mime type is not one listed in the parameters.</p>
</blockquote></td>
<td><code>mime_in[field_name,image/png,image/jpeg]</code></td>
</tr>
<tr>
<td>ext_in</td>
<td>Yes</td>
<td><blockquote>
<p>Fails if the file's extension is not one listed in the parameters.</p>
</blockquote></td>
<td><code>ext_in[field_name,png,jpg,gif]</code></td>
</tr>
<tr>
<td>is_image</td>
<td>Yes</td>
<td><blockquote>
<p>Fails if the file cannot be determined to be an image based on the mime type.</p>
</blockquote></td>
<td><code>is_image[field_name]</code></td>
</tr>
</tbody>
</table>

The file validation rules apply for both single and multiple file uploads.
