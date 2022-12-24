<?php
Inc::clas('db');

class Forum{
    static private $init = false; // if initialed
    static private $infinity = 2147483647; // prevent out of range
    static private $before, $after, $orderBy, $limit; // query args

    static function init(){
        if(self::$init){ return true; }
        self::resetArgs();
        self::$init = DB::connect() ? true : false;
        return self::$init;
    }
    static function isInit(){ return self::$init; }

    // reset args
    static function resetArgs(){
        // args
        self::$limit = 2;
        self::$before = self::$infinity;
        self::$after = 0;
        self::$orderBy = null;
    }

    // set args
    static function limit($num1, $num2=null){ self::init(); self::$limit = is_null($num2) ? $num1 : [$num1, $num2]; return self::class; }
    static function before($num){ self::init(); self::$before = $num; return self::class; }
    static function after($num){ self::init(); self::$after = $num; return self::class; }
    static function orderBy($field, $type){ self::init(); self::$orderBy = [$field, $type]; return self::class; }

    // get data
    static function getPosts($options=[]){
        if(!self::isInit()){ return false; };
        // get args
        $limit = self::$limit;
        $before = self::$before;
        $after = self::$after;
        $orderBy = self::$orderBy;
        // reset args
        self::resetArgs();

        $sql = "SELECT `post`.`id`, `post`.`content`, `post`.`poster`as`poster.id`, `post`.`datetime`
                , `account`.`username`as`poster.username`, `account`.`identity`as`poster.identity`
                , `profile`.`nickname`as`poster.nickname`, `profile`.`gender`as`poster.gender`, IFNULL(REPLACE(TO_BASE64(`profile`.`avatar`),'\n',''), NULL)as`poster.avatar`
                , COUNT(`post_edited`.`id`)as`edited.times`, MAX(`post_edited`.`datetime`)as`edited.last_datetime` 
                FROM `post` 
                LEFT JOIN `account` ON (`post`.`poster`=`account`.`id`) 
                LEFT JOIN `profile` ON (`post`.`poster`=`profile`.`id`) 
                LEFT JOIN `post_edited` ON (`post`.`id`=`post_edited`.`pid`) 
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
        $posts = DB::fetchAll();
        if(!$posts){ return null; }
        foreach ($posts as $key => $post){
            $posts[$key] = Arr::nd($post);
            $posts[$key]['content'] = htmlentities($post['content']);
        }
        return $posts;
    }

    static function getPost($pid){
        if(!self::init()){ return false; };
        // reset args
        self::resetArgs();

        $sql = "SELECT `post`.`id`, `post`.`content`, `post`.`poster`as`poster.id`, `post`.`datetime`
                , `account`.`username`as`poster.username`, `account`.`identity`as`poster.identity`
                , `profile`.`nickname`as`poster.nickname`, `profile`.`gender`as`poster.gender`, IFNULL(REPLACE(TO_BASE64(`profile`.`avatar`),'\n',''), NULL)as`poster.avatar`
                , COUNT(`post_edited`.`id`)as`edited.times`, MAX(`post_edited`.`datetime`)as`edited.last_datetime` 
                FROM `post` 
                LEFT JOIN `account` ON (`post`.`poster`=`account`.`id`) 
                JOIN `profile` ON (`post`.`poster`=`profile`.`id`) 
                LEFT JOIN `post_edited` ON (`post`.`id`=`post_edited`.`pid`) 
                WHERE `post`.`status`=:status AND `post`.`id`=:pid
                GROUP BY `post`.`id`
                LIMIT 1;
        ";
        // 
        DB::query($sql)::execute([
            ':status' => "alive",
            ':pid' => $pid
        ]);
        if(DB::error()){ return false; }
        $post = DB::fetch();
        if(!$post){ return null; }
        $post = Arr::nd($post);
        $post['content'] = htmlentities($post['content']);
        return $post;
    }

    // add data
    static function createPost($poster, $content){
        if(!self::init()){ return false; };
        $datetime = time();
        // create post
        $sql = "INSERT INTO `post` (`poster`, `content`, `datetime`) VALUES(:poster, :content, :datetime);";
        DB::query($sql)::execute([
            ':poster' => $poster,
            ':content' => $content,
            ':datetime' => $datetime,
        ]);
		if(DB::error()){ return false; }
        // done
        return DB::lastInsertId();
    }

    static function createComment($poster, $pid, $content){
        if(!self::init()){ return false; };
        $datetime = time();
        // create post
        $sql = "INSERT INTO `post` (`poster`, `pid`, `content`, `datetime`) VALUES(:poster, :pid, :content, :datetime);";
        DB::query($sql)::execute([
            ':poster' => $poster,
            ':pid' => $pid,
            ':content' => $content,
            ':datetime' => $datetime,
        ]);
		if(DB::error()){ return false; }
        // done
        return DB::lastInsertId();
    }

    // delete data
    static function deletePost($pid){
        if(!self::init()){ return false; }
        DB::beginTransaction();
        // delete post
		$sql = "UPDATE `post` SET `status`=:status WHERE `id`=:pid;";
        DB::query($sql)::execute([
            ':status' => 'removed',
            ':pid' => $pid,
        ]);
		if(DB::error()){ DB::rollback(); return false; }
        $rowCount = DB::rowCount();
        // delete comment
        $sql = "UPDATE `comment` SET `status`=:status WHERE `pid`=:pid;";
        DB::query($sql)::execute([
            ':status' => 'removed',
            ':pid' => $pid,
        ]);
		if(DB::error()){ DB::rollback(); return false; }
        // done
        DB::commit();
        return $rowCount;
    }
}