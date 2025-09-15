# CodeIgniter Repositories

- [codeigniter4 organization](#codeigniter4-organization)
- [Composer Packages](#composer-packages)
- [CodeIgniter 4 Projects](#codeigniter-4-projects)

## codeigniter4 organization

The CodeIgniter 4 open source project has its own [GitHub organization](https://github.com/codeigniter4).

There are several development repositories, of interest to potential contributors:

<table style="width:99%;">
<colgroup>
<col style="width: 18%" />
<col style="width: 14%" />
<col style="width: 65%" />
</colgroup>
<thead>
<tr>
<th>Repository</th>
<th>Audience</th>
<th>Description</th>
</tr>
</thead>
<tbody>
<tr>
<td><a href="https://github.com/codeigniter4/CodeIgniter4">CodeIgniter4</a></td>
<td>contributors</td>
<td>Project codebase, including tests &amp; user guide sources</td>
</tr>
<tr>
<td><a href="https://github.com/codeigniter4/translations">translations</a></td>
<td>developers</td>
<td>System message translations</td>
</tr>
<tr>
<td><a href="https://github.com/CodeIgniter/coding-standard">coding-standard</a></td>
<td>contributors</td>
<td>Coding style conventions &amp; rules</td>
</tr>
<tr>
<td><a href="https://github.com/codeigniter4/devkit">devkit</a></td>
<td>developers</td>
<td>Development toolkit for CodeIgniter libraries and projects</td>
</tr>
<tr>
<td><a href="https://github.com/codeigniter4/settings">settings</a></td>
<td>developers</td>
<td>Settings Library for CodeIgniter 4</td>
</tr>
<tr>
<td><a href="https://codeigniter4.github.io/shield">shield</a></td>
<td>developers</td>
<td>Authentication and Authorization Library for CodeIgniter 4</td>
</tr>
<tr>
<td><a href="https://github.com/codeigniter4/tasks">tasks</a></td>
<td>developers</td>
<td>Task Scheduler for CodeIgniter 4</td>
</tr>
<tr>
<td><a href="https://github.com/codeigniter4/cache">cache</a></td>
<td>developers</td>
<td>PSR-6 and PSR-16 Cache Adapters for CodeIgniter 4</td>
</tr>
</tbody>
</table>

There are also several deployment repositories, referenced in the installation directions. The deployment repositories are built automatically when a new version is released, and they are not directly contributed to.

<table style="width:99%;">
<colgroup>
<col style="width: 18%" />
<col style="width: 14%" />
<col style="width: 65%" />
</colgroup>
<thead>
<tr>
<th>Repository</th>
<th>Audience</th>
<th>Description</th>
</tr>
</thead>
<tbody>
<tr>
<td><a href="https://github.com/codeigniter4/framework">framework</a></td>
<td>developers</td>
<td>Released versions of the framework</td>
</tr>
<tr>
<td><a href="https://github.com/codeigniter4/appstarter">appstarter</a></td>
<td>developers</td>
<td>Starter project (app/public/writable). Dependent on "framework"</td>
</tr>
<tr>
<td><a href="https://github.com/codeigniter4/userguide">userguide</a></td>
<td>anyone</td>
<td>Pre-built user guide</td>
</tr>
</tbody>
</table>

In all the above, the latest version of a repository can be downloaded by selecting the "releases" link in the secondary navbar inside the "Code" tab of its GitHub repository page. The current (in development) version of each can be cloned or downloaded by selecting the "Clone or download" dropdown button on the right-hand side if the repository homepage.

## Composer Packages

We also maintain composer-installable packages on [packagist.org](https://packagist.org/search/?query=codeigniter4). These correspond to the repositories mentioned above:

- [codeigniter4/framework](https://packagist.org/packages/codeigniter4/framework)

- [codeigniter4/appstarter](https://packagist.org/packages/codeigniter4/appstarter)

- [codeigniter4/translations](https://packagist.org/packages/codeigniter4/translations)

- [codeigniter/coding-standard](https://packagist.org/packages/codeigniter/coding-standard)

- [codeigniter4/devkit](https://packagist.org/packages/codeigniter4/devkit)

- [codeigniter4/settings](https://packagist.org/packages/codeigniter4/settings)

- [codeigniter4/shield](https://packagist.org/packages/codeigniter4/shield)

- [codeigniter4/cache](https://packagist.org/packages/codeigniter4/cache)

See the [Installation](#/installation/index) page for more information.

## CodeIgniter 4 Projects

We maintain a [codeigniter4projects](https://github.com/codeigniter4projects) organization on GitHub as well, with projects that are not part of the framework, but which showcase it or make it easier to work with!

<table style="width:99%;">
<colgroup>
<col style="width: 18%" />
<col style="width: 14%" />
<col style="width: 65%" />
</colgroup>
<thead>
<tr>
<th>Repository</th>
<th>Audience</th>
<th>Description</th>
</tr>
</thead>
<tbody>
<tr>
<td><a href="https://github.com/codeigniter4projects/website">website</a></td>
<td>developers</td>
<td>The codeigniter.com website, written in CodeIgniter 4</td>
</tr>
<tr>
<td><a href="https://github.com/codeigniter4projects/playground">playground</a></td>
<td>developers</td>
<td>Basic code examples in project form. Still growing.</td>
</tr>
</tbody>
</table>

These are not composer-installable repositories.
