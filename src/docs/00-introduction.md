# RootPHP

RootPHP is a lightweight framework for bootstrapping PHP projects. It has no
build step, no templating language, and no package manager dependencies beyond
what PHP ships with. You write plain PHP, plain CSS, and plain JavaScript, and
the framework wires them together with a small set of conventions.

If you've used React or Vue, some of this will feel familiar: pages and
components are self-contained folders that own their own markup, styles, and
behavior. The difference is there's no build pipeline — everything renders on
the server, and the "componentization" is just PHP function calls and file
conventions, not a virtual DOM.

## Philosophy

- **Convention over configuration.** A page is a folder. A component is a
  folder. Naming and folder structure is how the framework finds things —
  there's no routes file to maintain by hand.
- **Co-location.** A page or component's PHP, JS, and CSS live in the same
  folder. You don't go hunting across `assets/`, `views/`, and `controllers/`
  directories for the three files that make up one feature.
- **No build step.** No bundler, no transpiler, no `node_modules`. Assets are
  served as-is through a small PHP script (`asset.php`).
- **Explicit over magic.** Every controller must explicitly list which of its
  functions are callable from the browser (`ALLOWED_ACTIONS`). Nothing is
  reachable by default.
- **Small surface area.** The whole framework is a handful of classes:
  `Router`, `Page`, `Component`, `Props`, `Database`, `EnvironmentVariables`,
  plus a `RouterResponse` value object.

## What RootPHP gives you

- A front-controller router that maps URLs to page files (GET) and to
  controller functions (POST), with path-traversal protection and an explicit
  allowlist for callable actions.
- A page/component system where each folder can register and serve its own
  CSS/JS automatically.
- A tiny client-side `API` helper (`API.post`) for calling back into PHP
  controller functions from JavaScript. How you trigger a call (which
  element, which event) is entirely up to your own script.
- A `Database` singleton wrapping PDO with named-parameter binding and
  automatic `RETURNING *` on writes.
- `.env`-style environment variable loading.

## What RootPHP doesn't do

- It doesn't include an ORM, migrations system, or query builder — you write
  SQL directly through `Database::query()`.
- It doesn't include client-side routing, state management, or a virtual DOM.
  jQuery is loaded by default for DOM interaction.
- It doesn't validate or sanitize your data for you beyond the security checks
  the router performs on route/action/component names.

## Where to go next

Start with **Getting Started** to see the folder structure and how a request
flows through the framework, then **Routing**, **Pages**, and **Components**
to understand the core building blocks.