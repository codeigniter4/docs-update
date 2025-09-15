# Working with Files

CodeIgniter provides a File class that wraps the \[SplFileInfo](#https://www.php.net/manual/en/class.splfileinfo.php)\_ class and provides some additional convenience methods. This class is the base class for [uploaded files](#/libraries/uploaded-files) and [images](#/libraries/images).

- [Getting a File instance](#getting-a-file-instance)
- [Taking Advantage of Spl](#taking-advantage-of-spl)
- [New Features](#new-features)
    - [getRandomName()](#getrandomname)
    - [getSize()](#getsize)
    - [getSizeByUnit()](#getsizebyunit)
    - [getMimeType()](#getmimetype)
    - [guessExtension()](#guessextension)
    - [Moving Files](#moving-files)

## Getting a File instance

You create a new File instance by passing in the path to the file in the constructor. By default, the file does not need to exist. However, you can pass an additional argument of "true" to check that the file exists and throw `FileNotFoundException()` if it does not.

```php
--8<--
libraries/files/001.php
--8<--
```

## Taking Advantage of Spl

Once you have an instance, you have the full power of the SplFileInfo class at the ready, including:

```php
--8<--
libraries/files/002.php
--8<--
```

## New Features

In addition to all of the methods in the SplFileInfo class, you get some new tools.

### getRandomName()

You can generate a cryptographically secure random filename, with the current timestamp prepended, with the `getRandomName()` method. This is especially useful to rename files when moving it so that the filename is unguessable:

```php
--8<--
libraries/files/003.php
--8<--
```

### getSize()

Returns the size of the uploaded file in bytes:

```php
--8<--
libraries/files/004.php
--8<--
```

### getSizeByUnit()

Returns the size of the uploaded file default in bytes. You can pass in either 'kb' or 'mb' as the first parameter to get the results in kilobytes or megabytes, respectively:

```php
--8<--
libraries/files/005.php
--8<--
```

### getMimeType()

Retrieve the media type (mime type) of the file. Uses methods that are considered as secure as possible when determining the type of file:

```php
--8<--
libraries/files/006.php
--8<--
```

### guessExtension()

Attempts to determine the file extension based on the trusted `getMimeType()` method. If the mime type is unknown, will return null. This is often a more trusted source than simply using the extension provided by the filename. Uses the values in **app/Config/Mimes.php** to determine extension:

```php
--8<--
libraries/files/007.php
--8<--
```

### Moving Files

Each file can be moved to its new location with the aptly named `move()` method. This takes the directory to move the file to as the first parameter:

```php
--8<--
libraries/files/008.php
--8<--
```

By default, the original filename was used. You can specify a new filename by passing it as the second parameter:

```php
--8<--
libraries/files/009.php
--8<--
```

The move() method returns a new File instance that for the relocated file, so you must capture the result if the resulting location is needed:

```php
--8<--
libraries/files/010.php
--8<--
```
