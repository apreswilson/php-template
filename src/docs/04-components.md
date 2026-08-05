# Components

A component is a self-contained, reusable piece of UI under
`src/components/{name}/`:

```
src/components/reaction/
├── component.php     # markup, receives $props
├── controller.php     # POST actions scoped to this component
├── reaction.js
└── reaction.css
```

Unlike a page, a component isn't tied to a URL route — it's rendered
*inside* a page (or another component's markup) via `Component::render()`,
and can appear multiple times on the same page with different props each
time.

## Rendering a component

```php
Component::render('card', [
    "title" => "First Card",
    "body"  => "This card was rendered with one set of props."
]);

Component::render('card', [
    "title" => "Second Card",
    "body"  => "This is the same component, rendered again with different props."
]);
```

`Component::render($name, $props)`:

1. Resolves `src/components/{name}/component.php`.
2. If it exists, calls `Page::loadAssets()` on the component's own directory
   — so the component's CSS/JS is registered the first time it's rendered,
   even if it's rendered many times on the same page.
3. Wraps `$props` in a `Props` object and makes it available to
   `component.php` as `$props`.
4. `require`s `component.php`, which echoes its markup directly into the
   page at the point `Component::render()` was called.

## `component.php`

Receives a `Props` instance. Read values with `->get(key, default)`:

```php
<?php
/** @var Props $props */

$id    = $props->get('id');
$label = $props->get('label');
?>

<div class="reaction" data-reaction-id="<?= htmlspecialchars($id) ?>">
    <button data-action="incrementReaction" data-component="reaction"
            data-reaction-id="<?= htmlspecialchars($id) ?>">
        <span><?= htmlspecialchars($label) ?></span>
    </button>
</div>
```

A component can do anything a page's view can — including running its own
database queries directly, as `reaction`'s `component.php` does to read its
initial count before rendering.

## `controller.php` — component-scoped actions

Same shape as a page controller, but the namespace is
`Components\{Ucfirst(name)}`:

```php
<?php

namespace Components\Reaction;

use Database;

const ALLOWED_ACTIONS = ['incrementReaction'];

function incrementReaction(string $reactionId) {
    Database::query("
        INSERT INTO example_reactions (reaction_id, count)
        VALUES (:reaction_id, 1)
        ON CONFLICT (reaction_id)
        DO UPDATE SET count = example_reactions.count + 1
    ", ["reaction_id" => $reactionId]);

    $row = Database::query("
        SELECT count FROM example_reactions WHERE reaction_id = :reaction_id
    ", ["reaction_id" => $reactionId]);

    return ["count" => $row[0]["count"] ?? 0];
}
```

To call a component action from JS, pass the component name as the third
argument to `API.post()`:

```js
$(`[data-action="incrementReaction"]`).on('click', async function () {
    const reactionId = $(this).data('reaction-id');
    const result = await API.post('incrementReaction', { reactionId }, 'reaction');
    $(`.reaction[data-reaction-id="${reactionId}"] .reaction-count`).text(result.count);
});
```

This sends `POST /{current-route}?action=incrementReaction&component=reaction`,
which routes to `src/components/reaction/controller.php` instead of the
current page's own controller. See **Routing** for the full mechanics.

## Instances on the same page

Because props are passed per-call and the component's markup keys off
whatever identifying prop you give it (`id`, `reactionId`, etc.), the same
component can be rendered multiple times with independent state:

```php
Component::render('reaction', ["id" => "example-star", "label" => "Star This Page"]);
Component::render('reaction', ["id" => "example-fire", "label" => "This Is Fire"]);
```

Each instance tracks its own count, keyed by `id` in the database and in the
DOM (`data-reaction-id`).

## Components vs. pages — quick comparison

| | Page | Component |
|---|---|---|
| Tied to a URL route | Yes (folder name = route) | No |
| Entry file | `page.php` | `component.php` |
| Receives props | No | Yes (`Props` object) |
| Rendered by | Router (GET) | `Component::render()`, called from anywhere |
| Controller namespace | `Pages\{Route}` | `Components\{Name}` |
| POST URL shape | `?action=fn` | `?action=fn&component=name` |
| Can appear multiple times per page | No (one per route) | Yes |
