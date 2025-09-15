# Logging Information

- [Log Levels](#log-levels)
- [Configuration](#configuration)
    - [Using Multiple Log Handlers](#using-multiple-log-handlers)
- [Modifying the Message with Context](#modifying-the-message-with-context)
- [Using Third-Party Loggers](#using-third-party-loggers)

## Log Levels

You can log information to the local log files by using the `log_message()` method. You must supply the "level" of the error in the first parameter, indicating what type of message it is (debug, error, etc). The second parameter is the message itself:

```php
--8<--
general/logging/001.php
--8<--
```

There are eight different log levels, matching to the [RFC 5424](https://tools.ietf.org/html/rfc5424) levels, and they are as follows:

<table>
<thead>
<tr>
<th>Level</th>
<th>Description</th>
</tr>
</thead>
<tbody>
<tr>
<td>debug</td>
<td>Detailed debug information.</td>
</tr>
<tr>
<td>info</td>
<td>Interesting events in your application, like a user logging in, logging SQL queries, etc.</td>
</tr>
<tr>
<td>notice</td>
<td>Normal, but significant events in your application.</td>
</tr>
<tr>
<td>warning</td>
<td>Exceptional occurrences that are not errors, like the use of deprecated APIs, poor use of an API, or other undesirable things that are not necessarily wrong.</td>
</tr>
<tr>
<td>error</td>
<td>Runtime errors that do not require immediate action but should typically be logged and monitored.</td>
</tr>
<tr>
<td>critical</td>
<td>Critical conditions, like an application component not available, or an unexpected exception.</td>
</tr>
<tr>
<td>alert</td>
<td>Action must be taken immediately, like when an entire website is down, the database unavailable, etc.</td>
</tr>
<tr>
<td>emergency</td>
<td>The system is unusable.</td>
</tr>
</tbody>
</table>

The logging system does not provide ways to alert sysadmins or webmasters about these events, they solely log the information. For many of the more critical event levels, the logging happens automatically by the Error Handler, described above.

## Configuration

You can modify which levels are actually logged, as well as assign different Loggers to handle different levels, within the **app/Config/Logger.php** configuration file.

The `threshold` value of the config file determines which levels are logged across your application. If any levels are requested to be logged by the application, but the threshold doesn't allow them to log currently, they will be ignored. The simplest method to use is to set this value to the minimum level that you want to have logged. For example, if you want to log warning messages, and not information messages, you would set the threshold to `5`. Any log requests with a level of 5 or less (which includes runtime errors, system errors, etc) would be logged and info, notices, and debug would be ignored:

```php
--8<--
general/logging/002.php
--8<--
```

A complete list of levels and their corresponding threshold value is in the configuration file for your reference.

You can pick and choose the specific levels that you would like logged by assigning an array of log level numbers to the threshold value:

```php
--8<--
general/logging/003.php
--8<--
```

### Using Multiple Log Handlers

The logging system can support multiple methods of handling logging running at the same time. Each handler can be set to handle specific levels and ignore the rest. Currently, three handlers come with a default install:

- **File Handler** is the default handler and will create a single file for every day locally. This is the recommended method of logging.

- **ChromeLogger Handler** If you have the [ChromeLogger extension](https://craig.is/writing/chrome-logger) installed in the Chrome web browser, you can use this handler to display the log information in Chrome's console window.

- **Errorlog Handler** This handler will take advantage of PHP's native `error_log()` function and write the logs there. Currently, only the `0` and `4` message types of `error_log()` are supported.

The handlers are configured in the main configuration file, in the `$handlers` property, which is simply an array of handlers and their configuration. Each handler is specified with the key being the fully name-spaced class name. The value will be an array of varying properties, specific to each handler. Each handler's section will have one property in common: `handles`, which is an array of log level *names* that the handler will log information for.

```php
--8<--
general/logging/004.php
--8<--
```

## Modifying the Message with Context

You will often want to modify the details of your message based on the context of the event being logged. You might need to log a user id, an IP address, the current POST variables, etc. You can do this by use placeholders in your message. Each placeholder must be wrapped in curly braces. In the third parameter, you must provide an array of placeholder names (without the braces) and their values. These will be inserted into the message string:

```php
--8<--
general/logging/005.php
--8<--
```

If you want to log an Exception or an Error, you can use the key of 'exception', and the value being the Exception or Error itself. A string will be generated from that object containing the error message, the file name and line number. You must still provide the exception placeholder in the message:

```php
--8<--
general/logging/006.php
--8<--
```

Several core placeholders exist that will be automatically expanded for you based on the current page request:

<table style="width:96%;">
<colgroup>
<col style="width: 23%" />
<col style="width: 72%" />
</colgroup>
<thead>
<tr>
<th>Placeholder</th>
<th>Inserted value</th>
</tr>
</thead>
<tbody>
<tr>
<td>{post_vars}</td>
<td>$_POST variables</td>
</tr>
<tr>
<td>{get_vars}</td>
<td>$_GET variables</td>
</tr>
<tr>
<td>{session_vars}</td>
<td>$_SESSION variables</td>
</tr>
<tr>
<td>{env}</td>
<td>Current environment name, i.e., development</td>
</tr>
<tr>
<td>{file}</td>
<td>The name of file calling the logger</td>
</tr>
<tr>
<td>{line}</td>
<td>The line in {file} where the logger was called</td>
</tr>
<tr>
<td>{env:foo}</td>
<td>The value of 'foo' in $_ENV</td>
</tr>
</tbody>
</table>

## Using Third-Party Loggers

You can use any other logger that you might like as long as it extends from either `Psr\Log\LoggerInterface` and is [PSR-3](https://www.php-fig.org/psr/psr-3/) compatible. This means that you can easily drop in use for any PSR-3 compatible logger, or create your own.

You must ensure that the third-party logger can be found by the system, by adding it to either the **app/Config/Autoload.php** configuration file, or through another autoloader, like Composer. Next, you should modify **app/Config/Services.php** to point the `logger` alias to your new class name.

Now, any call that is done through the `log_message()` function will use your library instead.
