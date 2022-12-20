<?php
Inc::clas('db');

class Forum{
    static private $infinity = 2147483647;
    static private $fields, $limit, $before, $after, $orderBy;
    static private $init = false;
    static private $whitelist = [
        'post' => [
            'id' => 'post.id',
            'content' => 'post.content', 
            'datetime' => 'post.datetime', 
        ],
        'poster' => [
            'id' => 'account.id',
            'username' => 'account.username',
            'identity' => 'account.identity',
            'nickname' => 'profile.nickname',
            'gender' => 'profile.gender',
            'avatar' => 'profile.avatar',
        ],
        'edited' => [
            'last_datetime' => 'MAX(post_edited.datetime)',
            'times' => 'COUNT(post_edited.id)',
        ],
    ];
 
    static function init($force=false){
        if(self::$init && !$force){ return true; }
        self::$whitelist[''] = self::$whitelist['post'];
        self::reset();
        self::$init = DB::connect();
        return self::$init;
    }

    static function isInit(){ return self::$init; }

    static function reset(){
        self::$fields = [];
        self::$limit = 16;
        self::$before = self::$infinity;
        self::$after = 0;
        self::$orderBy = null;
    }

    static function setFieldsAll(){
        self::$fields = self::getWhitelist();
        return self::class;
    }
    static function setFields($fields){ self::init(); self::$fields = $fields; return self::class; }
    static function addField($key, $val){ self::$fields[$key] = $val; return self::class; }
    static function delField($key){ unset(self::$fields[$key]); return self::class; }

    static function limit(){ self::init(); self::$limit = implode(',', func_get_args()); return self::class; }
    static function before($num){ self::init(); self::$before = $num; return self::class; }
    static function after($num){ self::init(); self::$after = $num; return self::class; }
    static function orderBy($field, $type){ self::init(); self::$orderBy = [$field, $type]; return self::class; }

    static function getWhitelist(){ return self::$whitelist; }
    static function getWhiteValue($table, $column){ return self::$whitelist[$table][$column]; }

    static function getPosts(){
        if(!self::isInit()){ return false; };
        $fields = self::$fields;
        $limit = self::$limit;
        $before = self::$before;
        $after = self::$after;
        $orderBy = self::$orderBy;
        self::reset();
        // 
        $sql = '';
        # setFields fields
        $tableNeed2Join = [];
        $fieldsString = '';
        foreach($fields as $gather => $junction){
            foreach($junction as $key => $val){
                $fieldsString .= ", {$val} AS `".($gather!==''?"{$gather}.":'')."{$key}`";
                $table = explode('.',$val)[0];
                $table = strripos($table, '(') ? substr($table, strripos($table, '(')+1) : $table;
                if(!in_array($table, $tableNeed2Join)){ array_push($tableNeed2Join, $table); }
            }
        } $fieldsString = trim($fieldsString, ',');
        // 
        $sql .= "SELECT {$fieldsString}"; 
        # join the table if using it
        $joinTable = [
            'account' => "LEFT JOIN `account` ON (`post`.`poster`=`account`.`id`)",
            'profile' => "LEFT JOIN `profile` ON (`post`.`poster`=`profile`.`id`)",
            'post_edited' => "LEFT JOIN `post_edited` ON (`post`.`id`=`post_edited`.`pid`)",
        ];
        $joinString = ' FROM `post`';
        foreach($tableNeed2Join as $table){
            if(isset($joinTable[$table])){
                $joinString .= " $joinTable[$table]";
            }
        }
        $sql .= $joinString;
        # where
        $sql .= " WHERE `post`.`status`=:status AND `post`.`id` > :after AND `post`.`id` < :before GROUP BY `post`.`id`";
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
        // 
        $rets = [];
        foreach ($posts as $post) {
            $ret = [];
            foreach($fields as $key => $nameAndVal){
                if($key !== '') { $ret[$key] = []; }
                foreach(array_keys($nameAndVal) as $name){
                    if($key === ''){ $ret[$name] = $post[$name]; }
                    else{ $ret[$key][$name] = $post["$key.$name"]; }
                }
            }
            // 
            if(isset($ret['poster'], $ret['poster']['avatar'])){ 
                $ret['poster']['avatar'] = !is_null($ret['poster']['avatar'])?base64_encode($ret['poster']['avatar']):null;
            }
            array_push($rets, $ret);
        }
        return $rets;
    }

    static function getPost($pid){
        if(!self::isInit()){ return false; };
        $fields = self::$fields;
        self::reset();
        // 
        $sql = '';
        # setFields fields
        $tableNeed2Join = [];
        $fieldsString = '';
        foreach($fields as $gather => $junction){
            foreach($junction as $key => $val){
                $fieldsString .= ", {$val} AS `".($gather!==''?"{$gather}.":'')."{$key}`";
                $table = explode('.',$val)[0];
                $table = strripos($table, '(') ? substr($table, strripos($table, '(')+1) : $table;
                if(!in_array($table, $tableNeed2Join)){ array_push($tableNeed2Join, $table); }
            }
        } $fieldsString = trim($fieldsString, ',');
        // 
        $sql .= "SELECT {$fieldsString}"; 
        # join the table if using it
        $joinTable = [
            'account' => "LEFT JOIN `account` ON (`post`.`poster`=`account`.`id`)",
            'profile' => "LEFT JOIN `profile` ON (`post`.`poster`=`profile`.`id`)",
            'post_edited' => "LEFT JOIN `post_edited` ON (`post`.`id`=`post_edited`.`pid`)",
        ];
        $joinString = ' FROM `post`';
        foreach($tableNeed2Join as $table){
            if(isset($joinTable[$table])){
                $joinString .= " $joinTable[$table]";
            }
        }
        $sql .= $joinString;
        # where
        $sql .= " WHERE `post`.`status`=:status AND `post`.`id` = :pid GROUP BY `post`.`id` LIMIT 1;";
        DB::query($sql)::execute([
            ':status' => "alive",
            ':pid' => $pid,
        ]);
        if(DB::error()){ return false; }
        $post = DB::fetch();
        if(!$post){ return null; }
        // 
        foreach($fields as $key => $nameAndVal){
            if($key !== '') { $ret[$key] = []; }
            foreach(array_keys($nameAndVal) as $name){
                if($key === ''){ $ret[$name] = $post[$name]; }
                else{ $ret[$key][$name] = $post["$key.$name"]; }
            }
        }
        // 
        if(isset($ret['poster'], $ret['poster']['avatar'])){ 
            $ret['poster']['avatar'] = !is_null($ret['poster']['avatar'])?base64_encode($ret['poster']['avatar']):null;
        }
        // 
        return $ret;
    }

    static function deletePost($pid){
        if(!self::isInit()){ return false; };
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
}