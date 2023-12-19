# Typography

The Typography library contains methods that help you format text in
semantically relevant ways.

<div class="contents" local="" depth="2">

</div>

## Loading the Library

Like all services in CodeIgniter, it can be loaded via
`Config\Services`, though you usually will not need to load it manually:

<div class="literalinclude">

typography/001.php

</div>

## Available static functions

The following functions are available:

> param string \$str  
> Input string
>
> param bool \$reduce_linebreaks  
> Whether to reduce multiple instances of double newlines to two
>
> returns  
> HTML-formatted typography-safe string
>
> rtype  
> string
>
> Formats text so that it is semantically and typographically correct
> HTML.
>
> Usage example:
>
> <div class="literalinclude">
>
> typography/002.php
>
> </div>
>
> > [!NOTE]
> > Typographic formatting can be processor intensive, particularly if
> > you have a lot of content being formatted. If you choose to use this
> > function you may want to consider `caching <../general/caching>`
> > your pages.

> param string \$str  
> Input string
>
> returns  
> String with formatted characters.
>
> rtype  
> string
>
> This function mainly converts double and single quotes to curly
> entities, but it also converts em-dashes, double spaces, and
> ampersands.
>
> Usage example:
>
> <div class="literalinclude">
>
> typography/003.php
>
> </div>

> param string \$str  
> Input string
>
> returns  
> String with HTML-formatted line breaks
>
> rtype  
> string
>
> Converts newlines to `<br />` tags unless they appear within `<pre>`
> tags. This function is identical to the native PHP `nl2br()` function,
> except that it ignores `<pre>` tags.
>
> Usage example:
>
> <div class="literalinclude">
>
> typography/004.php
>
> </div>
