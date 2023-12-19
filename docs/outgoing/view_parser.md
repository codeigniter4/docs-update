# View Parser

<div class="contents" local="" depth="2">

</div>

The View Parser can perform simple text substitution for
pseudo-variables contained within your view files. It can parse simple
variables or variable tag pairs.

Pseudo-variable names or control constructs are enclosed in braces, like
this:

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

These variables are not actual PHP variables, but rather plain text
representations that allow you to eliminate PHP from your templates
(view files).

> [!NOTE]
> CodeIgniter does **not** require you to use this class since using
> pure PHP in your view pages (for instance using the
> `View renderer </outgoing/view_renderer>` ) lets them run a little
> faster. However, some developers prefer to use some form of template
> engine if they work with designers who they feel would find some
> confusion working with PHP.

## Using the View Parser Class

The simplest method to load the parser class is through its service:

<div class="literalinclude">

view_parser/001.php

</div>

Alternately, if you are not using the `Parser` class as your default
renderer, you can instantiate it directly:

<div class="literalinclude">

view_parser/002.php

</div>

Then you can use any of the three standard rendering methods that it
provides: `render()`, `setVar()` and `setData()`. You will also be able
to specify delimiters directly, through the `setDelimiters()` method.

> [!IMPORTANT]
> Using the `Parser`, your view templates are processed only by the
> Parser itself, and not like a conventional view PHP script. PHP code
> in such a script is ignored by the parser, and only substitutions are
> performed.
>
> This is purposeful: view files with no PHP.

### What It Does

The `Parser` class processes "PHP/HTML scripts" stored in the
application's view path. These scripts can not contain any PHP.

Each view parameter (which we refer to as a pseudo-variable) triggers a
substitution, based on the type of value you provided for it.
Pseudo-variables are not extracted into PHP variables; instead their
value is accessed through the pseudo-variable syntax, where its name is
referenced inside braces.

The Parser class uses an associative array internally, to accumulate
pseudo-variable settings until you call its `render()`. This means that
your pseudo-variable names need to be unique, or a later parameter
setting will over-ride an earlier one.

This also impacts escaping parameter values for different contexts
inside your script. You will have to give each escaped value a unique
parameter name.

### Parser templates

You can use the `render()` method to parse (or render) simple templates,
like this:

<div class="literalinclude">

view_parser/003.php

</div>

View parameters are passed to `setData()` as an associative array of
data to be replaced in the template. In the above example, the template
would contain two variables: `{blog_title}` and `{blog_heading}` The
first parameter to `render()` contains the name of the `view
file </outgoing/views>`, Where *blog_template* is the name of your view
file.

> [!IMPORTANT]
> If the file extension is omitted, then the views are expected to end
> with the .php extension.

### Parser Configuration Options

Several options can be passed to the `render()` or `renderString()`
methods.

- `cache` - the time in seconds, to save a view's results; ignored for
  renderString()
- `cache_name` - the ID used to save/retrieve a cached view result;
  defaults to the viewpath; ignored for renderString()
- `saveData` - true if the view data parameters should be retained for
  subsequent calls; default is **true**
- `cascadeData` - true if pseudo-variable settings should be passed on
  to nested substitutions; default is **true**

<div class="literalinclude">

view_parser/004.php

</div>

## Substitution Variations

There are three types of substitution supported: simple, looping, and
nested. Substitutions are performed in the same sequence that
pseudo-variables were added.

The **simple substitution** performed by the parser is a one-to-one
replacement of pseudo-variables where the corresponding data parameter
has either a scalar or string value, as in this example:

<div class="literalinclude">

view_parser/005.php

</div>

The `Parser` takes substitution a lot further with "variable pairs",
used for nested substitutions or looping, and with some advanced
constructs for conditional substitution.

When the parser executes, it will generally

- handle any conditional substitutions
- handle any nested/looping substitutions
- handle the remaining single substitutions

### Loop Substitutions

A loop substitution happens when the value for a pseudo-variable is a
sequential array of arrays, like an array of row settings.

