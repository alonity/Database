<?php

/**
 * In class
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

class In {
    public $list = '';

    public function __construct(array $list) {
        $this->list = $list;
    }

    public function get(DriverInterface $driver) : string {
        $filter = [];

        if(!$this->list){
            return "";
        }

        foreach($this->list as $v){
            $filter[] = "'".$driver->escape($v)."'";
        }

        return implode(',', $filter);
    }
}

?>