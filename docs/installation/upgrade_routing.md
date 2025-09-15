# Upgrade Routing

- [Documentations](#documentations)
- [What has been changed](#what-has-been-changed)
- [Upgrade Guide](#upgrade-guide)
- [Code Example](#code-example)
    - [CodeIgniter Version 3.x](#codeigniter-version-3x)
    - [CodeIgniter Version 4.x](#codeigniter-version-4x)

## Documentations

- <a href="http://codeigniter.com/userguide3/general/routing.html" target="_blank">URI Routing Documentation CodeIgniter 3.X</a>

- [URI Routing Documentation CodeIgniter 4.X](#/incoming/routing)

## What has been changed

- In CI4 the Auto Routing is disabled by default.

- In CI4 the new more secure `auto-routing-improved` is introduced.

- In CI4 the routing is no longer configured by setting the routes as array.

- The Wildcard `(:any)` In CI3 will be the Placeholder `(:segment)` in CI4. The `(:any)` in CI4 matches multiple segements. See [URI Routing](#routing-placeholder-any).

## Upgrade Guide

1.  If you use the Auto Routing in the same way as CI3, you need to enable `auto-routing-legacy`.

2.  You have to change the syntax of each routing line and append it in **app/Config/Routes.php**. For example:

    > - `$route['journals'] = 'blogs';` to `$routes->add('journals', 'Blogs::index');`. This would map to the `index()` method in the `Blogs` controller.
    > - `$route['product/(:any)'] = 'catalog/product_lookup';` to `$routes->add('product/(:segment)', 'Catalog::productLookup');`. Don't forget to replace `(:any)` with `(:segment)`.
    > - `$route['login/(.+)'] = 'auth/login/$1';` to `$routes->add('login/(.+)', 'Auth::login/$1');`
    >
    > <div class="note">
    >
    > <div class="title">
    >
    > Note
    >
    > </div>
    >
    > For backward compatibility, `$routes->add()` is used here. But we strongly recommend to use `routing-http-verb-routes` like `$routes->get()` instead of `$routes->add()` for security.
    >
    > </div>

## Code Example

### CodeIgniter Version 3.x

Path: **application/config/routes.php**:

```php
--8<--
installation/upgrade_routing/ci3sample/001.php
--8<--
```

### CodeIgniter Version 4.x

Path: **app/Config/Routes.php**:

```php
--8<--
installation/upgrade_routing/001.php
--8<--
```

!!! note "Note"
    For backward compatibility, `$routes->add()` is used here. But we strongly recommend to use `routing-http-verb-routes` like `$routes->get()` instead of `$routes->add()` for security.
