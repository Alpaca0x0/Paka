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
        $sql = "SELECT `post`.`id`,`post`.`content`,`post`.`poster`,`post`.`datetime`,
                `account`.`username`as`poster_username`, `account`.`identity`as`poster_identity`,
                `profile`.`nickname`as`profile_nickname`, `profile`.`gender`as`profile_gender`, `profile`.`avatar`as`profile_avatar`,
                COUNT(`post_edited`.`id`)as`post_edited_times`, MAX(`post_edited`.`datetime`)as`post_edited_datetime` 
                FROM `post` 
                JOIN `account` ON (`post`.`poster`=`account`.`id`) 
                JOIN `profile` ON (`post`.`poster`=`profile`.`id`) 
                LEFT JOIN `post_edited` ON (`post`.`id`=`post_edited`.`post`) 
                WHERE `post`.`status`=:status AND `post`.`id` > :after AND `post`.`id` < :before
                GROUP BY `post`.`id`";
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
        // 
        $ret = [];
        foreach ($row as $key => $val) {
            array_push($ret, [
                'id' => (int)$val['id'],
                'content' => $val['content'],
                'datetime' => $val['datetime'],
                'poster' => [
                    'id' => $val['poster'],
                    'username' => $val['poster_username'],
                    'identity' => $val['poster_identity'],
                    'nickname' => $val['profile_nickname'],
                    'gender' => $val['profile_gender'],
                    'avatar' => !is_null($val['profile_avatar'])?base64_encode($val['profile_avatar']):null,
                ],
                'edited' => [
                    'times' => $val['post_edited_times'],
                    'last_time' => $val['post_edited_datetime'],
                ],
            ]);
        }
        return $ret;
    }

    static function getPost($pid){
        $sql = "SELECT `post`.`id`,`post`.`content`,`post`.`poster`,`post`.`datetime`,
                `account`.`username`as`poster_username`, `account`.`identity`as`poster_identity`,
                `profile`.`nickname`as`profile_nickname`, `profile`.`gender`as`profile_gender`, `profile`.`avatar`as`profile_avatar`,
                COUNT(`post_edited`.`id`)as`post_edited_times`, MAX(`post_edited`.`datetime`)as`post_edited_datetime` 
                FROM `post` 
                JOIN `account` ON (`post`.`poster`=`account`.`id`) 
                JOIN `profile` ON (`post`.`poster`=`profile`.`id`) 
                LEFT JOIN `post_edited` ON (`post`.`id`=`post_edited`.`post`) 
                WHERE `post`.`status`=:status AND `post`.`id` = :pid
                GROUP BY `post`.`id`
                LIMIT 1;";
        // 
        DB::query($sql)::execute([
            ':status' => "alive",
            ':pid' => $pid,
        ]);
        if(DB::error()){ return false; }
        $post = DB::fetch();
        if(!$post){ return null; }
        // 
        $ret = [
            'id' => (int)$post['id'],
            'content' => $post['content'],
            'datetime' => $post['datetime'],
            'poster' => [
                'id' => $post['poster'],
                'username' => $post['poster_username'],
                'identity' => $post['poster_identity'],
                'nickname' => $post['profile_nickname'],
                'gender' => $post['profile_gender'],
                'avatar' => !is_null($post['profile_avatar'])?base64_encode($post['profile_avatar']):null,
            ],
            'edited' => [
                'times' => $post['post_edited_times'],
                'last_time' => $post['post_edited_datetime'],
            ],
        ];
        return $ret;
    }

    static function deletePost($pid){
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
        $sql = "UPDATE `comment` SET `status`=:status WHERE `post`=:pid;";
        DB::query($sql)::execute([
            ':status' => 'removed',
            ':pid' => $pid,
        ]);
		if(DB::error()){ DB::rollback(); return false; }
        // done
        DB::commit();
        return $rowCount;
    }

    static function createPost($poster, $content){
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
}