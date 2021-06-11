<?php

/**
 * Query Inheritency database class
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

class QueryInheritance extends ConnectorInheritance {

    private $query = '';

    private $params = [];

    public function __construct(string $query, array $prepared = []){
        $this->setQuery($query)
            ->setPrepared($prepared);
    }


    public function setQuery(string $query) : self {
        $this->query = $query;

        return $this;
    }

    public function getQuery() : string {
        return $this->query;
    }


    public function setPrepared(array $params) : self {
        $this->params = $params;

        return $this;
    }

    public function getPrepared() : array {
        return $this->params;
    }

    public function buildQuery() : string {

        $driver = $this->getDriver();

        if(!$driver){ return $this->getQuery(); }

        $values = $this->getPrepared();

        if(empty($values)){
            return $this->getQuery();
        }

        $split = str_split($this->getQuery());

        $string = "";

        $i = 0;

        foreach($split as $char){
            if($char == '?' && isset($values[$i])){

                if($values[$i] instanceof Like || $values[$i] instanceof In || $values[$i] instanceof Against){
                    $string .= $values[$i]->get($driver);
                }else{
                    $string .= $driver->escape(strval($values[$i]));
                }

                $i++;
            }else{
                $string .= $char;
            }
        }

        return $string;
    }

    public function execute() : bool {

        $driver = $this->getDriver();

        if(!$driver){ return false; }

        $query = $this->buildQuery();

        if(!$driver->query($query)){
            $this->setError($driver->getError());

            return false;
        }

        return true;
    }
}

?>