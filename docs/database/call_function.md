# Custom Function Calls

<div class="contents" local="" depth="2">

</div>

## \$db-\>callFunction()

This function enables you to call PHP database functions that are not
natively included in CodeIgniter, in a platform-independent manner. For
example, let's say you want to call the `mysql_get_client_info()`
function, which is **not** natively supported by CodeIgniter. You could
do so like this:

<div class="literalinclude">

call_function/001.php

</div>

You must supply the name of the function, **without** the `mysql_`
prefix, in the first parameter. The prefix is added automatically based
on which database driver is currently being used. This permits you to
run the same function on different database platforms. Obviously, not
all function calls are identical between platforms, so there are limits
to how useful this function can be in terms of portability.

Any parameters needed by the function you are calling will be added to
the second parameter.

<div class="literalinclude">

call_function/002.php

</div>

Often, you will either need to supply a database connection ID or a
database result ID. The connection ID can be accessed using:

<div class="literalinclude">

call_function/003.php

</div>

The result ID can be accessed from within your result object, like this:

<div class="literalinclude">

call_function/004.php

</div>
