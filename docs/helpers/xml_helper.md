# XML Helper

The XML Helper file contains functions that assist in working with XML
data.

<div class="contents" local="" depth="2">

</div>

## Loading this Helper

This helper is loaded using the following code

<div class="literalinclude">

xml_helper/001.php

</div>

## Available Functions

The following functions are available:

> param string \$str  
> the text string to convert
>
> param bool \$protect_all  
> Whether to protect all content that looks like a potential entity
> instead of just numbered entities, e.g., &foo;
>
> returns  
> XML-converted string
>
> rtype  
> string
>
> Takes a string as input and converts the following reserved XML
> characters to entities:
>
> > - Ampersands: &
> > - Less than and greater than characters: \< \>
> > - Single and double quotes: ' "
> > - Dashes: -
>
> This function ignores ampersands if they are part of existing numbered
> character entities, e.g., &#123;. Example:
>
> <div class="literalinclude">
>
> xml_helper/002.php
>
> </div>
>
> outputs:
>
> ``` html
> &lt;p&gt;Here is a paragraph &amp; an entity (&#123;).&lt;/p&gt;
> ```
