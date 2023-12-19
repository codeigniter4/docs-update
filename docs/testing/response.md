# Testing Responses

The `TestResponse` class provides a number of helpful functions for
parsing and testing responses from your test cases. Usually a
`TestResponse` will be provided for you as a result of your
`Controller Tests <controllers>` or `HTTP Feature Tests <feature>`, but
you can always create your own directly using any `ResponseInterface`:

<div class="literalinclude" lines="2-">

response/001.php

</div>

<div class="contents" local="" depth="2">

</div>

## Testing the Response

Whether you have received a `TestResponse` as a result of your tests or
created one yourself, there are a number of new assertions that you can
use in your tests.

### Accessing Request/Response

#### request()

You can access directly the Request object, if it was set during
testing:

<div class="literalinclude" lines="2-">

response/002.php

</div>

#### response()

This allows you direct access to the response object:

<div class="literalinclude" lines="2-">

response/003.php

</div>

### Checking Response Status

#### isOK()

Returns a boolean true/false based on whether the response is perceived
to be "ok". This is primarily determined by a response status code in
the 200 or 300's. An empty body is not considered valid, unless in
redirects.

<div class="literalinclude" lines="2-">

response/004.php

</div>

#### assertOK()

This assertion simply uses the `isOK()` method to test a response.
`assertNotOK()` is the inverse of this assertion.

<div class="literalinclude" lines="2-">

response/005.php

</div>

#### isRedirect()

Returns a boolean true/false based on whether the response is a
redirected response.

<div class="literalinclude" lines="2-">

response/006.php

</div>

#### assertRedirect()

Asserts that the Response is an instance of RedirectResponse.
`assertNotRedirect()` is the inverse of this assertion.

<div class="literalinclude" lines="2-">

response/007.php

</div>

#### assertRedirectTo()

Asserts that the Response is an instance of RedirectResponse and the
destination matches the uri given.

<div class="literalinclude" lines="2-">

response/008.php

</div>

#### getRedirectUrl()

Returns the URL set for a RedirectResponse, or null for failure.

<div class="literalinclude" lines="2-">

response/009.php

</div>

#### assertStatus(int \$code)

Asserts that the HTTP status code returned matches \$code.

<div class="literalinclude" lines="2-">

response/010.php

</div>

### Session Assertions

#### assertSessionHas(string \$key, \$value = null)

Asserts that a value exists in the resulting session. If \$value is
passed, will also assert that the variable's value matches what was
specified.

<div class="literalinclude" lines="2-">

response/011.php

</div>

#### assertSessionMissing(string \$key)

Asserts that the resulting session does not include the specified \$key.

<div class="literalinclude" lines="2-">

response/012.php

</div>

### Header Assertions

#### assertHeader(string \$key, \$value = null)

Asserts that a header named `$key` exists in the response. If `$value`
is not empty, will also assert that the values match.

<div class="literalinclude" lines="2-">

response/013.php

</div>

#### assertHeaderMissing(string \$key)

Asserts that a header name `$key` does not exist in the response.

<div class="literalinclude" lines="2-">

response/014.php

</div>

### Cookie Assertions

#### assertCookie(string \$key, \$value = null, string \$prefix = '')

Asserts that a cookie named `$key` exists in the response. If `$value`
is not empty, will also assert that the values match. You can set the
cookie prefix, if needed, by passing it in as the third parameter.

<div class="literalinclude" lines="2-">

response/015.php

</div>

#### assertCookieMissing(string \$key)

Asserts that a cookie named `$key` does not exist in the response.

<div class="literalinclude" lines="2-">

response/016.php

</div>

#### assertCookieExpired(string \$key, string \$prefix = '')

Asserts that a cookie named `$key` exists, but has expired. You can set
the cookie prefix, if needed, by passing it in as the second parameter.

<div class="literalinclude" lines="2-">

response/017.php

</div>

### DOM Helpers

The response you get back contains a number of helper methods to inspect
the HTML output within the response. These are useful for using within
assertions in your tests.

#### see()

Returns a boolean true/false based on whether the text on the page
exists either by itself, or more specifically within a tag, as specified
by type, class, or id:

<div class="literalinclude" lines="2-">

response/018.php

</div>

The `dontSee()` method is the exact opposite:

<div class="literalinclude" lines="2-">

response/019.php

</div>

#### seeElement()

The `seeElement()` and `dontSeeElement()` are very similar to the
previous methods, but do not look at the values of the elements.
Instead, they simply check that the elements exist on the page:

<div class="literalinclude" lines="2-">

response/020.php

</div>

#### seeLink()

You can use `seeLink()` to ensure that a link appears on the page with
the specified text:

<div class="literalinclude" lines="2-">

response/021.php

</div>

#### seeInField()

The `seeInField()` method checks for any input tags exist with the name
and value:

<div class="literalinclude" lines="2-">

response/022.php

</div>

#### seeCheckboxIsChecked()

Finally, you can check if a checkbox exists and is checked with the
`seeCheckboxIsChecked()` method:

<div class="literalinclude" lines="2-">

response/023.php

</div>

#### seeXPath()

<div class="versionadded">

4.5.0

</div>

You can use `seeXPath()` to take advantage of the full power that xpath
gives you. This method is aimed at more advanced users who want to write
a more complex expressions using the DOMXPath object directly:

<div class="literalinclude" lines="2-">

response/033.php

</div>

The `dontSeeXPath()` method is the exact opposite:

<div class="literalinclude" lines="2-">

response/034.php

</div>

### DOM Assertions

You can perform tests to see if specific elements/text/etc exist with
the body of the response with the following assertions.

#### assertSee(string \$search = null, string \$element = null)

Asserts that text/HTML is on the page, either by itself or - more
specifically - within a tag, as specified by type, class, or id:

<div class="literalinclude" lines="2-">

response/024.php

</div>

#### assertDontSee(string \$search = null, string \$element = null)

Asserts the exact opposite of the `assertSee()` method:

<div class="literalinclude" lines="2-">

response/025.php

</div>

#### assertSeeElement(string \$search)

Similar to `assertSee()`, however this only checks for an existing
element. It does not check for specific text:

<div class="literalinclude" lines="2-">

response/026.php

</div>

#### assertDontSeeElement(string \$search)

Similar to `assertSee()`, however this only checks for an existing
element that is missing. It does not check for specific text:

<div class="literalinclude" lines="2-">

response/027.php

</div>

#### assertSeeLink(string \$text, string \$details = null)

Asserts that an anchor tag is found with matching `$text` as the body of
the tag:

<div class="literalinclude" lines="2-">

response/028.php

</div>

#### assertSeeInField(string \$field, string \$value = null)

Asserts that an input tag exists with the name and value:

<div class="literalinclude" lines="2-">

response/029.php

</div>

### Working with JSON

Responses will frequently contain JSON responses, especially when
working with API methods. The following methods can help to test the
responses.

#### getJSON()

This method will return the body of the response as a JSON string:

<div class="literalinclude" lines="2-">

response/030.php

</div>

You can use this method to determine if `$response` actually holds JSON
content:

<div class="literalinclude" lines="2-">

response/031.php

</div>

> [!NOTE]
> Be aware that the JSON string will be pretty-printed in the result.

#### assertJSONFragment(array \$fragment)

Asserts that `$fragment` is found within the JSON response. It does not
need to match the entire JSON value.

<div class="literalinclude" lines="2-">

response/032.php

</div>

#### assertJSONExact(\$test)

Similar to `assertJSONFragment()`, but checks the entire JSON response
to ensure exact matches.

### Working with XML

#### getXML()

If your application returns XML, you can retrieve it through this
method.
