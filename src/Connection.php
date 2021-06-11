<?php

/**
 * Connection class
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

class Connection {
    private $host = '127.0.0.1';

    private $port = 3306;

    private $username = 'root';

    private $password = '';

    private $database = '';

    private $driverName = 'MySQL';

    private $charset = 'utf8mb4';

    private $collation = 'utf8mb4_unicode_ci';

    private $key, $error, $driver;

    private $timeout = 3;

    public function __construct(string $host = '127.0.0.1', string $username = 'root', string $password = '', string $database = '', int $port = 3306, string $charset = 'utf8mb4', string $driverName = 'MySQL', string $key = 'default'){
        $this->setDriverName($driverName)
            ->setHost($host)
            ->setUsername($username)
            ->setPassword($password)
            ->setPort($port)
            ->setCharset($charset)
            ->setDatabase($database);

        DB::$connectionInstances++;

        $this->key = $key;
    }

    public function setDriverName(string $name) : self {
        $this->driverName = $name;

        return $this;
    }

    public function getDriverName() : string {
        return $this->driverName;
    }

    public function setCharset(string $value) : self {
        $this->charset = $value;

        return $this;
    }

    public function getCharset() : string {
        return $this->charset;
    }

    public function setCollation(string $value) : self {
        $this->collation = $value;

        return $this;
    }

    public function getCollation() : string {
        return $this->collation;
    }

    public function setTimeout(int $seconds) : self {
        $this->timeout = $seconds;

        return $this;
    }

    public function getTimeout() : int {
        return $this->timeout;
    }

    public function setDriver(DriverInterface $driver) : self {
        $this->driver = $driver;

        return $this;
    }

    public function getDriver() : DriverInterface {
        if(!is_null($this->driver)){ return $this->driver; }

        $name = "alonity\\database\\Drivers\\{$this->getDriverName()}";

        /** @var $driver DriverInterface */
        $driver = new $name($this);

        return $this->setDriver($driver)->getDriver();
    }

    public function setHost(string $host) : self {
        $this->host = $host;

        return $this;
    }

    public function getHost() : string {
        return $this->host;
    }

    public function setUsername(string $username) : self {
        $this->username = $username;

        return $this;
    }

    public function getUsername() : string {
        return $this->username;
    }

    public function setPassword(string $password) : self {
        $this->password = $password;

        return $this;
    }

    public function getPassword() : string {
        return $this->password;
    }

    public function setPort(int $port) : self {
        $this->port = $port;

        return $this;
    }

    public function getPort() : int {
        return $this->port;
    }

    public function setDatabase(string $name) : self {
        $this->database = $name;

        return $this;
    }

    public function getDatabase() : string {
        return $this->database;
    }

    public function getkey() : string {
        return $this->key;
    }

    public function setError(string $error) : self {
        $this->error = $error;

        return $this;
    }

    public function getError() : ?string {
        return $this->error;
    }
}

?>