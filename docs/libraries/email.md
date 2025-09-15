# Email Class

CodeIgniter's robust Email Class supports the following features:

- Multiple Protocols: Mail, Sendmail, and SMTP

- TLS and SSL Encryption for SMTP

- Multiple recipients

- CC and BCCs

- HTML or Plaintext email

- Attachments

- Word wrapping

- Priorities

- BCC Batch Mode, enabling large email lists to be broken into small BCC batches.

- Email Debugging tools

- [Using the Email Library](#using-the-email-library)
    - [Sending Email](#sending-email)
    - [Setting Email Preferences](#setting-email-preferences)
        - [Setting Email Preferences by Passing an Array](#setting-email-preferences-by-passing-an-array)
        - [Setting Email Preferences in a Config File](#setting-email-preferences-in-a-config-file)
        - [SSL versus TLS for SMTP Protocol](#ssl-versus-tls-for-smtp-protocol)
        - [Reviewing Preferences](#reviewing-preferences)
    - [Email Preferences](#email-preferences)
    - [Overriding Word Wrapping](#overriding-word-wrapping)
- [Class Reference](#class-reference)
- [CodeIgniter\Email](#codeigniteremail)
    - [Email](#email)
        - [setFrom($from\[, $name = ''\[, $returnPath = null]])](#setfromfrom-name-returnpath-null)
        - [setReplyTo($replyto\[, $name = ''])](#setreplytoreplyto-name)
        - [setTo($to)](#settoto)
        - [setCC($cc)](#setcccc)
        - [setBCC($bcc\[, $limit = ''])](#setbccbcc-limit)
        - [setSubject($subject)](#setsubjectsubject)
        - [setMessage($body)](#setmessagebody)
        - [setAltMessage($str)](#setaltmessagestr)
        - [setHeader($header, $value)](#setheaderheader-value)
        - [clear($clearAttachments = false)](#clearclearattachments-false)
        - [send($autoClear = true)](#sendautoclear-true)
        - [attach($filename\[, $disposition = ''\[, $newname = null\[, $mime = '']]])](#attachfilename-disposition-newname-null-mime)
        - [setAttachmentCID($filename)](#setattachmentcidfilename)
        - [printDebugger($include = \['headers', 'subject', 'body'])](#printdebuggerinclude-headers-subject-body)

## Using the Email Library

### Sending Email

Sending email is not only simple, but you can configure it on the fly or set your preferences in the **app/Config/Email.php** file.

Here is a basic example demonstrating how you might send email:

```php
--8<--
libraries/email/001.php
--8<--
```

### Setting Email Preferences

There are 21 different preferences available to tailor how your email messages are sent. You can either set them manually as described here, or automatically via preferences stored in your config file, described in [Email Preferences](#email-preferences).

#### Setting Email Preferences by Passing an Array

Preferences are set by passing an array of preference values to the email initialize method. Here is an example of how you might set some preferences:

```php
--8<--
libraries/email/002.php
--8<--
```

!!! note "Note"
    Most of the preferences have default values that will be used if you do not set them.

#### Setting Email Preferences in a Config File

If you prefer not to set preferences using the above method, you can instead put them into the config file. Simply open the **app/Config/Email.php** file, and set your configs in the Email properties. Then save the file and it will be used automatically. You will NOT need to use the `$email->initialize()` method if you set your preferences in the config file.

#### SSL versus TLS for SMTP Protocol

To protect the username, password and email content while communicating with the SMTP server, encryption on the channel should be used. Two different standards are widely deployed and it is important to understand the differences when trying to troubleshoot email sending issues.

Most SMTP servers allow connections on ports 465 or 587 when submitting emails. (The original port 25 is seldom used because of many ISPs have blocking rules in place and since the communication is entirely in clear-text).

The key difference is that port 465 expects the communication channel to be secured using TLS from the start as per <a href="https://tools.ietf.org/html/rfc8314" target="_blank">RFC 8314</a>. A connection to port 587 allows clear-text connection and later will upgrade the channel to use encryption using the `STARTTLS` SMTP command.

Upgrading a connection on port 465 may or may not be supported by the server, so the `STARTTLS` SMTP command may fail if the server does not allow it. If you set the port to 465, you should try to set the `SMTPCrypto` to an empty string (`''`) since the communication is secured using TLS from the start and the `STARTTLS` is not needed.

If your configuration requires you to connect to port 587, you should most likely set `SMTPCrypto` to `tls` as this will implement the `STARTTLS` command while communicating with the SMTP server to switch from clear-text to an encrypted channel. The initial communication will be made in clear-text and the channel will be upgraded to TLS with the `STARTTLS` command.

#### Reviewing Preferences

The settings used for the last successful send are available from the instance property `$archive`. This is helpful for testing and debugging to determine that actual values at the time of the `send()` call.

### Email Preferences

The following is a list of all the preferences that can be set when sending email.

<table>
<thead>
<tr>
<th>Preference</th>
<th>Default Value</th>
<th>Options</th>
<th>Description</th>
</tr>
</thead>
<tbody>
<tr>
<td><strong>userAgent</strong></td>
<td>CodeIgniter</td>
<td>None</td>
<td>The "user agent".</td>
</tr>
<tr>
<td><strong>protocol</strong></td>
<td>mail</td>
<td><code>mail</code>, <code>sendmail</code>, or <code>smtp</code></td>
<td>The mail sending protocol.</td>
</tr>
<tr>
<td><strong>mailPath</strong></td>
<td>/usr/sbin/sendmail</td>
<td>None</td>
<td>The server path to Sendmail.</td>
</tr>
<tr>
<td><strong>SMTPHost</strong></td>
<td>No Default</td>
<td>None</td>
<td>SMTP Server Hostname.</td>
</tr>
<tr>
<td><strong>SMTPUser</strong></td>
<td>No Default</td>
<td>None</td>
<td>SMTP Username.</td>
</tr>
<tr>
<td><strong>SMTPPass</strong></td>
<td>No Default</td>
<td>None</td>
<td>SMTP Password.</td>
</tr>
<tr>
<td><strong>SMTPPort</strong></td>
<td>25</td>
<td>None</td>
<td>SMTP Port. (If set to <code>465</code>, TLS will be used for the connection regardless of <code>SMTPCrypto</code> setting.)</td>
</tr>
<tr>
<td><strong>SMTPTimeout</strong></td>
<td>5</td>
<td>None</td>
<td>SMTP Timeout (in seconds).</td>
</tr>
<tr>
<td><strong>SMTPKeepAlive</strong></td>
<td>false</td>
<td><code>true</code>/<code>false</code> (boolean)</td>
<td>Enable persistent SMTP connections.</td>
</tr>
<tr>
<td><strong>SMTPCrypto</strong></td>
<td>tls</td>
<td><code>tls</code>, <code>ssl</code>, or empty string (<code>''</code>)</td>
<td>SMTP Encryption. Setting this to <code>ssl</code> will create a secure channel to the server using SSL, and <code>tls</code> will issue a <code>STARTTLS</code> command to the server. Connection on port <code>465</code> should set this to an empty string (<code>''</code>). See also <code class="interpreted-text" role="ref">email-ssl-tls-for-smtp</code>.</td>
</tr>
<tr>
<td><strong>wordWrap</strong></td>
<td>true</td>
<td><code>true</code>/<code>false</code> (boolean)</td>
<td>Enable word-wrap.</td>
</tr>
<tr>
<td><strong>wrapChars</strong></td>
<td>76</td>
<td></td>
<td>Character count to wrap at.</td>
</tr>
<tr>
<td><strong>mailType</strong></td>
<td>text</td>
<td><code>text</code> or <code>html</code></td>
<td>Type of mail. If you send HTML email you must send it as a complete web page. Make sure you don't have any relative links or relative image paths otherwise they will not work.</td>
</tr>
<tr>
<td><strong>charset</strong></td>
<td>utf-8</td>
<td></td>
<td>Character set (<code>utf-8</code>, <code>iso-8859-1</code>, etc.).</td>
</tr>
<tr>
<td><strong>validate</strong></td>
<td>true</td>
<td><code>true</code>/<code>false</code> (boolean)</td>
<td>Whether to validate the email address.</td>
</tr>
<tr>
<td><strong>priority</strong></td>
<td>3</td>
<td>1, 2, 3, 4, 5</td>
<td>Email Priority. <code>1</code> = highest. <code>5</code> = lowest. <code>3</code> = normal.</td>
</tr>
<tr>
<td><strong>CRLF</strong></td>
<td>\n</td>
<td><code>\r\n</code> or <code>\n</code> or <code>\r</code></td>
<td>Newline character. (Use <code>\r\n</code> to comply with RFC 822).</td>
</tr>
<tr>
<td><strong>newline</strong></td>
<td>\n</td>
<td><code>\r\n</code> or <code>\n</code> or <code>\r</code></td>
<td>Newline character. (Use <code>\r\n</code> to comply with RFC 822).</td>
</tr>
<tr>
<td><strong>BCCBatchMode</strong></td>
<td>false</td>
<td><code>true</code>/<code>false</code> (boolean)</td>
<td>Enable BCC Batch Mode.</td>
</tr>
<tr>
<td><strong>BCCBatchSize</strong></td>
<td>200</td>
<td>None</td>
<td>Number of emails in each BCC batch.</td>
</tr>
<tr>
<td><strong>DSN</strong></td>
<td>false</td>
<td><code>true</code>/<code>false</code> (boolean)</td>
<td>Enable notify message from server.</td>
</tr>
</tbody>
</table>

### Overriding Word Wrapping

If you have word wrapping enabled (recommended to comply with RFC 822) and you have a very long link in your email it can get wrapped too, causing it to become un-clickable by the person receiving it. CodeIgniter lets you manually override word wrapping within part of your message like this:

    The text of your email that
    gets wrapped normally.

    {unwrap}http://example.com/a_long_link_that_should_not_be_wrapped.html{/unwrap}

    More text that will be
    wrapped normally.

Place the item you do not want word-wrapped between: {unwrap} {/unwrap}

## Class Reference

## CodeIgniter\Email

### Email

#### setFrom($from\[, $name = ''\[, $returnPath = null]])

- **Parameters**  

- **$from** `string` "From" e-mail address

- **$name** `string` "From" display name

- **$returnPath** `string` Optional email address to redirect undelivered e-mail to

- **Returns**: CodeIgniter\Email\Email instance (method chaining)

- **Return type**: `CodeIgniter\Email\Email`

Sets the email address and name of the person sending the email:

```php
--8<--
libraries/email/003.php
--8<--
```

You can also set a Return-Path, to help redirect undelivered mail:

```php
--8<--
libraries/email/004.php
--8<--
```

!!! note "Note"
    Return-Path can't be used if you've configured 'smtp' as

your protocol.

#### setReplyTo($replyto\[, $name = ''])

- **Parameters**  

- **$replyto** `string` E-mail address for replies

- **$name** `string` Display name for the reply-to e-mail address

- **Returns**: CodeIgniter\Email\Email instance (method chaining)

- **Return type**: `CodeIgniter\Email\Email`

Sets the reply-to address. If the information is not provided the information in the \[setFrom](##setFrom) method is used. Example:

```php
--8<--
libraries/email/005.php
--8<--
```

#### setTo($to)

- **Parameters**  

- **$to** `mixed` Comma-delimited string or an array of e-mail addresses

- **Returns**: CodeIgniter\Email\Email instance (method chaining)

- **Return type**: `CodeIgniter\Email\Email`

Sets the email address(s) of the recipient(s). Can be a single e-mail, a comma-delimited list or an array:

```php
--8<--
libraries/email/006.php
--8<--
```

```php
--8<--
libraries/email/007.php
--8<--
```

```php
--8<--
libraries/email/008.php
--8<--
```

#### setCC($cc)

- **Parameters**  

- **$cc** `mixed` Comma-delimited string or an array of e-mail addresses

- **Returns**: CodeIgniter\Email\Email instance (method chaining)

- **Return type**: `CodeIgniter\Email\Email`

Sets the CC email address(s). Just like the "to", can be a single e-mail, a comma-delimited list or an array.

#### setBCC($bcc\[, $limit = ''])

- **Parameters**  

- **$bcc** `mixed` Comma-delimited string or an array of e-mail addresses

- **$limit** `int` Maximum number of e-mails to send per batch

- **Returns**: CodeIgniter\Email\Email instance (method chaining)

- **Return type**: `CodeIgniter\Email\Email`

Sets the BCC email address(s). Just like the `setTo()` method, can be a single e-mail, a comma-delimited list or an array.

If `$limit` is set, "batch mode" will be enabled, which will send the emails to batches, with each batch not exceeding the specified `$limit`.

#### setSubject($subject)

- **Parameters**  

- **$subject** `string` E-mail subject line

- **Returns**: CodeIgniter\Email\Email instance (method chaining)

- **Return type**: `CodeIgniter\Email\Email`

Sets the email subject:

```php
--8<--
libraries/email/009.php
--8<--
```

#### setMessage($body)

- **Parameters**  

- **$body** `string` E-mail message body

- **Returns**: CodeIgniter\Email\Email instance (method chaining)

- **Return type**: `CodeIgniter\Email\Email`

Sets the e-mail message body:

```php
--8<--
libraries/email/010.php
--8<--
```

#### setAltMessage($str)

- **Parameters**  

- **$str** `string` Alternative e-mail message body

- **Returns**: CodeIgniter\Email\Email instance (method chaining)

- **Return type**: `CodeIgniter\Email\Email`

Sets the alternative e-mail message body:

```php
--8<--
libraries/email/011.php
--8<--
```

This is an optional message string which can be used if you send HTML formatted email. It lets you specify an alternative message with no HTML formatting which is added to the header string for people who do not accept HTML email. If you do not set your own message CodeIgniter will extract the message from your HTML email and strip the tags.

#### setHeader($header, $value)

- **Parameters**  

- **$header** `string` Header name

- **$value** `string` Header value

- **Returns**: CodeIgniter\Email\Email instance (method chaining)

- **Return type**: `CodeIgniter\Email\Email`

Appends additional headers to the e-mail:

```php
--8<--
libraries/email/012.php
--8<--
```

#### clear($clearAttachments = false)

- **Parameters**  

- **$clearAttachments** `bool` Whether or not to clear attachments

- **Returns**: CodeIgniter\Email\Email instance (method chaining)

- **Return type**: `CodeIgniter\Email\Email`

Initializes all the email variables to an empty state. This method is intended for use if you run the email sending method in a loop, permitting the data to be reset between cycles.

```php
--8<--
libraries/email/013.php
--8<--
```

If you set the parameter to true any attachments will be cleared as well:

```php
--8<--
libraries/email/014.php
--8<--
```

#### send($autoClear = true)

- **Parameters**  

- **$autoClear** `bool` Whether to clear message data automatically

- **Returns**: true on success, false on failure

- **Return type**: `bool`

The e-mail sending method. Returns boolean true or false based on success or failure, enabling it to be used conditionally:

```php
--8<--
libraries/email/015.php
--8<--
```

This method will automatically clear all parameters if the request was successful. To stop this behaviour pass false:

```php
--8<--
libraries/email/016.php
--8<--
```

!!! note "Note"
    In order to use the `printDebugger()` method, you need

to avoid clearing the email parameters.

!!! note "Note"
    If `BCCBatchMode` is enabled, and there are more than

`BCCBatchSize` recipients, this method will always return boolean `true`.

#### attach($filename\[, $disposition = ''\[, $newname = null\[, $mime = '']]])

\* **Parameters**  

- **$filename** `string` File name

- **$disposition** `string` 'disposition' of the attachment. Most

email clients make their own decision regardless of the MIME specification used here. <https://www.iana.org/assignments/cont-disp/cont-disp.xhtml> \* **Parameters** \* **$newname** `string` Custom file name to use in the e-mail \* **$mime** `string` MIME type to use (useful for buffered data) \* **Returns**: CodeIgniter\Email\Email instance (method chaining) \* **Return type**: `CodeIgniter\Email\Email`

Enables you to send an attachment. Put the file path/name in the first parameter. For multiple attachments use the method multiple times. For example:

```php
--8<--
libraries/email/017.php
--8<--
```

To use the default disposition (attachment), leave the second parameter blank, otherwise use a custom disposition:

```php
--8<--
libraries/email/018.php
--8<--
```

You can also use a URL:

```php
--8<--
libraries/email/019.php
--8<--
```

If you'd like to use a custom file name, you can use the third parameter:

```php
--8<--
libraries/email/020.php
--8<--
```

If you need to use a buffer string instead of a real - physical - file you can use the first parameter as buffer, the third parameter as file name and the fourth parameter as mime-type:

```php
--8<--
libraries/email/021.php
--8<--
```

#### setAttachmentCID($filename)

- **Parameters**  

- **$filename** `string` Existing attachment filename

- **Returns**: Attachment Content-ID or false if not found

- **Return type**: `string`

Sets and returns an attachment's Content-ID, which enables you to embed an inline (picture) attachment into HTML. First parameter must be the already attached file name.

```php
--8<--
libraries/email/022.php
--8<--
```

!!! note "Note"
    Content-ID for each e-mail must be re-created for it to be unique.

#### printDebugger($include = \['headers', 'subject', 'body'])

- **Parameters**  

- **$include** `array` Which parts of the message to print out

- **Returns**: Formatted debug data

- **Return type**: `string`

Returns a string containing any server messages, the email headers, and the email message. Useful for debugging.

You can optionally specify which parts of the message should be printed. Valid options are: **headers**, **subject**, **body**.

Example:

```php
--8<--
libraries/email/023.php
--8<--
```

!!! note "Note"
    By default, all of the raw data will be printed.
