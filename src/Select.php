<?php

/**
 * Select database class
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

class Select extends QueryInheritance {
    public function objects() : array {

        $driver = parent::getDriver();

        return !$driver ? [] : $driver->objects();
    }

    public function assoc() : array {

        $driver = parent::getDriver();

        return !$driver ? [] : $driver->assoc();
    }

    public function array() : array {

        $driver = $this->getDriver();

        return !$driver ? [] : $driver->array();
    }

    public function row(int $type = 0) : array {

        $driver = $this->getDriver();

        return !$driver ? [] : $driver->row($type);
    }

    public function num() : int {
        $driver = $this->getDriver();

        return !$driver ? 0 : $driver->num();
    }

    public function count() : int {
        $rows = $this->row();

        if(empty($rows)){ return 0; }

        $keys = array_keys($rows);

        return intval($rows[$keys[0]]);
    }
}

?>