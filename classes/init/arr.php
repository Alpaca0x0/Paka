<?php
class Arr{
    static function every($array){
        $args = func_get_args();
        array_shift($args);
        foreach ($args as $arg) { if(!array_key_exists($arg, $array)) return false; }
        return true;
    }

    static function includes($array){
        $args = func_get_args();
        array_shift($args);
        foreach ($args as $arg) { if(array_key_exists($arg, $array)) return true; }
        return false;
    }
}