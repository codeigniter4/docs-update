# View Parser

- [Using the View Parser Class](#using-the-view-parser-class)
    - [What It Does](#what-it-does)
    - [Parser templates](#parser-templates)
    - [Parser Configuration Options](#parser-configuration-options)
- [Substitution Variations](#substitution-variations)
    - [Loop Substitutions](#loop-substitutions)
    - [Nested Substitutions](#nested-substitutions)
    - [Comments](#comments)
    - [Cascading Data](#cascading-data)
    - [Preventing Parsing](#preventing-parsing)
    - [Conditional Logic](#conditional-logic)
    - [Escaping Data](#escaping-data)
    - [Filters](#filters)
    - [Parser Plugins](#parser-plugins)
- [Usage Notes](#usage-notes)
    - [View Fragments](#view-fragments)
- [Class Reference](#class-reference)
- [CodeIgniter\View](#codeigniterview)
    - [Parser](#parser)

The View Parser can perform simple text substitution for pseudo-variables contained within your view files. It can parse simple variables or variable tag pairs.

Pseudo-variable names or control constructs are enclosed in braces, like this:

    <html>
    <head>
    <title>{blog_title}</title>
    </head>
    <body>
    <h3>{blog_heading}</h3>

    {blog_entries}
        <h5>{title}</h5>
        <p>{body}</p>
    {/blog_entries}

    </body>
    </html>

These variables are not actual PHP variables, but rather plain text representations that allow you to eliminate PHP from your templates (view files).

!!! note "Note"
    CodeIgniter does **not** require you to use this class since using pure PHP in your view pages (for instance using the [View renderer](#/outgoing/view-renderer) ) lets them run a little faster. However, some developers prefer to use some form of template engine if they work with designers who they feel would find some confusion working with PHP.

## Using the View Parser Class

The simplest method to load the parser class is through its service:

```php
--8<--
outgoing/view_parser/001.php
--8<--
```

Alternately, if you are not using the `Parser` class as your default renderer, you can instantiate it directly:

```php
--8<--
outgoing/view_parser/002.php
--8<--
```

Then you can use any of the three standard rendering methods that it provides: `render()`, `setVar()` and `setData()`. You will also be able to specify delimiters directly, through the `setDelimiters()` method.

!!! important "Important"
    Using the `Parser`, your view templates are processed only by the Parser itself, and not like a conventional view PHP script. PHP code in such a script is ignored by the parser, and only substitutions are performed.
    
    This is purposeful: view files with no PHP.

### What It Does

The `Parser` class processes "PHP/HTML scripts" stored in the application's view path. These scripts can not contain any PHP.

Each view parameter (which we refer to as a pseudo-variable) triggers a substitution, based on the type of value you provided for it. Pseudo-variables are not extracted into PHP variables; instead their value is accessed through the pseudo-variable syntax, where its name is referenced inside braces.

The Parser class uses an associative array internally, to accumulate pseudo-variable settings until you call its `render()`. This means that your pseudo-variable names need to be unique, or a later parameter setting will over-ride an earlier one.

This also impacts escaping parameter values for different contexts inside your script. You will have to give each escaped value a unique parameter name.

### Parser templates

You can use the `render()` method to parse (or render) simple templates, like this:

```php
--8<--
outgoing/view_parser/003.php
--8<--
```

View parameters are passed to `setData()` as an associative array of data to be replaced in the template. In the above example, the template would contain two variables: `{blog_title}` and `{blog_heading}` The first parameter to `render()` contains the name of the [view file](#/outgoing/views), Where *blog_template* is the name of your view file.

!!! important "Important"
    If the file extension is omitted, then the views are expected to end with the .php extension.

### Parser Configuration Options

Several options can be passed to the `render()` or `renderString()` methods.

- `cache` - the time in seconds, to save a view's results; ignored for renderString()

- `cache_name` - the ID used to save/retrieve a cached view result; defaults to the viewpath; ignored for renderString()

- `saveData` - true if the view data parameters should be retained for subsequent calls; default is **true**

- `cascadeData` - true if pseudo-variable settings should be passed on to nested substitutions; default is **true**

```php
--8<--
outgoing/view_parser/004.php
--8<--
```

## Substitution Variations

There are three types of substitution supported: simple, looping, and nested. Substitutions are performed in the same sequence that pseudo-variables were added.

The **simple substitution** performed by the parser is a one-to-one replacement of pseudo-variables where the corresponding data parameter has either a scalar or string value, as in this example:

```php
--8<--
outgoing/view_parser/005.php
--8<--
```

The `Parser` takes substitution a lot further with "variable pairs", used for nested substitutions or looping, and with some advanced constructs for conditional substitution.

When the parser executes, it will generally

- handle any conditional substitutions

- handle any nested/looping substitutions

- handle the remaining single substitutions

### Loop Substitutions

A loop substitution happens when the value for a pseudo-variable is a sequential array of arrays, like an array of row settings.

The above example code allows simple variables to be replaced. What if you would like an entire block of variables to be repeated, with each iteration containing new values? Consider the template example we showed at the top of the page:

    <html>
    <head>
    <title>{blog_title}</title>
    </head>
    <body>
    <h3>{blog_heading}</h3>

    {blog_entries}
        <h5>{title}</h5>
        <p>{body}</p>
    {/blog_entries}

    </body>
    </html>

In the above code you'll notice a pair of variables: `{blog_entries}` data... `{/blog_entries}`. In a case like this, the entire chunk of data between these pairs would be repeated multiple times, corresponding to the number of rows in the "blog_entries" element of the parameters array.

Parsing variable pairs is done using the identical code shown above to parse single variables, except, you will add a multi-dimensional array corresponding to your variable pair data. Consider this example:

```php
--8<--
outgoing/view_parser/006.php
--8<--
```

The value for the pseudo-variable `blog_entries` is a sequential array of associative arrays. The outer level does not have keys associated with each of the nested "rows".

If your "pair" data is coming from a database result, which is already a multi-dimensional array, you can simply use the database `getResultArray()` method:

```php
--8<--
outgoing/view_parser/007.php
--8<--
```

If the array you are trying to loop over contains objects instead of arrays, the parser will first look for an `asArray()` method on the object. If it exists, that method will be called and the resulting array is then looped over just as described above. If no `asArray()` method exists, the object will be cast as an array and its public properties will be made available to the Parser.

This is especially useful with the Entity classes, which has an `asArray()` method that returns all public and protected properties (minus the <span id="options">options</span> property) and makes them available to the Parser.

### Nested Substitutions

A nested substitution happens when the value for a pseudo-variable is an associative array of values, like a record from a database:

```php
--8<--
outgoing/view_parser/008.php
--8<--
```

The value for the pseudo-variable `blog_entries` is an associative array. The key/value pairs defined inside it will be exposed inside the variable pair loop for that variable.

A **blog_template.php** that might work for the above:

    <h1>{blog_title} - {blog_heading}</h1>
    {blog_entries}
    <div>
        <h2>{title}</h2>
        <p>{body}</p>
    </div>
    {/blog_entries}

If you would like the other pseudo-variables accessible inside the `blog_entries` scope, then make sure that the `cascadeData` option is set to true.

### Comments

You can place comments in your templates that will be ignored and removed during parsing by wrapping the comments in a `{#  #}` symbols.

    {# This comment is removed during parsing. #}
    {blog_entry}
    <div>
        <h2>{title}</h2>
        <p>{body}</p>
    </div>
    {/blog_entry}

### Cascading Data

With both a nested and a loop substitution, you have the option of cascading data pairs into the inner substitution.

The following example is not impacted by cascading:

```php
--8<--
outgoing/view_parser/009.php
--8<--
```

This example gives different results, depending on cascading:

```php
--8<--
outgoing/view_parser/010.php
--8<--
```

### Preventing Parsing

You can specify portions of the page to not be parsed with the `{noparse}` `{/noparse}` tag pair. Anything in this section will stay exactly as it is, with no variable substitution, looping, etc, happening to the markup between the brackets.

    {noparse}
    <h1>Untouched Code</h1>
    {/noparse}

### Conditional Logic

The Parser class supports some basic conditionals to handle `if`, `else`, and `elseif` syntax. All `if` blocks must be closed with an `endif` tag:

    {if $role=='admin'}
    <h1>Welcome, Admin!</h1>
    {endif}

This simple block is converted to the following during parsing:

```php
--8<--
outgoing/view_parser/011.php
--8<--
```

All variables used within if statements must have been previously set with the same name. Other than that, it is treated exactly like a standard PHP conditional, and all standard PHP rules would apply here. You can use any of the comparison operators you would normally, like `==`, `===`, `!==`, `<`, `>`, etc.

    {if $role=='admin'}
    <h1>Welcome, Admin</h1>
    {elseif $role=='moderator'}
    <h1>Welcome, Moderator</h1>
    {else}
    <h1>Welcome, User</h1>
    {endif}

!!! warning "Warning"
    In the background, conditionals are parsed using an `eval()`, so you must ensure that you take care with the user data that is used within conditionals, or you could open your application up to security risks.

#### Changing the Conditional Delimiters

If you have JavaScript code like the following in your templates, the Parser raises a syntax error because there are strings that can be interpreted as a conditional:

    <script type="text/javascript">
    var f = function() {
        if (hasAlert) {
            alert('{message}');
        }
    }
    </script>

In that case, you can change the delimiters for conditionals with the `setConditionalDelimiters()` method to avoid misinterpretations:

```php
--8<--
outgoing/view_parser/027.php
--8<--
```

In this case, you will write code in your template:

    {% if $role=='admin' %}
    <h1>Welcome, Admin</h1>
    {% else %}
    <h1>Welcome, User</h1>
    {% endif %}

### Escaping Data

By default, all variable substitution is escaped to help prevent XSS attacks on your pages. CodeIgniter's `esc()` method supports several different contexts, like general `html`, when it's in an HTML `attr`, in `css`, etc. If nothing else is specified, the data will be assumed to be in an HTML context. You can specify the context used by using the `esc()` filter:

    { user_styles | esc(css) }
    <a href="{ user_link | esc(attr) }">{ title }</a>

There will be times when you absolutely need something to used and NOT escaped. You can do this by adding exclamation marks to the opening and closing braces:

    {! unescaped_var !}

### Filters

Any single variable substitution can have one or more filters applied to it to modify the way it is presented. These are not intended to drastically change the output, but provide ways to reuse the same variable data but with different presentations. The `esc` filter discussed above is one example. Dates are another common use case, where you might need to format the same data differently in several sections on the same page.

Filters are commands that come after the pseudo-variable name, and are separated by the pipe symbol, `|`:

    // -55 is displayed as 55
    { value|abs }

If the parameter takes any arguments, they must be separated by commas and enclosed in parentheses:

    { created_at|date(Y-m-d) }

Multiple filters can be applied to the value by piping multiple ones together. They are processed in order, from left to right:

    { created_at|date_modify(+5 days)|date(Y-m-d) }

#### Provided Filters

The following filters are available when using the parser:

<table>
<thead>
<tr>
<th>Filter</th>
<th>Arguments</th>
<th>Description</th>
<th>Example</th>
</tr>
</thead>
<tbody>
<tr>
<td>abs</td>
<td></td>
<td>Displays the absolute value of a number.</td>
<td>{ v|abs }</td>
</tr>
<tr>
<td>capitalize</td>
<td></td>
<td>Displays the string in sentence case: all lowercase with firstletter capitalized.</td>
<td>{ v|capitalize}</td>
</tr>
<tr>
<td>date</td>
<td><blockquote>
<p>format (Y-m-d)</p>
</blockquote></td>
<td>A PHP <strong>date</strong>-compatible formatting string.</td>
<td>{ v|date(Y-m-d) }</td>
</tr>
<tr>
<td>date_modify</td>
<td><blockquote>
<p>value to add / subtract</p>
</blockquote></td>
<td>A <strong>strtotime</strong> compatible string to modify the date, like <code>+5 day</code> or <code>-1 week</code>.</td>
<td>{ v|date_modify(+1 day) }</td>
</tr>
<tr>
<td>default</td>
<td><blockquote>
<p>default value</p>
</blockquote></td>
<td>Displays the default value if the variable is empty or undefined.</td>
<td>{ v|default(just in case) }</td>
</tr>
<tr>
<td>esc</td>
<td><blockquote>
<p>html, attr, css, js</p>
</blockquote></td>
<td>Specifies the context to escape the data.</td>
<td>{ v|esc(attr) }</td>
</tr>
<tr>
<td>excerpt</td>
<td><blockquote>
<p>phrase, radius</p>
</blockquote></td>
<td>Returns the text within a radius of words from a given phrase. Same as <strong>excerpt</strong> helper function.</td>
<td>{ v|excerpt(green giant, 20) }</td>
</tr>
<tr>
<td>highlight</td>
<td><blockquote>
<p>phrase</p>
</blockquote></td>
<td>Highlights a given phrase within the text using '&lt;mark&gt;&lt;/mark&gt;' tags.</td>
<td>{ v|highlight(view parser) }</td>
</tr>
<tr>
<td>highlight_code</td>
<td></td>
<td>Highlights code samples with HTML/CSS.</td>
<td>{ v|highlight_code }</td>
</tr>
<tr>
<td>limit_chars</td>
<td><blockquote>
<p>limit</p>
</blockquote></td>
<td>Limits the number of characters to $limit.</td>
<td>{ v|limit_chars(100) }</td>
</tr>
<tr>
<td>limit_words</td>
<td><blockquote>
<p>limit</p>
</blockquote></td>
<td>Limits the number of words to $limit.</td>
<td>{ v|limit_words(20) }</td>
</tr>
<tr>
<td>local_currency</td>
<td><blockquote>
<p>currency, locale, fraction</p>
</blockquote></td>
<td>Displays a localized version of a currency. "currency" valueis any 3-letter ISO 4217 currency code.</td>
<td>{ v|local_currency(EUR,en_US) }</td>
</tr>
<tr>
<td>local_number</td>
<td><blockquote>
<p>type, precision, locale</p>
</blockquote></td>
<td>Displays a localized version of a number. "type" can be one of: decimal, currency, percent, scientific, spellout, ordinal, duration.</td>
<td>{ v|local_number(decimal,2,en_US) }</td>
</tr>
<tr>
<td>lower</td>
<td></td>
<td>Converts a string to lowercase.</td>
<td>{ v|lower }</td>
</tr>
<tr>
<td>nl2br</td>
<td></td>
<td>Replaces all newline characters (n) to an HTML &lt;br/&gt; tag.</td>
<td>{ v|nl2br }</td>
</tr>
<tr>
<td>number_format</td>
<td><blockquote>
<p>places</p>
</blockquote></td>
<td>Wraps PHP <strong>number_format</strong> function for use within the parser.</td>
<td>{ v|number_format(3) }</td>
</tr>
<tr>
<td>prose</td>
<td></td>
<td>Takes a body of text and uses the <strong>auto_typography()</strong> method to turn it into prettier, easier-to-read, prose.</td>
<td>{ v|prose }</td>
</tr>
<tr>
<td>round</td>
<td><blockquote>
<p>places, type</p>
</blockquote></td>
<td>Rounds a number to the specified places. Types of <strong>ceil</strong> and <strong>floor</strong> can be passed to use those functions instead.</td>
<td>{ v<a href="##SUBST##|round(3) } { v|">|round(3) } { v|</a>round(ceil) }</td>
</tr>
<tr>
<td>strip_tags</td>
<td><blockquote>
<p>allowed chars</p>
</blockquote></td>
<td>Wraps PHP <strong>strip_tags</strong>. Can accept a string of allowed tags.</td>
<td>{ v|strip_tags(&lt;br&gt;) }</td>
</tr>
<tr>
<td>title</td>
<td></td>
<td>Displays a "title case" version of the string, with all lowercase, and each word capitalized.</td>
<td>{ v|title }</td>
</tr>
<tr>
<td>upper</td>
<td></td>
<td>Displays the string in all uppercase.</td>
<td>{ v|upper }</td>
</tr>
</tbody>
</table>

See \[PHP's NumberFormatter](#https://www.php.net/manual/en/numberformatter.create.php)\_ for details relevant to the "local_number" filter.

#### Custom Filters

You can easily create your own filters by editing **app/Config/View.php** and adding new entries to the `$filters` array. Each key is the name which the filter is called by in the view, and its value is any valid PHP callable:

```php
--8<--
outgoing/view_parser/012.php
--8<--
```

### Parser Plugins

Plugins allow you to extend the parser, adding custom features for each project. They can be any PHP callable, making them very simple to implement. Within templates, plugins are specified by `{+ +}` tags:

    {+ foo +} inner content {+ /foo +}

This example shows a plugin named **foo**. It can manipulate any of the content between its opening and closing tags. In this example, it could work with the text " inner content ". Plugins are processed before any pseudo-variable replacements happen.

While plugins will often consist of tag pairs, like shown above, they can also be a single tag, with no closing tag:

    {+ foo +}

Opening tags can also contain parameters that can customize how the plugin works. The parameters are represented as key/value pairs:

    {+ foo bar=2 baz="x y" +}

Parameters can also be single values:

    {+ include somefile.php +}

#### Provided Plugins

The following plugins are available when using the parser:

<table>
<thead>
<tr>
<th>Plugin</th>
<th>Arguments</th>
<th>Description</th>
<th>Example</th>
</tr>
</thead>
<tbody>
<tr>
<td>current_url</td>
<td></td>
<td>Alias for the current_url helper function.</td>
<td>{+ current_url +}</td>
</tr>
<tr>
<td>previous_url</td>
<td></td>
<td>Alias for the previous_url helper function.</td>
<td>{+ previous_url +}</td>
</tr>
<tr>
<td>siteURL</td>
<td></td>
<td>Alias for the site_url helper function.</td>
<td>{+ siteURL "login" +}</td>
</tr>
<tr>
<td>mailto</td>
<td>email, title, attributes</td>
<td>Alias for the mailto helper function.</td>
<td>{+ mailto <a href="mailto:email=foo@example.com">email=foo@example.com</a> title="Stranger Things" +}</td>
</tr>
<tr>
<td>safe_mailto</td>
<td>email, title, attributes</td>
<td>Alias for the safe_mailto helper function.</td>
<td>{+ safe_mailto <a href="mailto:email=foo@example.com">email=foo@example.com</a> title="Stranger Things" +}</td>
</tr>
<tr>
<td>lang</td>
<td>language string</td>
<td>Alias for the lang helper function.</td>
<td>{+ lang number.terabyteAbbr +}</td>
</tr>
<tr>
<td>validation_errors</td>
<td>fieldname(optional)</td>
<td>Returns either error string for the field (if specified) or all validation errors.</td>
<td>{+ validation_errors +} , {+ validation_errors field="email" +}</td>
</tr>
<tr>
<td>route</td>
<td>route name</td>
<td>Alias for the route_to helper function.</td>
<td>{+ route "login" +}</td>
</tr>
<tr>
<td>csp_script_nonce</td>
<td></td>
<td>Alias for the csp_script_nonce helper function.</td>
<td>{+ csp_script_nonce +}</td>
</tr>
<tr>
<td>csp_style_nonce</td>
<td></td>
<td>Alias for the csp_style_nonce helper function.</td>
<td>{+ csp_style_nonce +}</td>
</tr>
</tbody>
</table>

#### Registering a Plugin

At its simplest, all you need to do to register a new plugin and make it ready for use is to add it to the **app/Config/View.php**, under the `$plugins` array. The key is the name of the plugin that is used within the template file. The value is any valid PHP callable, including static class methods:

```php
--8<--
outgoing/view_parser/014.php
--8<--
```

You can also use closures, but these can only be defined in the config file's constructor:

```php
--8<--
outgoing/view_parser/015.php
--8<--
```

If the callable is on its own, it is treated as a single tag, not a open/close one. It will be replaced by the return value from the plugin:

```php
--8<--
outgoing/view_parser/016.php
--8<--
```

If the callable is wrapped in an array, it is treated as an open/close tag pair that can operate on any of the content between its tags:

```php
--8<--
outgoing/view_parser/017.php
--8<--
```

## Usage Notes

If you include substitution parameters that are not referenced in your template, they are ignored:

```php
--8<--
outgoing/view_parser/018.php
--8<--
```

If you do not include a substitution parameter that is referenced in your template, the original pseudo-variable is shown in the result:

```php
--8<--
outgoing/view_parser/019.php
--8<--
```

If you provide a string substitution parameter when an array is expected, i.e., for a variable pair, the substitution is done for the opening variable pair tag, but the closing variable pair tag is not rendered properly:

```php
--8<--
outgoing/view_parser/020.php
--8<--
```

### View Fragments

You do not have to use variable pairs to get the effect of iteration in your views. It is possible to use a view fragment for what would be inside a variable pair, and to control the iteration in your controller instead of in the view.

An example with the iteration controlled in the view:

    $template = '<ul>{menuitems}
    <li><a href="{link}">{title}</a></li>
    {/menuitems}</ul>';

    $data = [
    'menuitems' => [
        ['title' => 'First Link', 'link' => '/first'],
        ['title' => 'Second Link', 'link' => '/second'],
    ]
    ];

    return $parser->setData($data)->renderString($template);

Result:

    <ul>
    <li><a href="/first">First Link</a></li>
    <li><a href="/second">Second Link</a></li>
    </ul>

An example with the iteration controlled in the controller, using a view fragment:

```php
--8<--
outgoing/view_parser/021.php
--8<--
```

Result:

    <ul>
    <li><a href="/first">First Link</a></li>
    <li><a href="/second">Second Link</a></li>
    </ul>

## Class Reference

## CodeIgniter\View

### Parser

#### render($view\[, $options\[, $saveData]])

- **Parameters**  

- **$view** `string` File name of the view source

- **$options** `array` Array of options, as key/value pairs

- **$saveData** `boolean` If true, will save data for use with any other calls, if false, will clean the data after rendering the view.

- **Returns**: The rendered text for the chosen view

- **Return type**: `string`

Builds the output based upon a file name and any data that has already been set:

```php
--8<--
outgoing/view_parser/022.php
--8<--
```

Options supported:

- `cache` - the time in seconds, to save a view's results

- `cache_name` - the ID used to save/retrieve a cached view result; defaults to the viewpath

- `cascadeData` - true if the data pairs in effect when a nested or loop substitution occurs should be propagated

- `saveData` - true if the view data parameter should be retained for subsequent calls

Any conditional substitutions are performed first, then remaining substitutions are performed for each data pair.

#### renderString($template\[, $options\[, $saveData]])

- **Parameters**  

- **$template** `string` View source provided as a string

- **$options** `array` Array of options, as key/value pairs

- **$saveData** `boolean` If true, will save data for use with any other calls, if false, will clean the data after rendering the view.

- **Returns**: The rendered text for the chosen view

- **Return type**: `string`

Builds the output based upon a provided template source and any data that has already been set:

```php
--8<--
outgoing/view_parser/023.php
--8<--
```

Options supported, and behavior, as above.

#### setData(\[$data\[, $context = null]])

- **Parameters**  

- **$data** `array` Array of view data strings, as key/value pairs

- **$context** `string` The context to use for data escaping.

- **Returns**: The Renderer, for method chaining

- **Return type**: `CodeIgniter\View\RendererInterface.`

Sets several pieces of view data at once:

```php
--8<--
outgoing/view_parser/024.php
--8<--
```

Supported escape contexts: html, css, js, url, or attr or raw. If 'raw', no escaping will happen.

#### setVar($name\[, $value = null\[, $context = null]])

- **Parameters**  

- **$name** `string` Name of the view data variable

- **$value** `mixed` The value of this view data

- **$context** `string` The context to use for data escaping.

- **Returns**: The Renderer, for method chaining

- **Return type**: `CodeIgniter\View\RendererInterface.`

Sets a single piece of view data:

```php
--8<--
outgoing/view_parser/025.php
--8<--
```

Supported escape contexts: html, css, js, url, attr or raw. If 'raw', no escaping will happen.

#### setDelimiters($leftDelimiter = '{', $rightDelimiter = '}')

- **Parameters**  

- **$leftDelimiter** `string` Left delimiter for substitution fields

- **$rightDelimiter** `string` right delimiter for substitution fields

- **Returns**: The Renderer, for method chaining

- **Return type**: `CodeIgniter\View\RendererInterface.`

Override the substitution field delimiters:

```php
--8<--
outgoing/view_parser/026.php
--8<--
```

#### setConditionalDelimiters($leftDelimiter = '{', $rightDelimiter = '}')

- **Parameters**  

- **$leftDelimiter** `string` Left delimiter for conditionals

- **$rightDelimiter** `string` right delimiter for conditionals

- **Returns**: The Renderer, for method chaining

- **Return type**: `CodeIgniter\View\RendererInterface.`

Override the conditional delimiters:

```php
--8<--
outgoing/view_parser/027.php
--8<--
```
