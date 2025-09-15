# Testing Responses

The `TestResponse` class provides a number of helpful functions for parsing and testing responses from your test cases. Usually a `TestResponse` will be provided for you as a result of your [Controller Tests](#controllers) or [HTTP Feature Tests](#feature), but you can always create your own directly using any `ResponseInterface`:

```php
--8<--
testing/response/001.php:2:
--8<--
```

- [Testing the Response](#testing-the-response)
    - [Accessing Request/Response](#accessing-requestresponse)
    - [Checking Response Status](#checking-response-status)
    - [Session Assertions](#session-assertions)
    - [Header Assertions](#header-assertions)
    - [Cookie Assertions](#cookie-assertions)
    - [DOM Helpers](#dom-helpers)
    - [DOM Assertions](#dom-assertions)
    - [Working with JSON](#working-with-json)
    - [Working with XML](#working-with-xml)

## Testing the Response

Whether you have received a `TestResponse` as a result of your tests or created one yourself, there are a number of new assertions that you can use in your tests.

### Accessing Request/Response

#### request()

You can access directly the Request object, if it was set during testing:

```php
--8<--
testing/response/002.php:2:
--8<--
```

#### response()

This allows you direct access to the response object:

```php
--8<--
testing/response/003.php:2:
--8<--
```

### Checking Response Status

#### isOK()

Returns a boolean true/false based on whether the response is perceived to be "ok". This is primarily determined by a response status code in the 200 or 300's. An empty body is not considered valid, unless in redirects.

```php
--8<--
testing/response/004.php:2:
--8<--
```

#### assertOK()

This assertion simply uses the `isOK()` method to test a response. `assertNotOK()` is the inverse of this assertion.

```php
--8<--
testing/response/005.php:2:
--8<--
```

#### isRedirect()

Returns a boolean true/false based on whether the response is a redirected response.

```php
--8<--
testing/response/006.php:2:
--8<--
```

#### assertRedirect()

Asserts that the Response is an instance of RedirectResponse. `assertNotRedirect()` is the inverse of this assertion.

```php
--8<--
testing/response/007.php:2:
--8<--
```

#### assertRedirectTo()

Asserts that the Response is an instance of RedirectResponse and the destination matches the uri given.

```php
--8<--
testing/response/008.php:2:
--8<--
```

#### getRedirectUrl()

Returns the URL set for a RedirectResponse, or null for failure.

```php
--8<--
testing/response/009.php:2:
--8<--
```

#### assertStatus(int $code)

Asserts that the HTTP status code returned matches $code.

```php
--8<--
testing/response/010.php:2:
--8<--
```

### Session Assertions

#### assertSessionHas(string $key, $value = null)

Asserts that a value exists in the resulting session. If $value is passed, will also assert that the variable's value matches what was specified.

```php
--8<--
testing/response/011.php:2:
--8<--
```

#### assertSessionMissing(string $key)

Asserts that the resulting session does not include the specified $key.

```php
--8<--
testing/response/012.php:2:
--8<--
```

### Header Assertions

#### assertHeader(string $key, $value = null)

Asserts that a header named `$key` exists in the response. If `$value` is not empty, will also assert that the values match.

```php
--8<--
testing/response/013.php:2:
--8<--
```

#### assertHeaderMissing(string $key)

Asserts that a header name `$key` does not exist in the response.

```php
--8<--
testing/response/014.php:2:
--8<--
```

### Cookie Assertions

#### assertCookie(string $key, $value = null, string $prefix = '')

Asserts that a cookie named `$key` exists in the response. If `$value` is not empty, will also assert that the values match. You can set the cookie prefix, if needed, by passing it in as the third parameter.

```php
--8<--
testing/response/015.php:2:
--8<--
```

#### assertCookieMissing(string $key)

Asserts that a cookie named `$key` does not exist in the response.

```php
--8<--
testing/response/016.php:2:
--8<--
```

#### assertCookieExpired(string $key, string $prefix = '')

Asserts that a cookie named `$key` exists, but has expired. You can set the cookie prefix, if needed, by passing it in as the second parameter.

```php
--8<--
testing/response/017.php:2:
--8<--
```

### DOM Helpers

The response you get back contains a number of helper methods to inspect the HTML output within the response. These are useful for using within assertions in your tests.

#### see()

Returns a boolean true/false based on whether the text on the page exists either by itself, or more specifically within a tag, as specified by type, class, or id:

```php
--8<--
testing/response/018.php:2:
--8<--
```

The `dontSee()` method is the exact opposite:

```php
--8<--
testing/response/019.php:2:
--8<--
```

#### seeElement()

The `seeElement()` and `dontSeeElement()` are very similar to the previous methods, but do not look at the values of the elements. Instead, they simply check that the elements exist on the page:

```php
--8<--
testing/response/020.php:2:
--8<--
```

#### seeLink()

You can use `seeLink()` to ensure that a link appears on the page with the specified text:

```php
--8<--
testing/response/021.php:2:
--8<--
```

#### seeInField()

The `seeInField()` method checks for any input tags exist with the name and value:

```php
--8<--
testing/response/022.php:2:
--8<--
```

#### seeCheckboxIsChecked()

Finally, you can check if a checkbox exists and is checked with the `seeCheckboxIsChecked()` method:

```php
--8<--
testing/response/023.php:2:
--8<--
```

#### seeXPath()

!!! success "Available from version 4.5.0"

You can use `seeXPath()` to take advantage of the full power that xpath gives you. This method is aimed at more advanced users who want to write a more complex expressions using the DOMXPath object directly:

```php
--8<--
testing/response/033.php:2:
--8<--
```

The `dontSeeXPath()` method is the exact opposite:

```php
--8<--
testing/response/034.php:2:
--8<--
```

### DOM Assertions

You can perform tests to see if specific elements/text/etc exist with the body of the response with the following assertions.

#### assertSee(string $search = null, string $element = null)

Asserts that text/HTML is on the page, either by itself or - more specifically - within a tag, as specified by type, class, or id:

```php
--8<--
testing/response/024.php:2:
--8<--
```

#### assertDontSee(string $search = null, string $element = null)

Asserts the exact opposite of the `assertSee()` method:

```php
--8<--
testing/response/025.php:2:
--8<--
```

#### assertSeeElement(string $search)

Similar to `assertSee()`, however this only checks for an existing element. It does not check for specific text:

```php
--8<--
testing/response/026.php:2:
--8<--
```

#### assertDontSeeElement(string $search)

Similar to `assertSee()`, however this only checks for an existing element that is missing. It does not check for specific text:

```php
--8<--
testing/response/027.php:2:
--8<--
```

#### assertSeeLink(string $text, string $details = null)

Asserts that an anchor tag is found with matching `$text` as the body of the tag:

```php
--8<--
testing/response/028.php:2:
--8<--
```

#### assertSeeInField(string $field, string $value = null)

Asserts that an input tag exists with the name and value:

```php
--8<--
testing/response/029.php:2:
--8<--
```

### Working with JSON

Responses will frequently contain JSON responses, especially when working with API methods. The following methods can help to test the responses.

#### getJSON()

This method will return the body of the response as a JSON string:

```php
--8<--
testing/response/030.php:2:
--8<--
```

You can use this method to determine if `$response` actually holds JSON content:

```php
--8<--
testing/response/031.php:2:
--8<--
```

!!! note "Note"
    Be aware that the JSON string will be pretty-printed in the result.

#### assertJSONFragment(array $fragment)

Asserts that `$fragment` is found within the JSON response. It does not need to match the entire JSON value.

```php
--8<--
testing/response/032.php:2:
--8<--
```

#### assertJSONExact($test)

Similar to `assertJSONFragment()`, but checks the entire JSON response to ensure exact matches.

### Working with XML

#### getXML()

If your application returns XML, you can retrieve it through this method.
