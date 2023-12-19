# Text Helper

The Text Helper file contains functions that assist in working with
Text.

<div class="contents" local="" depth="2">

</div>

## Loading this Helper

This helper is loaded using the following code:

<div class="literalinclude">

text_helper/001.php

</div>

## Available Functions

The following functions are available:

> param string \$type  
> Randomization type
>
> param int \$len  
> Output string length
>
> returns  
> A random string
>
> rtype  
> string
>
> Generates a random string based on the type and length you specify.
> Useful for creating passwords or generating random hashes.
>
> > [!WARNING]
> > For types: **basic**, **md5**, and **sha1**, generated strings are
> > not cryptographically secure. Therefore, these types cannot be used
> > for cryptographic purposes or purposes requiring unguessable return
> > values. Since v4.3.3, these types are deprecated.
>
> The first parameter specifies the type of string, the second parameter
> specifies the length. The following choices are available:
>
> - **alpha**: A string with lower and uppercase letters only.
> - **alnum**: Alphanumeric string with lower and uppercase characters.
> - **basic**: \[deprecated\] A random number based on `mt_rand()`
>   (length ignored).
> - **numeric**: Numeric string.
> - **nozero**: Numeric string with no zeros.
> - **md5**: \[deprecated\] An encrypted random number based on `md5()`
>   (fixed length of 32).
> - **sha1**: \[deprecated\] An encrypted random number based on
>   `sha1()` (fixed length of 40).
> - **crypto**: A random string based on `random_bytes()`.
>
> > [!NOTE]
> > When you use **crypto**, you must set an even number to the second
> > parameter. Since v4.2.2, if you set an odd number,
> > `InvalidArgumentException` will be thrown.
>
> > [!NOTE]
> > Since v4.3.3, **alpha**, **alnum** and **nozero** use
> > `random_byte()`, and **numeric** uses `random_int()`. In the
> > previous versions, it used `str_shuffle()` that is not
> > cryptographically secure.
>
> Usage example:
>
> <div class="literalinclude">
>
> text_helper/002.php
>
> </div>

> param string \$str  
> Input string
>
> param string \$separator  
> Separator to append a duplicate number with
>
> param int \$first  
> Starting number
>
> returns  
> An incremented string
>
> rtype  
> string
>
> Increments a string by appending a number to it or increasing the
> number. Useful for creating "copies" or a file or duplicating database
> content which has unique titles or slugs.
>
> Usage example:
>
> <div class="literalinclude">
>
> text_helper/003.php
>
> </div>

> param mixed \$args  
> A variable number of arguments
>
> returns  
> Alternated string(s)
>
> rtype  
> mixed
>
> Allows two or more items to be alternated between, when cycling
> through a loop. Example:
>
> <div class="literalinclude">
>
> text_helper/004.php
>
> </div>
>
> You can add as many parameters as you want, and with each iteration of
> your loop the next item will be returned.
>
> <div class="literalinclude">
>
> text_helper/005.php
>
> </div>
>
> > [!NOTE]
> > To use multiple separate calls to this function simply call the
> > function with no arguments to re-initialize.

