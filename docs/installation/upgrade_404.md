# Upgrading from 4.0.x to 4.0.4

Please refer to the upgrade instructions corresponding to your
installation method.

- `Composer Installation App Starter Upgrading <app-starter-upgrading>`
- `Composer Installation Adding CodeIgniter4 to an Existing Project Upgrading <adding-codeigniter4-upgrading>`
- `Manual Installation Upgrading <installing-manual-upgrading>`

<div class="contents" local="" depth="2">

</div>

CodeIgniter 4.0.4 fixes a bug in the implementation of
`Controller Filters </incoming/filters>`, breaking code implementing the
`FilterInterface`.

## Breaking Changes

### Update FilterInterface Declarations

The method signatures for `after()` and `before()` must be updated to
include `$arguments`. The function definitions should be changed from:

    public function before(RequestInterface $request)
    public function after(RequestInterface $request, ResponseInterface $response)

to:

    public function before(RequestInterface $request, $arguments = null)
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
