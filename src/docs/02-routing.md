# Routing

All routing goes through `Router::route($uri)`, called once from
`public/index.php`. It branches on HTTP method and returns a `RouterResponse`:

```php
class RouterResponse {
    public function __construct(
        public string $page,
        public string $method,
        public string $route,
        public array $data
    ) {}
}
```

## GET — resolving a page

```
GET /example
```

1. The URL path is trimmed of slashes to get the route name. An empty path
   (`/`) becomes `home`.
2. The route name is validated against `^[a-zA-Z0-9_\-]+$`. If it fails, the
   route is forced to `404`.
3. The router builds a path to `src/pages/{route}/page.php` and confirms via
   `realpath()` that it actually resolves *inside* `src/pages` (blocking
   `../` traversal). If the file doesn't exist or escapes the directory, the
   route falls back to `404`.
4. A `RouterResponse` is returned with `method: 'GET'`, the resolved `page`
   path, and the `route` name. `data` is always empty for GET — pages fetch
   their own data by calling their controller actions via `API.post()` after
   the page has loaded.

Because the route name is used later to build a namespace (see below), route
folder names should be simple lowercase words — dashes are technically
allowed by the routing regex, but namespace resolution (`ucfirst($route)`)
won't handle them the way you might expect, so stick to
`^[a-zA-Z][a-zA-Z0-9_]*$` in practice.

## POST — calling a controller action

```
POST /example?action=addMessage
POST /example?action=incrementReaction&component=reaction
```

POST requests don't render a page — they invoke one function inside a
controller and return its result as JSON. The flow:

1. The URL path (ignoring the query string) becomes `$route`, same rules as
   GET.
2. The query string is parsed for two parameters:
   - `action` — **required**. The controller function to call.
   - `component` — **optional**. If present, the request targets a
     component's controller instead of a page's.
3. `route`, `component`, and `action` are each validated against
   `^[a-zA-Z0-9_\-]+$`. Any failure throws an exception.
4. The controller file to load is chosen based on whether `component` was
   given:
   - No component: `src/pages/{route}/controller.php`
   - With component: `src/components/{component}/controller.php`
   Same `realpath()` containment check as GET — the resolved controller path
   must live inside `src/pages` or `src/components`.
5. The controller is `require_once`'d. Every controller file must declare a
   namespace and a constant allowlist:
   ```php
   namespace Pages\Example;        // or Components\Reaction
   const ALLOWED_ACTIONS = ['addMessage', 'loadMessages'];
   ```
   The router builds the namespace as `Pages\{ucfirst($route)}` or
   `Components\{ucfirst($component)}` and checks that
   `{Namespace}\ALLOWED_ACTIONS` is defined. If it isn't, or if the requested
   `action` isn't in that array, the request is rejected.
6. The JSON request body is decoded into an associative array and spread into
   the target function as **named arguments**:
   ```php
   $callable = strtolower($namespace . '\\' . $function);
   $callable(...$payload);
   ```
   This means the keys in the JSON payload sent from JavaScript must match
   the PHP function's parameter names exactly. For example:
   ```js
   API.post('addMessage', { body: 'hi' });
   ```
   ```php
   function addMessage(string $body) { ... }
   ```
   `{ body: 'hi' }` → `addMessage(body: 'hi')`. Mismatched keys will throw a
   PHP argument error.
7. The function's return value becomes `RouterResponse->data`, which
   `index.php` JSON-encodes straight to the browser.

## Security model at a glance

Routing is deliberately paranoid, since it's the only way client code reaches
server code:

- **Format whitelisting** — route, component, and action names must match
  `^[a-zA-Z0-9_\-]+$`. No slashes, dots, or special characters.
- **Path containment** — every resolved file path is checked with
  `realpath()` + `str_starts_with()` against its expected base directory, so
  a crafted route name can't escape `src/pages` or `src/components`.
- **Explicit allowlisting** — a controller function is only callable if its
  namespace defines `ALLOWED_ACTIONS` *and* the requested action appears in
  it. There is no "call any function in this file" fallback.

When writing a new controller, forgetting `ALLOWED_ACTIONS` (or forgetting to
add a new function to it) is the most common way an action silently fails
with "Invalid request" — check that first.
