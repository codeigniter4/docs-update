# Security Helper

The Security Helper file contains security related functions.

<div class="contents" local="" depth="2">

</div>

## Loading this Helper

This helper is loaded using the following code:

<div class="literalinclude">

security_helper/001.php

</div>

## Available Functions

The following functions are available:

> param string \$filename  
> Filename
>
> returns  
> Sanitized file name
>
> rtype  
> string
>
> Provides protection against directory traversal.
>
> This function is an alias for
> `\CodeIgniter\Security::sanitizeFilename()`. For more info, please see
> the `Security Library <../libraries/security>` documentation.

> param string \$str  
> Input string
>
> returns  
> The input string with no image tags
>
> rtype  
> string
>
> This is a security function that will strip image tags from a string.
> It leaves the image URL as plain text.
>
> Example:
>
> <div class="literalinclude">
>
> security_helper/002.php
>
> </div>

> param string \$str  
> Input string
>
> returns  
> Safely formatted string
>
> rtype  
> string
>
> This is a security function that converts PHP tags to entities.
>
> Example:
>
> <div class="literalinclude">
>
> security_helper/003.php
>
> </div>
