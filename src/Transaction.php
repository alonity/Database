<?php

/**
 * Transaction database class
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

class Transaction extends ConnectorInheritance {

    private $queries, $flags;

    public function __construct(array $queries = [], int $flags = 0){
        $this->setQueries($queries)
            ->setFlags($flags);
    }

    public function append(QueryInheritance $query) : self {
        $this->queries[] = $query;

        return $this;
    }

    public function prepend(QueryInheritance $query) : self {
        array_unshift($this->queries, $query);

        return $this;
    }

    public function setQueries(array $queries) : self {
        $this->queries = $queries;

        return $this;
    }

    public function getQueries() : array {
        return $this->queries;
    }

    public function setFlags(int $flags) : self {
        $this->flags = $flags;

        return $this;
    }

    public function getFlags() : int {
        return $this->flags;
    }

    public function execute() : bool {

        $driver = $this->getDriver();

        if(!$driver){ return false; }

        $begin = $driver->transaction($this->getQueries(), $this->getFlags());

        if(!$begin){
            $this->setError($driver->getError());
        }

        return $begin;
    }
}

?>