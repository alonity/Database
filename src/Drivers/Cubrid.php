<?php

/**
 * Cubrid driver class
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
 * @version 1.1.0
 *
 */

namespace alonity\database\Drivers;

use alonity\database\Connection;
use alonity\database\DB;
use alonity\database\DriverInterface;
use alonity\database\QueryInheritance;

class Cubrid implements DriverInterface {

    /** @var object|null $connect */
    private $connect;

    private $connection, $error, $result;

    private $queries = [];

    public function __construct(Connection $connection){
        $this->connection = $connection;

        DB::$connectionsDriversInstances++;
    }

    public function constantBridge(int $value) : int {
        switch($value){
            case 0: return CUBRID_ASSOC;
            case 1: return CUBRID_NUM;
            case 2: return CUBRID_BOTH;
            case 3: return CUBRID_OBJECT;
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

        $connect = @cubrid_connect($con->getHost(), $con->getPort(), $con->getDatabase(), $con->getUsername(), $con->getPassword());

        $this->connect = false;

        if(!$connect){
            $this->setError("Connection error. Check out connection parameters ".@cubrid_error_msg());

            @cubrid_close($connect);

            return $this;

        }

        if(!@cubrid_client_encoding($connect)){
            $this->setError("Error set client encoding ".@cubrid_error_msg());

            @cubrid_close($connect);

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

    public function getInstance() {
        return $this->connect;
    }

    public function setError(string $error) : self {
        $this->error = $error;

        return $this;
    }

    public function getError() : ?string {
        return $this->error;
    }

    public function getResult() {
        return $this->result;
    }

    public function query(string $query) : bool {
        $query = str_replace('`', '"', $query);

        $this->connect();

        if(!$this->connect){ return false; }

        $this->queries[] = $query;

        $request = @cubrid_query($this->connect, $query);

        if(!$request){
            $this->setError("SQL Error: ".@cubrid_error_msg());

            return false;
        }

        $this->result = $request;

        return true;
    }

    /**
     *
     * @param QueryInheritance[] $queries
     *
     * @param int $flags
     *
     * @return bool
     */
    public function transaction(array $queries, int $flags = 0) : bool {

        $this->connect();

        if(!$this->connect){ return false; }

        $result = true;

        @cubrid_set_autocommit($this->connect, false);

        $this->queries[] = "SET autocommit = 0";

        foreach($queries as $query){
            if(!$query->execute()){ $result = false; break; }
        }

        if(!$result){
            @cubrid_rollback($this->connect);

            return false;
        }

        @cubrid_commit($this->connect);

        return true;
    }

    public function escape($value) : string {

        $this->connect();

        return !$this->connect ? '' : @cubrid_real_escape_string($this->connect, $value);
    }

    public function objects() : array {
        $res = $this->getResult();

        $list = [];

        if(!$res){ return $list; }

        while($ar = @cubrid_fetch_object($res)){ $list[] = $ar; }

        return $list;
    }

    public function assoc() : array {
        $res = $this->getResult();

        $list = [];

        if(!$res){ return $list; }

        while($ar = @cubrid_fetch_assoc($res)){ $list[] = $ar; }

        return $list;
    }

    public function array() : array {

        $res = $this->getResult();

        $list = [];

        if(!$res){ return $list; }

        while($ar = @cubrid_fetch_row($res)){ $list[] = $ar; }

        return $list;
    }

    public function row(int $type = 0) : array {
        $res = $this->getResult();

        return !$res ? [] : @cubrid_fetch_array($res, $this->constantBridge($type));
    }

    public function num() : int {
        $res = $this->getResult();

        return !$res ? 0 : @cubrid_num_rows($res);
    }

    public function affected() : int {
        $res = $this->getResult();

        return !$res ? 0 : @cubrid_affected_rows($res);
    }

    public function lastInsertID() : int {
        $res = $this->getResult();

        return !$res ? 0 : intval(@cubrid_insert_id($res));
    }
}

?>