<?php
Inc::clas('db');

class Forum{
    static private $init = false; // it's will mark true when inited
    static private $infinity = 2147483647; // prevent out of range
    static private $fields, $limit, $before, $after, $orderBy, $joinTables, $isHtml, $nl2br; // query args
    // fields white-list
    static private $allowedFields = [
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
        'comments' => [
            'id' => 'comment.id',
            'content' => 'comment.content',
        ]
    ];
 
    static function init($force=false){
        if(self::$init && !$force){ return true; }
        // self::reset(); // do not reset on here
        self::$init = DB::connect();
        return self::$init;
    }
    static function isInit(){ return self::$init; }

    static function reset(){
        self::$fields = [];
        self::$limit = 4;
        self::$before = self::$infinity;
        self::$after = 0;
        self::$orderBy = null;
        self::$joinTables = [];
        // self::$isHtml = false;
    }

    // setting fields for querying
    static function allFields(){
        self::$fields = [];
        foreach(self::$allowedFields as $table => $columnsAndSql){
            if(!array_key_exists($table, self::$fields)){ self::$fields[$table] = []; }
            array_push(self::$fields[$table], ...array_keys(self::$allowedFields[$table]));
        }
        return self::class;
    }
    // check if fields are allowed
    static function toAllowedFields($fields){
        $ret = [];
        foreach ($fields as $table => $columns) {
            // table
            $table = Type::string($table, null);
            if(is_null($table)){ return false; }
            if(!array_key_exists($table, self::$allowedFields)){ return false; }
            if(!array_key_exists($table, $ret)){ $ret[$table] = []; }
            // columns
            $columns = Type::array($columns, null);
            if(is_null($columns)){ return false; }
            if(!is_array($columns)){ $columns = [$columns]; }
            // column
            foreach($columns as $column) {
                $column = Type::string($column, null);
                if(is_null($column)){ return false; }
                if(!array_key_exists($column, self::$allowedFields[$table])){ return false; }
                // pass
                if(!array_key_exists($column, $ret[$table])){ array_push($ret[$table], $column); }  
            }
        }
        // pass
        return $ret;
    }
    static function fields($fields){ self::$fields = $fields; return self::class; }
    static function addField($key, $val){ self::$fields[$key] = $val; return self::class; }
    static function delField($table, $column=false){
        if($column){ unset(self::$fields[$table][$column]); }
        else{ unset(self::$fields[$table]); }
        return self::class;
    }
    // setting other args of querying
    static function limit(){ self::init(); self::$limit = implode(',', func_get_args()); return self::class; }
    static function before($num){ self::init(); self::$before = $num; return self::class; }
    static function after($num){ self::init(); self::$after = $num; return self::class; }
    static function orderBy($field, $type){ self::init(); self::$orderBy = [$field, $type]; return self::class; }
    static function isHtml($val=true){ self::$isHtml = $val; return self::class; }
    static function nl2br($val=true){ self::$nl2br = $val; return self::class; }
    // get fields
    static function getFields(){ return self::$fields; }
    static function getAllFields(){ return self::$allowedFields; }
    static function getFieldValue($table, $column){ return isset(self::$allowedFields[$table][$column]) ? self::$allowedFields[$table][$column] : false; }

