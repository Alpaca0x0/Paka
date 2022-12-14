<?php
class Arr{
    static function every($array){
        $args = func_get_args();
        array_shift($args);
        foreach ($args as $arg) { if(!isset($array[$arg])) return false; }
        return true;
    }

    static function includes($array){
        $args = func_get_args();
        array_shift($args);
        foreach ($args as $arg) { if(isset($array[$arg])) return true; }
        return false;
    }
}