# Class Reference

Quick lookup for the core framework classes. All live in `src/classes/` and
are autoloaded by name (see **Autoloading**, below) — no `use` statement is
needed to reference them from unnamespaced code.

## `Router`

| Method | Description |
|---|---|
| `Router::route(string $url): RouterResponse` | Entry point called once from `index.php`. Dispatches to an internal `get()` or `post()` handler based on `$_SERVER['REQUEST_METHOD']` and returns a `RouterResponse`. |

See **Routing** for the full GET/POST resolution logic and security checks.

## `RouterResponse`

Plain value object returned by `Router::route()`.

```php
class RouterResponse {
    public function __construct(
        public string $page,   // GET: resolved page file path. POST: the raw request URL.
        public string $method, // 'GET', 'POST', or 'N/A' for unsupported methods
        public string $route,  // resolved route name (e.g. 'example', '404')
        public array $data     // POST: the controller function's return value. GET: always [].
    ) {}
}
```

## `Page`

| Method | Description |
|---|---|
| `Page::loadAssets(array $directories): void` | Scans each directory for `.css`/`.js` files and registers them in a static in-memory registry, keyed by `type` (parent folder name) and `name` (folder name). Safe to call repeatedly on the same directory — files aren't re-registered. |
| `Page::importAssets(string $type): void` | Echoes `<link>` tags (for `'css'`) or `<script>` tags (for `'js'`) for every asset currently in the registry. Called from `src/layout/page/page.php`. |

See **Assets** for the full registration/serving flow, including
`public/asset.php`.

## `Component`

| Method | Description |
|---|---|
| `Component::render(string $component_name, array $props = []): void` | Resolves `src/components/{name}/component.php`. If found, registers the component's assets via `Page::loadAssets()`, wraps `$props` in a `Props` object, and `require`s the file — echoing its output at the call site. |

See **Components**.

## `Props`

```php
class Props {
    public function __construct(public array $props = []) {}
    public function get(string $key, mixed $default = null): mixed;
}
```

Read-only wrapper passed to a component's `component.php` as `$props`. Use
`$props->get('key', $default)` rather than accessing `$props->props[...]`
directly.

## `Database`

| Method | Description |
|---|---|
| `Database::query(string $sql, array $params = []): array\|null` | The only method you should call directly. Runs `$sql` with named `$params`, auto-typed and bound. Appends `RETURNING *` to write queries that don't already have one. Returns rows as an array of associative arrays, or `null` on failure (logged via `error_log`). |
| `Database::getInstance(): Database` | Internal — lazily creates and caches the singleton PDO connection. Not meant to be called from application code. |

See **Database** for connection details and the `RETURNING *` behavior.

## `EnvironmentVariables`

| Method | Description |
|---|---|
| `EnvironmentVariables::loadEnvironmentVariables(string $file_path): void` | Reads a `KEY=value`-per-line file and populates `$_ENV`. Called once from `src/bootstrap.php` against the project's `vars.env`. |

See **Environment Variables** for format caveats.

## Autoloading

`src/autoload.php` registers a single `spl_autoload_register` callback:

```php
spl_autoload_register(function ($class) {
    $file = __DIR__ . '/classes/' . $class . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});
```

This only autoloads flat, unnamespaced classes living directly in
`src/classes/{ClassName}.php` — matched by class name to filename. It does
**not** autoload page or component controllers: those live under
`src/pages/*/controller.php` and `src/components/*/controller.php`, use
namespaces (`Pages\...`, `Components\...`), and are `require_once`'d
explicitly by `Router` at request time, not discovered by name. If you add a
new core class, drop it in `src/classes/` with a matching filename and it's
picked up automatically; no registration needed.

## Front-end pieces

| File | Description |
|---|---|
| `public/assets/API.js` | Global `window.API` with a single `API.post(action, payload, component)` method. See **The API Client Helper**. |
| jQuery 3.7.1 (CDN) | Loaded in `<head>` by `src/layout/page/page.php`. Available globally as `$` in every page/component script. |

## Layout

| File | Description |
|---|---|
| `src/layout/page/page.php` | The HTML shell wrapping every GET response: buffers the page's output, registers header/footer/page assets, emits `<head>` CSS, renders header → page content → footer, then loads `API.js` and emits closing `<script>` tags. |
| `src/layout/header/header.php` | Shared header markup, rendered inside the shell. |
| `src/layout/footer/footer.php` | Shared footer markup, rendered inside the shell. |