The above example code allows simple variables to be replaced. What if
you would like an entire block of variables to be repeated, with each
iteration containing new values? Consider the template example we showed
at the top of the page:

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

In the above code you'll notice a pair of variables: `{blog_entries}`
data... `{/blog_entries}`. In a case like this, the entire chunk of data
between these pairs would be repeated multiple times, corresponding to
the number of rows in the "blog_entries" element of the parameters
array.

Parsing variable pairs is done using the identical code shown above to
parse single variables, except, you will add a multi-dimensional array
corresponding to your variable pair data. Consider this example:

<div class="literalinclude">

view_parser/006.php

</div>

The value for the pseudo-variable `blog_entries` is a sequential array
of associative arrays. The outer level does not have keys associated
with each of the nested "rows".

If your "pair" data is coming from a database result, which is already a
multi-dimensional array, you can simply use the database
`getResultArray()` method:

<div class="literalinclude">

view_parser/007.php

</div>

If the array you are trying to loop over contains objects instead of
arrays, the parser will first look for an `asArray()` method on the
object. If it exists, that method will be called and the resulting array
is then looped over just as described above. If no `asArray()` method
exists, the object will be cast as an array and its public properties
will be made available to the Parser.

This is especially useful with the Entity classes, which has an
`asArray()` method that returns all public and protected properties
(minus the \_options property) and makes them available to the Parser.

### Nested Substitutions

A nested substitution happens when the value for a pseudo-variable is an
associative array of values, like a record from a database:

<div class="literalinclude">

view_parser/008.php

</div>

The value for the pseudo-variable `blog_entries` is an associative
array. The key/value pairs defined inside it will be exposed inside the
variable pair loop for that variable.

A **blog_template.php** that might work for the above:

    <h1>{blog_title} - {blog_heading}</h1>
    {blog_entries}
        <div>
            <h2>{title}</h2>
            <p>{body}</p>
        </div>
    {/blog_entries}

If you would like the other pseudo-variables accessible inside the
`blog_entries` scope, then make sure that the `cascadeData` option is
set to true.

### Comments

