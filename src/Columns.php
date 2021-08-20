<?php

/**
 * Columns class
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

class Columns {
    private $list = [];

    public function __construct(string $name, array $list, ?string $prefix = null, bool $alias = false) {
        $this->list[$name] = [
            'list' => $list,
            'prefix' => $prefix,
            'alias' => $alias
        ];
    }

    public function set(string $name, array $list, ?string $prefix = null, bool $alias = false) : self {
        $this->list[$name] = [
            'list' => $list,
            'prefix' => $prefix,
            'alias' => $alias
        ];
    }

    public function render() : string {
        $columns = [];

        if(empty($this->list)){
            return implode(', ', $columns);
        }

        foreach($this->list as $block){
            foreach($block['list'] as $column){

                $set = "`{$column}`";

                if(!is_null($block['prefix'])){
                    $set = "`{$block['prefix']}`.{$set}";
                }

                if($block['alias']){
                    $set = "{$set} AS `{$block['prefix']}_{$column}`";
                }

                $columns[] = $set;
            }
        }

        return implode(', ', $columns);
    }

    public function export(string $name, array $assoc) : array {
        $columns = [];

        if(empty($this->list) || !isset($this->list[$name])){ return $columns; }

        foreach($this->list[$name]['list'] as $column){

            $colname = $this->list[$name]['alias'] ? "{$this->list[$name]['prefix']}_{$column}" : $column;

            $columns[$column] = $assoc[$colname] ?? "";
        }

        return $columns;
    }
}

?>