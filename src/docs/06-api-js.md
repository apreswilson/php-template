# The `API` Client Helper

`public/assets/API.js` is a tiny static class, loaded on every page, that
gives your page/component JavaScript a single way to call back into PHP
controller actions.

```js
class API {
    static post(action, payload = {}, component = null) {
        return $.ajax({
            type        : "POST",
            url         : `${window.location.href}?action=${action}${component ? `&component=${component}` : ``}`,
            contentType : "application/json",
            data        : JSON.stringify(payload),
        });
    }
}

window.API = API;
```

It's loaded globally as `window.API` (right after jQuery, before any page or
component scripts), so you can call `API.post(...)` from any page or
component JS file without importing anything.

## `API.post(action, payload, component)`

| Argument | Required | Description |
|---|---|---|
| `action` | Yes | Name of the controller function to call. Must be listed in that controller's `ALLOWED_ACTIONS`. |
| `payload` | No | Plain object, JSON-encoded and sent as the request body. Keys must match the target function's parameter names (see below). |
| `component` | No | Name of a component whose controller should handle the action, instead of the current page's controller. |

It returns a jQuery `Promise` (from `$.ajax`), so use `await` or `.then()`:

```js
const messages = await API.post('loadMessages');
```

```js
const result = await API.post('incrementReaction', { reactionId }, 'reaction');
```

## Payload keys must match parameter names

The router decodes the JSON payload and spreads it into your PHP function
using named arguments — not positional ones. That means:

```js
API.post('addMessage', { body: 'hello', pinned: true });
```

only works if the controller function's parameters are literally named
`$body` and `$pinned`:

```php
function addMessage(string $body, bool $pinned = false) { ... }
```

Extra keys not present as parameters, or misspelled keys, will throw a PHP
argument error rather than being silently ignored.

## Where the request goes

`API.post()` always posts to the **current URL** (`window.location.href`)
plus an `?action=` (and optional `&component=`) query string — it never
targets a different page's controller. If you're on `/example` and call
`API.post('addMessage')`, the request is `POST /example?action=addMessage`,
which the router resolves to `src/pages/example/controller.php`. Calling a
different page's action requires navigating there first (or explicitly
posting to a different path yourself, bypassing `API.post`).

## Typical pattern in a page or component script

`API` has no opinion about how you trigger a call — there's no attribute or
selector convention baked into `API.js` itself. Bind it to whatever DOM
elements you like, however you normally would with jQuery:

```js
(function () {
    async function loadMessages() {
        const messages = await API.post('loadMessages');
        // render messages into the DOM
    }

    $('#load-messages-btn').on('click', loadMessages);

    $('#add-message-btn').on('click', async () => {
        const body = $('#example-message-input').val();
        await API.post('addMessage', { body });
        await loadMessages();
    });
})();
```

Older examples in this codebase wire buttons up using a `data-action`
attribute (e.g. `$('[data-action="addMessage"]')`) whose value happens to
match the action name. That was never something `API.js` reads or requires
— it's legacy from an earlier pattern, not a framework convention. Any
selector (`id`, `class`, `data-*`, whatever fits your markup) works exactly
the same. Don't take `data-action` as the "correct" way to bind buttons if
you see it in older pages/components — it's just one option, no longer the
recommended one.

> **Note:** the framework's own comments flag that always posting to
> `window.location.href` and passing `component` as a separate string is a
> known rough edge the author is still thinking through — expect this API to
> evolve as routing conventions mature.