# Running Controllers via CLI

As well as calling an application's
`Controllers </incoming/controllers>` via the URL in a browser they can
also be loaded via the command-line interface (CLI).

> [!NOTE]
> It is recommended to use Spark Commands for CLI scripts instead of
> calling controllers via CLI. See the `spark_commands` and
> `cli_commands` page for detailed information.

<div class="contents" local="" depth="2">

</div>

## Let's try it: Hello World!

### Create a Controller

Let's create a simple controller so you can see it in action. Using your
text editor, create a file called Tools.php, and put the following code
in it:

<div class="literalinclude">

cli_controllers/001.php

</div>

> [!NOTE]
> If you use `auto-routing-improved`, change the method name to
> `cliMessage()`.

Then save the file to your **app/Controllers/** directory.

### Define a Route

If you use Auto Routing, skip this.

In your **app/Config/Routes.php** file you can create routes that are
only accessible from the CLI as easily as you would create any other
route. Instead of using the `get()`, `post()`, or similar method, you
would use the `cli()` method. Everything else works exactly like a
normal route definition:

<div class="literalinclude">

cli_controllers/002.php

</div>

For more information, see the `Routes <command-line-only-routes>` page.

> [!WARNING]
> If you enable `auto-routing-legacy` and place the command file in
> **app/Controllers**, anyone could access the command with the help of
> `auto-routing-legacy` via HTTP.

### Run via CLI

Now normally you would visit your site using a URL similar to this:

    example.com/index.php/tools/message/to

Instead, we are going to open Terminal in Mac/Linux or go to Run \>
"cmd" in Windows and navigate to our CodeIgniter project's web root.

``` bash
$ cd /path/to/project/public
$ php index.php tools message
```

If you did it right, you should see "Hello World!" printed.

``` bash
$ php index.php tools message "John Smith"
```

Here we are passing it an argument in the same way that URL parameters
work. "John Smith" is passed as an argument and output is:

    Hello John Smith!

## That's the Basics!

That, in a nutshell, is all there is to know about controllers on the
command line. Remember that this is just a normal controller, so routing
and `_remap()` works fine.

> [!NOTE]
> `_remap()` does not work with `auto-routing-improved`.

If you want to make sure running via CLI, check the return value of
`is_cli()`.

However, CodeIgniter provides additional tools to make creating
CLI-accessible scripts even more pleasant, include CLI-only routing, and
a library that helps you with CLI-only tools.
