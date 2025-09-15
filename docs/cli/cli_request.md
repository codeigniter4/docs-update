# CLIRequest Class

If a request comes from a command line invocation, the request object is actually a `CLIRequest`. It behaves the same as a [conventional request](#/incoming/request) but adds some accessor methods for convenience.

## Additional Accessors

### getSegments()

Returns an array of the command line arguments deemed to be part of a path:

```php
--8<--
cli/cli_request/001.php
--8<--
```

### getPath()

Returns the reconstructed path as a string:

```php
--8<--
cli/cli_request/002.php
--8<--
```

### getOptions()

Returns an array of the command line arguments deemed to be options:

```php
--8<--
cli/cli_request/003.php
--8<--
```

### getOption($which)

Returns the value of a specific command line argument deemed to be an option:

```php
--8<--
cli/cli_request/004.php
--8<--
```

### getOptionString()

Returns the reconstructed command line string for the options:

```php
--8<--
cli/cli_request/005.php
--8<--
```

Passing `true` to the first argument will try to write long options using two dashes:

```php
--8<--
cli/cli_request/006.php
--8<--
```