    // select fields sentence convertor, it will return sql sentence
    static private function selectFields($fields){
        $sql = '';
        self::$joinTables = [];
        $fieldsString = '';
        foreach($fields as $table => $columns){
            if(!is_array($columns)){ $columns = [$columns]; }
            foreach($columns as $column){
                $tableColumnString = self::getFieldValue($table, $column);
                if(!$tableColumnString){ continue; }
                $fieldsString .= ", {$tableColumnString} AS `{$table}.{$column}`";
                $joinTable = explode('.',$tableColumnString)[0];
                $joinTable = strripos($joinTable, '(') ? substr($joinTable, strripos($joinTable, '(')+1) : $joinTable;
                if(!in_array($joinTable, self::$joinTables)){ array_push(self::$joinTables, $joinTable); }
            }
        } $fieldsString = trim($fieldsString, ',');
        $sql .= "SELECT {$fieldsString} "; 
        return $sql;
    }
    // join table sentence convertor, it will return sql sentence
    static private function joinTable($fields){
        $sql = '';
        $joinTable = [
            'account' => "LEFT JOIN `account` ON (`post`.`poster`=`account`.`id`)",
            'profile' => "LEFT JOIN `profile` ON (`post`.`poster`=`profile`.`id`)",
            'post_edited' => "LEFT JOIN `post_edited` ON (`post`.`id`=`post_edited`.`pid`)",
            'comments' => "LEFT JOIN `comment` ON (`post`.`id`=`comment`.`pid`)",
        ];
        $joinString = ' FROM `post`';
        foreach(self::$joinTables as $table){
            if(isset($joinTable[$table])){
                $joinString .= " $joinTable[$table]";
            }
        }
        $sql .= $joinString.' ';
        return $sql;
    }
    // return format convertor
    static private function returnFormat($post){
        $ret = [];
        foreach($post as $keys => $val){
            $key = explode('.', $keys);
            if(count($key)<2){ continue; }
            $ret[$key[0]][$key[1]] = $val;
        }
        // base64 avatar
        if(isset($ret['poster']['avatar'])){ 
            $ret['poster']['avatar'] = !is_null($ret['poster']['avatar'])?base64_encode($ret['poster']['avatar']):null;
        }
        // return html format content
        if(self::$isHtml && isset($ret['post']['content'])){
            $ret['post']['content'] = htmlentities($ret['post']['content']);
        }
        // return html format content
        if(self::$nl2br && isset($ret['post']['content'])){
            $ret['post']['content'] = nl2br($ret['post']['content']);
        }
        // *****************************************
        // extract key "post"
        if(isset($ret['post'])){
            foreach($ret['post'] as $key => $val){
                $ret[$key] = $val;
            } unset($ret['post']);
        }
        // return
        return $ret;
    }

    static function getPosts(){
        if(!self::init()){ return false; };
        // get args
        $fields = self::$fields;
        $limit = self::$limit;
        $before = self::$before;
        $after = self::$after;
        $orderBy = self::$orderBy;
        // reset args after execute the function
        self::reset();
        $sql = '';
        // must have post id
        $fields['post']['id'] = self::getFieldValue('post', 'id');
        // select fields
        $sql .= self::selectFields($fields);
        // join table
        $sql .= self::joinTable($fields);
        // where
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
            $ret = self::returnFormat($post);
            array_push($rets, $ret);
        }
        return $rets;
    }

    static function getPost($pid){
        if(!self::init()){ return false; }
        if(!DB::connect()){ return false; }
        // get args
        $fields = self::$fields;
        self::reset();
        $sql = '';
        // must have post id
        $fields['post']['id'] = self::getFieldValue('post', 'id');
        // select fields
        $sql .= self::selectFields($fields);
        // join table
        $sql .= self::joinTable($fields);
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
        $ret = self::returnFormat($post);
        return $ret;
    }

    static function getComments($pids){
        if(!self::init()){ return false; };
        // get args
        $pids = is_array($pids) ? $pids : [$pids];
        $fields = self::$fields;
        $limit = self::$limit;
        $before = self::$before;
        $after = self::$after;
        $orderBy = self::$orderBy;
        // reset args after execute the function
        self::reset();
        $sql = '';
        // must have post and comment id
        $fields['post']['id'] = self::getFieldValue('post', 'id');
        $fields['comment']['id'] = self::getFieldValue('comment', 'id');
        // select fields
        $sql .= self::selectFields($fields);
        // join table
        $sql .= self::joinTable($fields);
        // where
        $sql .= " WHERE `post`.`status`=:status AND `comment`.`status`=:status2 AND `comment`.`id` > :after AND `comment`.`id` < :before GROUP BY `post`.`id`";
        $sql .= is_null($orderBy) ? '' : " ORDER BY $orderBy[0] $orderBy[1] ";
        $sql .= " LIMIT {$limit}";
        $sql .= ';';
        return $sql;
        // 
        DB::query($sql)::execute([
            ':status' => "alive",
            ':status2' => "alive",
            ':before' => $before,
            ':after' => $after,
        ]);
        if(DB::error()){ return false; }
        $posts = DB::fetchAll();
        if(!$posts){ return null; }
        // 
        $rets = [];
        foreach ($posts as $post) {
            $ret = self::returnFormat($post);
            array_push($rets, $ret);
        }
        return $rets;
    }

    static function deletePost($pid){
        if(!self::init()){ return false; }
        if(!DB::connect()){ return false; }
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
}