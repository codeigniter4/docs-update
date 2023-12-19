# Date Helper

The Date Helper file contains functions that assist in working with
dates.

<div class="contents" local="" depth="2">

</div>

> [!NOTE]
> Many functions previously found in the CodeIgniter 3 `date_helper`
> have been moved to the `Time <../libraries/time>` class in CodeIgniter
> 4.

## Loading this Helper

This helper is loaded using the following code:

<div class="literalinclude">

date_helper/001.php

</div>

## Available Functions

The following functions are available:

> param string \$timezone  
> Timezone
>
> returns  
> UNIX timestamp
>
> rtype  
> int
>
> > [!NOTE]
> > It is recommended to use the `Time <../libraries/time>` class
> > instead. Use `Time::now()->getTimestamp()` to get the current UNIX
> > timestamp.
>
> If a timezone is not provided, it will return the current UNIX
> timestamp by `time()`.
>
> <div class="literalinclude">
>
> date_helper/002.php
>
> </div>
>
> If any PHP supported timezone is provided, it will return a timestamp
> that is offset by the time difference. It is not the same as the
> current UNIX timestamp.
>
> If you do not intend to set your master time reference to any other
> PHP supported timezone (which you'll typically do if you run a site
> that lets each user set their own timezone settings) there is no
> benefit to using this function over PHP's `time()` function.

> param string \$class  
> Optional class to apply to the select field
>
> param string \$default  
> Default value for initial selection
>
> param int \$what  
> DateTimeZone class constants (see
> [listIdentifiers](https://www.php.net/manual/en/datetimezone.listidentifiers.php))
>
> param string \$country  
> A two-letter ISO 3166-1 compatible country code (see
> [listIdentifiers](https://www.php.net/manual/en/datetimezone.listidentifiers.php))
>
> returns  
> Preformatted HTML select field
>
> rtype  
> string
>
> Generates a <span class="title-ref">select</span> form field of
> available timezones (optionally filtered by `$what` and `$country`).
> You can supply an option class to apply to the field to make
> formatting easier, as well as a default selected value.
>
> <div class="literalinclude">
>
> date_helper/003.php
>
> </div>
