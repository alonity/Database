<?php

/**
 * Connector Inheritency database class
 *
 *
 * @author Qexy admin@qexy.org
 *
 * @copyright © 2022 Alonity
 *
 * @package alonity\database
 *
 * @license MIT
 *
 * @version 1.1.0
 *
 */

namespace alonity\database;

class ConnectorInheritance {

    private $error, $connection;

    private $connectionKey;

    public function use(string $connectionKey) : self {
        if(!isset(DB::$connections[$connectionKey])){
            $this->setError("Connection \"{$connectionKey}\" not found");

            return $this;
        }

        $this->connectionKey = $connectionKey;

        $this->connection = DB::$connections[$connectionKey];

        return $this;
    }

    public function setConnetion(Connection $connection) : self {
        $this->connection = $connection;

        $this->connectionKey = $connection->getkey();

        return $this;
    }

    public function getConnection() : ?Connection {
        if(!is_null($this->connection)){ return $this->connection; }

        if(empty(DB::$connections)){
            $this->setError("No connections found");

            return null;
        }

        $key = $this->connectionKey ?? DB::getConnectionKey();

        /** @var $connection Connection */
        $connection = DB::$connections[$key];

        $this->connection = $connection;

        return $this->connection;
    }


    public function setError(?string $error) : self {
        $this->error = $error;

        return $this;
    }

    public function getError() : ?string {
        return $this->error;
    }

    protected function getDriver() : ?DriverInterface {

        if(!empty($this->getError())){
            return null;
        }

        $connection = $this->getConnection();

        if(is_null($connection)){
            return null;
        }

        $driver = $connection->getDriver();

        if(is_null($driver)){
            $this->setError('Driver not found');

            return null;
        }

        return $driver;
    }
}

?>