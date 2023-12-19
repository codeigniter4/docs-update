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
- BCC Batch Mode, enabling large email lists to be broken into small BCC
  batches.
- Email Debugging tools

<div class="contents" local="" depth="3">

</div>

## Using the Email Library

### Sending Email

Sending email is not only simple, but you can configure it on the fly or
set your preferences in the **app/Config/Email.php** file.

Here is a basic example demonstrating how you might send email:

<div class="literalinclude">

email/001.php

</div>

### Setting Email Preferences

There are 21 different preferences available to tailor how your email
messages are sent. You can either set them manually as described here,
or automatically via preferences stored in your config file, described
in [Email Preferences](#email-preferences).

#### Setting Email Preferences by Passing an Array

Preferences are set by passing an array of preference values to the
email initialize method. Here is an example of how you might set some
preferences:

<div class="literalinclude">

email/002.php

</div>

> [!NOTE]
> Most of the preferences have default values that will be used if you
> do not set them.

#### Setting Email Preferences in a Config File

If you prefer not to set preferences using the above method, you can
instead put them into the config file. Simply open the
**app/Config/Email.php** file, and set your configs in the Email
properties. Then save the file and it will be used automatically. You
will NOT need to use the `$email->initialize()` method if you set your
preferences in the config file.

#### SSL versus TLS for SMTP Protocol

To protect the username, password and email content while communicating
with the SMTP server, encryption on the channel should be used. Two
different standards are widely deployed and it is important to
understand the differences when trying to troubleshoot email sending
issues.

Most SMTP servers allow connections on ports 465 or 587 when submitting
emails. (The original port 25 is seldom used because of many ISPs have
blocking rules in place and since the communication is entirely in
clear-text).

The key difference is that port 465 expects the communication channel to
be secured using TLS from the start as per [RFC
8314](https://tools.ietf.org/html/rfc8314). A connection to port 587
allows clear-text connection and later will upgrade the channel to use
encryption using the `STARTTLS` SMTP command.

Upgrading a connection on port 465 may or may not be supported by the
server, so the `STARTTLS` SMTP command may fail if the server does not
allow it. If you set the port to 465, you should try to set the
`SMTPCrypto` to an empty string (`''`) since the communication is
secured using TLS from the start and the `STARTTLS` is not needed.

If your configuration requires you to connect to port 587, you should
most likely set `SMTPCrypto` to `tls` as this will implement the
`STARTTLS` command while communicating with the SMTP server to switch
from clear-text to an encrypted channel. The initial communication will
be made in clear-text and the channel will be upgraded to TLS with the
`STARTTLS` command.

#### Reviewing Preferences

The settings used for the last successful send are available from the
instance property `$archive`. This is helpful for testing and debugging
to determine that actual values at the time of the `send()` call.

### Email Preferences

The following is a list of all the preferences that can be set when
sending email.

<table>
<thead>
<tr class="header">
<th>Preference</th>
<th>Default Value</th>
<th>Options</th>
<th>Description</th>
</tr>
</thead>
<tbody>
<tr class="odd">
<td><strong>userAgent</strong></td>
<td>CodeIgniter</td>
<td>None</td>
<td>The "user agent".</td>
</tr>
<tr class="even">
<td><p><strong>protocol</strong></p></td>
<td><p>mail</p></td>
<td><p><code>mail</code>, <code>sendmail</code>, or
<code>smtp</code></p></td>
<td><p>The mail sending protocol.</p></td>
</tr>
<tr class="odd">
<td><strong>mailPath</strong></td>
<td>/usr/sbin/sendmail</td>
<td>None</td>
<td>The server path to Sendmail.</td>
</tr>
<tr class="even">
<td><strong>SMTPHost</strong></td>
<td>No Default</td>
<td>None</td>
<td>SMTP Server Hostname.</td>
</tr>
<tr class="odd">
<td><strong>SMTPUser</strong></td>
<td>No Default</td>
<td>None</td>
<td>SMTP Username.</td>
</tr>
<tr class="even">
<td><strong>SMTPPass</strong></td>
<td>No Default</td>
<td>None</td>
<td>SMTP Password.</td>
</tr>
<tr class="odd">
<td><p><strong>SMTPPort</strong></p></td>
<td><p>25</p></td>
<td><p>None</p></td>
<td><p>SMTP Port. (If set to <code>465</code>, TLS will be used for the
connection regardless of <code>SMTPCrypto</code> setting.)</p></td>
</tr>
<tr class="even">
<td><strong>SMTPTimeout</strong></td>
<td>5</td>
<td>None</td>
<td>SMTP Timeout (in seconds).</td>
</tr>
<tr class="odd">
<td><strong>SMTPKeepAlive</strong></td>
<td>false</td>
<td><code>true</code>/<code>false</code> (boolean)</td>
<td>Enable persistent SMTP connections.</td>
</tr>
<tr class="even">
<td><p><strong>SMTPCrypto</strong></p></td>
<td><p>tls</p></td>
<td><p><code>tls</code>, <code>ssl</code>, or empty string
(<code>''</code>)</p></td>
<td><p>SMTP Encryption. Setting this to <code>ssl</code> will create a
secure channel to the server using SSL, and <code>tls</code> will issue
a <code>STARTTLS</code> command to the server. Connection on port
<code>465</code> should set this to an empty string (<code>''</code>).
See also <code class="interpreted-text"
role="ref">email-ssl-tls-for-smtp</code>.</p></td>
</tr>
<tr class="odd">
<td><p><strong>wordWrap</strong> <strong>wrapChars</strong></p></td>
<td><p>true 76</p></td>
<td><p><code>true</code>/<code>false</code> (boolean)</p></td>
<td><p>Enable word-wrap. Character count to wrap at.</p></td>
</tr>
<tr class="even">
<td><p><strong>mailType</strong></p>
<p><strong>charset</strong></p></td>
<td><p>text</p>
<p>utf-8</p></td>
<td><p><code>text</code> or <code>html</code></p></td>
<td><p>Type of mail. If you send HTML email you must send it as a
complete web page. Make sure you don't have any relative links or
relative image paths otherwise they will not work. Character set
(<code>utf-8</code>, <code>iso-8859-1</code>, etc.).</p></td>
</tr>
<tr class="odd">
<td><strong>validate</strong></td>
<td>true</td>
<td><code>true</code>/<code>false</code> (boolean)</td>
<td>Whether to validate the email address.</td>
</tr>
<tr class="even">
<td><strong>priority</strong></td>
<td>3</td>
<td>1, 2, 3, 4, 5</td>
<td>Email Priority. <code>1</code> = highest. <code>5</code> = lowest.
<code>3</code> = normal.</td>
</tr>
<tr class="odd">
<td><strong>CRLF</strong></td>
<td>\n</td>
<td><code>\r\n</code> or <code>\n</code> or <code>\r</code></td>
<td>Newline character. (Use <code>\r\n</code> to comply with RFC
822).</td>
</tr>
<tr class="even">
<td><strong>newline</strong></td>
<td>\n</td>
<td><code>\r\n</code> or <code>\n</code> or <code>\r</code></td>
<td>Newline character. (Use <code>\r\n</code> to comply with RFC
822).</td>
</tr>
<tr class="odd">
<td><strong>BCCBatchMode</strong></td>
<td>false</td>
<td><code>true</code>/<code>false</code> (boolean)</td>
<td>Enable BCC Batch Mode.</td>
</tr>
<tr class="even">
<td><strong>BCCBatchSize</strong></td>
<td>200</td>
<td>None</td>
<td>Number of emails in each BCC batch.</td>
</tr>
<tr class="odd">
<td><strong>DSN</strong></td>
<td>false</td>
<td><code>true</code>/<code>false</code> (boolean)</td>
<td>Enable notify message from server.</td>
</tr>
</tbody>
</table>

### Overriding Word Wrapping

If you have word wrapping enabled (recommended to comply with RFC 822)
and you have a very long link in your email it can get wrapped too,
causing it to become un-clickable by the person receiving it.
CodeIgniter lets you manually override word wrapping within part of your
message like this:

    The text of your email that
    gets wrapped normally.

    {unwrap}http://example.com/a_long_link_that_should_not_be_wrapped.html{/unwrap}

    More text that will be
    wrapped normally.

Place the item you do not want word-wrapped between: {unwrap} {/unwrap}

## Class Reference

> > param string \$from  
> > "From" e-mail address
> >
> > param string \$name  
> > "From" display name
> >
> > param string \$returnPath  
> > Optional email address to redirect undelivered e-mail to
> >
> > returns  
> > CodeIgniter\Email\Email instance (method chaining)
> >
> > rtype  
> > CodeIgniter\Email\Email
> >
> > Sets the email address and name of the person sending the email:
> >
> > <div class="literalinclude">
> >
> > email/003.php
> >
> > </div>
> >
> > You can also set a Return-Path, to help redirect undelivered mail:
> >
> > <div class="literalinclude">
> >
> > email/004.php
> >
> > </div>
> >
> > > [!NOTE]
> > > Return-Path can't be used if you've configured 'smtp' as your
> > > protocol.
>
> > param string \$replyto  
> > E-mail address for replies
> >
> > param string \$name  
> > Display name for the reply-to e-mail address
> >
> > returns  
> > CodeIgniter\Email\Email instance (method chaining)
> >
> > rtype  
> > CodeIgniter\Email\Email
> >
> > Sets the reply-to address. If the information is not provided the
> > information in the [setFrom](#setFrom) method is used. Example:
> >
> > <div class="literalinclude">
> >
> > email/005.php
> >
> > </div>
>
> > param mixed \$to  
> > Comma-delimited string or an array of e-mail addresses
> >
> > returns  
> > CodeIgniter\Email\Email instance (method chaining)
> >
> > rtype  
> > CodeIgniter\Email\Email
> >
> > Sets the email address(s) of the recipient(s). Can be a single
> > e-mail, a comma-delimited list or an array:
> >
> > <div class="literalinclude">
> >
> > email/006.php
> >
> > </div>
> >
> > <div class="literalinclude">
> >
> > email/007.php
> >
> > </div>
> >
> > <div class="literalinclude">
> >
> > email/008.php
> >
> > </div>
>
> > param mixed \$cc  
> > Comma-delimited string or an array of e-mail addresses
> >
> > returns  
> > CodeIgniter\Email\Email instance (method chaining)
> >
> > rtype  
> > CodeIgniter\Email\Email
> >
> > Sets the CC email address(s). Just like the "to", can be a single
> > e-mail, a comma-delimited list or an array.
>
> > param mixed \$bcc  
> > Comma-delimited string or an array of e-mail addresses
> >
> > param int \$limit  
> > Maximum number of e-mails to send per batch
> >
> > returns  
> > CodeIgniter\Email\Email instance (method chaining)
> >
> > rtype  
> > CodeIgniter\Email\Email
> >
> > Sets the BCC email address(s). Just like the `setTo()` method, can
> > be a single e-mail, a comma-delimited list or an array.
> >
> > If `$limit` is set, "batch mode" will be enabled, which will send
> > the emails to batches, with each batch not exceeding the specified
> > `$limit`.
>
> > param string \$subject  
> > E-mail subject line
> >
> > returns  
> > CodeIgniter\Email\Email instance (method chaining)
> >
> > rtype  
> > CodeIgniter\Email\Email
> >
> > Sets the email subject:
> >
> > <div class="literalinclude">
> >
> > email/009.php
> >
> > </div>
>
> > param string \$body  
> > E-mail message body
> >
> > returns  
> > CodeIgniter\Email\Email instance (method chaining)
> >
> > rtype  
> > CodeIgniter\Email\Email
> >
> > Sets the e-mail message body:
> >
> > <div class="literalinclude">
> >
> > email/010.php
> >
> > </div>
>
> > param string \$str  
> > Alternative e-mail message body
> >
> > returns  
> > CodeIgniter\Email\Email instance (method chaining)
> >
> > rtype  
> > CodeIgniter\Email\Email
> >
> > Sets the alternative e-mail message body:
> >
> > <div class="literalinclude">
> >
> > email/011.php
> >
> > </div>
> >
> > This is an optional message string which can be used if you send
> > HTML formatted email. It lets you specify an alternative message
> > with no HTML formatting which is added to the header string for
> > people who do not accept HTML email. If you do not set your own
> > message CodeIgniter will extract the message from your HTML email
> > and strip the tags.
>
> > param string \$header  
> > Header name
> >
> > param string \$value  
> > Header value
> >
> > returns  
> > CodeIgniter\Email\Email instance (method chaining)
> >
> > rtype  
> > CodeIgniter\Email\Email
> >
> > Appends additional headers to the e-mail:
> >
> > <div class="literalinclude">
> >
> > email/012.php
> >
> > </div>
>
> > param bool \$clearAttachments  
> > Whether or not to clear attachments
> >
> > returns  
> > CodeIgniter\Email\Email instance (method chaining)
> >
> > rtype  
> > CodeIgniter\Email\Email
> >
> > Initializes all the email variables to an empty state. This method
> > is intended for use if you run the email sending method in a loop,
> > permitting the data to be reset between cycles.
> >
> > <div class="literalinclude">
> >
> > email/013.php
> >
> > </div>
> >
> > If you set the parameter to true any attachments will be cleared as
> > well:
> >
> > <div class="literalinclude">
> >
> > email/014.php
> >
> > </div>
>
> > param bool \$autoClear  
> > Whether to clear message data automatically
> >
> > returns  
> > true on success, false on failure
> >
> > rtype  
> > bool
> >
> > The e-mail sending method. Returns boolean true or false based on
> > success or failure, enabling it to be used conditionally:
> >
> > <div class="literalinclude">
> >
> > email/015.php
> >
> > </div>
> >
> > This method will automatically clear all parameters if the request
> > was successful. To stop this behaviour pass false:
> >
> > <div class="literalinclude">
> >
> > email/016.php
> >
> > </div>
> >
> > > [!NOTE]
> > > In order to use the `printDebugger()` method, you need to avoid
> > > clearing the email parameters.
> >
> > > [!NOTE]
> > > If `BCCBatchMode` is enabled, and there are more than
> > > `BCCBatchSize` recipients, this method will always return boolean
> > > `true`.
>
> > param string \$filename  
> > File name
> >
> > param string \$disposition  
> > 'disposition' of the attachment. Most email clients make their own
> > decision regardless of the MIME specification used here.
> > <https://www.iana.org/assignments/cont-disp/cont-disp.xhtml>
> >
> > param string \$newname  
> > Custom file name to use in the e-mail
> >
> > param string \$mime  
> > MIME type to use (useful for buffered data)
> >
> > returns  
> > CodeIgniter\Email\Email instance (method chaining)
> >
> > rtype  
> > CodeIgniter\Email\Email
> >
> > Enables you to send an attachment. Put the file path/name in the
> > first parameter. For multiple attachments use the method multiple
> > times. For example:
> >
> > <div class="literalinclude">
> >
> > email/017.php
> >
> > </div>
> >
> > To use the default disposition (attachment), leave the second
> > parameter blank, otherwise use a custom disposition:
> >
> > <div class="literalinclude">
> >
> > email/018.php
> >
> > </div>
> >
> > You can also use a URL:
> >
> > <div class="literalinclude">
> >
> > email/019.php
> >
> > </div>
> >
> > If you'd like to use a custom file name, you can use the third
> > parameter:
> >
> > <div class="literalinclude">
> >
> > email/020.php
> >
> > </div>
> >
> > If you need to use a buffer string instead of a real - physical -
> > file you can use the first parameter as buffer, the third parameter
> > as file name and the fourth parameter as mime-type:
> >
> > <div class="literalinclude">
> >
> > email/021.php
> >
> > </div>
>
> > param string \$filename  
> > Existing attachment filename
> >
> > returns  
> > Attachment Content-ID or false if not found
> >
> > rtype  
> > string
> >
> > Sets and returns an attachment's Content-ID, which enables you to
> > embed an inline (picture) attachment into HTML. First parameter must
> > be the already attached file name.
> >
> > <div class="literalinclude">
> >
> > email/022.php
> >
> > </div>
> >
> > > [!NOTE]
> > > Content-ID for each e-mail must be re-created for it to be unique.
>
> > param array \$include  
> > Which parts of the message to print out
> >
> > returns  
> > Formatted debug data
> >
> > rtype  
> > string
> >
> > Returns a string containing any server messages, the email headers,
> > and the email message. Useful for debugging.
> >
> > You can optionally specify which parts of the message should be
> > printed. Valid options are: **headers**, **subject**, **body**.
> >
> > Example:
> >
> > <div class="literalinclude">
> >
> > email/023.php
> >
> > </div>
> >
> > > [!NOTE]
> > > By default, all of the raw data will be printed.
