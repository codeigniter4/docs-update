# RESTful Resource Handling

<div class="contents" local="" depth="2">

</div>

Representational State Transfer (REST) is an architectural style for
distributed applications, first described by Roy Fielding in his 2000
PhD dissertation, [Architectural Styles and the Design of Network-based
Software
Architectures](https://www.ics.uci.edu/~fielding/pubs/dissertation/top.htm).
That might be a bit of a dry read, and you might find Martin Fowler's
[Richardson Maturity
Model](https://martinfowler.com/articles/richardsonMaturityModel.html) a
gentler introduction.

REST has been interpreted, and mis-interpreted, in more ways than most
software architectures, and it might be easier to say that the more of
Roy Fielding's principles that you embrace in an architecture, the most
"RESTful" your application would be considered.

CodeIgniter makes it easy to create RESTful APIs for your resources,
with its resource routes and
<span class="title-ref">ResourceController</span>.

## Resource Routes

You can quickly create a handful of RESTful routes for a single resource
with the `resource()` method. This creates the five most common routes
needed for full CRUD of a resource: create a new resource, update an
existing one, list all of that resource, show a single resource, and
delete a single resource. The first parameter is the resource name:

<div class="literalinclude">

restful/001.php

</div>

> [!NOTE]
> The ordering above is for clarity, whereas the actual order the routes
> are created in, in RouteCollection, ensures proper route resolution

> [!IMPORTANT]
> The routes are matched in the order they are specified, so if you have
> a resource photos above a get 'photos/poll' the show action's route
> for the resource line will be matched before the get line. To fix
> this, move the get line above the resource line so that it is matched
> first.

The second parameter accepts an array of options that can be used to
modify the routes that are generated. While these routes are geared
toward API-usage, where more methods are allowed, you can pass in the
`websafe` option to have it generate update and delete methods that work
with HTML forms:

<div class="literalinclude">

restful/002.php

</div>

### Change the Controller Used

You can specify the controller that should be used by passing in the
`controller` option with the name of the controller that should be used:

<div class="literalinclude">

restful/003.php

</div>

<div class="literalinclude">

restful/017.php

</div>

<div class="literalinclude">

restful/018.php

</div>

See also `controllers-namespace`.

### Change the Placeholder Used

By default, the `(:segment)` placeholder is used when a resource ID is
needed. You can change this by passing in the `placeholder` option with
the new string to use:

<div class="literalinclude">

restful/004.php

</div>

### Limit the Routes Made

You can restrict the routes generated with the `only` option. This
should be **an array** or **comma separated list** of method names that
should be created. Only routes that match one of these methods will be
created. The rest will be ignored:

<div class="literalinclude">

restful/005.php

</div>

Otherwise you can remove unused routes with the `except` option. This
should also be **an array** or **comma separated list** of method names.
This option run after `only`:

<div class="literalinclude">

restful/006.php

</div>

Valid methods are: `index`, `show`, `create`, `update`, `new`, `edit`
and `delete`.

## ResourceController

The `ResourceController` provides a convenient starting point for your
RESTful API, with methods that correspond to the resource routes above.

Extend it, over-riding the `modelName` and `format` properties, and then
implement those methods that you want handled:

<div class="literalinclude">

restful/007.php

</div>

The routing for this would be:

<div class="literalinclude">

restful/008.php

</div>

## Presenter Routes

You can quickly create a presentation controller which aligns with a
resource controller, using the `presenter()` method. This creates routes
for the controller methods that would return views for your resource, or
process forms submitted from those views.

It is not needed, since the presentation can be handled with a
conventional controller - it is a convenience. Its usage is similar to
the resource routing:

<div class="literalinclude">

restful/009.php

</div>

> [!NOTE]
> The ordering above is for clarity, whereas the actual order the routes
> are created in, in RouteCollection, ensures proper route resolution

You would not have routes for <span class="title-ref">photos</span> for
both a resource and a presenter controller. You need to distinguish
them, for instance:

<div class="literalinclude">

restful/010.php

</div>

The second parameter accepts an array of options that can be used to
modify the routes that are generated.

### Change the Controller Used

You can specify the controller that should be used by passing in the
`controller` option with the name of the controller that should be used:

<div class="literalinclude">

restful/011.php

</div>

<div class="literalinclude">

restful/019.php

</div>

<div class="literalinclude">

restful/020.php

</div>

See also `controllers-namespace`.

### Change the Placeholder Used

By default, the `(:segment)` placeholder is used when a resource ID is
needed. You can change this by passing in the `placeholder` option with
the new string to use:

<div class="literalinclude">

restful/012.php

</div>

### Limit the Routes Made

You can restrict the routes generated with the `only` option. This
should be **an array** or **comma separated list** of method names that
should be created. Only routes that match one of these methods will be
created. The rest will be ignored:

<div class="literalinclude">

restful/013.php

</div>

Otherwise you can remove unused routes with the `except` option. This
should also be **an array** or **comma separated list** of method names.
This option run after `only`:

<div class="literalinclude">

restful/014.php

</div>

Valid methods are: `index`, `show`, `new`, `create`, `edit`, `update`,
`remove` and `delete`.

## ResourcePresenter

The `ResourcePresenter` provides a convenient starting point for
presenting views of your resource, and processing data from forms in
those views, with methods that align to the resource routes above.

Extend it, over-riding the `modelName` property, and then implement
those methods that you want handled:

<div class="literalinclude">

restful/015.php

</div>

The routing for this would be:

<div class="literalinclude">

restful/016.php

</div>

## Presenter/Controller Comparison

This table presents a comparison of the default routes created by
<span class="title-ref">resource()</span> and
<span class="title-ref">presenter()</span> with their corresponding
Controller functions.

<table>
<thead>
<tr class="header">
<th>Operation</th>
<th>Method</th>
<th>Controller Route</th>
<th>Presenter Route</th>
<th>Controller Function</th>
<th>Presenter Function</th>
</tr>
</thead>
<tbody>
<tr class="odd">
<td><strong>New</strong></td>
<td>GET</td>
<td>photos/new</td>
<td>photos/new</td>
<td><code>new()</code></td>
<td><code>new()</code></td>
</tr>
<tr class="even">
<td><p><strong>Create</strong> Create (alias)</p></td>
<td><p>POST POST</p></td>
<td><p>photos</p></td>
<td><p>photos photos/create</p></td>
<td><p><code>create()</code></p></td>
<td><p><code>create()</code> <code>create()</code></p></td>
</tr>
<tr class="odd">
<td><strong>List</strong></td>
<td>GET</td>
<td>photos</td>
<td>photos</td>
<td><code>index()</code></td>
<td><code>index()</code></td>
</tr>
<tr class="even">
<td><p><strong>Show</strong> Show (alias)</p></td>
<td><p>GET GET</p></td>
<td><p>photos/(:segment)</p></td>
<td><p>photos/(:segment) photos/show/(:segment)</p></td>
<td><p><code>show($id = null)</code></p></td>
<td><p><code>show($id = null)</code>
<code>show($id = null)</code></p></td>
</tr>
<tr class="odd">
<td><p><strong>Edit</strong> <strong>Update</strong></p></td>
<td><p>GET PUT/PATCH</p></td>
<td><p>photos/(:segment)/edit photos/(:segment)</p></td>
<td><p>photos/edit/(:segment)</p></td>
<td><p><code>edit($id = null)</code>
<code>update($id = null)</code></p></td>
<td><p><code>edit($id = null)</code></p></td>
</tr>
<tr class="even">
<td><p>Update (websafe) <strong>Remove</strong> <strong>Delete</strong>
Delete (websafe)</p></td>
<td><p>POST GET DELETE POST</p></td>
<td><p>photos/(:segment)</p>
<p>photos/(:segment)</p></td>
<td><p>photos/update/(:segment) photos/remove/(:segment)</p>
<p>photos/delete/(:segment)</p></td>
<td><p><code>update($id = null)</code></p>
<p><code>delete($id = null)</code>
<code>delete($id = null)</code></p></td>
<td><p><code>update($id = null)</code>
<code>remove($id = null)</code></p>
<p><code>delete($id = null)</code></p></td>
</tr>
</tbody>
</table>
