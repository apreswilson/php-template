# Getting Started

## Folder structure

```
project-root/
├── vars.env                    # environment variables (not committed)
├── public/                     # web server document root
│   ├── index.php               # front controller — entry point for every request
│   ├── asset.php                # serves .css/.js files out of src/
│   └── assets/
│       └── API.js              # client-side helper for calling controller actions
└── src/
    ├── bootstrap.php           # loads the autoloader + environment variables
    ├── autoload.php             # autoloads src/classes/*.php
    ├── classes/                 # core framework classes (autoloaded)
    │   ├── Router.php
    │   ├── RouterResponse.php
    │   ├── Page.php
    │   ├── Component.php
    │   ├── Props.php
    │   ├── Database.php
    │   └── EnvironmentVariables.php
    ├── layout/
    │   ├── page/page.php        # HTML shell wrapping every GET page
    │   ├── header/header.php
    │   └── footer/footer.php
    ├── pages/
    │   └── {route}/
    │       ├── page.php          # markup for this route
    │       ├── controller.php    # POST actions for this route
    │       ├── {route}.js
    │       └── {route}.css
    └── components/
        └── {name}/
            ├── component.php     # markup, receives $props
            ├── controller.php    # POST actions for this component
            ├── {name}.js
            └── {name}.css
```

Your web server's document root should point at `public/`. Everything under
`src/` is unreachable directly by URL — the only way into it is through
`index.php`, `asset.php`, or the router.

## `vars.env`

Sits at the project root, next to `public/` and `src/`. Simple `KEY=value`
pairs, one per line:

```
db_type=pgsql
host=localhost
port=5432
db=rootphp
username=postgres
password=secret
```

See **Environment Variables** for details and caveats on this format.

## Request lifecycle

Every request — page load or API call — enters through `public/index.php`:

```php
require_once __DIR__ . '/../src/bootstrap.php';

$response = Router::route($_SERVER['REQUEST_URI']);

if ($response->method === 'GET') {
    $page  = $response->page;
    $route = $response->route;
    require_once __DIR__ . '/../src/layout/page/page.php';
}

if ($response->method === 'POST') {
    header('Content-Type: application/json');
    echo json_encode($response->data);
}
```

1. `bootstrap.php` registers the autoloader and loads `vars.env` into `$_ENV`.
2. `Router::route()` inspects the HTTP method and URI and returns a
   `RouterResponse`.
3. **GET** requests hand off `$page` (a file path) and `$route` (the route
   name) to `src/layout/page/page.php`, which renders the full HTML document.
4. **POST** requests have already had their controller function executed
   inside the router — `index.php` just JSON-encodes whatever it returned.

Static assets (CSS/JS belonging to a page or component) are requested
separately by the browser via `<link>`/`<script>` tags pointing at
`public/asset.php`, not through this same flow — see **Assets**.

## Creating your first page

1. Create `src/pages/hello/page.php`:
   ```php
   <?php
   Page::loadAssets([__DIR__]);
   ?>
   <h1>Hello, RootPHP</h1>
   ```
2. Visit `/hello` in the browser. `Router` resolves the route, `page.php`
   requires your file, and the layout wraps it in the HTML shell.

No routes file, no registration step — the folder name **is** the route.
