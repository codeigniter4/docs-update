# Upgrading from 4.3.2 to 4.3.3

Please refer to the upgrade instructions corresponding to your
installation method.

- `Composer Installation App Starter Upgrading <app-starter-upgrading>`
- `Composer Installation Adding CodeIgniter4 to an Existing Project Upgrading <adding-codeigniter4-upgrading>`
- `Manual Installation Upgrading <installing-manual-upgrading>`

<div class="contents" local="" depth="2">

</div>

## Project Files

Some files in the **project space** (root, app, public, writable)
received updates. Due to these files being outside of the **system**
scope they will not be changed without your intervention.

There are some third-party CodeIgniter modules available to assist with
merging changes to the project space: [Explore on
Packagist](https://packagist.org/explore/?query=codeigniter4%20updates).

### Content Changes

The following files received significant changes (including deprecations
or visual adjustments) and it is recommended that you merge the updated
versions with your application:

#### Config

- app/Config/Encryption.php  
  - The missing property `$cipher` is added for CI3 Encryption
    compatibility. See `encryption-compatible-with-ci3`.

### All Changes

This is a list of all files in the **project space** that received
changes; many will be simple comments or formatting that have no effect
on the runtime:

- app/Common.php
- app/Config/Encryption.php
- composer.json
