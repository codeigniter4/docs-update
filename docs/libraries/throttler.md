# Throttler

- [Overview](#overview)
- [Rate Limiting](#rate-limiting)
    - [The Code](#the-code)
    - [Applying the Filter](#applying-the-filter)
- [Class Reference](#class-reference)

The Throttler class provides a very simple way to limit an activity to be performed to a certain number of attempts within a set period of time. This is most often used for performing rate limiting on APIs, or restricting the number of attempts a user can make against a form to help prevent brute force attacks. The class itself can be used for anything that you need to throttle based on actions within a set time interval.

## Overview

The Throttler implements a simplified version of the \[Token Bucket](#https://en.wikipedia.org/wiki/Token-bucket)\_ algorithm. This basically treats each action that you want as a bucket. When you call the `check()` method, you tell it how large the bucket is, and how many tokens it can hold and the time interval. Each `check()` call uses 1 of the available tokens, by default. Let's walk through an example to make this clear.

Let's say we want an action to happen once every second. The first call to the Throttler would look like the following. The first parameter is the bucket name, the second parameter the number of tokens the bucket holds, and the third being the amount of time it takes the bucket to refill:

```php
--8<--
libraries/throttler/001.php
--8<--
```

Here we're using one of the [global constants](#/general/common-functions) for the time, to make it a little more readable. This says that the bucket allows 60 actions every minute, or 1 action every second.

Let's say that a third-party script was trying to hit a URL repeatedly. At first, it would be able to use all 60 of those tokens in less than a second. However, after that the Throttler would only allow one action per second, potentially slowing down the requests enough that the attack is no longer worth it.

!!! note "Note"
    For the Throttler class to work, the Cache library must be set up to use a handler other than dummy. For best performance, an in-memory cache, like Redis or Memcached, is recommended.

## Rate Limiting

The Throttler class does not do any rate limiting or request throttling on its own, but is the key to making one work. An example [Filter](#/incoming/filters) is provided that implements a very simple rate limiting at one request per second per IP address. Here we will run through how it works, and how you could set it up and start using it in your application.

### The Code

You could make your own Throttler filter, at **app/Filters/Throttle.php**, along the lines of:

```php
--8<--
libraries/throttler/002.php
--8<--
```

When run, this method first grabs an instance of the throttler. Next, it uses the IP address as the bucket name, and sets things to limit them to one request per second. If the throttler rejects the check, returning false, then we return a Response with the status code set to 429 - Too Many Attempts, and the script execution ends before it ever hits the controller. This example will throttle based on a single IP address across all requests made to the site, not per page.

### Applying the Filter

We don't necessarily need to throttle every page on the site. For many web applications, this makes the most sense to apply only to POST requests, though API's might want to limit every request made by a user. In order to apply this to incoming requests, you need to edit **app/Config/Filters.php** and first add an alias to the filter:

```php
--8<--
libraries/throttler/003.php
--8<--
```

Next, we assign it to all POST requests made on the site:

```php
--8<--
libraries/throttler/004.php
--8<--
```

!!! warning "Warning"
    If you use `$methods` filters, you should [disable Auto Routing (Legacy)](#use-defined-routes-only) because `auto-routing-legacy` permits any HTTP method to access a controller. Accessing the controller with a method you don't expect could bypass the filter.

And that's all there is to it. Now all POST requests made on the site will have to be rate limited.

## Class Reference

#### check(string $key, int $capacity, int $seconds\[, int $cost = 1])

- **Parameters**  

- **$key** `string` The name of the bucket

- **$capacity** `int` The number of tokens the bucket holds

- **$seconds** `int` The number of seconds it takes for a bucket to completely fill

- **$cost** `int` The number of tokens that are spent on this action

- **Returns**: true if action can be performed, false if not

- **Return type**: `bool`

Checks to see if there are any tokens left within the bucket, or if too many have been used within the allotted time limit. During each check the available tokens are reduced by $cost if successful.

#### getTokentime()

- **Returns**: The number of seconds until another token should be available.

- **Return type**: `integer`

After `check()` has been run and returned false, this method can be used to determine the time until a new token should be available and the action can be tried again. In this case, the minimum enforced wait time is one second.

#### remove(string $key) : self

- **Parameters**  

- **$key** `string` The name of the bucket

- **Returns**: $this

- **Return type**: `self`

Removes & resets the bucket. Won't fail if the bucket doesn't exist.
