# Working with Files

CodeIgniter provides a File class that wraps the
[SplFileInfo](https://www.php.net/manual/en/class.splfileinfo.php) class
and provides some additional convenience methods. This class is the base
class for `uploaded files </libraries/uploaded_files>` and
`images </libraries/images>`.

<div class="contents" local="" depth="2">

</div>

## Getting a File instance

You create a new File instance by passing in the path to the file in the
constructor. By default, the file does not need to exist. However, you
can pass an additional argument of "true" to check that the file exists
and throw `FileNotFoundException()` if it does not.

<div class="literalinclude">

files/001.php

</div>

## Taking Advantage of Spl

Once you have an instance, you have the full power of the SplFileInfo
class at the ready, including:

<div class="literalinclude">

files/002.php

</div>

## New Features

In addition to all of the methods in the SplFileInfo class, you get some
new tools.

### getRandomName()

You can generate a cryptographically secure random filename, with the
current timestamp prepended, with the `getRandomName()` method. This is
especially useful to rename files when moving it so that the filename is
unguessable:

<div class="literalinclude">

files/003.php

</div>

### getSize()

Returns the size of the uploaded file in bytes:

<div class="literalinclude">

files/004.php

</div>

### getSizeByUnit()

Returns the size of the uploaded file default in bytes. You can pass in
either 'kb' or 'mb' as the first parameter to get the results in
kilobytes or megabytes, respectively:

<div class="literalinclude">

files/005.php

</div>

### getMimeType()

Retrieve the media type (mime type) of the file. Uses methods that are
considered as secure as possible when determining the type of file:

<div class="literalinclude">

files/006.php

</div>

### guessExtension()

Attempts to determine the file extension based on the trusted
`getMimeType()` method. If the mime type is unknown, will return null.
This is often a more trusted source than simply using the extension
provided by the filename. Uses the values in **app/Config/Mimes.php** to
determine extension:

<div class="literalinclude">

files/007.php

</div>

### Moving Files

Each file can be moved to its new location with the aptly named `move()`
method. This takes the directory to move the file to as the first
parameter:

<div class="literalinclude">

files/008.php

</div>

By default, the original filename was used. You can specify a new
filename by passing it as the second parameter:

<div class="literalinclude">

files/009.php

</div>

The move() method returns a new File instance that for the relocated
file, so you must capture the result if the resulting location is
needed:

<div class="literalinclude">

files/010.php

</div>
