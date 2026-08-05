# Assets

Pages and components co-locate their CSS/JS with their PHP files instead of
keeping them in a shared `public/css` or `public/js` folder. The `Page` class
and `public/asset.php` work together to make that safe and automatic.

## Registering assets — `Page::loadAssets()`

```php
Page::loadAssets([__DIR__]);
```

Call this from a `page.php` (with its own directory) or let
`Component::render()` call it for you on a component's directory. It:

1. Resolves each given directory with `realpath()`.
2. Infers a `type` (the parent folder name — `pages`, `components`, or
   `layout`) and a `name` (the folder itself — e.g. `example`, `reaction`)
   from the path.
3. Scans the directory for files and registers any `.js` or `.css` files it
   finds into an internal static registry, building a URL for each:
   ```
   /asset.php?file={filename}&type={pages|components|layout}&name={folder}
   ```

You can pass multiple directories at once — `src/layout/page/page.php` does
exactly this to register the page's own assets alongside the header and
footer's:

```php
Page::loadAssets([
    __DIR__ . '/../header',
    __DIR__ . '/../footer',
    __DIR__,
]);
```

The registry is static (shared across the whole request), so calling
`loadAssets()` multiple times on the same directory — e.g. rendering the same
component several times — won't register duplicate `<link>`/`<script>` tags.

## Emitting assets — `Page::importAssets()`

```php
Page::importAssets('css');   // in <head>
Page::importAssets('js');    // near the end of <body>
```

Walks the registry and echoes a `<link rel="stylesheet">` for every
registered CSS file, or a `<script src="...">` for every registered JS file.
This is called from `src/layout/page/page.php`, which controls exactly where
in the document these tags land — you don't call it yourself in a normal
page or component.

## Why the order matters

`src/layout/page/page.php` buffers your page's output *before* it registers
the layout's own assets:

```php
ob_start();
require $page;               // your page.php runs here — its Page::loadAssets() fires now
$content = ob_get_clean();

Page::loadAssets([
    __DIR__ . '/../header',
    __DIR__ . '/../footer',
    __DIR__,
]);
```

This ensures a page's (and any components it renders') CSS/JS get registered
into the shared registry before the layout imports everything into `<head>`
and `<body>`. If this order were reversed, a page's assets would either be
missing from the output or require a second, redundant require.

## Serving the files — `public/asset.php`

The actual bytes are served by a small standalone script, not through
`Router`:

```
GET /asset.php?type=pages&name=example&file=example.css
```

`asset.php`:

1. Requires `type`, `name`, and `file` to each be non-empty and match
   `^[a-zA-Z0-9_\-.]+$` — rejecting anything with slashes or other special
   characters (400 if not).
2. Builds `src/{type}/{name}/{file}` and resolves it with `realpath()`,
   confirming it's actually inside `src/` (404 if not — blocks traversal).
3. Checks the file extension against a whitelist:
   ```php
   const FILE_TYPE_WHITELIST = [
       'css' => 'text/css',
       'js'  => 'application/javascript',
   ];
   ```
   Anything else is rejected with 403.
4. Sets the appropriate `Content-Type` and streams the file contents.

Only `.css` and `.js` files are servable this way, and only from inside
`src/`. There's no caching layer — every request re-reads the file from
disk.

## Practical notes

- A page or component only needs to call `Page::loadAssets([__DIR__])` once,
  even if it has both a `.js` and `.css` file — `loadAssets()` picks up every
  recognized file extension in the directory in one pass.
- Files with extensions other than `.js`/`.css` in a page/component folder
  (like `controller.php` itself) are simply ignored by the registry and
  never exposed through `asset.php`.
- Because `type` is derived from the parent directory name, don't move
  `page.php`/`component.php` files outside their expected `src/pages/*` or
  `src/components/*` location — the asset URLs depend on that structure.
