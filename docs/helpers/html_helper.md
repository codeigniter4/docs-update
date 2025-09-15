# HTML Helper

The HTML Helper file contains functions that assist in working with HTML.

- [Configuration](#configuration)
- [Loading this Helper](#loading-this-helper)
- [Available Functions](#available-functions)

## Configuration

Since `v4.3.0`, void HTML elements (e.g. `<img>`) in `html_helper` functions have been changed to be HTML5-compatible by default and if you need to be compatible with XHTML, you must set the `$html5` property in **app/Config/DocTypes.php** to `false`.

## Loading this Helper

This helper is loaded using the following code:

```php
--8<--
helpers/html_helper/001.php
--8<--
```

## Available Functions

The following functions are available:

#### img(\[$src = ''\[, $indexPage = false\[, $attributes = '']]])

- **Parameters**  

- **$src** `string|array` Image source URI, or array of attributes and values

- **$indexPage** `bool` Whether to treat `$src` as a routed URI string

- **$attributes** `mixed` Additional HTML attributes

- **Returns**: HTML image tag

- **Return type**: `string`

Lets you create HTML `<img />` tags. The first parameter contains the image source. Example:

```php
--8<--
helpers/html_helper/002.php
--8<--
```

There is an optional second parameter that is a true/false value that specifics if the *src* should have the page specified by `$config['indexPage']` added to the address it creates. Presumably, this would be if you were using a media controller:

```php
--8<--
helpers/html_helper/003.php
--8<--
```

Additionally, an associative array can be passed as the first parameter, for complete control over all attributes and values. If an *alt* attribute is not provided, CodeIgniter will generate an empty string.

Example:

```php
--8<--
helpers/html_helper/004.php
--8<--
```

#### img_data(\[$src = ''\[, $indexPage = false\[, $attributes = '']]])

- **Parameters**  

- **$path** `string` Path to the image file

- **$mime** `string|null` MIME type to use, or null to guess

- **Returns**: base64 encoded binary image string

- **Return type**: `string`

Generates a src-ready string from an image using the "data:" protocol. Example:

```php
--8<--
helpers/html_helper/005.php
--8<--
```

There is an optional second parameter to specify the MIME type, otherwise the function will use your Mimes config to guess:

```php
--8<--
helpers/html_helper/006.php
--8<--
```

Note that `$path` must exist and be a readable image format supported by the `data:` protocol. This function is not recommended for very large files, but it provides a convenient way of serving images from your app that are not web-accessible (e.g., in **public/**).

#### link_tag(\[$href = ''\[, $rel = 'stylesheet'\[, $type = 'text/css'\[, $title = ''\[, $media = ''\[, $indexPage = false\[, $hreflang = '']]]]]]])

- **Parameters**  

- **$href** `string` The source of the link file

- **$rel** `string` Relation type

- **$type** `string` Type of the related document

- **$title** `string` Link title

- **$media** `string` Media type

- **$indexPage** `bool` Whether to treat `$src` as a routed URI string

- **$hreflang** `string` Hreflang type

- **Returns**: HTML link tag

- **Return type**: `string`

Lets you create HTML `<link />` tags. This is useful for stylesheet links, as well as other links. The parameters are *href*, with optional *rel*, *type*, *title*, *media* and *indexPage*.

*indexPage* is a boolean value that specifies if the *href* should have the page specified by `$config['indexPage']` added to the address it creates.

Example:

```php
--8<--
helpers/html_helper/007.php
--8<--
```

Further examples:

```php
--8<--
helpers/html_helper/008.php
--8<--
```

Alternately, an associative array can be passed to the `link_tag()` function for complete control over all attributes and values:

```php
--8<--
helpers/html_helper/009.php
--8<--
```

#### script_tag(\[$src = ''\[, $indexPage = false]])

- **Parameters**  

- **$src** `array|string` The source name or URL of a JavaScript file, or an associative array specifying the attributes

- **$indexPage** `bool` Whether to treat `$src` as a routed URI string

- **Returns**: HTML script tag

- **Return type**: `string`

Lets you create HTML `<script></script>` tags. The parameters is *src*, with optional *indexPage*.

*indexPage* is a boolean value that specifies if the *src* should have the page specified by `$config['indexPage']` added to the address it creates.

Example:

```php
--8<--
helpers/html_helper/010.php
--8<--
```

Alternately, an associative array can be passed to the `script_tag()` function for complete control over all attributes and values:

```php
--8<--
helpers/html_helper/011.php
--8<--
```

#### ul($list\[, $attributes = ''])

- **Parameters**  

- **$list** `array` List entries

- **$attributes** `array` HTML attributes

- **Returns**: HTML-formatted unordered list

- **Return type**: `string`

Permits you to generate unordered HTML lists from simple or multi-dimensional arrays. Example:

```php
--8<--
helpers/html_helper/012.php
--8<--
```

The above code will produce this:

``` html
```

\<ul class="boldlist" id="mylist"\> \<li\>red\</li\> \<li\>blue\</li\> \<li\>green\</li\> \<li\>yellow\</li\> \</ul\>

Here is a more complex example, using a multi-dimensional array:

```php
--8<--
helpers/html_helper/013.php
--8<--
```

The above code will produce this:

``` html
```

\<ul class="boldlist" id="mylist"\> \<li\>colors \<ul\> \<li\>red\</li\> \<li\>blue\</li\> \<li\>green\</li\> \</ul\> \</li\> \<li\>shapes \<ul\> \<li\>round\</li\> \<li\>square\</li\> \<li\>circles \<ul\> \<li\>ellipse\</li\> \<li\>oval\</li\> \<li\>sphere\</li\> \</ul\> \</li\> \</ul\> \</li\> \<li\>moods \<ul\> \<li\>happy\</li\> \<li\>upset \<ul\> \<li\>defeated \<ul\> \<li\>dejected\</li\> \<li\>disheartened\</li\> \<li\>depressed\</li\> \</ul\> \</li\> \<li\>annoyed\</li\> \<li\>cross\</li\> \<li\>angry\</li\> \</ul\> \</li\> \</ul\> \</li\> \</ul\>

#### ol($list, $attributes = '')

- **Parameters**  

- **$list** `array` List entries

- **$attributes** `array` HTML attributes

- **Returns**: HTML-formatted ordered list

- **Return type**: `string`

Identical to `ul()`, only it produces the `<ol>` tag for ordered lists instead of `<ul>`.

#### video($src\[, $unsupportedMessage = ''\[, $attributes = ''\[, $tracks = \[]\[, $indexPage = false]]]])

- **Parameters**  

- **$src** `mixed` Either a source string or an array of sources. See `source()` function

- **$unsupportedMessage** `string` The message to display if the media tag is not supported by the browser

- **$attributes** `string` HTML attributes

- **$tracks** `array` Use the track function inside an array. See `track()` function

- bool `$indexPage`

- **Returns**: HTML-formatted video element

- **Return type**: `string`

Permits you to generate HTML video element from simple or source arrays. Example:

```php
--8<--
helpers/html_helper/014.php
--8<--
```

The above code will produce this:

``` html
```

\<video src="test.mp4" controls\> Your browser does not support the video tag. \</video\>

\<video src="http://www.codeigniter.com/test.mp4" controls\> \<track src="subtitles_no.vtt" kind="subtitles" srclang="no" label="Norwegian No" /\> \<track src="subtitles_yes.vtt" kind="subtitles" srclang="yes" label="Norwegian Yes" /\> Your browser does not support the video tag. \</video\>

\<video class="test" controls\> \<source src="movie.mp4" type="video/mp4" class="test" /\> \<source src="movie.ogg" type="video/ogg" /\> \<source src="movie.mov" type="video/quicktime" /\> \<source src="movie.ogv" type="video/ogv; codecs=dirac, speex" /\> \<track src="subtitles_no.vtt" kind="subtitles" srclang="no" label="Norwegian No" /\> \<track src="subtitles_yes.vtt" kind="subtitles" srclang="yes" label="Norwegian Yes" /\> Your browser does not support the video tag. \</video\>

#### audio($src\[, $unsupportedMessage = ''\[, $attributes = ''\[, $tracks = \[]\[, $indexPage = false]]]])

- **Parameters**  

- **$src** `mixed` Either a source string or an array of sources. See `source()` function

- **$unsupportedMessage** `string` The message to display if the media tag is not supported by the browser

- string `$attributes`

- **$tracks** `array` Use the track function inside an array. See `track()` function

- bool `$indexPage`

- **Returns**: HTML-formatted audio element

- **Return type**: `string`

Identical to `video()`, only it produces the `<audio>` tag instead of `<video>`.

#### source($src = ''\[, $type = false\[, $attributes = '']])

- **Parameters**  

- **$src** `string` The path of the media resource

- **$type** `bool` The MIME-type of the resource with optional codecs parameters

- **$attributes** `array` HTML attributes

- **Returns**: HTML source tag

- **Return type**: `string`

Lets you create HTML `<source />` tags. The first parameter contains the source source. Example:

```php
--8<--
helpers/html_helper/015.php
--8<--
```

#### embed($src = ''\[, $type = false\[, $attributes = ''\[, $indexPage = false]]])

- **Parameters**  

- **$src** `string` The path of the resource to embed

- **$type** `bool` MIME-type

- **$attributes** `array` HTML attributes

- bool `$indexPage`

- **Returns**: HTML embed tag

- **Return type**: `string`

Lets you create HTML `<embed />` tags. The first parameter contains the embed source. Example:

```php
--8<--
helpers/html_helper/016.php
--8<--
```

#### object($data = ''\[, $type = false\[, $attributes = '']])

- **Parameters**  

- **$data** `string` A resource URL

- **$type** `bool` Content-type of the resource

- **$attributes** `array` HTML attributes

- **$params** `array` Use the param function inside an array. See `param()` function

- **Returns**: HTML object tag

- **Return type**: `string`

Lets you create HTML `<object />` tags. The first parameter contains the object data. Example:

```php
--8<--
helpers/html_helper/017.php
--8<--
```

The above code will produce this:

``` html
```

\<object data="movie.swf" class="test"\>\</object\>

\<object data="movie.swf" class="test"\> \<param name="foo" type="ref" value="bar" class="test" /\> \<param name="hello" type="ref" value="world" class="test" /\> \</object\>

#### param($name = ''\[, $type = false\[, $attributes = '']])

- **Parameters**  

- **$name** `string` The name of the parameter

- **$value** `string` The value of the parameter

- **$attributes** `array` HTML attributes

- **Returns**: HTML param tag

- **Return type**: `string`

Lets you create HTML `<param />` tags. The first parameter contains the param source. Example:

```php
--8<--
helpers/html_helper/018.php
--8<--
```

#### track($name = ''\[, $type = false\[, $attributes = '']])

- **Parameters**  

- **$name** `string` The name of the parameter

- **$value** `string` The value of the parameter

- **$attributes** `array` HTML attributes

- **Returns**: HTML track tag

- **Return type**: `string`

Generates a track element to specify timed tracks. The tracks are formatted in WebVTT format. Example:

```php
--8<--
helpers/html_helper/019.php
--8<--
```

#### doctype(\[$type = 'html5'])

- **Parameters**  

- **$type** `string` Doctype name

- **Returns**: HTML DocType tag

- **Return type**: `string`

Helps you generate document type declarations, or DTD's. HTML 5 is used by default, but many doctypes are available.

Example:

```php
--8<--
helpers/html_helper/020.php
--8<--
```

The following is a list of the pre-defined doctype choices. These are configurable, pulled from **app/Config/DocTypes.php**, or they could be over-ridden in your **.env** configuration.

<table>
<thead>
<tr>
<th>Document type</th>
<th>Option</th>
<th>Result</th>
</tr>
</thead>
<tbody>
<tr>
<td>XHTML 1.1</td>
<td>xhtml11</td>
<td>&lt;!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd"&gt;</td>
</tr>
<tr>
<td>XHTML 1.0 Strict</td>
<td>xhtml1-strict</td>
<td>&lt;!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"&gt;</td>
</tr>
<tr>
<td>XHTML 1.0 Transitional</td>
<td>xhtml1-trans</td>
<td>&lt;!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"&gt;</td>
</tr>
<tr>
<td>XHTML 1.0 Frameset</td>
<td>xhtml1-frame</td>
<td>&lt;!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd"&gt;</td>
</tr>
<tr>
<td>XHTML Basic 1.1</td>
<td>xhtml-basic11</td>
<td>&lt;!DOCTYPE html PUBLIC "-//W3C//DTD XHTML Basic 1.1//EN" "http://www.w3.org/TR/xhtml-basic/xhtml-basic11.dtd"&gt;</td>
</tr>
<tr>
<td>HTML 5</td>
<td>html5</td>
<td>&lt;!DOCTYPE html&gt;</td>
</tr>
<tr>
<td>HTML 4 Strict</td>
<td>html4-strict</td>
<td>&lt;!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd"&gt;</td>
</tr>
<tr>
<td>HTML 4 Transitional</td>
<td>html4-trans</td>
<td>&lt;!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd"&gt;</td>
</tr>
<tr>
<td>HTML 4 Frameset</td>
<td>html4-frame</td>
<td>&lt;!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd"&gt;</td>
</tr>
<tr>
<td>MathML 1.01</td>
<td>mathml1</td>
<td>&lt;!DOCTYPE math SYSTEM "http://www.w3.org/Math/DTD/mathml1/mathml.dtd"&gt;</td>
</tr>
<tr>
<td>MathML 2.0</td>
<td>mathml2</td>
<td>&lt;!DOCTYPE math PUBLIC "-//W3C//DTD MathML 2.0//EN" "http://www.w3.org/Math/DTD/mathml2/mathml2.dtd"&gt;</td>
</tr>
<tr>
<td>SVG 1.0</td>
<td>svg10</td>
<td>&lt;!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.0//EN" "http://www.w3.org/TR/2001/REC-SVG-20010904/DTD/svg10.dtd"&gt;</td>
</tr>
<tr>
<td>SVG 1.1 Full</td>
<td>svg11</td>
<td>&lt;!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" "http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd"&gt;</td>
</tr>
<tr>
<td>SVG 1.1 Basic</td>
<td>svg11-basic</td>
<td>&lt;!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1 Basic//EN" "http://www.w3.org/Graphics/SVG/1.1/DTD/svg11-basic.dtd"&gt;</td>
</tr>
<tr>
<td>SVG 1.1 Tiny</td>
<td>svg11-tiny</td>
<td>&lt;!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1 Tiny//EN" "http://www.w3.org/Graphics/SVG/1.1/DTD/svg11-tiny.dtd"&gt;</td>
</tr>
<tr>
<td>XHTML+MathML+SVG (XHTML host)</td>
<td>xhtml-math-svg-xh</td>
<td>&lt;!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1 plus MathML 2.0 plus SVG 1.1//EN" "http://www.w3.org/2002/04/xhtml-math-svg/xhtml-math-svg.dtd"&gt;</td>
</tr>
<tr>
<td>XHTML+MathML+SVG (SVG host)</td>
<td>xhtml-math-svg-sh</td>
<td>&lt;!DOCTYPE svg:svg PUBLIC "-//W3C//DTD XHTML 1.1 plus MathML 2.0 plus SVG 1.1//EN" "http://www.w3.org/2002/04/xhtml-math-svg/xhtml-math-svg.dtd"&gt;</td>
</tr>
<tr>
<td>XHTML+RDFa 1.0</td>
<td>xhtml-rdfa-1</td>
<td>&lt;!DOCTYPE html PUBLIC "-//W3C//DTD XHTML+RDFa 1.0//EN" "http://www.w3.org/MarkUp/DTD/xhtml-rdfa-1.dtd"&gt;</td>
</tr>
<tr>
<td>XHTML+RDFa 1.1</td>
<td>xhtml-rdfa-2</td>
<td>&lt;!DOCTYPE html PUBLIC "-//W3C//DTD XHTML+RDFa 1.1//EN" "http://www.w3.org/MarkUp/DTD/xhtml-rdfa-2.dtd"&gt;</td>
</tr>
</tbody>
</table>
