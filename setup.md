# PHP + PostgreSQL Project Setup

## 1. Create project

```text
project/
├── public/
│   └── index.php
├── src/
│   ├── Database.php
│   └── config.php
└── .gitignore
```

## 2. Create database

```sql
CREATE DATABASE appdb;
```

## 3. Configure database

```php
// src/config.php

return [
    'host' => 'localhost',
    'port' => 5432,
    'dbname' => 'appdb',
    'user' => 'postgres',
    'password' => 'password',
];
```

## 4. Create PDO connection

```php
// src/Database.php

$config = require __DIR__ . '/config.php';

$pdo = new PDO(
    "pgsql:host={$config['host']};port={$config['port']};dbname={$config['dbname']}",
    $config['user'],
    $config['password'],
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]
);
```

## 5. Start PHP server

```bash
php -S localhost:8000 -t public
```

## 6. Test connection

```php
require '../src/Database.php';

echo "Connected!";
```