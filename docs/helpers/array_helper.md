# Array Helper

The array helper provides several functions to simplify more complex
usages of arrays. It is not intended to duplicate any of the existing
functionality that PHP provides - unless it is to vastly simplify their
usage.

<div class="contents" local="" depth="2">

</div>

## Loading this Helper

This helper is loaded using the following code:

<div class="literalinclude">

array_helper/001.php

</div>

## Available Functions

The following functions are available:

> param string \$search  
> The dot-notation string describing how to search the array
>
> param array \$values  
> The array to search
>
> returns  
> The value found within the array, or null
>
> rtype  
> mixed
>
> This method allows you to use dot-notation to search through an array
> for a specific-key, and allows the use of a the '\*' wildcard. Given
> the following array:
>
> <div class="literalinclude">
>
> array_helper/002.php
>
> </div>
>
> We can locate the value of 'fizz' by using the search string
> "foo.buzz.fizz". Likewise, the value of baz can be found with
> "foo.bar.baz":
>
> <div class="literalinclude">
>
> array_helper/003.php
>
> </div>
>
> You can use the asterisk as a wildcard to replace any of the segments.
> When found, it will search through all of the child nodes until it
> finds it. This is handy if you don't know the values, or if your
> values have a numeric index:
>
> <div class="literalinclude">
>
> array_helper/004.php
>
> </div>
>
> If the array key contains a dot, then the key can be escaped with a
> backslash:
>
> <div class="literalinclude">
>
> array_helper/005.php
>
> </div>

> [!NOTE]
> Prior to v4.2.0,
> `dot_array_search('foo.bar.baz', ['foo' => ['bar' => 23]])` returned
> `23` due to a bug. v4.2.0 and later returns `null`.

> param mixed \$key  
> The target key
>
> param array \$array  
> The array to search
>
> returns  
> The value found within the array, or null
>
> rtype  
> mixed
>
> Returns the value of an element with a key value in an array of
> uncertain depth

> param array \$array  
> The array to be sorted (passed by reference).
>
> param array \$sortColumns  
> The array keys to sort after and the respective PHP sort flags as an
> associative array.
>
> returns  
> Whether sorting was successful or not.
>
> rtype  
> bool
>
> This method sorts the elements of a multidimensional array by the
> values of one or more keys in a hierarchical way. Take the following
> array, that might be returned from, e.g., the `find()` function of a
> model:
>
> <div class="literalinclude">
>
> array_helper/006.php
>
> </div>
>
> Now sort this array by two keys. Note that the method supports the
> dot-notation to access values in deeper array levels, but does not
> support wildcards:
>
> <div class="literalinclude">
>
> array_helper/007.php
>
> </div>
>
> The `$players` array is now sorted by the 'order' value in each
> players' 'team' subarray. If this value is equal for several players,
> these players will be ordered by their 'position'. The resulting array
> is:
>
> <div class="literalinclude">
>
> array_helper/008.php
>
> </div>
>
> In the same way, the method can also handle an array of objects. In
> the example above it is further possible that each 'player' is
> represented by an array, while the 'teams' are objects. The method
> will detect the type of elements in each nesting level and handle it
> accordingly.

> param iterable \$array  
> The multidimensional array to flatten
>
> param string \$id  
> Optional ID to prepend to the outer keys. Used internally for
> flattening keys.
>
> rtype  
> array
>
> returns  
> The flattened array
>
> This function flattens a multidimensional array to a single key-value
> array by using dots as separators for the keys.
>
> <div class="literalinclude">
>
> array_helper/009.php
>
> </div>
>
> On inspection, `$flattened` is equal to:
>
> <div class="literalinclude">
>
> array_helper/010.php
>
> </div>
>
> Users may use the `$id` parameter on their own, but are not required
> to do so. The function uses this parameter internally to track the
> flattened keys. If users will be supplying an initial `$id`, it will
> be prepended to all keys.
>
> <div class="literalinclude">
>
> array_helper/011.php
>
> </div>

> param array \$array  
> Data rows (most likely from query results)
>
> param array \$indexes  
> Indexes to group values. Follows dot syntax
>
> param bool \$includeEmpty  
> If true, `null` and `''` values are not filtered out
>
> rtype  
> array
>
> returns  
> An array grouped by indexes values
>
> This function allows you to group data rows together by index values.
> The depth of returned array equals the number of indexes passed as
> parameter.
>
> The example shows some data (i.e. loaded from an API) with nested
> arrays.
>
> <div class="literalinclude">
>
> array_helper/012.php
>
> </div>
>
> We want to group them first by "gender", then by "hr.department" (max
> depth = 2). First the result when excluding empty values:
>
> <div class="literalinclude">
>
> array_helper/013.php
>
> </div>
>
> And here the same code, but this time we want to include empty values:
>
> <div class="literalinclude">
>
> array_helper/014.php
>
> </div>