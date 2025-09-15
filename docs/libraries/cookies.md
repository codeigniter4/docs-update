# Cookies

An **HTTP cookie** (web cookie, browser cookie) is a small piece of data that a server sends to the user's web browser. The browser may store it and send it back with later requests to the same server. Typically, it's used to tell if two requests came from the same browser - keeping a user logged-in, for example. It remembers stateful information for the stateless HTTP protocol.

Cookies are mainly used for three purposes:

- **Session management**: Logins, shopping carts, game scores, or anything else the server should remember

- **Personalization**: User preferences, themes, and other settings

- **Tracking**: Recording and analyzing user behavior

To help you efficiently send cookies to browsers, CodeIgniter provides the `CodeIgniter\Cookie\Cookie` class to abstract the cookie interaction.

- [Creating Cookies](#creating-cookies)
    - [Overriding Defaults](#overriding-defaults)
- [Accessing Cookie's Attributes](#accessing-cookies-attributes)
- [Immutable Cookies](#immutable-cookies)
- [Validating a Cookie's Attributes](#validating-a-cookies-attributes)
    - [Validating the Name Attribute](#validating-the-name-attribute)
    - [Validating the Prefix Attribute](#validating-the-prefix-attribute)
    - [Validating the SameSite Attribute](#validating-the-samesite-attribute)
- [Sending Cookies](#sending-cookies)
- [Using the Cookie Store](#using-the-cookie-store)
    - [Getting the Store from Response](#getting-the-store-from-response)
    - [Creating CookieStore](#creating-cookiestore)
    - [Checking Cookies in Store](#checking-cookies-in-store)
    - [Getting Cookies in Store](#getting-cookies-in-store)
    - [Adding/Removing Cookies in Store](#addingremoving-cookies-in-store)
    - [Dispatching Cookies in Store](#dispatching-cookies-in-store)
- [Cookie Personalization](#cookie-personalization)
- [Class Reference](#class-reference)
- [CodeIgniter\Cookie](#codeignitercookie)
    - [Cookie](#cookie)
    - [CookieStore](#cookiestore)

## Creating Cookies

There are currently four (4) ways to create a new `Cookie` value object.

```php
--8<--
libraries/cookies/001.php
--8<--
```

When constructing the `Cookie` object, only the `name` attribute is required. All other else are optional. If the optional attributes are not modified, their values will be filled up by the default values saved in the `Cookie` class.

### Overriding Defaults

To override the defaults currently stored in the class, you can pass a `Config\Cookie` instance or an array of defaults to the static `Cookie::setDefaults()` method.

```php
--8<--
libraries/cookies/002.php
--8<--
```

Passing the `Config\Cookie` instance or an array to `Cookie::setDefaults()` will effectively overwrite your defaults and will persist until new defaults are passed.

#### Changing Defaults for a Limited Time

If you do not want this behavior but only want to change defaults for a limited time, you can take advantage of `Cookie::setDefaults()` return which returns the old defaults array.

```php
--8<--
libraries/cookies/003.php
--8<--
```

## Accessing Cookie's Attributes

Once instantiated, you can easily access a `Cookie`'s attribute by using one of its getter methods.

```php
--8<--
libraries/cookies/004.php
--8<--
```

## Immutable Cookies

A new `Cookie` instance is an immutable value object representation of an HTTP cookie. Being immutable, modifying any of the instance's attributes will not affect the original instance. The modification **always** returns a new instance. You need to retain this new instance in order to use it.

```php
--8<--
libraries/cookies/005.php
--8<--
```

## Validating a Cookie's Attributes

An HTTP cookie is regulated by several specifications that need to be followed in order to be accepted by browsers. Thus, when creating or modifying certain attributes of the `Cookie`, these are validated in order to check if these follow the specifications.

A `CookieException` is thrown if violations were reported.

### Validating the Name Attribute

A cookie name can be any US-ASCII character, except for the following:

- control characters;

- spaces or tabs;

- separator characters, such as `( ) < > @ , ; : \ " / [ ] ? = { }`

If setting the `$raw` parameter to `true` this validation will be strictly made. This is because PHP's [setcookie()](https://www.php.net/manual/en/function.setcookie.php) and [setrawcookie()](https://www.php.net/manual/en/function.setrawcookie.php) will reject cookies with invalid names. Additionally, cookie names cannot be an empty string.

### Validating the Prefix Attribute

When using the `__Secure-` prefix, cookies must be set with the `$secure` flag set to `true`. If using the `__Host-` prefix, cookies must exhibit the following:

- `$secure` flag set to `true`

- `$domain` is empty

- `$path` must be `/`

### Validating the SameSite Attribute

The SameSite attribute only accepts three (3) values:

- **Lax**: Cookies are not sent on normal cross-site subrequests (for example to load images or frames into a third party site), but are sent when a user is navigating to the origin site (*i.e.* when following a link).

- **Strict**: Cookies will only be sent in a first-party context and not be sent along with requests initiated by third party websites.

- **None**: Cookies will be sent in all contexts, *i.e.* in responses to both first-party and cross-origin requests.

CodeIgniter, however, allows you to set the SameSite attribute to an empty string. When an empty string is provided, the default SameSite setting saved in the `Cookie` class is used. You can change the default SameSite by using the `Cookie::setDefaults()` as discussed above.

Recent cookie specifications have changed such that modern browsers are being required to give a default SameSite if nothing was provided. This default is `Lax`. If you have set the SameSite to be an empty string and your default SameSite is also an empty string, your cookie will be given the `Lax` value.

If the SameSite is set to `None` you need to make sure that `Secure` is also set to `true`.

When writing the SameSite attribute, the `Cookie` class accepts any of the values case-insensitively. You can also take advantage of the class's constants to make it not a hassle.

```php
--8<--
libraries/cookies/006.php
--8<--
```

## Sending Cookies

Set the `Cookie` objects in the `CookieStore` of the Response object, and the framework will automatically send the cookies.

Use `CodeIgniter\\HTTP\\Response::setCookie()` to set:

```php
--8<--
libraries/cookies/017.php
--8<--
```

You can also use the `set_cookie()` helper function:

```php
--8<--
libraries/cookies/018.php
--8<--
```

## Using the Cookie Store

!!! note "Note"
    Normally, there is no need to use CookieStore directly.

The `CookieStore` class represents an immutable collection of `Cookie` objects.

### Getting the Store from Response

The `CookieStore` instance can be accessed from the current `Response` object.

```php
--8<--
libraries/cookies/007.php
--8<--
```

### Creating CookieStore

CodeIgniter provides three (3) other ways to create a new instance of the `CookieStore`.

```php
--8<--
libraries/cookies/008.php
--8<--
```

!!! note "Note"
    When using the global `cookies()` function, the passed `Cookie` array will only be considered if the second argument, `$getGlobal`, is set to `false`.

### Checking Cookies in Store

To check whether a `Cookie` object exists in the `CookieStore` instance, you can use several ways:

```php
--8<--
libraries/cookies/009.php
--8<--
```

### Getting Cookies in Store

Retrieving a `Cookie` instance in a cookie collection is very easy:

```php
--8<--
libraries/cookies/010.php
--8<--
```

When getting a `Cookie` instance directly from a `CookieStore`, an invalid name will throw a `CookieException`.

```php
--8<--
libraries/cookies/011.php
--8<--
```

When getting a `Cookie` instance from the current `Response`'s cookie collection, an invalid name will just return `null`.

```php
--8<--
libraries/cookies/012.php
--8<--
```

If no arguments are supplied in when getting cookies from the `Response`, all `Cookie` objects in store will be displayed.

```php
--8<--
libraries/cookies/013.php
--8<--
```

!!! note "Note"
    The helper function `get_cookie()` gets the cookie from the current `Request` object, not from `Response`. This function checks the `$_COOKIE` array if that cookie is set and fetches it right away.

### Adding/Removing Cookies in Store

As previously mentioned, `CookieStore` objects are immutable. You need to save the modified instance in order to work on it. The original instance is left unchanged.

```php
--8<--
libraries/cookies/014.php
--8<--
```

!!! note "Note"
    Removing a cookie from the store **DOES NOT** delete it from the browser. If you intend to delete a cookie *from the browser*, you must put an empty value cookie with the same name to the store.

When interacting with the cookies in store in the current `Response` object, you can safely add or delete cookies without worrying the immutable nature of the cookie collection. The `Response` object will replace the instance with the modified instance.

```php
--8<--
libraries/cookies/015.php
--8<--
```

### Dispatching Cookies in Store

!!! danger "Deprecated since version 4.1.6"

!!! important "Important"
    This method is deprecated. It will be removed in future releases.

More often than not, you do not need to concern yourself in manually sending cookies. CodeIgniter will do this for you. However, if you really need to manually send cookies, you can use the `dispatch` method. Just like in sending other headers, you need to make sure the headers are not yet sent by checking the value of `headers_sent()`.

```php
--8<--
libraries/cookies/016.php
--8<--
```

## Cookie Personalization

Sane defaults are already in place inside the `Cookie` class to ensure the smooth creation of cookie objects. However, you may wish to define your own settings by changing the following settings in the `Config\Cookie` class in **app/Config/Cookie.php** file.

<table>
<thead>
<tr>
<th>Setting</th>
<th>Options/ Types</th>
<th>Default</th>
<th>Description</th>
</tr>
</thead>
<tbody>
<tr>
<td><strong>$prefix</strong></td>
<td><code>string</code></td>
<td><code>''</code></td>
<td>Prefix to prepend to the cookie name.</td>
</tr>
<tr>
<td><strong>$expires</strong></td>
<td><code>DateTimeInterface|string|int</code></td>
<td><code>0</code></td>
<td>The expires timestamp.</td>
</tr>
<tr>
<td><strong>$path</strong></td>
<td><code>string</code></td>
<td><code>/</code></td>
<td>The path property of the cookie.</td>
</tr>
<tr>
<td><strong>$domain</strong></td>
<td><code>string</code></td>
<td><code>''</code></td>
<td>The domain property of the cookie.with trailing slash.</td>
</tr>
<tr>
<td><strong>$secure</strong></td>
<td><code>true/false</code></td>
<td><code>false</code></td>
<td>If to be sent over secure HTTPS.</td>
</tr>
<tr>
<td><strong>$httponly</strong></td>
<td><code>true/false</code></td>
<td><code>true</code></td>
<td>If not accessible to JavaScript.</td>
</tr>
<tr>
<td><strong>$samesite</strong></td>
<td><code>Lax|None|Strict|lax|none|strict''</code></td>
<td><code>Lax</code></td>
<td>The SameSite attribute.</td>
</tr>
<tr>
<td><strong>$raw</strong></td>
<td><code>true/false</code></td>
<td><code>false</code></td>
<td>If to be dispatched using <code>setrawcookie()</code>.</td>
</tr>
</tbody>
</table>

In runtime, you can manually supply a new default using the `Cookie::setDefaults()` method.

## Class Reference

## CodeIgniter\Cookie

### Cookie

#### setDefaults(\[$config = \[]])

- **Parameters**  

- \Config\Cookie|array `$config` The configuration array or instance

- **Return type**: `array<string, mixed>`

- **Returns**: The old defaults

Set the default attributes to a Cookie instance by injecting the values from the `Config\Cookie` config or an array.

#### fromHeaderString(string $header\[, bool $raw = false])

- **Parameters**  

- **$header** `string` The `Set-Cookie` header string

- **$raw** `bool` Whether this cookie is not to be URL encoded and sent via `setrawcookie()`

- **Return type**: ``Cookie``

- **Returns**: `Cookie` instance

- **Throws**: `CookieException`

Create a new Cookie instance from a `Set-Cookie` header.

#### \_\_construct(string $name\[, string $value = ''\[, array $options = \[]]])

- **Parameters**  

- **$name** `string` The cookie name

- **$value** `string` The cookie value

- **$options** `array` The cookie options

- **Return type**: ``Cookie``

- **Returns**: `Cookie` instance

- **Throws**: `CookieException`

Construct a new Cookie instance.

#### getId()

- **Return type**: `string`

- **Returns**: The ID used in indexing in the cookie collection.

#### getPrefix(): string #### getName(): string #### getPrefixedName(): string #### getValue(): string #### getExpiresTimestamp(): int #### getExpiresString(): string #### isExpired(): bool #### getMaxAge(): int #### getDomain(): string #### getPath(): string #### isSecure(): bool #### isHTTPOnly(): bool #### getSameSite(): string #### isRaw(): bool #### getOptions(): array

#### withRaw(\[bool $raw = true])

- **Parameters**  

- bool `$raw`

- **Return type**: ``Cookie``

- **Returns**: new `Cookie` instance

Creates a new Cookie with URL encoding option updated.

#### withPrefix(\[string $prefix = ''])

- **Parameters**  

- string `$prefix`

- **Return type**: ``Cookie``

- **Returns**: new `Cookie` instance

Creates a new Cookie with new prefix.

#### withName(string $name)

- **Parameters**  

- string `$name`

- **Return type**: ``Cookie``

- **Returns**: new `Cookie` instance

Creates a new Cookie with new name.

#### withValue(string $value)

- **Parameters**  

- string `$value`

- **Return type**: ``Cookie``

- **Returns**: new `Cookie` instance

Creates a new Cookie with new value.

#### withExpires($expires)

- **Parameters**  

- DateTimeInterface[|string|](##SUBST##|string|)int `$expires`

- **Return type**: ``Cookie``

- **Returns**: new `Cookie` instance

Creates a new Cookie with new cookie expires time.

#### withExpired()

- **Return type**: ``Cookie``

- **Returns**: new `Cookie` instance

Creates a new Cookie that will expire from the browser.

#### withNeverExpiring()

!!! danger "Deprecated since version 4.2.6"

!!! important "Important"
    This method is deprecated. It will be removed in future releases.

- **Parameters**  

- string `$name`

- **Return type**: ``Cookie``

- **Returns**: new `Cookie` instance

Creates a new Cookie that will virtually never expire.

#### withDomain(?string $domain)

- **Parameters**  

- string|null `$domain`

- **Return type**: ``Cookie``

- **Returns**: new `Cookie` instance

Creates a new Cookie with new domain.

#### withPath(?string $path)

- **Parameters**  

- string|null `$path`

- **Return type**: ``Cookie``

- **Returns**: new `Cookie` instance

Creates a new Cookie with new path.

#### withSecure(\[bool $secure = true])

- **Parameters**  

- bool `$secure`

- **Return type**: ``Cookie``

- **Returns**: new `Cookie` instance

Creates a new Cookie with new "Secure" attribute.

#### withHTTPOnly(\[bool $httponly = true])

- **Parameters**  

- bool `$httponly`

- **Return type**: ``Cookie``

- **Returns**: new `Cookie` instance

Creates a new Cookie with new "HttpOnly" attribute.

#### withSameSite(string $samesite)

- **Parameters**  

- string `$samesite`

- **Return type**: ``Cookie``

- **Returns**: new `Cookie` instance

Creates a new Cookie with new "SameSite" attribute.

#### toHeaderString()

- **Return type**: `string`

- **Returns**: Returns the string representation that can be passed as a header string.

#### toArray()

- **Return type**: `array`

- **Returns**: Returns the array representation of the Cookie instance.

### CookieStore

#### fromCookieHeaders(array $headers\[, bool $raw = false])

- **Parameters**  

- **$header** `array` Array of `Set-Cookie` headers

- **$raw** `bool` Whether not to use URL encoding

- **Return type**: ``CookieStore``

- **Returns**: `CookieStore` instance

- **Throws**: `CookieException`

Creates a CookieStore from an array of `Set-Cookie` headers.

#### \_\_construct(array $cookies)

- **Parameters**  

- **$cookies** `array` Array of `Cookie` objects

- **Return type**: ``CookieStore``

- **Returns**: `CookieStore` instance

- **Throws**: `CookieException`

#### has(string $name\[, string $prefix = ''\[, ?string $value = null]]): bool

- **Parameters**  

- **$name** `string` Cookie name

- **$prefix** `string` Cookie prefix

- **$value** `string|null` Cookie value

- **Return type**: `bool`

- **Returns**: Checks if a `Cookie` object identified by name and prefix is present in the collection.

#### get(string $name\[, string $prefix = '']): Cookie

- **Parameters**  

- **$name** `string` Cookie name

- **$prefix** `string` Cookie prefix

- **Return type**: ``Cookie``

- **Returns**: Retrieves an instance of Cookie identified by a name and prefix.

- **Throws**: `CookieException`

#### put(Cookie $cookie): CookieStore

- **Parameters**  

- **$cookie** `Cookie` A Cookie object

- **Return type**: ``CookieStore``

- **Returns**: new `CookieStore` instance

Store a new cookie and return a new collection. The original collection is left unchanged.

#### remove(string $name\[, string $prefix = '']): CookieStore

- **Parameters**  

- **$name** `string` Cookie name

- **$prefix** `string` Cookie prefix

- **Return type**: ``CookieStore``

- **Returns**: new `CookieStore` instance

Removes a cookie from a collection and returns an updated collection. The original collection is left unchanged.

#### dispatch(): void

- **Return type**: `void`

Dispatches all cookies in store.

#### display(): array

- **Return type**: `array`

- **Returns**: Returns all cookie instances in store.

#### clear(): void

- **Return type**: `void`

Clears the cookie collection.
