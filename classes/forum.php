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
    static function getPosts(){
        if(!self::isInit()){ return false; };
        // get args
        $limit = self::$limit;
        $before = self::$before;
        $after = self::$after;
        $orderBy = self::$orderBy;
        // reset args
        self::resetArgs();

        $sql = 'SELECT `post`.`id`, `post`.`content`, `post`.`poster`as`poster.id`, UNIX_TIMESTAMP(`post`.`datetime`)as`datetime`
                , `account`.`username`as`poster.username`, `account`.`identity`as`poster.identity`
                , `profile`.`nickname`as`poster.nickname`, `profile`.`gender`as`poster.gender`, IFNULL(REPLACE(TO_BASE64(`profile`.`avatar`),"\n",""), NULL)as`poster.avatar`
                , COUNT(`post_edited`.`id`)as`edited.times`, MAX(`post_edited`.`datetime`)as`edited.last_datetime` 
                , (SELECT COUNT(`comment`.`id`) FROM `comment` WHERE `comment`.`status`="alive" AND `comment`.`post`=`post`.`id`)as`comments.times`
                FROM `post` 
                LEFT JOIN `account` ON (`post`.`poster`=`account`.`id`) 
                LEFT JOIN `profile` ON (`post`.`poster`=`profile`.`id`) 
                LEFT JOIN `post_edited` ON (`post`.`id`=`post_edited`.`post`) 
                WHERE `post`.`status`=:status AND `post`.`id` > :after AND `post`.`id` < :before
                GROUP BY `post`.`id`
        ';
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
        return $posts;
    }

    static function getPost($pid){
        if(!self::init()){ return false; };
        // reset args
        self::resetArgs();

        $sql = 'SELECT `post`.`id`, `post`.`content`, `post`.`poster`as`poster.id`, UNIX_TIMESTAMP(`post`.`datetime`)as`datetime`
                , `account`.`username`as`poster.username`, `account`.`identity`as`poster.identity`
                , `profile`.`nickname`as`poster.nickname`, `profile`.`gender`as`poster.gender`, IFNULL(REPLACE(TO_BASE64(`profile`.`avatar`),"\n",""), NULL)as`poster.avatar`
                , COUNT(`post_edited`.`id`)as`edited.times`, MAX(`post_edited`.`datetime`)as`edited.last_datetime` 
                , (SELECT COUNT(`comment`.`id`) FROM `comment` WHERE `comment`.`status`="alive" AND `comment`.`post`=`post`.`id`)as`comments.times`
                FROM `post` 
                LEFT JOIN `account` ON (`post`.`poster`=`account`.`id`) 
                LEFT JOIN `profile` ON (`post`.`poster`=`profile`.`id`) 
                LEFT JOIN `post_edited` ON (`post`.`id`=`post_edited`.`post`) 
                WHERE `post`.`status`=:status AND `post`.`id`=:pid
                GROUP BY `post`.`id`
                LIMIT 1;
        ';
        // 
        DB::query($sql)::execute([
            ':status' => "alive",
            ':pid' => $pid
        ]);
        if(DB::error()){ return false; }
        $post = DB::fetch();
        if(!$post){ return null; }
        return $post;
    }

    static function getComments($pids){
        if(!self::isInit()){ return false; };
        // get args
        $limit = self::$limit;
        $before = self::$before;
        $after = self::$after;
        $orderBy = self::$orderBy;
        // reset args
        self::resetArgs();
        $pids = is_array($pids) ? $pids : [$pids];
        $pids = implode(", ", $pids);

        $sql = 'SELECT `comment`.`id`
                , `comment`.`content`
                , UNIX_TIMESTAMP(`comment`.`datetime`)as`datetime`
                , `comment`.`commenter`as`commenter.id`

                , `post`.`id` as `post`

                , `account`.`username`as`commenter.username`, `account`.`identity`as`commenter.identity`
                , `profile`.`nickname`as`commenter.nickname`, `profile`.`gender`as`commenter.gender`, IFNULL(REPLACE(TO_BASE64(`profile`.`avatar`),"\n",""), NULL)as`commenter.avatar`

                , COUNT(`reply`.`id`)as`replies.times`

                , COUNT(`comment_edited`.`id`)as`edited.times`, MAX(`comment_edited`.`datetime`)as`edited.last_datetime` 

                FROM `comment` 
                LEFT JOIN `post` ON (`comment`.`post`=`post`.`id`) 
                LEFT JOIN `account` ON (`comment`.`commenter`=`account`.`id`) 
                LEFT JOIN `profile` ON (`comment`.`commenter`=`profile`.`id`) 
                LEFT JOIN `comment_edited` ON (`comment`.`id`=`comment_edited`.`comment`) 

                LEFT JOIN `comment` as `reply` ON (`comment`.`id`=`reply`.`reply` AND `reply`.`status`="alive") 

                WHERE `comment`.`reply` IS NULL 
                AND `comment`.`status`="alive" 
                AND `comment`.`post` IN ('.$pids.')
                AND `comment`.`id` > :after AND `comment`.`id` < :before
                GROUP BY `comment`.`id`
        ';
        $sql .= is_null($orderBy) ? '' : " ORDER BY $orderBy[0] $orderBy[1] ";
        $sql .= " LIMIT {$limit}";
        $sql .= ';';
        // 
        DB::query($sql)::execute([
            ':before' => $before,
            ':after' => $after,
        ]);
        if(DB::error()){ return false; }
        $comments = DB::fetchAll();
        if(!$comments){ return null; }
        return $comments;
    }

    static function getComment($commentId){
        if(!self::init()){ return false; };
        // reset args
        self::resetArgs();

        $sql = 'SELECT `comment`.`id`
                , `comment`.`content`
                , UNIX_TIMESTAMP(`comment`.`datetime`)as`datetime`
                , `comment`.`commenter`as`commenter.id`

                , `post`.`id` as `post`

                , `account`.`username`as`commenter.username`, `account`.`identity`as`commenter.identity`
                , `profile`.`nickname`as`commenter.nickname`, `profile`.`gender`as`commenter.gender`, IFNULL(REPLACE(TO_BASE64(`profile`.`avatar`),"\n",""), NULL)as`commenter.avatar`

                , COUNT(`reply`.`id`)as`replies.times`

                , COUNT(`comment_edited`.`id`)as`edited.times`, MAX(`comment_edited`.`datetime`)as`edited.last_datetime` 

                FROM `comment` 
                LEFT JOIN `post` ON (`comment`.`post`=`post`.`id`) 
                LEFT JOIN `account` ON (`comment`.`commenter`=`account`.`id`) 
                LEFT JOIN `profile` ON (`comment`.`commenter`=`profile`.`id`) 
                LEFT JOIN `comment_edited` ON (`comment`.`id`=`comment_edited`.`comment`) 

                LEFT JOIN `comment` as `reply` ON (`comment`.`id`=`reply`.`reply` AND `reply`.`status`="alive") 

                WHERE `comment`.`id` = :commentId
                AND `comment`.`reply` IS NULL 
                AND `comment`.`status`="alive" 
                GROUP BY `comment`.`id`
                LIMIT 1
        ';
        // 
        DB::query($sql)::execute([
            ':commentId' => $commentId,
        ]);
        if(DB::error()){ return false; }
        $comment = DB::fetch();
        if(!$comment){ return null; }
        return $comment;
    }

    static function getReplies($commentIds){
        if(!self::isInit()){ return false; };
        // get args
        $limit = self::$limit;
        $before = self::$before;
        $after = self::$after;
        $orderBy = self::$orderBy;
        // reset args
        self::resetArgs();
        $commentIds = is_array($commentIds) ? $commentIds : [$commentIds];
        $commentIds = implode(", ", $commentIds);

        $sql = 'SELECT `reply`.`id`
                , `reply`.`content`
                , UNIX_TIMESTAMP(`reply`.`datetime`)as`datetime`
                , `reply`.`commenter`as`replier.id`

                , `post`.`id` as `post` 

                , `account`.`username`as`replier.username`, `account`.`identity`as`replier.identity`
                , `profile`.`nickname`as`replier.nickname`, `profile`.`gender`as`replier.gender`, IFNULL(REPLACE(TO_BASE64(`profile`.`avatar`),"\n",""), NULL)as`replier.avatar`

                , COUNT(`reply_edited`.`id`)as`edited.times`, MAX(`reply_edited`.`datetime`)as`edited.last_datetime` 

                FROM `comment` as `reply` 
                LEFT JOIN `post` ON (`reply`.`post`=`post`.`id`) 
                LEFT JOIN `account` ON (`reply`.`commenter`=`account`.`id`) 
                LEFT JOIN `profile` ON (`reply`.`commenter`=`profile`.`id`) 
                LEFT JOIN `comment_edited` as `reply_edited` ON (`reply`.`id`=`reply_edited`.`comment`) 

                WHERE `reply`.`reply` IN ('.$commentIds.')
                AND `reply`.`reply` IS NOT NULL 
                AND `reply`.`status`="alive" 
                AND `reply`.`id` > :after AND `reply`.`id` < :before
                GROUP BY `reply`.`id`
        ';
        $sql .= is_null($orderBy) ? '' : " ORDER BY $orderBy[0] $orderBy[1] ";
        $sql .= " LIMIT {$limit}";
        $sql .= ';';
        // 
        DB::query($sql)::execute([
            ':before' => $before,
            ':after' => $after,
        ]);
        if(DB::error()){ return false; }
        $replies = DB::fetchAll();
        if(!$replies){ return null; }
        return $replies;
    }

    static function getReply($replyId){
        if(!self::init()){ return false; };
        // reset args
        self::resetArgs();

        $sql = 'SELECT `reply`.`id`
            , `reply`.`content`
            , UNIX_TIMESTAMP(`reply`.`datetime`)as`datetime`
            , `reply`.`commenter`as`replier.id`

            , `post`.`id` as `post`

            , `account`.`username`as`replier.username`, `account`.`identity`as`replier.identity`
            , `profile`.`nickname`as`replier.nickname`, `profile`.`gender`as`replier.gender`, IFNULL(REPLACE(TO_BASE64(`profile`.`avatar`),"\n",""), NULL)as`replier.avatar`

            , COUNT(`reply_edited`.`id`)as`edited.times`, MAX(`reply_edited`.`datetime`)as`edited.last_datetime` 

            FROM `comment` AS `reply` 
            LEFT JOIN `post` ON (`reply`.`post`=`post`.`id`) 
            LEFT JOIN `account` ON (`reply`.`commenter`=`account`.`id`) 
            LEFT JOIN `profile` ON (`reply`.`commenter`=`profile`.`id`) 
            LEFT JOIN `comment_edited` AS `reply_edited` ON (`reply`.`id`=`reply_edited`.`comment`) 

            WHERE `reply`.`id` = :replyId
            AND `reply`.`reply` IS NOT NULL 
            AND `reply`.`status`="alive" 
            GROUP BY `reply`.`id`
            LIMIT 1
        ';
        // 
        DB::query($sql)::execute([
            ':replyId' => $replyId,
        ]);
        if(DB::error()){ return false; }
        $reply = DB::fetch();
        if(!$reply){ return null; }
        return $reply;
    }

    // add data
    static function createPost($poster, $content){
        if(!self::init()){ return false; };
        $datetime = date("Y-m-d H:i:s");
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

    static function createComment($commenter, $postId, $content){
        if(!self::init()){ return false; };
        $datetime = date("Y-m-d H:i:s");
        // create post
        $sql = "INSERT INTO `comment`(`commenter`, `post`, `content`, `datetime`) VALUES (:commenter, :postId, :content, :datetime)";
        DB::query($sql)::execute([
            ':commenter' => $commenter,
            ':postId' => $postId,
            ':content' => $content,
            ':datetime' => $datetime,
        ]);
		if(DB::error()){ return false; }
        // done
        return DB::lastInsertId();
    }

    static function createReply($commenter, $reply, $content){
        if(!self::init()){ return false; };
        $datetime = date("Y-m-d H:i:s");
        // check if post and comment is exist
        $postId = DB::query('SELECT `post` 
                    FROM `comment` 
                    WHERE `id`=:commentId AND `status`="alive" 
                    LIMIT 1
        ;')::execute([
            ':commentId' => $reply,
        ]);
        if(DB::error()){ return false; }
        if(!$postId){ return null; }

        // create post
        $sql = "INSERT INTO `comment`(`commenter`, `post`, `reply`, `content`, `datetime`) VALUES (:commenter, :postId, :reply, :content, :datetime)";
        DB::query($sql)::execute([
            ':commenter' => $commenter,
            ':postId' => $postId,
            ':reply' => $reply,
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

    static function deleteComment($commentId){
        if(!self::init()){ return false; }
        // delete comment
		$sql = 'UPDATE `comment` SET `status`="removed" WHERE `status`="alive" AND (`id`=:commentId OR `reply`=:commentId2);';
        DB::query($sql)::execute([
            ':commentId' => $commentId,
            ':commentId2' => $commentId,
        ]);
		if(DB::error()){ return false; }
        $rowCount = DB::rowCount();
        // done
        return $rowCount;
    }
}