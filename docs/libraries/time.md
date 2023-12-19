# Times and Dates

CodeIgniter provides a fully-localized, immutable, date/time class that
is built on PHP's DateTimeImmutable object, but uses the Intl
extension's features to convert times across timezones and display the
output correctly for different locales. This class is the `Time` class
and lives in the `CodeIgniter\I18n` namespace.

> [!NOTE]
> Since the Time class extends `DateTimeImmutable`, if there are
> features that you need that this class doesn't provide, you can likely
> find them within the
> [DateTimeImmutable](https://www.php.net/manual/en/class.datetimeimmutable.php)
> class itself.

> [!NOTE]
> Prior to v4.3.0, the Time class extended `DateTime` and some inherited
> methods changed the current object state. The bug was fixed in v4.3.0.
> If you need the old Time class for backward compatibility, you can use
> deprecated `TimeLegacy` class for the time being.

<div class="contents" local="" depth="2">

</div>

## Instantiating

There are several ways that a new Time instance can be created. The
first is simply to create a new instance like any other class.

When you do it this way, you can pass in a string representing the
desired time. This can be any string that PHP's
[strtotime()](https://www.php.net/manual/en/function.strtotime.php)
function can parse:

<div class="literalinclude">

time/001.php

</div>

You can pass in strings representing the timezone and the locale in the
second and parameters, respectively. Timezones can be any supported by
PHP's [DateTimeZone](https://www.php.net/manual/en/timezones.php) class.
The locale can be any supported by PHP's
[Locale](https://www.php.net/manual/en/class.locale.php) class. If no
locale or timezone is provided, the application defaults will be used.

<div class="literalinclude">

time/002.php

</div>

### now()

The Time class has several helper methods to instantiate the class. The
first of these is the `now()` method that returns a new instance set to
the current time. You can pass in strings representing the timezone and
the locale in the second and parameters, respectively. If no locale or
timezone is provided, the application defaults will be used.

<div class="literalinclude">

time/003.php

</div>

### parse()

This helper method is a static version of the default constructor. It
takes a string acceptable as DateTimeImmutable's constructor as the
first parameter, a timezone as the second parameter, and the locale as
the third parameter:

<div class="literalinclude">

time/004.php

</div>

### today()

Returns a new instance with the date set to the current date, and the
time set to midnight. It accepts strings for the timezone and locale in
the first and second parameters:

<div class="literalinclude">

time/005.php

</div>

### yesterday()

Returns a new instance with the date set to the yesterday's date and the
time set to midnight. It accepts strings for the timezone and locale in
the first and second parameters:

<div class="literalinclude">

time/006.php

</div>

### tomorrow()

Returns a new instance with the date set to tomorrow's date and the time
set to midnight. It accepts strings for the timezone and locale in the
first and second parameters:

<div class="literalinclude">

time/007.php

</div>

### createFromDate()

Given separate inputs for **year**, **month**, and **day**, will return
a new instance. If any of these parameters are not provided, it will use
the current value to fill it in. Accepts strings for the timezone and
locale in the fourth and fifth parameters:

<div class="literalinclude">

time/008.php

</div>

### createFromTime()

Like `createFromDate()` except it is only concerned with the **hours**,
**minutes**, and **seconds**. Uses the current day for the date portion
of the Time instance. Accepts strings for the timezone and locale in the
fourth and fifth parameters:

<div class="literalinclude">

time/009.php

</div>

### create()

A combination of the previous two methods, takes **year**, **month**,
**day**, **hour**, **minutes**, and **seconds** as separate parameters.
Any value not provided will use the current date and time to determine.
Accepts strings for the timezone and locale in the fourth and fifth
parameters:

<div class="literalinclude">

time/010.php

</div>

### createFromFormat()

This is a replacement for DateTimeImmutable's method of the same name.
This allows the timezone to be set at the same time, and returns a
`Time` instance, instead of DateTimeImmutable:

<div class="literalinclude">

time/011.php

</div>

### createFromTimestamp()

This method takes a UNIX timestamp and, optionally, the timezone and
locale, to create a new Time instance:

<div class="literalinclude">

time/012.php

</div>

### createFromInstance()

When working with other libraries that provide a DateTime instance, you
can use this method to convert that to a Time instance, optionally
setting the locale. The timezone will be automatically determined from
the DateTime instance passed in:

<div class="literalinclude">

time/013.php

</div>

### toDateTime()

While not an instantiator, this method is the opposite of the
**instance** method, allowing you to convert a Time instance into a
DateTime instance. This preserves the timezone setting, but loses the
locale, since DateTime is not aware of locales:

<div class="literalinclude">

time/014.php

</div>

## Displaying the Value

Since the Time class extends DateTimeImmutable, you get all of the
output methods that provides, including the `format()` method. However,
the DateTimeImmutable methods do not provide a localized result. The
Time class does provide a number of helper methods to display localized
versions of the value, though.

### toLocalizedString()

This is the localized version of DateTimeImmutable's `format()` method.
Instead of using the values you might be familiar with, though, you must
use values acceptable to the
[IntlDateFormatter](https://www.php.net/manual/en/class.intldateformatter.php)
class. A full listing of values can be found
[here](https://unicode-org.github.io/icu-docs/apidoc/released/icu4c/classSimpleDateFormat.html#details).

<div class="literalinclude">

time/015.php

</div>

### toDateTimeString()

This is the first of three helper methods to work with the
[IntlDateFormatter](https://www.php.net/manual/en/class.intldateformatter.php)
without having to remember their values. This will return a localized
version of string formatted as (`Y-m-d H:i:s`):

<div class="literalinclude">

time/016.php

</div>

### toDateString()

Displays just the localized version of date portion of the Time:

<div class="literalinclude">

time/017.php

</div>

### toTimeString()

Displays just the localized version of time portion of the value:

<div class="literalinclude">

time/018.php

</div>

### humanize()

This methods returns a string that displays the difference between the
current date/time and the instance in a human readable format that is
geared towards being easily understood. It can create strings like '3
hours ago', 'in 1 month', etc:

<div class="literalinclude">

time/019.php

</div>

The exact time displayed is determined in the following manner:

<table>
<thead>
<tr class="header">
<th>Time difference</th>
<th>Result</th>
</tr>
</thead>
<tbody>
<tr class="odd">
<td>$time &gt; 1 year &amp;&amp; &lt; 2 years</td>
<td><blockquote>
<p>in 1 year / 1 year ago</p>
</blockquote></td>
</tr>
<tr class="even">
<td>$time &gt; 1 month &amp;&amp; &lt; 1 year</td>
<td><blockquote>
<p>in 6 months / 6 months ago</p>
</blockquote></td>
</tr>
<tr class="odd">
<td>$time &gt; 7 days &amp;&amp; &lt; 1 month</td>
<td><blockquote>
<p>in 3 weeks / 3 weeks ago</p>
</blockquote></td>
</tr>
<tr class="even">
<td>$time &gt; today &amp;&amp; &lt; 7 days</td>
<td><blockquote>
<p>in 4 days / 4 days ago</p>
</blockquote></td>
</tr>
<tr class="odd">
<td>$time == tomorrow / yesterday</td>
<td><blockquote>
<p>Tomorrow / Yesterday</p>
</blockquote></td>
</tr>
<tr class="even">
<td>$time &gt; 59 minutes &amp;&amp; &lt; 1 day</td>
<td><blockquote>
<p>in 2 hours / 2 hours ago</p>
</blockquote></td>
</tr>
<tr class="odd">
<td>$time &gt; now &amp;&amp; &lt; 1 hour</td>
<td><blockquote>
<p>in 35 minutes / 35 minutes ago</p>
</blockquote></td>
</tr>
<tr class="even">
<td>$time == now</td>
<td><blockquote>
<p>Now</p>
</blockquote></td>
</tr>
</tbody>
</table>

The exact language used is controlled through the language file,
**Time.php**.

## Working with Individual Values

The Time object provides a number of methods to allow to get and set
individual items, like the year, month, hour, etc, of an existing
instance. All of the values retrieved through the following methods will
be fully localized and respect the locale that the Time instance was
created with.

All of the following `getX()` and `setX()` methods can also be used as
if they were a class property. So, any calls to methods like `getYear()`
can also be accessed through `$time->year`, and so on.

### Getters

The following basic getters exist:

<div class="literalinclude">

time/020.php

</div>

In addition to these, a number of methods exist to provide additional
information about the date:

<div class="literalinclude">

time/021.php

</div>

#### getAge()

Returns the age, in years, of between the Time's instance and the
current time. Perfect for checking the age of someone based on their
birthday:

<div class="literalinclude">

time/022.php

</div>

#### getDST()

Returns boolean true/false based on whether the Time instance is
currently observing Daylight Savings Time:

<div class="literalinclude">

time/023.php

</div>

#### getLocal()

Returns boolean true if the Time instance is in the same timezone as the
application is currently running in:

<div class="literalinclude">

time/024.php

</div>

#### getUtc()

Returns boolean true if the Time instance is in UTC time:

<div class="literalinclude">

time/025.php

</div>

#### getTimezone()

Returns a new
[DateTimeZone](https://www.php.net/manual/en/class.datetimezone.php)
object set the timezone of the Time instance:

<div class="literalinclude">

time/026.php

</div>

#### getTimezoneName()

Returns the full [timezone
string](https://www.php.net/manual/en/timezones.php) of the Time
instance:

<div class="literalinclude">

time/027.php

</div>

### Setters

The following basic setters exist. If any of the values set are out of
range, an `InvalidArgumentExeption` will be thrown.

> [!NOTE]
> All setters will return a new Time instance, leaving the original
> instance untouched.

> [!NOTE]
> All setters will throw an InvalidArgumentException if the value is out
> of range.

<div class="literalinclude">

time/028.php

</div>

#### setTimezone()

Converts the time from it's current timezone into the new one:

<div class="literalinclude">

time/029.php

</div>

#### setTimestamp()

Returns a new instance with the date set to the new timestamp:

<div class="literalinclude">

time/030.php

</div>

### Modifying the Value

The following methods allow you to modify the date by adding or
subtracting values to the current Time. This will not modify the
existing Time instance, but will return a new instance.

<div class="literalinclude">

time/031.php

</div>

### Comparing Two Times

The following methods allow you to compare one Time instance with
another. All comparisons are first converted to UTC before comparisons
are done, to ensure that different timezones respond correctly.

#### equals()

Determines if the datetime passed in is equal to the current instance.
Equal in this case means that they represent the same moment in time,
and are not required to be in the same timezone, as both times are
converted to UTC and compared that way:

<div class="literalinclude">

time/032.php

</div>

The value being tested against can be a Time instance, a DateTime
instance, or a string with the full date time in a manner that a new
DateTime instance can understand. When passing a string as the first
parameter, you can pass a timezone string in as the second parameter. If
no timezone is given, the system default will be used:

<div class="literalinclude">

time/033.php

</div>

#### sameAs()

This is identical to the `equals()` method, except that it only returns
true when the date, time, AND timezone are all identical:

<div class="literalinclude">

time/034.php

</div>

#### isBefore()

Checks if the passed in time is before the current instance. The
comparison is done against the UTC versions of both times:

<div class="literalinclude">

time/035.php

</div>

The value being tested against can be a Time instance, a DateTime
instance, or a string with the full date time in a manner that a new
DateTime instance can understand. When passing a string as the first
parameter, you can pass a timezone string in as the second parameter. If
no timezone is given, the system default will be used:

<div class="literalinclude">

time/036.php

</div>

#### isAfter()

Works exactly the same as `isBefore()` except checks if the time is
after the time passed in:

<div class="literalinclude">

time/037.php

</div>

### Viewing Differences

To compare two Times directly, you would use the `difference()` method,
which returns a `CodeIgniter\I18n\TimeDifference` instance. The first
parameter is either a Time instance, a DateTime instance, or a string
with the date/time. If a string is passed in the first parameter, the
second parameter can be a timezone string:

<div class="literalinclude">

time/038.php

</div>

Once you have the TimeDifference instance, you have several methods you
can use to find information about the difference between the two times.
The value returned will be negative if it was in the past, or positive
if in the future from the original time:

<div class="literalinclude">

time/039.php

</div>

You can use either `getX()` methods, or access the calculate values as
if they were properties:

<div class="literalinclude">

time/040.php

</div>

#### humanize()

Much like Time's `humanize()` method, this returns a string that
displays the difference between the times in a human readable format
that is geared towards being easily understood. It can create strings
like '3 hours ago', 'in 1 month', etc. The biggest differences are in
how very recent dates are handled:

<div class="literalinclude">

time/041.php

</div>

The exact time displayed is determined in the following manner:

<table>
<thead>
<tr class="header">
<th>Time difference</th>
<th>Result</th>
</tr>
</thead>
<tbody>
<tr class="odd">
<td>$time &gt; 1 year &amp;&amp; &lt; 2 years</td>
<td><blockquote>
<p>in 1 year / 1 year ago</p>
</blockquote></td>
</tr>
<tr class="even">
<td>$time &gt; 1 month &amp;&amp; &lt; 1 year</td>
<td><blockquote>
<p>in 6 months / 6 months ago</p>
</blockquote></td>
</tr>
<tr class="odd">
<td>$time &gt; 7 days &amp;&amp; &lt; 1 month</td>
<td><blockquote>
<p>in 3 weeks / 3 weeks ago</p>
</blockquote></td>
</tr>
<tr class="even">
<td>$time &gt; today &amp;&amp; &lt; 7 days</td>
<td><blockquote>
<p>in 4 days / 4 days ago</p>
</blockquote></td>
</tr>
<tr class="odd">
<td>$time &gt; 1 hour &amp;&amp; &lt; 1 day</td>
<td><blockquote>
<p>in 8 hours / 8 hours ago</p>
</blockquote></td>
</tr>
<tr class="even">
<td>$time &gt; 1 minute &amp;&amp; &lt; 1 hour</td>
<td><blockquote>
<p>in 35 minutes / 35 minutes ago</p>
</blockquote></td>
</tr>
<tr class="odd">
<td>$time &lt; 1 minute</td>
<td><blockquote>
<p>Now</p>
</blockquote></td>
</tr>
</tbody>
</table>

The exact language used is controlled through the language file,
**Time.php**.
