<?php

/**
 * Against class
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

namespace alonity\database;

class Against {
    public $data;

    const NATURAL = 0;

    const NATURAL_EXPANSION = 1;

    const BOOLEAN = 2;

    const EXPANSION = 3;

    private $mode = 0;

    private $separator = '';

    private $borderLeft = '';

    private $borderRight = '';

    public function __construct($data, string $separator = '', int $mode = 0) {
        $this->data = $data;

        if(!is_array($this->data)){
            $this->data = preg_replace('/[^\w\']+/iu', ' ', $this->data);
            $this->data = preg_replace('/\s+/iu', ' ', $this->data);
            $this->data = explode(' ', $this->data);
        }

        $this->separator = $separator;

        $this->mode = $mode;
    }

    public function borders(string $left = null, string $right = '') : self {
        $this->borderLeft = $left;

        $this->borderRight = $right;

        return $this;
    }

    public function get(DriverInterface $driver) : string {

        if(empty($this->data)){ return ''; }

        $format = "";

        foreach($this->data as $v){
            $format .= $this->separator.$this->borderLeft.$v.$this->borderRight;
        }

        switch($this->mode){
            case 1: $mode = ' IN NATURAL LANGUAGE MODE WITH QUERY EXPANSION'; break;
            case 2: $mode = ' IN BOOLEAN MODE'; break;
            case 3: $mode = ' WITH QUERY EXPANSION'; break;

            default: $mode = ''; break;
        }

        return "'".$driver->escape(trim($format))."'{$mode}";
    }
}

?>