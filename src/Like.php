<?php

/**
 * Like class
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

class Like {
    public $data = '';

    public function __construct($data) {
        $this->data = $data;
    }

    public function get(DriverInterface $driver) : string {
        return addcslashes($driver->escape($this->data), '_%$[]');
    }
}

?>