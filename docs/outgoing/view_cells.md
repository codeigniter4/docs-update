# View Cells

Many applications have small view fragments that can be repeated from
page to page, or in different places on the pages. These are often help
boxes, navigation controls, ads, login forms, etc. CodeIgniter lets you
encapsulate the logic for these presentation blocks within View Cells.
They are basically mini-views that can be included in other views. They
can have logic built in to handle any cell-specific display logic. They
can be used to make your views more readable and maintainable by
separating the logic for each cell into its own class.

<div class="contents" local="" depth="2">

</div>

## Simple and Controlled Cells

CodeIgniter supports two types of View Cells: simple and controlled.

**Simple View Cells** can be generated from any class and method of your
choice and does not have to follow any rules, except that it must return
a string.

**Controlled View Cells** must be generated from a class that extends
`Codeigniter\View\Cells\Cell` class which provides additional capability
making your View Cells more flexible and faster to use.

## Calling a View Cell

No matter which type of View Cell you are using, you can call it from
any view by using the `view_cell()` helper function.

The first parameter is (1) *the name of the class and method* (Simple
Cell) or (2) *the name of the class and optional method* (Controlled
Cell) to call, and the second parameter is an array or string of
parameters to pass to the method:

<div class="literalinclude">

view_cells/001.php

</div>

The string that the Cell returns will be inserted into the view where
the `view_cell()` function was called.

### Namespace Omission

<div class="versionadded">

4.3.0

</div>

If you do not include the full namespace for the class, it will assume
in can be found in the `App\Cells` namespace. So, the following example
would attempt to find the `MyClass` class in **app/Cells/MyClass.php**.
If it is not found there, all namespaces will be scanned until it is
found, searching within a **Cells** subdirectory of each namespace:

<div class="literalinclude">

view_cells/002.php

</div>

### Passing Parameters as Key/Value String

You can also pass the parameters along as a key/value string:

<div class="literalinclude">

view_cells/003.php

</div>

## Simple Cells

Simple Cells are classes that return a string from the chosen method. An
example of a simple Alert Message cell might look like this:

<div class="literalinclude">

view_cells/004.php

</div>

You would call it from within a view like:

<div class="literalinclude">

view_cells/005.php

</div>

Additionally, you can use parameter names that match the parameter
variables in the method for better readability. When you use it this
way, all of the parameters must always be specified in the view cell
call:

<div class="literalinclude">

view_cells/006.php

</div>

<div class="literalinclude">

view_cells/007.php

</div>

## Controlled Cells

<div class="versionadded">

4.3.0

</div>

Controlled cells have two primary goals: (1) to make it as fast as
possible to build the cell, and (2) provide additional logic and
flexibility to your views, if they need it.

The class must extend `CodeIgniter\View\Cells\Cell`. They should have a
view file in the same folder. By convention, the class name should be in
PascalCase suffixed with `Cell` and the view should be the snake_cased
version of the class name, without the suffix. For example, if you have
a `MyCell` class, the view file should be `my.php`.

> [!NOTE]
> Prior to v4.3.5, the generated view file ends with `_cell.php`. Though
> v4.3.5 and newer will generate view files without the `_cell` suffix,
> existing view files will still be located and loaded.

### Creating a Controlled Cell

At the most basic level, all you need to implement within the class are
public properties. These properties will be made available to the view
file automatically.

Implementing the AlertMessage from above as a Controlled Cell would look
like this:

<div class="literalinclude">

view_cells/008.php

</div>

<div class="literalinclude">

view_cells/009.php

</div>

<div class="literalinclude">

view_cells/010.php

</div>

### Generating Cell via Command

You can also create a controlled cell via a built in command from the
CLI. The command is `php spark make:cell`. It takes one argument, the
name of the cell to create. The name should be in PascalCase, and the
class will be created in the **app/Cells** directory. The view file will
also be created in the **app/Cells** directory.

``` console
php spark make:cell AlertMessageCell
```

### Using a Different View

You can specify a custom view name by setting the `view` property in the
class. The view will be located like any view would be normally:

<div class="literalinclude">

view_cells/011.php

</div>

### Customize the Rendering

If you need more control over the rendering of the HTML, you can
implement a `render()` method. This method allows you to perform
additional logic and pass extra data the view, if needed. The `render()`
method must return a string.

To take advantage of the full features of controlled Cells, you should
use `$this->view()` instead of the normal `view()` helper function:

<div class="literalinclude">

view_cells/012.php

</div>

### Computed Properties

If you need to perform additional logic for one or more properties you
can use computed properties. These require setting the property to
either `protected` or `private` and implementing a public method whose
name consists of the property name surrounded by `get` and `Property`:

<div class="literalinclude">

view_cells/013.php

</div>

<div class="literalinclude">

view_cells/014.php

</div>

<div class="literalinclude">

view_cells/015.php

</div>

> [!IMPORTANT]
> You can't set properties that are declared as private during cell
> initialization.

### Presentation Methods

Sometimes you need to perform additional logic for the view, but you
don't want to pass it as a parameter. You can implement a method that
will be called from within the cell's view itself. This can help the
readability of your views:

<div class="literalinclude">

view_cells/016.php

</div>

<div class="literalinclude">

view_cells/017.php

</div>

### Performing Setup Logic

If you need to perform additional logic before the view is rendered, you
can implement a `mount()` method. This method will be called just after
the class is instantiated, and can be used to set additional properties
or perform other logic:

<div class="literalinclude">

view_cells/018.php

</div>

You can pass additional parameters to the `mount()` method by passing
them as an array to the `view_cell()` helper function. Any of the
parameters sent that match a parameter name of the `mount()` method will
be passed in:

<div class="literalinclude">

view_cells/019.php

</div>

<div class="literalinclude">

view_cells/020.php

</div>

## Cell Caching

You can cache the results of the view cell call by passing the number of
seconds to cache the data for as the third parameter. This will use the
currently configured cache engine:

<div class="literalinclude">

view_cells/021.php

</div>

You can provide a custom name to use instead of the auto-generated one
if you like, by passing the new name as the fourth parameter:

<div class="literalinclude">

view_cells/022.php

</div>
