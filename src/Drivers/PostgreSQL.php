<?php

/**
 * PostgreSQL driver class
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

class PostgreSQL implements DriverInterface {

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
            case 0: return PGSQL_ASSOC;
            case 1: return PGSQL_NUM;
            case 2: return PGSQL_BOTH;
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

        $connectString = "host='{$con->getHost()}' port='{$con->getPort()}' dbname='{$con->getDatabase()}' user='{$con->getUsername()}' password='{$con->getPassword()}' connect_timeout='{$con->getTimeout()}' options='--client_encoding={$con->getCharset()}'";

        $connect = @pg_connect($connectString);

        $this->connect = false;

        if(!$connect){
            $this->setError("Connection error. Check out connection parameters ".@pg_last_error());

            @pg_close($connect);

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

        $request = @pg_query($this->connect, $query);

        if(!$request){
            $this->setError("SQL Error: ".@pg_last_error());

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

        if(!$this->query("BEGIN")){
            return false;
        }

        foreach($queries as $query){
            if(!$query->execute()){ break; }
        }

        if(!$this->query("COMMIT")){
            $this->query("ROLLBACK");
            return false;
        }

        return true;
    }

    public function escape($value) : string {

        $this->connect();

        return !$this->connect ? '' : @pg_escape_string($this->connect, $value);
    }

    public function objects() : array {
        $res = $this->getResult();

        $list = [];

        if(!$res){ return $list; }

        while($ar = @pg_fetch_object($res)){ $list[] = $ar; }

        return $list;
    }

    public function assoc() : array {
        $res = $this->getResult();

        return !$res ? [] : @pg_fetch_all($res, PGSQL_ASSOC);
    }

    public function array() : array {
        $res = $this->getResult();

        return !$res ? [] : @pg_fetch_all($res, PGSQL_NUM);
    }

    public function row(int $type = 0) : array {
        $res = $this->getResult();

        return !$res ? [] : @pg_fetch_array($res, 0, $this->constantBridge($type));
    }

    public function num() : int {
        $res = $this->getResult();

        return !$res ? 0 : @pg_num_rows($res);
    }

    public function affected() : int {
        $res = $this->getResult();

        return !$res ? 0 : @pg_affected_rows($res);
    }

    public function lastInsertID() : int {
        $res = $this->getResult();

        return !$res ? 0 : intval(@pg_last_oid($res)); // deprecated
    }
}

?>