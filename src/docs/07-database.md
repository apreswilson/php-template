# Database

`Database` is a small singleton wrapper around PDO. There's no separate
"connect" step to remember — you only ever call `Database::query()`.

```php
$rows = Database::query("SELECT * FROM example_messages ORDER BY id DESC");
```

## Connecting

The connection is created lazily, the first time `Database::query()` (via
`getInstance()`) is called, using these `$_ENV` variables (populated from
`vars.env` at bootstrap):

```php
new PDO(
    $_ENV['db_type'] . ":host=" . $_ENV['host'] . ";port=" . $_ENV['port'] . ";dbname=" . $_ENV['db'],
    $_ENV['username'],
    $_ENV['password'],
    [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]
);
```

The instance is cached in a private static property, so the whole request
(and process, if running under something long-lived like PHP-FPM's worker
reuse) shares a single connection. You never instantiate `Database` yourself
or call `getInstance()` directly in application code — `query()` is the only
entry point.

## `Database::query(string $sql, array $params = [])`

- **Named parameters.** Pass values as an associative array; keys correspond
  to `:name` placeholders in your SQL. Each value's PHP type
  (`bool`/`int`/`null`/anything else) is used to pick the matching
  `PDO::PARAM_*` constant automatically — you don't bind types yourself.
  ```php
  Database::query("
      UPDATE example_messages SET pinned = NOT pinned WHERE id = :id
  ", ["id" => $id]);
  ```
- **Return value.** Always an array of associative-array rows
  (`PDO::FETCH_ASSOC`), or `null` if the query threw a `PDOException` (the
  exception is logged with `error_log()`, not re-thrown). Check for `null`
  if a query might fail rather than assuming you'll always get an array.
- **Automatic `RETURNING *`.** If the SQL starts with `INSERT`, `UPDATE`,
  `DELETE`, or `MERGE` and doesn't already contain a `RETURNING` clause, the
  query has `\nRETURNING *` appended before it runs. This is why write
  operations in controllers can return the affected row(s) directly:
  ```php
  function addMessage(string $body, bool $pinned = false) {
      return Database::query("
          INSERT INTO example_messages (body, pinned)
          VALUES (:body, :pinned)
      ", ["body" => $body, "pinned" => $pinned]);
      // returns the newly inserted row, no separate SELECT needed
  }
  ```
  Because `RETURNING` isn't standard syntax in every database engine
  (Postgres supports it; MySQL and SQLite historically don't), this behavior
  assumes a Postgres-compatible database. If you configure RootPHP against a
  different engine, be aware this auto-append will break write queries and
  you'll need to adjust or bypass it.
- **No transactions or raw `exec()` helper.** Everything goes through
  `query()`. If you need multi-statement transactions, you'd currently need
  to extend `Database` yourself.

## Schema management

There's no migrations system. The framework's own example pattern is to run
`CREATE TABLE IF NOT EXISTS` directly from a controller action the first
time it's needed:

```php
function createExampleTable() {
    return Database::query("
        CREATE TABLE IF NOT EXISTS example_messages (
            id INTEGER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
            body TEXT NOT NULL,
            pinned BOOLEAN NOT NULL DEFAULT FALSE
        )
    ");
}
```

For anything beyond ad hoc `IF NOT EXISTS` tables, you'll want to manage
schema outside the framework (a plain `.sql` file, or your database's own
tooling).
