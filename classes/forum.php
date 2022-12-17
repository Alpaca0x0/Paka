<?php
Inc::clas('db');

class Forum{
    static private $infinity = 2147483647;
    static $fields, $limit, $before, $after, $orderBy;
 
    static function init(){
        self::reset();
        return DB::connect();
    }

    static function reset(){
        self::$fields = '*';
        self::$limit = 16;
        self::$before = self::$infinity;
        self::$after = 0;
        self::$orderBy = null;
    }

    static function select(){ self::$fields = implode(',', func_get_args()); return self::class; }
    static function limit(){ self::$limit = implode(',', func_get_args()); return self::class; }
    static function before($num){ self::$before = $num; return self::class; }
    static function after($num){ self::$after = $num; return self::class; }
    static function orderBy($field, $type){ self::$orderBy = [$field, $type]; return self::class; }

    static function getPosts(){
        $limit = self::$limit;
        $before = self::$before;
        $after = self::$after;
        $orderBy = self::$orderBy;
        self::reset();
        // 
        $sql = "SELECT `post`.`id`,`post`.`title`,`post`.`content`,`post`.`poster`,`post`.`datetime`,
                `account`.`username`as`poster_username`, `account`.`identity`as`poster_identity`,
                `profile`.`nickname`as`profile_nickname`, `profile`.`gender`as`profile_gender`, `profile`.`avatar`as`profile_avatar`,
                COUNT(`post_edited`.`id`)as`post_edited_times`, MAX(`post_edited`.`datetime`)as`post_edited_datetime` 
                FROM `post` 
                JOIN `account` ON (`post`.`poster`=`account`.`id`) 
                JOIN `profile` ON (`post`.`poster`=`profile`.`id`) 
                LEFT JOIN `post_edited` ON (`post`.`id`=`post_edited`.`post`) 
                WHERE `post`.`status`=:status AND `post`.`id` > :after AND `post`.`id` < :before
                GROUP BY `post`.`id`
        ";
        $sql .= is_null($orderBy) ? '' : " ORDER BY $orderBy[0] $orderBy[1] ";
        $sql .= " LIMIT {$limit}";
        $sql .= ';';
        // 
        DB::query($sql)::execute([
            ':status' => "alive",
            ':before' => $before,
            ':after' => $after,
        ]);
        if(DB::error()){ return false; }
        $row = DB::fetchAll();
        if(!$row){ return null; }
        return $row;
    }
}