# Upgrade HTTP Responses

<div class="contents" local="" depth="2">

</div>

## Documentations

- [Output Class Documentation CodeIgniter
  3.X](http://codeigniter.com/userguide3/libraries/output.html)
- `HTTP Responses Documentation CodeIgniter 4.X </outgoing/response>`

## What has been changed

- The methods have been renamed

## Upgrade Guide

1.  The methods in the HTTP Responses class are named slightly
    different. The most important change in the naming is the switch
    from underscored method names to camelCase. The method
    `set_content_type()` from version 3 is now named `setContentType()`
    and so on.
2.  In the most cases you have to change `$this->output` to
    `$this->response` followed by the method. You can find all methods
    in `../outgoing/response`.

## Code Example

### CodeIgniter Version 3.x

<div class="literalinclude">

upgrade_responses/ci3sample/001.php

</div>

### CodeIgniter Version 4.x

<div class="literalinclude">

upgrade_responses/001.php

</div>
