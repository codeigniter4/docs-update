# Filesystem Helper

The Filesystem Helper file contains functions that assist in working with files and directories.

- [Loading this Helper](#loading-this-helper)
- [Available Functions](#available-functions)

## Loading this Helper

This helper is loaded using the following code:

```php
--8<--
helpers/filesystem_helper/001.php
--8<--
```

## Available Functions

The following functions are available:

#### directory_map($sourceDir\[, $directoryDepth = 0\[, $hidden = false]])

- **Parameters**  

- **$sourceDir** `string` Path to the source directory

- **$directoryDepth** `int` Depth of directories to traverse (`0` = fully recursive, `1` = current dir, etc)

- **$hidden** `bool` Whether to include hidden paths

- **Returns**: An array of files

- **Return type**: `array`

Examples:

```php
--8<--
helpers/filesystem_helper/002.php
--8<--
```

!!! note "Note"
    Paths are almost always relative to your main **index.php** file.

Sub-folders contained within the directory will be mapped as well. If you wish to control the recursion depth, you can do so using the second parameter (integer). A depth of `1` will only map the top level directory:

```php
--8<--
helpers/filesystem_helper/003.php
--8<--
```

By default, hidden files will not be included in the returned array and hidden directories will be skipped. To override this behavior, you may set a third parameter to `true` (boolean):

```php
--8<--
helpers/filesystem_helper/004.php
--8<--
```

Each folder name will be an array index, while its contained files will be numerically indexed. Here is an example of a typical array:

Array ( \[libraries] =\> Array ( \[0] =\> benchmark.html \[1] =\> config.html \["database/"] =\> Array ( \[0] =\> query_builder.html \[1] =\> binds.html \[2] =\> configuration.html \[3] =\> connecting.html \[4] =\> examples.html \[5] =\> fields.html \[6] =\> index.html \[7] =\> queries.html ) \[2] =\> email.html \[3] =\> file_uploading.html \[4] =\> image_lib.html \[5] =\> input.html \[6] =\> language.html \[7] =\> loader.html \[8] =\> pagination.html \[9] =\> uri.html ) )

If no results are found, this will return an empty array.

#### directory_mirror($original, $target\[, $overwrite = true])

- **Parameters**  

- **$original** `string` Original source directory

- **$target** `string` Target destination directory

- **$overwrite** `bool` Whether individual files overwrite on collision

Recursively copies the files and directories of the origin directory into the target directory, i.e. "mirror" its contents.

Example:

```php
--8<--
helpers/filesystem_helper/005.php
--8<--
```

You can optionally change the overwrite behavior via the third parameter.

#### write_file($path, $data\[, $mode = 'wb'])

- **Parameters**  

- **$path** `string` File path

- **$data** `string` Data to write to file

- **$mode** `string` `fopen()` mode

- **Returns**: `true` if the write was successful, `false` in case of an error

- **Return type**: `bool`

Writes data to the file specified in the path. If the file does not exist then the function will create it.

Example:

```php
--8<--
helpers/filesystem_helper/006.php
--8<--
```

You can optionally set the write mode via the third parameter:

```php
--8<--
helpers/filesystem_helper/007.php
--8<--
```

The default mode is `'wb'`. Please see [fopen()](https://www.php.net/manual/en/function.fopen.php) in the PHP manual for mode options.

!!! note "Note"
    In order for this function to write data to a file, its permissions must

be set such that it is writable. If the file does not already exist, then the directory containing it must be writable.

!!! note "Note"
    The path is relative to your main site **index.php** file, NOT your

controller or view files. CodeIgniter uses a front controller so paths are always relative to the main site index.

!!! note "Note"
    This function acquires an exclusive lock on the file while writing to it.

#### delete_files($path\[, $delDir = false\[, $htdocs = false\[, $hidden = false]]])

- **Parameters**  

- **$path** `string` Directory path

- **$delDir** `bool` Whether to also delete directories

- **$htdocs** `bool` Whether to skip deleting .htaccess and index page files

- **$hidden** `bool` Whether to also delete hidden files (files beginning with a period)

- **Returns**: `true` on success, `false` in case of an error

- **Return type**: `bool`

Deletes ALL files contained in the supplied path.

Example:

```php
--8<--
helpers/filesystem_helper/008.php
--8<--
```

If the second parameter is set to `true`, any directories contained within the supplied root path will be deleted as well.

Example:

```php
--8<--
helpers/filesystem_helper/009.php
--8<--
```

!!! note "Note"
    The files must be writable or owned by the system in order to be deleted.

#### get_filenames($sourceDir\[, $includePath = false\[, $hidden = false\[, $includeDir = true]]])

- **Parameters**  

- **$sourceDir** `string` Directory path

- **$includePath** `bool|null` Whether to include the path as part of the filename; false for no path, null for the path relative to `$sourceDir`, true for the full path

- **$hidden** `bool` Whether to include hidden files (files beginning with a period)

- **$includeDir** `bool` Whether to include directories in the array output

- **Returns**: An array of file names

- **Return type**: `array`

Takes a server path as input and returns an array containing the names of all files contained within it. The file path can optionally be added to the file names by setting the second parameter to 'relative' for relative paths or any other non-empty value for a full file path.

!!! note "Note"
    Prior to v4.4.4, due to a bug, this function did not follow symlink folders.

Example:

```php
--8<--
helpers/filesystem_helper/010.php
--8<--
```

#### get_dir_file_info($sourceDir\[, $topLevelOnly = true])

- **Parameters**  

- **$sourceDir** `string` Directory path

- **$topLevelOnly** `bool` Whether to look only at the specified directory (excluding sub-directories)

- **Returns**: An array containing info on the supplied directory's contents

- **Return type**: `array`

Reads the specified directory and builds an array containing the filenames, filesize, dates, and permissions. Sub-folders contained within the specified path are only read if forced by sending the second parameter to false, as this can be an intensive operation.

Example:

```php
--8<--
helpers/filesystem_helper/011.php
--8<--
```

#### get_file_info($file\[, $returnedValues = \['name', 'server_path', 'size', 'date']])

- **Parameters**  

- **$file** `string` File path

- **$returnedValues** `array|string` What type of info to return to be passed as array or comma separated string

- **Returns**: An array containing info on the specified file or false on failure

- **Return type**: `array`

Given a file and path, returns (optionally) the *name*, *path*, *size* and *date modified* information attributes for a file. Second parameter allows you to explicitly declare what information you want returned.

Valid `$returnedValues` options are: `name`, `size`, `date`, `readable`, `writeable`, `executable` and `fileperms`.

#### symbolic_permissions($perms)

- **Parameters**  

- **$perms** `int` Permissions

- **Returns**: Symbolic permissions string

- **Return type**: `string`

Takes numeric permissions (such as is returned by [fileperms()](https://www.php.net/manual/en/function.fileperms.php)) and returns standard symbolic notation of file permissions.

```php
--8<--
helpers/filesystem_helper/012.php
--8<--
```

#### octal_permissions($perms)

- **Parameters**  

- **$perms** `int` Permissions

- **Returns**: Octal permissions string

- **Return type**: `string`

Takes numeric permissions (such as is returned by [fileperms()](https://www.php.net/manual/en/function.fileperms.php)) and returns a three character octal notation of file permissions.

```php
--8<--
helpers/filesystem_helper/013.php
--8<--
```

#### same_file($file1, $file2)

- **Parameters**  

- **$file1** `string` Path to the first file

- **$file2** `string` Path to the second file

- **Returns**: Whether both files exist with identical hashes

- **Return type**: `boolean`

Compares two files to see if they are the same (based on their MD5 hash).

```php
--8<--
helpers/filesystem_helper/014.php
--8<--
```

#### set_realpath($path\[, $checkExistence = false])

- **Parameters**  

- **$path** `string` Path

- **$checkExistence** `bool` Whether to check if the path actually exists

- **Returns**: An absolute path

- **Return type**: `string`

This function will return a server path without symbolic links or relative directory structures. An optional second argument will cause an error to be triggered if the path cannot be resolved.

Examples:

```php
--8<--
helpers/filesystem_helper/015.php
--8<--
```
