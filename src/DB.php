<?php

/**
 * Database class
 *
 *
 * @author Qexy admin@qexy.org
 *
 * @copyright © 2021 Alonity
 *
 * @package alonity\database
 *
 * @license MIT
 *
 * @version 1.0.0
 *
 */

namespace alonity\database;

class DB {
    const VERSION = '1.0.0';

    const ARRAY_ASSOC = 0;

    const ARRAY_NUM = 1;

    const ARRAY_BOTH = 2;

    const ARRAY_OBJECT = 3;

    const TRANS_READ_ONLY = 4;

    const TRANS_READ_WRITE = 5;

    const TRANS_READ_SNAPSHOT = 6;

    public static $connections = [];

    public static $connectionInstances = 0;

    public static $connectionsNum = 0;

    public static $connectionsDriversInstances = 0;

    public static function connection(string $host = '127.0.0.1', string $username = 'root', string $password = '', string $database = '', int $port = 3306, string $charset = 'utf8mb4', string $driver = 'MySQL', string $key = 'default') : Connection {
        self::$connections[$key] = new Connection($host, $username, $password, $database, $port, $charset, $driver, $key);

        return self::$connections[$key];
    }

    public static function Like($data) : Like {
        return new Like($data);
    }

    public static function In(array $data) : In {
        return new In($data);
    }

    public static function Against(string $data) : Against {
        return new Against($data);
    }

    public static function select(string $query = '', array $prepared = []) : Select {
        return new Select($query, $prepared);
    }

    public static function insert(string $query = '', array $prepared = []) : Insert {
        return new Insert($query, $prepared);
    }

    public static function update(string $query = '', array $prepared = []) : Update {
        return new Update($query, $prepared);
    }

    public static function delete(string $query = '', array $prepared = []) : Delete {
        return new Delete($query, $prepared);
    }
}

?>