You can place comments in your templates that will be ignored and
removed during parsing by wrapping the comments in a `{#  #}` symbols.

    {# This comment is removed during parsing. #}
    {blog_entry}
        <div>
            <h2>{title}</h2>
            <p>{body}</p>
        </div>
    {/blog_entry}

### Cascading Data

With both a nested and a loop substitution, you have the option of
cascading data pairs into the inner substitution.

The following example is not impacted by cascading:

<div class="literalinclude">

view_parser/009.php

</div>

This example gives different results, depending on cascading:

<div class="literalinclude">

view_parser/010.php

</div>

### Preventing Parsing

You can specify portions of the page to not be parsed with the
`{noparse}` `{/noparse}` tag pair. Anything in this section will stay
exactly as it is, with no variable substitution, looping, etc, happening
to the markup between the brackets.

    {noparse}
        <h1>Untouched Code</h1>
    {/noparse}

### Conditional Logic

The Parser class supports some basic conditionals to handle `if`,
`else`, and `elseif` syntax. All `if` blocks must be closed with an
`endif` tag:

    {if $role=='admin'}
        <h1>Welcome, Admin!</h1>
    {endif}

This simple block is converted to the following during parsing:

<div class="literalinclude">

view_parser/011.php

</div>

All variables used within if statements must have been previously set
with the same name. Other than that, it is treated exactly like a
standard PHP conditional, and all standard PHP rules would apply here.
You can use any of the comparison operators you would normally, like
`==`, `===`, `!==`, `<`, `>`, etc.

    {if $role=='admin'}
        <h1>Welcome, Admin</h1>
    {elseif $role=='moderator'}
        <h1>Welcome, Moderator</h1>
    {else}
        <h1>Welcome, User</h1>
    {endif}

> [!WARNING]
> In the background, conditionals are parsed using an `eval()`, so you
> must ensure that you take care with the user data that is used within
> conditionals, or you could open your application up to security risks.

#### Changing the Conditional Delimiters

If you have JavaScript code like the following in your templates, the
Parser raises a syntax error because there are strings that can be
interpreted as a conditional:

    <script type="text/javascript">
        var f = function() {
            if (hasAlert) {
                alert('{message}');
            }
        }
    </script>

In that case, you can change the delimiters for conditionals with the
`setConditionalDelimiters()` method to avoid misinterpretations:

<div class="literalinclude">

view_parser/027.php

</div>

In this case, you will write code in your template:

    {% if $role=='admin' %}
        <h1>Welcome, Admin</h1>
    {% else %}
        <h1>Welcome, User</h1>
    {% endif %}

### Escaping Data

By default, all variable substitution is escaped to help prevent XSS
attacks on your pages. CodeIgniter's `esc()` method supports several
different contexts, like general `html`, when it's in an HTML `attr`, in
`css`, etc. If nothing else is specified, the data will be assumed to be
in an HTML context. You can specify the context used by using the
`esc()` filter:

    { user_styles | esc(css) }
    <a href="{ user_link | esc(attr) }">{ title }</a>

There will be times when you absolutely need something to used and NOT
escaped. You can do this by adding exclamation marks to the opening and
closing braces:

    {! unescaped_var !}

### Filters

Any single variable substitution can have one or more filters applied to
it to modify the way it is presented. These are not intended to
drastically change the output, but provide ways to reuse the same
variable data but with different presentations. The `esc` filter
discussed above is one example. Dates are another common use case, where
you might need to format the same data differently in several sections
on the same page.

Filters are commands that come after the pseudo-variable name, and are
separated by the pipe symbol, `|`:

    // -55 is displayed as 55
    { value|abs }

If the parameter takes any arguments, they must be separated by commas
and enclosed in parentheses:

    { created_at|date(Y-m-d) }

Multiple filters can be applied to the value by piping multiple ones
together. They are processed in order, from left to right:

    { created_at|date_modify(+5 days)|date(Y-m-d) }

#### Provided Filters

The following filters are available when using the parser:

<table>
<thead>
<tr class="header">
<th>Filter</th>
<th>Arguments</th>
<th>Description</th>
<th>Example</th>
</tr>
</thead>
<tbody>
<tr class="odd">
<td><p>abs</p>
<p>capitalize</p></td>
<td></td>
<td><p>Displays the absolute value of a number.</p>
<p>Displays the string in sentence case: all lowercase with firstletter
capitalized.</p></td>
<td><p>{ v|abs }</p>
<p>{ v|capitalize}</p></td>
</tr>
<tr class="even">
<td><p>date</p></td>
<td><blockquote>
<p>format (Y-m-d)</p>
</blockquote></td>
<td><p>A PHP <strong>date</strong>-compatible formatting
string.</p></td>
<td><p>{ v|date(Y-m-d) }</p></td>
</tr>
<tr class="odd">
<td><p>date_modify</p></td>
<td><blockquote>
<p>value to add / subtract</p>
</blockquote></td>
<td><p>A <strong>strtotime</strong> compatible string to modify the
date, like <code>+5 day</code> or <code>-1 week</code>.</p></td>
<td><p>{ v|date_modify(+1 day) }</p></td>
</tr>
<tr class="even">
<td><p>default</p></td>
<td><blockquote>
<p>default value</p>
</blockquote></td>
<td><p>Displays the default value if the variable is empty or
undefined.</p></td>
<td><p>{ v|default(just in case) }</p></td>
</tr>
<tr class="odd">
<td><p>esc</p></td>
<td><blockquote>
<p>html, attr, css, js</p>
</blockquote></td>
<td><p>Specifies the context to escape the data.</p></td>
<td><p>{ v|esc(attr) }</p></td>
</tr>
<tr class="even">
<td><p>excerpt</p></td>
<td><blockquote>
<p>phrase, radius</p>
</blockquote></td>
<td><p>Returns the text within a radius of words from a given phrase.
Same as <strong>excerpt</strong> helper function.</p></td>
<td><p>{ v|excerpt(green giant, 20) }</p></td>
</tr>
<tr class="odd">
<td><p>highlight</p>
<p>highlight_code</p></td>
<td><blockquote>
<p>phrase</p>
</blockquote></td>
<td><p>Highlights a given phrase within the text using
'&lt;mark&gt;&lt;/mark&gt;' tags.</p>
<p>Highlights code samples with HTML/CSS.</p></td>
<td><p>{ v|highlight(view parser) }</p>
<p>{ v|highlight_code }</p></td>
</tr>
<tr class="even">
<td><p>limit_chars</p></td>
<td><blockquote>
<p>limit</p>
</blockquote></td>
<td><p>Limits the number of characters to $limit.</p></td>
<td><p>{ v|limit_chars(100) }</p></td>
</tr>
<tr class="odd">
<td><p>limit_words</p></td>
<td><blockquote>
<p>limit</p>
</blockquote></td>
<td><p>Limits the number of words to $limit.</p></td>
<td><p>{ v|limit_words(20) }</p></td>
</tr>
<tr class="even">
<td><p>local_currency</p></td>
<td><blockquote>
<p>currency, locale, fraction</p>
</blockquote></td>
<td><p>Displays a localized version of a currency. "currency" valueis
any 3-letter ISO 4217 currency code.</p></td>
<td><p>{ v|local_currency(EUR,en_US) }</p></td>
</tr>
<tr class="odd">
<td><p>local_number</p>
<p>lower</p>
<p>nl2br</p></td>
<td><blockquote>
<p>type, precision, locale</p>
</blockquote></td>
<td><p>Displays a localized version of a number. "type" can be one of:
decimal, currency, percent, scientific, spellout, ordinal, duration.</p>
<p>Converts a string to lowercase.</p>
<p>Replaces all newline characters (n) to an HTML &lt;br/&gt;
tag.</p></td>
<td><p>{ v|local_number(decimal,2,en_US) }</p>
<p>{ v|lower }</p>
<p>{ v|nl2br }</p></td>
</tr>
<tr class="even">
<td><p>number_format</p>
<p>prose</p></td>
<td><blockquote>
<p>places</p>
</blockquote></td>
<td><p>Wraps PHP <strong>number_format</strong> function for use within
the parser.</p>
<p>Takes a body of text and uses the <strong>auto_typography()</strong>
method to turn it into prettier, easier-to-read, prose.</p></td>
<td><p>{ v|number_format(3) }</p>
<p>{ v|prose }</p></td>
</tr>
<tr class="odd">
<td><p>round</p></td>
<td><blockquote>
<p>places, type</p>
</blockquote></td>
<td><p>Rounds a number to the specified places. Types of
<strong>ceil</strong> and <strong>floor</strong> can be passed to use
those functions instead.</p></td>
<td><p>{ vround(ceil) }</p></td>
</tr>
<tr class="even">
<td><p>strip_tags</p>
<p>title</p>
<p>upper</p></td>
<td><blockquote>
<p>allowed chars</p>
</blockquote></td>
<td><p>Wraps PHP <strong>strip_tags</strong>. Can accept a string of
allowed tags.</p>
<p>Displays a "title case" version of the string, with all lowercase,
and each word capitalized.</p>
<p>Displays the string in all uppercase.</p></td>
<td><p>{ v|strip_tags(&lt;br&gt;) }</p>
<p>{ v|title }</p>
<p>{ v|upper }</p></td>
</tr>
</tbody>
</table>

See [PHP's
NumberFormatter](https://www.php.net/manual/en/numberformatter.create.php)
for details relevant to the "local_number" filter.

#### Custom Filters

You can easily create your own filters by editing
**app/Config/View.php** and adding new entries to the `$filters` array.
Each key is the name which the filter is called by in the view, and its
value is any valid PHP callable:

<div class="literalinclude">

view_parser/012.php

</div>

### Parser Plugins

Plugins allow you to extend the parser, adding custom features for each
project. They can be any PHP callable, making them very simple to
implement. Within templates, plugins are specified by `{+ +}` tags:

    {+ foo +} inner content {+ /foo +}

This example shows a plugin named **foo**. It can manipulate any of the
content between its opening and closing tags. In this example, it could
work with the text " inner content ". Plugins are processed before any
pseudo-variable replacements happen.

While plugins will often consist of tag pairs, like shown above, they
can also be a single tag, with no closing tag:

    {+ foo +}

Opening tags can also contain parameters that can customize how the
plugin works. The parameters are represented as key/value pairs:

    {+ foo bar=2 baz="x y" +}

Parameters can also be single values:

    {+ include somefile.php +}

#### Provided Plugins

The following plugins are available when using the parser:

<table>
<thead>
<tr class="header">
<th>Plugin</th>
<th>Arguments</th>
<th>Description</th>
<th>Example</th>
</tr>
</thead>
<tbody>
<tr class="odd">
<td><p>current_url previous_url siteURL</p></td>
<td></td>
<td><p>Alias for the current_url helper function. Alias for the
previous_url helper function. Alias for the site_url helper
function.</p></td>
<td><p>{+ current_url +} {+ previous_url +} {+ siteURL "login"
+}</p></td>
</tr>
<tr class="even">
<td>mailto</td>
<td>email, title, attributes</td>
<td>Alias for the mailto helper function.</td>
<td>{+ mailto <a
href="mailto:email=foo@example.com">email=foo@example.com</a>
title="Stranger Things" +}</td>
</tr>
<tr class="odd">
<td>safe_mailto</td>
<td>email, title, attributes</td>
<td>Alias for the safe_mailto helper function.</td>
<td>{+ safe_mailto <a
href="mailto:email=foo@example.com">email=foo@example.com</a>
title="Stranger Things" +}</td>
</tr>
<tr class="even">
<td>lang</td>
<td>language string</td>
<td>Alias for the lang helper function.</td>
<td>{+ lang number.terabyteAbbr +}</td>
</tr>
<tr class="odd">
<td><p>validation_errors</p></td>
<td><p>fieldname(optional)</p></td>
<td><p>Returns either error string for the field (if specified) or all
validation errors.</p></td>
<td><p>{+ validation_errors +} , {+ validation_errors field="email"
+}</p></td>
</tr>
<tr class="even">
<td><p>route csp_script_nonce</p>
<p>csp_style_nonce</p></td>
<td><p>route name</p></td>
<td><p>Alias for the route_to helper function. Alias for the
csp_script_nonce helper function. Alias for the csp_style_nonce helper
function.</p></td>
<td><p>{+ route "login" +} {+ csp_script_nonce +}</p>
<p>{+ csp_style_nonce +}</p></td>
</tr>
</tbody>
</table>

#### Registering a Plugin

At its simplest, all you need to do to register a new plugin and make it
ready for use is to add it to the **app/Config/View.php**, under the
`$plugins` array. The key is the name of the plugin that is used within
the template file. The value is any valid PHP callable, including static
class methods:

<div class="literalinclude">

view_parser/014.php

</div>

You can also use closures, but these can only be defined in the config
file's constructor:

<div class="literalinclude">

view_parser/015.php

</div>

If the callable is on its own, it is treated as a single tag, not a
open/close one. It will be replaced by the return value from the plugin:

<div class="literalinclude">

view_parser/016.php

</div>

If the callable is wrapped in an array, it is treated as an open/close
tag pair that can operate on any of the content between its tags:

<div class="literalinclude">

view_parser/017.php

</div>

## Usage Notes

If you include substitution parameters that are not referenced in your
template, they are ignored:

<div class="literalinclude">

view_parser/018.php

</div>

If you do not include a substitution parameter that is referenced in
your template, the original pseudo-variable is shown in the result:

<div class="literalinclude">

view_parser/019.php

</div>

If you provide a string substitution parameter when an array is
expected, i.e., for a variable pair, the substitution is done for the
opening variable pair tag, but the closing variable pair tag is not
rendered properly:

<div class="literalinclude">

view_parser/020.php

</div>

### View Fragments

You do not have to use variable pairs to get the effect of iteration in
your views. It is possible to use a view fragment for what would be
inside a variable pair, and to control the iteration in your controller
instead of in the view.

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

An example with the iteration controlled in the controller, using a view
fragment:

<div class="literalinclude">

view_parser/021.php

</div>

Result:

    <ul>
        <li><a href="/first">First Link</a></li>
        <li><a href="/second">Second Link</a></li>
    </ul>

## Class Reference

> > param string \$view  
> > File name of the view source
> >
> > param array \$options  
> > Array of options, as key/value pairs
> >
> > param boolean \$saveData  
> > If true, will save data for use with any other calls, if false, will
> > clean the data after rendering the view.
> >
> > returns  
> > The rendered text for the chosen view
> >
> > rtype  
> > string
> >
> > Builds the output based upon a file name and any data that has
> > already been set:
> >
> > <div class="literalinclude">
> >
> > view_parser/022.php
> >
> > </div>
> >
> > Options supported:
> >
> > > - `cache` - the time in seconds, to save a view's results
> > > - `cache_name` - the ID used to save/retrieve a cached view
> > >   result; defaults to the viewpath
> > > - `cascadeData` - true if the data pairs in effect when a nested
> > >   or loop substitution occurs should be propagated
> > > - `saveData` - true if the view data parameter should be retained
> > >   for subsequent calls
> >
> > Any conditional substitutions are performed first, then remaining
> > substitutions are performed for each data pair.
>
> > param string \$template  
> > View source provided as a string
> >
> > param array \$options  
> > Array of options, as key/value pairs
> >
> > param boolean \$saveData  
> > If true, will save data for use with any other calls, if false, will
> > clean the data after rendering the view.
> >
> > returns  
> > The rendered text for the chosen view
> >
> > rtype  
> > string
> >
> > Builds the output based upon a provided template source and any data
> > that has already been set:
> >
> > <div class="literalinclude">
> >
> > view_parser/023.php
> >
> > </div>
> >
> > Options supported, and behavior, as above.
>
> > param array \$data  
> > Array of view data strings, as key/value pairs
> >
> > param string \$context  
> > The context to use for data escaping.
> >
> > returns  
> > The Renderer, for method chaining
> >
> > rtype  
> > CodeIgniter\View\RendererInterface.
> >
> > Sets several pieces of view data at once:
> >
> > <div class="literalinclude">
> >
> > view_parser/024.php
> >
> > </div>
> >
> > Supported escape contexts: html, css, js, url, or attr or raw. If
> > 'raw', no escaping will happen.
>
> > param string \$name  
> > Name of the view data variable
> >
> > param mixed \$value  
> > The value of this view data
> >
> > param string \$context  
> > The context to use for data escaping.
> >
> > returns  
> > The Renderer, for method chaining
> >
> > rtype  
> > CodeIgniter\View\RendererInterface.
> >
> > Sets a single piece of view data:
> >
> > <div class="literalinclude">
> >
> > view_parser/025.php
> >
> > </div>
> >
> > Supported escape contexts: html, css, js, url, attr or raw. If
> > 'raw', no escaping will happen.
>
> > param string \$leftDelimiter  
> > Left delimiter for substitution fields
> >
> > param string \$rightDelimiter  
> > right delimiter for substitution fields
> >
> > returns  
> > The Renderer, for method chaining
> >
> > rtype  
> > CodeIgniter\View\RendererInterface.
> >
> > Override the substitution field delimiters:
> >
> > <div class="literalinclude">
> >
> > view_parser/026.php
> >
> > </div>
>
> > param string \$leftDelimiter  
> > Left delimiter for conditionals
> >
> > param string \$rightDelimiter  
> > right delimiter for conditionals
> >
> > returns  
> > The Renderer, for method chaining
> >
> > rtype  
> > CodeIgniter\View\RendererInterface.
> >
> > Override the conditional delimiters:
> >
> > <div class="literalinclude">
> >
> > view_parser/027.php
> >
> > </div>
