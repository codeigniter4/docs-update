# Test Helper

The Test Helper file contains functions that assist in testing your
project.

<div class="contents" local="" depth="2">

</div>

## Loading this Helper

This helper is loaded using the following code:

<div class="literalinclude">

test_helper/001.php

</div>

## Available Functions

The following functions are available:

> param Modelstring \$model  
> Instance or name of the model to use with Fabricator
>
> param array\|null \$overrides  
> Overriding data to pass to Fabricator::setOverrides()
>
> returns  
> A random fake item created and added to the database by Fabricator
>
> rtype  
> object\|array
>
> Uses `CodeIgniter\Test\Fabricator` to create a random item and add it
> to the database.
>
> Usage example:
>
> <div class="literalinclude">
>
> test_helper/002.php
>
> </div>
