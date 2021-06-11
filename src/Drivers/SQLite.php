<?php

/**
 * SQLite driver class
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
use SQLite3, SQLite3Result, Exception, stdClass;

class SQLite implements DriverInterface {

    /** @var SQLite3|null $connect */
    private $connect;

    private $connection, $error, $result;

    private $queries = [];

    public function __construct(Connection $connection){
        $this->connection = $connection;

        DB::$connectionsDriversInstances++;
    }

    public function constantBridge(int $value) : int {
        switch($value){
            case 0: return SQLITE3_ASSOC;
            case 1: return SQLITE3_NUM;
            case 2: return SQLITE3_BOTH;
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

        $encryption = empty($con->getPassword()) ? null : $con->getPassword();

        $error = '';

        try{
            $connect = new SQLite3($con->getHost()."\\{$con->getDatabase()}.db", SQLITE3_OPEN_READWRITE | SQLITE3_OPEN_CREATE, $encryption);
        }catch (Exception $e){
            $error = $e->getMessage();
        }

        if(!empty($error)){
            $this->setError("Connection error {$error}");

            if(isset($connect) && is_object($connect)){
                @$connect->close();
            }

            return $this;
        }

        $this->connect = false;

        if(!isset($connect) || !@$connect){
            $this->setError("Error connection to sqlite3");

            if(isset($connect) && is_object($connect)){
                @$connect->close();
            }

            return $this;

        }

        $this->connect = $connect;

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

    public function getInstance() : ?SQLite3 {
        return $this->connect;
    }

    public function setError(string $error) : self {
        $this->error = $error;

        return $this;
    }

    public function getError() : ?string {
        return $this->error;
    }

    public function getResult() : ?SQLite3Result {
        return $this->result;
    }

    public function query(string $query) : bool {

        $this->connect();

        if(!$this->connect){ return false; }

        $this->queries[] = $query;

        $request = $this->connect->query($query);

        if(!$request){
            $this->setError("SQL Error: {$this->connect->lastErrorMsg()}");

            return false;
        }

        $this->result = $request;

        return true;
    }

    public function escape($value) : string {
        $value = str_replace("\0", '', $value);

        return SQLite3::escapeString($value);
    }

    // polyfill
    public function objects() : array {
        $res = $this->getResult();

        $list = [];

        if(!$res){ return $list; }

        $class = new stdClass();

        $columns = [];

        for($i = 0; $i < $res->numColumns(); $i++){
            $columns[$i] = $res->columnName($i);
        }

        while($ar = $res->fetchArray(SQLITE3_NUM)){
            foreach($columns as $k => $v){
                $class->{$v} = $ar[$k];
            }

            $list[] = $class;
        }

        return $list;
    }

    public function assoc() : array {
        $res = $this->getResult();

        $list = [];

        if(!$res){ return $list; }

        while($ar = $res->fetchArray(SQLITE3_ASSOC)){ $list[] = $ar; }

        return $list;
    }

    public function array() : array {

        $res = $this->getResult();

        $list = [];

        if(!$res){ return $list; }

        while($ar = $res->fetchArray(SQLITE3_NUM)){ $list[] = $ar; }

        return $list;
    }

    public function row(int $type = 0) : array {
        $res = $this->getResult();

        return !$res ? [] : $res->fetchArray($this->constantBridge($type));
    }

    public function num() : int {
        $res = $this->getResult();

        $i = 0;

        if(!$res){ return $i; }

        while($res->fetchArray(SQLITE3_ASSOC)){ $i++; }

        return $i;
    }

    public function affected() : int {
        $this->connect();

        return !$this->connect ? 0 : $this->connect->changes();
    }

    public function lastInsertID() : int {
        $this->connect();

        return !$this->connect ? 0 : $this->connect->lastInsertRowID();
    }
}

?>