> param string \$str  
> Input string
>
> returns  
> A string with normalized slashes
>
> rtype  
> string
>
> Converts double slashes in a string to a single slash, except those
> found in URL protocol prefixes (e.g., http&#58;//).
>
> Example:
>
> <div class="literalinclude">
>
> text_helper/006.php
>
> </div>

> param mixed \$data  
> Input string or an array of strings
>
> returns  
> String(s) with stripped slashes
>
> rtype  
> mixed
>
> Removes any slashes from an array of strings.
>
> Example:
>
> <div class="literalinclude">
>
> text_helper/007.php
>
> </div>
>
> The above will return the following array:
>
> <div class="literalinclude">
>
> text_helper/008.php
>
> </div>
>
> > [!NOTE]
> > For historical reasons, this function will also accept and handle
> > string inputs. This however makes it just an alias for
> > `stripslashes()`.

> param string \$str  
> Text to search in
>
> param string \$character  
> Character to reduce
>
> param bool \$trim  
> Whether to also trim the specified character
>
> returns  
> Reduced string
>
> rtype  
> string
>
> Reduces multiple instances of a particular character occurring
> directly after each other. Example:
>
> <div class="literalinclude">
>
> text_helper/009.php
>
> </div>
>
> If the third parameter is set to true it will remove occurrences of
> the character at the beginning and the end of the string. Example:
>
> <div class="literalinclude">
>
> text_helper/010.php
>
> </div>

> param string \$str  
> Input string
>
> returns  
> String with quotes converted to HTML entities
>
> rtype  
> string
>
> Converts single and double quotes in a string to the corresponding
> HTML entities. Example:
>
> <div class="literalinclude">
>
> text_helper/011.php
>
> </div>

> param string \$str  
> Input string
>
> returns  
> String with quotes stripped
>
> rtype  
> string
>
> Removes single and double quotes from a string. Example:
>
> <div class="literalinclude">
>
> text_helper/012.php
>
> </div>

> param string \$str  
> Input string
>
> param int \$limit  
> Limit
>
> param string \$end_char  
> End character (usually an ellipsis)
>
> returns  
> Word-limited string
>
> rtype  
> string
>
> Truncates a string to the number of *words* specified. Example:
>
> <div class="literalinclude">
>
> text_helper/013.php
>
> </div>
>
> The third parameter is an optional suffix added to the string. By
> default it adds an ellipsis.

> param string \$str  
> Input string
>
> param int \$n  
> Number of characters
>
> param string \$end_char  
> End character (usually an ellipsis)
>
> returns  
> Character-limited string
>
> rtype  
> string
>
> Truncates a string to the number of *characters* specified. It
> maintains the integrity of words so the character count may be
> slightly more or less than what you specify.
>
> Example:
>
> <div class="literalinclude">
>
> text_helper/014.php
>
> </div>
>
> The third parameter is an optional suffix added to the string, if
> undeclared this helper uses an ellipsis.
>
> > [!NOTE]
> > If you need to truncate to an exact number of characters, please see
> > the `ellipsize()` function below.

> param string \$str  
> Input string
>
> returns  
> A string with ASCII values converted to entities
>
> rtype  
> string
>
> Converts ASCII values to character entities, including high ASCII and
> MS Word characters that can cause problems when used in a web page, so
> that they can be shown consistently regardless of browser settings or
> stored reliably in a database. There is some dependence on your
> server's supported character sets, so it may not be 100% reliable in
> all cases, but for the most part, it should correctly identify
> characters outside the normal range (like accented characters).
>
> Example:
>
> <div class="literalinclude">
>
> text_helper/015.php
>
> </div>

> param string \$str  
> Input string
>
> param bool \$all  
> Whether to convert unsafe entities as well
>
> returns  
> A string with HTML entities converted to ASCII characters
>
> rtype  
> string
>
> This function does the opposite of `ascii_to_entities()`. It turns
> character entities back into ASCII.

> param string \$str  
> Input string
>
> returns  
> A string with accented characters converted
>
> rtype  
> string
>
> Transliterates high ASCII characters to low ASCII equivalents. Useful
> when non-English characters need to be used where only standard ASCII
> characters are safely used, for instance, in URLs.
>
> Example:
>
> <div class="literalinclude">
>
> text_helper/016.php
>
> </div>
>
> > [!NOTE]
> > This function uses a companion config file
> > **app/Config/ForeignCharacters.php** to define the to and from array
> > for transliteration.

> param string \$str  
> Input string
>
> param array \$censored  
> List of bad words to censor
>
> param string \$replacement  
> What to replace bad words with
>
> returns  
> Censored string
>
> rtype  
> string
>
> Enables you to censor words within a text string. The first parameter
> will contain the original string. The second will contain an array of
> words which you disallow. The third (optional) parameter can contain a
> replacement value for the words. If not specified they are replaced
> with pound signs: \####.
>
> Example:
>
> <div class="literalinclude">
>
> text_helper/017.php
>
> </div>

> param string \$str  
> Input string
>
> returns  
> String with code highlighted via HTML
>
> rtype  
> string
>
> Colorizes a string of code (PHP, HTML, etc.). Example:
>
> <div class="literalinclude">
>
> text_helper/018.php
>
> </div>
>
> The function uses PHP's `highlight_string()` function, so the colors
> used are the ones specified in your php.ini file.

> param string \$str  
> Input string
>
> param string \$phrase  
> Phrase to highlight
>
> param string \$tag_open  
> Opening tag used for the highlight
>
> param string \$tag_close  
> Closing tag for the highlight
>
> returns  
> String with a phrase highlighted via HTML
>
> rtype  
> string
>
> Will highlight a phrase within a text string. The first parameter will
> contain the original string, the second will contain the phrase you
> wish to highlight. The third and fourth parameters will contain the
> opening/closing HTML tags you would like the phrase wrapped in.
>
> Example:
>
> <div class="literalinclude">
>
> text_helper/019.php
>
> </div>
>
> The above code prints:
>
>     Here is a <span style="color:#990000;">nice text</span> string about nothing in particular.
>
> > [!NOTE]
> > This function used to use the `<strong>` tag by default. Older
> > browsers might not support the new HTML5 mark tag, so it is
> > recommended that you insert the following CSS code into your
> > stylesheet if you need to support such browsers:
> >
> > mark {  
> > background: \#ff0; color: \#000;
> >
> > };

> param string \$str  
> Input string
>
> param int \$charlim  
> Character limit
>
> returns  
> Word-wrapped string
>
> rtype  
> string
>
> Wraps text at the specified *character* count while maintaining
> complete words.
>
> Example:
>
> <div class="literalinclude">
>
> text_helper/020.php
>
> </div>

> param string \$str  
> Input string
>
> param int \$max_length  
> String length limit
>
> param mixed \$position  
> Position to split at (int or float)
>
> param string \$ellipsis  
> What to use as the ellipsis character
>
> returns  
> Ellipsized string
>
> rtype  
> string
>
> This function will strip tags from a string, split it at a defined
> maximum length, and insert an ellipsis.
>
> The first parameter is the string to ellipsize, the second is the
> number of characters in the final string. The third parameter is where
> in the string the ellipsis should appear from 0 - 1, left to right.
> For example. a value of 1 will place the ellipsis at the right of the
> string, .5 in the middle, and 0 at the left.
>
> An optional fourth parameter is the kind of ellipsis. By default,
> &hellip; will be inserted.
>
> Example:
>
> <div class="literalinclude">
>
> text_helper/021.php
>
> </div>
>
> Produces:
>
>     this_string_is_e&hellip;ak_my_design.jpg

> param string \$text  
> Text to extract an excerpt
>
> param string \$phrase  
> Phrase or word to extract the text around
>
> param int \$radius  
> Number of characters before and after \$phrase
>
> param string \$ellipsis  
> What to use as the ellipsis character
>
> returns  
> Excerpt.
>
> rtype  
> string
>
> This function will extract \$radius number of characters before and
> after the central \$phrase with an ellipsis before and after.
>
> The first parameter is the text to extract an excerpt from, the second
> is the central word or phrase to count before and after. The third
> parameter is the number of characters to count before and after the
> central phrase. If no phrase passed, the excerpt will include the
> first \$radius characters with the ellipsis at the end.
>
> Example:
>
> <div class="literalinclude">
>
> text_helper/022.php
>
> </div>
>
> Produces:
>
>     ... non mauris lectus. Phasellus eu sodales sem. Integer dictum purus ac
>     enim hendrerit gravida. Donec ac magna vel nunc tincidunt molestie sed
>     vitae nisl. Cras sed auctor mauris, non dictum tortor. ...
