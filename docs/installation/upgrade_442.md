# Upgrading from 4.4.1 to 4.4.2

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

### All Changes

This is a list of all files in the **project space** that received
changes; many will be simple comments or formatting that have no effect
on the runtime:

- app/Config/Migrations.php
- app/Config/View.php
- composer.json
