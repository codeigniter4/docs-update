# CLIRequest Class

If a request comes from a command line invocation, the request object is
actually a `CLIRequest`. It behaves the same as a
`conventional request </incoming/request>` but adds some accessor
methods for convenience.

## Additional Accessors

### getSegments()

Returns an array of the command line arguments deemed to be part of a
path:

<div class="literalinclude">

cli_request/001.php

</div>

### getPath()

Returns the reconstructed path as a string:

<div class="literalinclude">

cli_request/002.php

</div>

### getOptions()

Returns an array of the command line arguments deemed to be options:

<div class="literalinclude">

cli_request/003.php

</div>

### getOption(\$which)

Returns the value of a specific command line argument deemed to be an
option:

<div class="literalinclude">

cli_request/004.php

</div>

### getOptionString()

Returns the reconstructed command line string for the options:

<div class="literalinclude">

cli_request/005.php

</div>

Passing `true` to the first argument will try to write long options
using two dashes:

<div class="literalinclude">

cli_request/006.php

</div>
