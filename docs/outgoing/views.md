# Views

<div class="contents" local="" depth="2">

</div>

A view is simply a web page, or a page fragment, like a header, footer,
sidebar, etc. In fact, views can flexibly be embedded within other views
(within other views, etc.) if you need this type of hierarchy.

Views are never called directly, they must be loaded by a controller or
`view route <view-routes>`.

Remember that in an MVC framework, the Controller acts as the traffic
cop, so it is responsible for fetching a particular view. If you have
not read the `Controllers </incoming/controllers>` page, you should do
so before continuing.

Using the example controller you created in the controller page, let's
add a view to it.

## Creating a View

Using your text editor, create a file called **blog_view.php** and put
this in it:

    <html>
        <head>
            <title>My Blog</title>
        </head>
        <body>
            <h1>Welcome to my Blog!</h1>
        </body>
    </html>

Then save the file in your **app/Views** directory.

## Displaying a View

To load and display a particular view file you will use the following
code in your controller:

<div class="literalinclude" lines="2-">

views/001.php

</div>

Where *name* is the name of your view file.

> [!IMPORTANT]
> If the file extension is omitted, then the views are expected to end
> with the **.php** extension.

Now, create a file called **Blog.php** in the **app/Controllers**
directory, and put this in it:

<div class="literalinclude">

views/002.php

</div>

Open the routing file located at **app/Config/Routes.php**, and look for
the "Route Definitions". Add the following code:

<div class="literalinclude" lines="2-">

views/013.php

</div>

If you visit your site, you should see your new view. The URL was
similar to this:

    example.com/index.php/blog/

## Loading Multiple Views

CodeIgniter will intelligently handle multiple calls to `view()` from
within a controller. If more than one call happens they will be appended
together. For example, you may wish to have a header view, a menu view,
a content view, and a footer view. That might look something like this:

<div class="literalinclude">

views/003.php

</div>

In the example above, we are using "dynamically added data", which you
will see below.

## Storing Views within Sub-directories

Your view files can also be stored within sub-directories if you prefer
that type of organization. When doing so you will need to include the
directory name loading the view. Example:

<div class="literalinclude" lines="2-">

views/004.php

</div>

## Namespaced Views

You can store views under a **View** directory that is namespaced, and
load that view as if it was namespaced. While PHP does not support
loading non-class files from a namespace, CodeIgniter provides this
feature to make it possible to package your views together in a
module-like fashion for easy re-use or distribution.

If you have **example/blog** directory that has a PSR-4 mapping set up
in the `Autoloader </concepts/autoloader>` living under the namespace
`Example\Blog`, you could retrieve view files as if they were namespaced
also.

Following this example, you could load the **blog_view.php** file from
**example/blog/Views** by prepending the namespace to the view name:

<div class="literalinclude">

views/005.php

</div>

## Caching Views

You can cache a view with the `view()` function by passing a `cache`
option with the number of seconds to cache the view for, in the third
parameter:

<div class="literalinclude" lines="2-">

views/006.php

</div>

By default, the view will be cached using the same name as the view file
itself. You can customize this by passing along `cache_name` and the
cache ID you wish to use:

<div class="literalinclude" lines="2-">

views/007.php

</div>

## Adding Dynamic Data to the View

Data is passed from the controller to the view by way of an array in the
second parameter of the `view()` function. Here's an example:

<div class="literalinclude" lines="2-">

views/008.php

</div>

Let's try it with your controller file. Open it and add this code:

<div class="literalinclude">

views/009.php

</div>

Now open your view file and change the text to variables that correspond
to the array keys in your data:

    <html>
        <head>
            <title><?= esc($title) ?></title>
        </head>
        <body>
            <h1><?= esc($heading) ?></h1>
        </body>
    </html>

Then load the page at the URL you've been using and you should see the
variables replaced.

### The saveData Option

The data passed in is retained for subsequent calls to `view()`. If you
call the function multiple times in a single request, you will not have
to pass the desired data to each `view()`.

But this might not keep any data from "bleeding" into other views,
potentially causing issues. If you would prefer to clean the data after
one call, you can pass the `saveData` option into the `$option` array in
the third parameter.

<div class="literalinclude" lines="2-">

views/010.php

</div>

Additionally, if you would like the default functionality of the
`view()` function to be that it does clear the data between calls, you
can set `$saveData` to `false` in **app/Config/Views.php**.

## Creating Loops

The data array you pass to your view files is not limited to simple
variables. You can pass multi dimensional arrays, which can be looped to
generate multiple rows. For example, if you pull data from your database
it will typically be in the form of a multi-dimensional array.

Here's a simple example. Add this to your controller:

<div class="literalinclude">

views/011.php

</div>

Now open your view file and create a loop:

<div class="literalinclude">

views/012.php

</div>
