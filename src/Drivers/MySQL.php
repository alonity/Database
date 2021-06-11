<?php

/**
 * MySQL driver class
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

namespace alonity\database\Drivers;

use alonity\database\Connection;
use alonity\database\DB;
use alonity\database\DriverInterface;
use mysqli;
use mysqli_result;

class MySQL implements DriverInterface {

    /** @var mysqli|null $connect */
    private $connect;

    private $connection, $error, $result;

    private $queries = [];

    public function __construct(Connection $connection){
        $this->connection = $connection;

        DB::$connectionsDriversInstances++;
    }

    public function constantBridge(int $value) : int {
        switch($value){
            case 0: return MYSQLI_ASSOC;
            case 1: return MYSQLI_NUM;
            case 2: return MYSQLI_BOTH;
        }

        return $value;
    }

    public function getQueriesNum() : int {
        return count($this->queries);
    }

    public function getQueries() : array {
        return $this->queries;
    }

    public function connect() {

        if(!is_null($this->connect)){ return $this->connect; }

        $con = $this->getConnection();

        $connect = mysqli_init();

        $connect->options(MYSQLI_OPT_CONNECT_TIMEOUT, $con->getTimeout());

        $connect->options(MYSQLI_OPT_READ_TIMEOUT, $con->getTimeout());

        $connect->options(MYSQLI_SET_CHARSET_NAME, $con->getCharset());

        $this->connect = false;

        if(!@$connect->real_connect($con->getHost(), $con->getUsername(), $con->getPassword(), $con->getDatabase(), $con->getPort())){
            $this->setError("Connection error {$connect->connect_error} ({$connect->connect_errno})");

            @$connect->close();

            return $this;

        }

        if($connect->connect_errno){
            $this->setError("Connection error {$connect->connect_error} ({$connect->connect_errno})");

            return $this;
        }

        $this->connect = $connect;

        if(!$this->query("SET collation_connection = {$con->getCollation()}")){
            return $this;
        }

        DB::$connectionsNum++;

        return $this->connect;
    }

    public function getDriverName() : string {
        return str_replace(__NAMESPACE__.'\\', '', __CLASS__);
    }

    public function getConnection() : Connection {
        return $this->connection;
    }

    public function setConnection(Connection $connection) : DriverInterface {
        $this->connection = $connection;

        return $this;
    }

    public function getInstance(): ?mysqli {
        return $this->connect;
    }

    public function setError(string $error) : self {
        $this->error = $error;

        return $this;
    }

    public function getError() : ?string {
        return $this->error;
    }

    public function getResult(): ?mysqli_result {
        return $this->result;
    }

    public function query(string $query) : bool {

        $this->connect();

        if(!$this->connect){ return false; }

        $this->queries[] = $query;

        $request = $this->connect->query($query);

        if(!$request){
            $this->setError("SQL Error: {$this->connect->error}");

            return false;
        }

        $this->result = $request;

        return true;
    }

    public function escape($value) : string {

        $this->connect();

        return !$this->connect ? '' : $this->connect->escape_string($value);
    }

    public function objects() : array {
        $res = $this->getResult();

        $list = [];

        if(!$res){ return $list; }

        while($ar = $res->fetch_object()){ $list[] = $ar; }

        return $list;
    }

    public function assoc() : array {
        $res = $this->getResult();

        return !$res ? [] : $res->fetch_all(MYSQLI_ASSOC);
    }

    public function array() : array {
        $res = $this->getResult();

        return !$res ? [] : $res->fetch_all(MYSQLI_NUM);
    }

    public function row(int $type = 0) : array {
        $res = $this->getResult();

        return !$res ? [] : $res->fetch_array($this->constantBridge($type));
    }

    public function num() : int {
        $res = $this->getResult();

        return !$res ? 0 : $res->num_rows;
    }

    public function affected() : int {
        $this->connect();

        return !$this->connect ? 0 : $this->connect->affected_rows;
    }

    public function lastInsertID() : int {
        $this->connect();

        return !$this->connect ? 0 : $this->connect->insert_id;
    }
}

?>