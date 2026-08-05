# Pages

A page is a folder under `src/pages/{route}/`. The folder name is the URL
route (see **Routing**). A page typically contains:

```
src/pages/example/
├── page.php         # markup, rendered inside the layout
├── controller.php    # POST actions callable from this page
├── example.js
└── example.css
```

Only `page.php` is required. `controller.php` and asset files are added as
needed.

## `page.php`

This is your view. It's `require`'d by `src/layout/page/page.php`, which
buffers its output and wraps it in the shared HTML shell (head, header,
footer, script tags). Anything you `echo` or output with `?>...<?php` tags
becomes the page body.

At minimum, register the page's own CSS/JS by calling `Page::loadAssets()`
with its own directory:

```php
<?php
Page::loadAssets([
    __DIR__,
]);
?>

<div class="doc">
    <h1>Example Page</h1>
    ...
</div>
```

`Page::loadAssets()` doesn't render anything itself — it just registers files
so the layout can emit `<link>`/`<script>` tags for them later, in the
correct place in the document. See **Assets** for how this works.

## `controller.php`

Defines the functions your page's JavaScript can call over POST. It must
declare a `Pages\{Ucfirst(route)}` namespace and an `ALLOWED_ACTIONS`
constant listing every callable function name:

```php
<?php

namespace Pages\Example;

use Database;

const ALLOWED_ACTIONS = [
    'createExampleTable',
    'loadMessages',
    'addMessage',
    'togglePinMessage',
    'deleteMessage',
];

function loadMessages() {
    return Database::query("SELECT * FROM example_messages ORDER BY id DESC");
}

function addMessage(string $body, bool $pinned = false) {
    return Database::query("
        INSERT INTO example_messages (body, pinned)
        VALUES (:body, :pinned)
    ", ["body" => $body, "pinned" => $pinned]);
}
```

Each function's parameter names must match the keys of the JSON payload sent
from the client (see **Routing** for why). Whatever a function returns is
JSON-encoded and sent back to the browser as-is.

Controller files are **not** autoloaded — the router `require_once`'s the
specific controller file it needs at request time, based on the route/
component in the URL.

## Co-located JS/CSS

A page's `.js` and `.css` files sit right next to `page.php` and
`controller.php` in the same folder, and get served automatically once
`Page::loadAssets([__DIR__])` is called from `page.php`. There's no manual
`<script src="...">` — the layout emits the tag for you.

The JS typically wires DOM elements — bound however you'd normally do it in
jQuery, no special attribute required — to `API.post()` calls that match the
controller's `ALLOWED_ACTIONS`:

```js
$('#add-message-btn').on('click', async () => {
    const body = $('#example-message-input').val();
    await API.post('addMessage', { body });
});
```

See **The `API` Client Helper** for the full round trip.

## Worked example: `src/pages/example`

The `example` page in the framework is meant to be read end-to-end as a live
reference — it exercises components, the asset system, and the full
GET/POST cycle in one place:

- `page.php` renders two `card` components with different props, two
  `reaction` components, and a row of buttons demonstrating create/read/
  update/delete actions.
- `controller.php` defines `createExampleTable`, `loadMessages`,
  `addMessage`, `togglePinMessage`, and `deleteMessage` — a full CRUD set
  against a `example_messages` table.
- `example.js` binds each button to its matching `API.post()` call and
  re-renders the message list after each mutation.

Reading through those three files together is the fastest way to understand
how a page fits together.