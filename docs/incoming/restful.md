# RESTful Resource Handling

- [Resource Routes](#resource-routes)
    - [Change the Controller Used](#change-the-controller-used)
    - [Change the Placeholder Used](#change-the-placeholder-used)
    - [Limit the Routes Made](#limit-the-routes-made)
- [ResourceController](#resourcecontroller)
- [Presenter Routes](#presenter-routes)
    - [Change the Controller Used](#change-the-controller-used)
    - [Change the Placeholder Used](#change-the-placeholder-used)
    - [Limit the Routes Made](#limit-the-routes-made)
- [ResourcePresenter](#resourcepresenter)
- [Presenter/Controller Comparison](#presentercontroller-comparison)

Representational State Transfer (REST) is an architectural style for distributed applications, first described by Roy Fielding in his 2000 PhD dissertation, <a href="https://www.ics.uci.edu/~fielding/pubs/dissertation/top.htm" target="_blank">Architectural Styles and the Design of Network-based Software Architectures</a>. That might be a bit of a dry read, and you might find Martin Fowler's <a href="https://martinfowler.com/articles/richardsonMaturityModel.html" target="_blank">Richardson Maturity Model</a> a gentler introduction.

REST has been interpreted, and mis-interpreted, in more ways than most software architectures, and it might be easier to say that the more of Roy Fielding's principles that you embrace in an architecture, the most "RESTful" your application would be considered.

CodeIgniter makes it easy to create RESTful APIs for your resources, with its resource routes and <span class="title-ref">ResourceController</span>.

## Resource Routes

You can quickly create a handful of RESTful routes for a single resource with the `resource()` method. This creates the five most common routes needed for full CRUD of a resource: create a new resource, update an existing one, list all of that resource, show a single resource, and delete a single resource. The first parameter is the resource name:

```php
--8<--
incoming/restful/001.php
--8<--
```

!!! note "Note"
    The ordering above is for clarity, whereas the actual order the routes are created in, in RouteCollection, ensures proper route resolution

!!! important "Important"
    The routes are matched in the order they are specified, so if you have a resource photos above a get 'photos/poll' the show action's route for the resource line will be matched before the get line. To fix this, move the get line above the resource line so that it is matched first.

The second parameter accepts an array of options that can be used to modify the routes that are generated. While these routes are geared toward API-usage, where more methods are allowed, you can pass in the `websafe` option to have it generate update and delete methods that work with HTML forms:

```php
--8<--
incoming/restful/002.php
--8<--
```

### Change the Controller Used

You can specify the controller that should be used by passing in the `controller` option with the name of the controller that should be used:

```php
--8<--
incoming/restful/003.php
--8<--
```

```php
--8<--
incoming/restful/017.php
--8<--
```

```php
--8<--
incoming/restful/018.php
--8<--
```

See also `controllers-namespace`.

### Change the Placeholder Used

By default, the `(:segment)` placeholder is used when a resource ID is needed. You can change this by passing in the `placeholder` option with the new string to use:

```php
--8<--
incoming/restful/004.php
--8<--
```

### Limit the Routes Made

You can restrict the routes generated with the `only` option. This should be **an array** or **comma separated list** of method names that should be created. Only routes that match one of these methods will be created. The rest will be ignored:

```php
--8<--
incoming/restful/005.php
--8<--
```

Otherwise you can remove unused routes with the `except` option. This should also be **an array** or **comma separated list** of method names. This option run after `only`:

```php
--8<--
incoming/restful/006.php
--8<--
```

Valid methods are: `index`, `show`, `create`, `update`, `new`, `edit` and `delete`.

## ResourceController

The `ResourceController` provides a convenient starting point for your RESTful API, with methods that correspond to the resource routes above.

Extend it, over-riding the `modelName` and `format` properties, and then implement those methods that you want handled:

```php
--8<--
incoming/restful/007.php
--8<--
```

The routing for this would be:

```php
--8<--
incoming/restful/008.php
--8<--
```

## Presenter Routes

You can quickly create a presentation controller which aligns with a resource controller, using the `presenter()` method. This creates routes for the controller methods that would return views for your resource, or process forms submitted from those views.

It is not needed, since the presentation can be handled with a conventional controller - it is a convenience. Its usage is similar to the resource routing:

```php
--8<--
incoming/restful/009.php
--8<--
```

!!! note "Note"
    The ordering above is for clarity, whereas the actual order the routes are created in, in RouteCollection, ensures proper route resolution

You would not have routes for <span class="title-ref">photos</span> for both a resource and a presenter controller. You need to distinguish them, for instance:

```php
--8<--
incoming/restful/010.php
--8<--
```

The second parameter accepts an array of options that can be used to modify the routes that are generated.

### Change the Controller Used

You can specify the controller that should be used by passing in the `controller` option with the name of the controller that should be used:

```php
--8<--
incoming/restful/011.php
--8<--
```

```php
--8<--
incoming/restful/019.php
--8<--
```

```php
--8<--
incoming/restful/020.php
--8<--
```

See also `controllers-namespace`.

### Change the Placeholder Used

By default, the `(:segment)` placeholder is used when a resource ID is needed. You can change this by passing in the `placeholder` option with the new string to use:

```php
--8<--
incoming/restful/012.php
--8<--
```

### Limit the Routes Made

You can restrict the routes generated with the `only` option. This should be **an array** or **comma separated list** of method names that should be created. Only routes that match one of these methods will be created. The rest will be ignored:

```php
--8<--
incoming/restful/013.php
--8<--
```

Otherwise you can remove unused routes with the `except` option. This should also be **an array** or **comma separated list** of method names. This option run after `only`:

```php
--8<--
incoming/restful/014.php
--8<--
```

Valid methods are: `index`, `show`, `new`, `create`, `edit`, `update`, `remove` and `delete`.

## ResourcePresenter

The `ResourcePresenter` provides a convenient starting point for presenting views of your resource, and processing data from forms in those views, with methods that align to the resource routes above.

Extend it, over-riding the `modelName` property, and then implement those methods that you want handled:

```php
--8<--
incoming/restful/015.php
--8<--
```

The routing for this would be:

```php
--8<--
incoming/restful/016.php
--8<--
```

## Presenter/Controller Comparison

This table presents a comparison of the default routes created by <span class="title-ref">resource()</span> and <span class="title-ref">presenter()</span> with their corresponding Controller functions.

<table>
<thead>
<tr>
<th>Operation</th>
<th>Method</th>
<th>Controller Route</th>
<th>Presenter Route</th>
<th>Controller Function</th>
<th>Presenter Function</th>
</tr>
</thead>
<tbody>
<tr>
<td><strong>New</strong></td>
<td>GET</td>
<td>photos/new</td>
<td>photos/new</td>
<td><code>new()</code></td>
<td><code>new()</code></td>
</tr>
<tr>
<td><strong>Create</strong></td>
<td>POST</td>
<td>photos</td>
<td>photos</td>
<td><code>create()</code></td>
<td><code>create()</code></td>
</tr>
<tr>
<td>Create (alias)</td>
<td>POST</td>
<td></td>
<td>photos/create</td>
<td></td>
<td><code>create()</code></td>
</tr>
<tr>
<td><strong>List</strong></td>
<td>GET</td>
<td>photos</td>
<td>photos</td>
<td><code>index()</code></td>
<td><code>index()</code></td>
</tr>
<tr>
<td><strong>Show</strong></td>
<td>GET</td>
<td>photos/(:segment)</td>
<td>photos/(:segment)</td>
<td><code>show($id = null)</code></td>
<td><code>show($id = null)</code></td>
</tr>
<tr>
<td>Show (alias)</td>
<td>GET</td>
<td></td>
<td>photos/show/(:segment)</td>
<td></td>
<td><code>show($id = null)</code></td>
</tr>
<tr>
<td><strong>Edit</strong></td>
<td>GET</td>
<td>photos/(:segment)/edit</td>
<td>photos/edit/(:segment)</td>
<td><code>edit($id = null)</code></td>
<td><code>edit($id = null)</code></td>
</tr>
<tr>
<td><strong>Update</strong></td>
<td>PUT/PATCH</td>
<td>photos/(:segment)</td>
<td></td>
<td><code>update($id = null)</code></td>
<td></td>
</tr>
<tr>
<td>Update (websafe)</td>
<td>POST</td>
<td>photos/(:segment)</td>
<td>photos/update/(:segment)</td>
<td><code>update($id = null)</code></td>
<td><code>update($id = null)</code></td>
</tr>
<tr>
<td><strong>Remove</strong></td>
<td>GET</td>
<td></td>
<td>photos/remove/(:segment)</td>
<td></td>
<td><code>remove($id = null)</code></td>
</tr>
<tr>
<td><strong>Delete</strong></td>
<td>DELETE</td>
<td>photos/(:segment)</td>
<td></td>
<td><code>delete($id = null)</code></td>
<td></td>
</tr>
<tr>
<td>Delete (websafe)</td>
<td>POST</td>
<td></td>
<td>photos/delete/(:segment)</td>
<td><code>delete($id = null)</code></td>
<td><code>delete($id = null)</code></td>
</tr>
</tbody>
</table>
