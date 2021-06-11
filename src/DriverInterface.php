<?php

/**
 * Driver interface
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

interface DriverInterface {

    public function __construct(Connection $connection);

    public function getDriverName() : string;

    public function setConnection(Connection $connection) : self;

    public function getConnection() : Connection;

    public function getInstance();

    public function getResult();

    public function getError() : ?string;

    public function query(string $query) : bool;

    public function escape($value) : string;

    public function objects() : array;

    public function assoc() : array;

    public function array() : array;

    public function affected() : int;

    public function row(int $type = 0) : array;

    public function getQueriesNum() : int;

    public function getQueries() : array;

    public function num() : int;

    public function lastInsertID() : int;

    public function constantBridge(int $value) : int;
}

?>