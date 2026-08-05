# Environment Variables

RootPHP loads a plain `vars.env` file into PHP's `$_ENV` superglobal at
bootstrap, via `EnvironmentVariables::loadEnvironmentVariables()`.

## File location and format

`vars.env` lives at the project root (a sibling of `public/` and `src/`), and
is loaded from `src/bootstrap.php`:

```php
EnvironmentVariables::loadEnvironmentVariables(__DIR__ . '/../vars.env');
```

The format is one `KEY=value` pair per line, no quotes, no sections:

```
db_type=pgsql
host=localhost
port=5432
db=rootphp
username=postgres
password=secret
```

This file typically holds secrets and machine-specific config, so it should
be excluded from version control (add it to `.gitignore`) with an example
template (e.g. `vars.env.example`) committed instead.

## How it's loaded

```php
class EnvironmentVariables {
    public static function loadEnvironmentVariables(string $file_path) {
        $vars = file($file_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($vars as $var) {
            [$key, $value] = explode('=', $var);
            $_ENV[$key] = $value;
        }
    }
}
```

- Reads the file line by line, skipping blank lines.
- Splits each line on the **first** `=` — `explode('=', $var)` without a
  limit actually splits on *every* `=`, so a value containing `=` will throw
  a "too many elements" error when destructured into `[$key, $value]`. Avoid
  `=` characters inside values.
- No comment syntax (`#` or `;` lines aren't ignored — they'll be parsed as
  literal keys/values, which is almost certainly not what you want).
- No whitespace trimming — `KEY = value` (with spaces around `=`) will
  produce a key literally named `KEY ` and a value literally `` value`` with
  a leading space. Write entries as `KEY=value` with no surrounding spaces.
- Values are always strings — there's no type coercion, so `Database`
  interpolates `$_ENV['port']` etc. directly as strings into the DSN.

## Using variables

Anywhere after bootstrap has run, read values straight from `$_ENV`:

```php
$dbHost = $_ENV['host'];
```

`Database` is the framework's only current consumer of `$_ENV`, reading
`db_type`, `host`, `port`, `db`, `username`, and `password` to build its PDO
connection string. Add whatever additional keys your own app needs — they'll
be available the same way.
