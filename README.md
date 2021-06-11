# Database
Alonity database query builder

## Install

`composer require alonity/database`

### Examples
```php
use alonity\database\DB;

require('vendor/autoload.php');

// Add mysql connection settings
DB::connection('127.0.0.1', 'root', '', 'database');

// Add postgresql connection settings
DB::connection('127.0.0.1', 'postgres', 'postgres', 'database', 5432, 'utf8', 'PostgreSQL', 'pgskey'); // pgskey - connection name (key)

$select = DB::select("SELECT * FROM `mytable`");

if(!$select->execute()){
    echo $select->getError(); exit;
}

var_dump($select->assoc());
```

## Supported drivers

- MySQL
- Cubrid
- PostgreSQL
- SQLite