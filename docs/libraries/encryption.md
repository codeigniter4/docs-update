# Encryption Service

!!! important "Important"
    DO NOT use this or any other *encryption* library for password storage! Passwords must be *hashed* instead, and you should do that through PHP's [Password Hashing extension](https://www.php.net/password).

The Encryption Service provides two-way symmetric (secret key) data encryption. The service will instantiate and/or initialize an encryption **handler** to suit your parameters as explained below.

Encryption Service handlers must implement CodeIgniter's simple `EncrypterInterface`. Using an appropriate PHP cryptographic extension or third-party library may require additional software to be installed on your server and/or might need to be explicitly enabled in your instance of PHP.

The following PHP extensions are currently supported:

- [OpenSSL](https://www.php.net/openssl)

- [Sodium](https://www.php.net/manual/en/book.sodium)

This is not a full cryptographic solution. If you need more capabilities, for example, public-key encryption, we suggest you consider direct use of OpenSSL or one of the other [Cryptography Extensions](https://www.php.net/manual/en/refs.crypto.php). A more comprehensive package like [Halite](https://github.com/paragonie/halite) (an O-O package built on libsodium) is another possibility.

!!! note "Note"
    Support for the `MCrypt` extension has been dropped, as that has been deprecated as of PHP 7.2.

- [Using the Encryption Library](#using-the-encryption-library)
    - [Configuring the Library](#configuring-the-library)
        - [Configuration to Maintain Compatibility with CI3](#configuration-to-maintain-compatibility-with-ci3)
        - [Supported HMAC Authentication Algorithms](#supported-hmac-authentication-algorithms)
    - [Default Behavior](#default-behavior)
    - [Setting Your Encryption Key](#setting-your-encryption-key)
        - [Encoding Keys or Results](#encoding-keys-or-results)
        - [Using Prefixes in Storing Keys](#using-prefixes-in-storing-keys)
    - [Padding](#padding)
    - [Encryption Handler Notes](#encryption-handler-notes)
        - [OpenSSL Notes](#openssl-notes)
        - [Sodium Notes](#sodium-notes)
    - [Message Length](#message-length)
    - [Using the Encryption Service Directly](#using-the-encryption-service-directly)
- [Class Reference](#class-reference)
- [CodeIgniter\Encryption](#codeigniterencryption)
    - [Encryption](#encryption)
        - [createKey(\[$length = 32])](#createkeylength-32)
        - [initialize(\[Encryption $config = null])](#initializeencryption-config-null)
        - [encrypt($data\[, $params = null])](#encryptdata-params-null)
        - [decrypt($data\[, $params = null])](#decryptdata-params-null)

## Using the Encryption Library

Like all services in CodeIgniter, it can be loaded via `Config\Services`:

```php
--8<--
libraries/encryption/001.php
--8<--
```

Assuming you have set your starting key (see `configuration`), encrypting and decrypting data is simple - pass the appropriate string to `encrypt()` and/or `decrypt()` methods:

```php
--8<--
libraries/encryption/002.php
--8<--
```

And that's it! The Encryption library will do everything necessary for the whole process to be cryptographically secure out-of-the-box. You don't need to worry about it.

### Configuring the Library

The example above uses the configuration settings found in **app/Config/Encryption.php**.

<table>
<thead>
<tr>
<th>Option</th>
<th>Possible values (default in parentheses)</th>
</tr>
</thead>
<tbody>
<tr>
<td>key</td>
<td>Encryption key starter</td>
</tr>
<tr>
<td>driver</td>
<td>Preferred handler, e.g., OpenSSL or Sodium (<code>OpenSSL</code>)</td>
</tr>
<tr>
<td>digest</td>
<td>Message digest algorithm (<code>SHA512</code>)</td>
</tr>
<tr>
<td>blockSize</td>
<td>[<strong>SodiumHandler</strong> only] Padding length in bytes (<code>16</code>)</td>
</tr>
<tr>
<td>cipher</td>
<td>[<strong>OpenSSLHandler</strong> only] Cipher to use (<code>AES-256-CTR</code>)</td>
</tr>
<tr>
<td>encryptKeyInfo</td>
<td>[<strong>OpenSSLHandler</strong> only] Encryption key info (<code>''</code>)</td>
</tr>
<tr>
<td>authKeyInfo</td>
<td>[<strong>OpenSSLHandler</strong> only] Authentication key info (<code>''</code>)</td>
</tr>
<tr>
<td>rawData</td>
<td>[<strong>OpenSSLHandler</strong> only] Whether the cipher-text should be raw (<code>true</code>)</td>
</tr>
</tbody>
</table>

You can replace the config file's settings by passing a configuration object of your own to the `Services` call. The `$config` variable must be an instance of the `Config\Encryption` class.

```php
--8<--
libraries/encryption/003.php
--8<--
```

#### Configuration to Maintain Compatibility with CI3

!!! success "Available from version 4.3.0"

Since v4.3.0, you can decrypt data encrypted with CI3's Encryption. If you need to decrypt such data, use the following settings to maintain compatibility.

```php
--8<--
libraries/encryption/013.php
--8<--
```

#### Supported HMAC Authentication Algorithms

For HMAC message authentication, the Encryption library supports usage of the SHA-2 family of algorithms:

<table>
<thead>
<tr>
<th>Algorithm</th>
<th>Raw length (bytes)</th>
<th>Hex-encoded length (bytes)</th>
</tr>
</thead>
<tbody>
<tr>
<td>SHA512</td>
<td>64</td>
<td>128</td>
</tr>
<tr>
<td>SHA384</td>
<td>48</td>
<td>96</td>
</tr>
<tr>
<td>SHA256</td>
<td>32</td>
<td>64</td>
</tr>
<tr>
<td>SHA224</td>
<td>28</td>
<td>56</td>
</tr>
</tbody>
</table>

The reason for not including other popular algorithms, such as MD5 or SHA1 is that they are no longer considered secure enough and as such, we don't want to encourage their usage. If you absolutely need to use them, it is easy to do so via PHP's native [hash_hmac()](http://php.net/manual/en/function.hash-hmac.php) function.

Stronger algorithms of course will be added in the future as they appear and become widely available.

### Default Behavior

By default, the Encryption Library uses the OpenSSL handler. That handler encrypts using the AES-256-CTR algorithm, your configured *key*, and SHA512 HMAC authentication.

### Setting Your Encryption Key

Your encryption key **must** be as long as the encryption algorithm in use allows. For AES-256, that's 256 bits or 32 bytes (characters) long.

The key should be as random as possible, and it **must not** be a regular text string, nor the output of a hashing function, etc. To create a proper key, you can use the Encryption library's `createKey()` method.

```php
--8<--
libraries/encryption/004.php
--8<--
```

The key can be stored in **app/Config/Encryption.php**, or you can design a storage mechanism of your own and pass the key dynamically when encrypting/decrypting.

To save your key to your **app/Config/Encryption.php**, open the file and set:

```php
--8<--
libraries/encryption/005.php
--8<--
```

#### Encoding Keys or Results

You'll notice that the `createKey()` method outputs binary data, which is hard to deal with (i.e., a copy-paste may damage it), so you may use `bin2hex()`, or `base64_encode` to work with the key in a more friendly manner. For example:

```php
--8<--
libraries/encryption/006.php
--8<--
```

You might find the same technique useful for the results of encryption:

```php
--8<--
libraries/encryption/007.php
--8<--
```

#### Using Prefixes in Storing Keys

You may take advantage of two special prefixes in storing your encryption keys: `hex2bin:` and `base64:`. When these prefixes immediately precede the value of your key, `Encryption` will intelligently parse the key and still pass a binary string to the library.

```php
--8<--
libraries/encryption/008.php
--8<--
```

Similarly, you can use these prefixes in your **.env** file, too! :

    // For hex2bin
    encryption.key = hex2bin:<your-hex-encoded-key>

    // or
    encryption.key = base64:<your-base64-encoded-key>

### Padding

Sometimes, the length of a message may provide a lot of information about its nature. If a message is one of "yes", "no" and "maybe", encrypting the message doesn't help: knowing the length is enough to know what the message is.

Padding is a technique to mitigate this, by making the length a multiple of a given block size.

Padding is implemented in `SodiumHandler` using libsodium's native `sodium_pad` and `sodium_unpad` functions. This requires the use of a padding length (in bytes) that is added to the plaintext message prior to encryption, and removed after decryption. Padding is configurable via the `$blockSize` property of `Config\Encryption`. This value should be greater than zero.

!!! important "Important"
    You are advised not to devise your own padding implementation. You must always use the more secure implementation of a library. Also, passwords should not be padded. Usage of padding in order to hide the length of a password is not recommended. A client willing to send a password to a server should hash it instead (even with a single iteration of the hash function). This ensures that the length of the transmitted data is constant, and that the server doesn't effortlessly get a copy of the password.

### Encryption Handler Notes

#### OpenSSL Notes

The [OpenSSL](https://www.php.net/openssl) extension has been a standard part of PHP for a long time.

CodeIgniter's OpenSSL handler uses the AES-256-CTR cipher.

The *key* your configuration provides is used to derive two other keys, one for encryption and one for authentication. This is achieved by way of a technique known as an [HMAC-based Key Derivation Function](https://en.wikipedia.org/wiki/HKDF) (HKDF).

#### Sodium Notes

The [Sodium](https://www.php.net/manual/en/book.sodium) extension is bundled by default in PHP as of PHP 7.2.0.

Sodium uses the algorithms XSalsa20 to encrypt, Poly1305 for MAC, and XS25519 for key exchange in sending secret messages in an end-to-end scenario. To encrypt and/or authenticate a string using a shared-key, such as symmetric encryption, Sodium uses the XSalsa20 algorithm to encrypt and HMAC-SHA512 for the authentication.

!!! note "Note"
    CodeIgniter's `SodiumHandler` uses `sodium_memzero` in every encryption or decryption session. After each session, the message (whether plaintext or ciphertext) and starter key are wiped out from the buffers. You may need to provide again the key before starting a new session.

### Message Length

An encrypted string is usually longer than the original, plain-text string (depending on the cipher).

This is influenced by the cipher algorithm itself, the initialization vector (IV) prepended to the cipher-text, and the HMAC authentication message that is also prepended. Furthermore, the encrypted message is also Base64-encoded so that it is safe for storage and transmission regardless of the character-set in use.

Keep this information in mind when selecting your data storage mechanism. Cookies, for example, can only hold 4K of information.

### Using the Encryption Service Directly

Instead of (or in addition to) using `Services` as described in `usage`, you can create an "Encrypter" directly, or change the settings of an existing instance.

```php
--8<--
libraries/encryption/009.php
--8<--
```

Remember, that `$config` must be an instance of `Config\Encryption` class.

## Class Reference

## CodeIgniter\Encryption

### Encryption

#### createKey(\[$length = 32])

- **Parameters**  

- **$length** `int` Output length

- **Returns**: A pseudo-random cryptographic key with the specified length, or `false` on failure

- **Return type**: `string`

Creates a cryptographic key by fetching random data from the operating system's sources (*i.e.* `/dev/urandom`).

#### initialize(\[Encryption $config = null])

- **Parameters**  

- Config\Encryption `$config` Configuration parameters

- **Returns**: `CodeIgniter\Encryption\EncrypterInterface` instance

- **Return type**: ``CodeIgniterEncryptionEncrypterInterface``

- **Throws**: `CodeIgniter\Encryption\Exceptions\EncryptionException`

Initializes (configures) the library to use different settings.

Example:

```php
--8<--
libraries/encryption/010.php
--8<--
```

Please refer to the `configuration` section for detailed info.

#### encrypt($data\[, $params = null])

- **Parameters**  

- **$data** `string` Data to encrypt

- **$params** `array|string|null` Configuration parameters (key)

- **Returns**: Encrypted data

- **Return type**: `string`

- **Throws**: `CodeIgniter\Encryption\Exceptions\EncryptionException`

Encrypts the input data and returns its ciphertext.

If you pass parameters as the second argument, the `key` element will be used as the starting key for this operation if `$params` is an array; or the starting key may be passed as a string.

If you are using the SodiumHandler and want to pass a different `blockSize` on runtime, pass the `blockSize` key in the `$params` array.

Examples:

```php
--8<--
libraries/encryption/011.php
--8<--
```

#### decrypt($data\[, $params = null])

- **Parameters**  

- **$data** `string` Data to decrypt

- **$params** `array|string|null` Configuration parameters (key)

- **Returns**: Decrypted data

- **Return type**: `string`

- **Throws**: `CodeIgniter\Encryption\Exceptions\EncryptionException`

Decrypts the input data and returns it in plain-text.

If you pass parameters as the second argument, the `key` element will be used as the starting key for this operation if `$params` is an array; or the starting key may be passed as a string.

If you are using the SodiumHandler and want to pass a different `blockSize` on runtime, pass the `blockSize` key in the `$params` array.

Examples:

```php
--8<--
libraries/encryption/012.php
--8<--
```
