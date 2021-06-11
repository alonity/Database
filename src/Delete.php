<?php

/**
 * Delete database class
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

class Delete extends QueryInheritance {
    public function affected() : int {

        $driver = $this->getDriver();

        return !$driver ? 0 : $driver->affected();
    }
}

?>