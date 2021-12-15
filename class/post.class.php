<?php defined('INIT') or die('NO INIT'); ?>

<?php 
@include_once(Func('db'));
@include_once(Func('user'));
?>

<?php
class Post{

	function __construct(){
		//
	}

	function Init($config=false){
		//
	}

	function Create($title, $content){
		global $DB, $User;
		if($User->Is('logout')){ return 'logout'; }
		$poster = $User->Get('id',false);
		$poster_username = $User->Get('username',false);
		$poster_identity = $User->Get('identity',false);
		$datetime = time();
		
		// start to create post
		$sql = "INSERT INTO `post`(`title`, `content`, `poster`, `datetime`) VALUES (:title, :content, :poster, :t)";
		$DB->Query($sql);
		$result = $DB->Execute([':title' => $title, ':content' => $content, ':poster' => $poster, 't' => $datetime,]);
		if(!$result){ return 'error_insert'; }
		return [
			'id' => $DB->Connect->lastInsertId(),
			'title' => $title,
			'content' => $content,
			'poster' => $poster,
			'datetime' => $datetime,
			'poster_username' => $poster_username,
			'poster_identity' => $poster_identity,
		];
	}

	function Reply($postId, $content, $reply=null){
		global $DB, $User;
		if($User->Is('logout')){ return 'logout'; }
		$replier = $User->Get('id',false);
		$replier_username = $User->Get('username',false);
		$replier_identity = $User->Get('identity',false);
		$datetime = time();
		$reply = $reply;
		//check
		if(!$replier) { return 'no_replier'; }
		// start to reply
		$sql = "INSERT INTO `reply`(`post`, `content`, `reply`, `replier`, `datetime`) VALUES (:postId, :content, :reply, :replier, :t)";
		$DB->Query($sql);
		$result = $DB->Execute([':postId' => $postId, ':content' => $content, ':reply' => $reply, ':replier' => $replier, ':t' => $datetime,]);
		if(!$result){ return $datetime; } // error
		return [
			'id' => $DB->Connect->lastInsertId(),
			'post' => $postId,
			'content' => $content,
			'reply' => $reply,
			'replier' => $replier,
			'datetime' => $datetime,
			'replier_username' => $replier_username,
			'replier_identity' => $replier_identity,
		];
	}

	function Remove($postId){
		global $DB, $User;
		$poster = $User->Get('id',false);
		if($User->Is('logout') || !$poster){ return false; }
		// filter
		// make some special chars be space " "
		$postId = (int)$postId;
		if($postId<1){ return false; }
		// check access
		$sql = "SELECT `poster` FROM `post` WHERE `id`=:postId AND `status`='alive';";
		$DB->Query($sql);
		$result = $DB->Execute([':postId' => $postId,]);
		if(!$result){ return false; }
		$row = $DB->Fetch($result,'assoc');
		if(!$row){ return false; }
		// start to remove post
		// $sql = "DELETE FROM `post` WHERE `post`.`id`=:postId";
		$sql = "UPDATE `post` SET `status` = 'removed' WHERE `post`.`id`=:postId;";
		$DB->Query($sql);
		$result = $DB->Execute([':postId' => $postId,]);
		if(!$result){ return false; }
		return true;
	}

	function Get($what){
		$what = strtolower(trim($what));
		$args = array_values(func_get_args());
		global $DB;
		switch ($what) {
			case 'info':
				$postId = $args[1];
				$sql = "SELECT * FROM `post` WHERE `id`=:postId";
				$DB->Query($sql);
				$result = $DB->Execute([':postId' => $postId]);
				if(!$result){ return false; } // error
				$row = $DB->Fetch($result,'assoc');
				if(!$row){ return false; } // not found
				return $row;

			break;case 'poster':
				$postId = $args[1];
				$sql = "SELECT `id`,`username`,`identity`,`email` FROM `account` WHERE `id`=(SELECT `poster` FROM `post` WHERE `id`=:postId AND `status`='alive' LIMIT 1)";
				$DB->Query($sql);
				$result = $DB->Execute([':postId' => $postId]);
				if(!$result){ return false; } // error
				$row = $DB->Fetch($result,'assoc');
				if(!$row){ return false; } // not found
				return $row;

			break;case 'posts':
				if(isset($args[1])){ $limit = $args[1]; }
				else{ $limit = [0,5]; }
				$sql = "SELECT `id`,`title`,`content`,`poster`,`datetime` FROM `post` WHERE `status`='alive' ORDER BY `datetime` DESC LIMIT $limit[0],$limit[1];";
				$DB->Query($sql);
				if(!$result = $DB->Execute()){ return false; } // error
				$row = $DB->FetchAll($result,'assoc');
				if(!$row){ return false; } // not found
				return $row;

			break;case 'posts_poster':
				if(isset($args[1])){ $limit = $args[1]; }
				else{ $limit = [0,5]; }
				$sql = "SELECT `post`.`id`,`post`.`title`,`post`.`content`,`post`.`poster`,`post`.`datetime`,`account`.`username`as`poster_username`, `account`.`identity`as`poster_identity` FROM `post` INNER JOIN `account` ON (`post`.`poster`=`account`.`id`) WHERE `status`='alive' ORDER BY `post`.`datetime` DESC LIMIT $limit[0],$limit[1];";
				$DB->Query($sql);
				$result = $DB->Execute();
				if(!$result){ return false; } // error
				$row = $DB->FetchAll($result,'assoc');
				if(!$row){ return false; } // not found
				return $row;
			
			break;case 'reply':
				$postId = $args[1];
				if(isset($args[2])){ $limit = $args[2]; }
				else{ $limit = [0,5]; }
				$sql = "SELECT `id`,`reply`,`content`,`replier`,`datetime`,`post` FROM `reply` WHERE `status`='alive' AND `post`=:postId ORDER BY `datetime` DESC LIMIT $limit[0],$limit[1];";
				$DB->Query($sql);
				if(!$result = $DB->Execute([':postId'=>$postId,])){ return false; } // error
				$row = $DB->FetchAll($result,'assoc');
				if(!$row){ return false; } // not found
				return $row;

			break;case 'reply_replier':
				$postId = $args[1];
				if(isset($args[2])){ $limit = $args[2]; }
				else{ $limit = [0,5]; }
				$sql = "SELECT `reply`.`id`, `reply`.`reply`, `reply`.`content`, `reply`.`replier`, `reply`.`datetime`, `reply`.`post`, `account`.`username`as`replier_username`, `account`.`identity`as`replier_identity` FROM `reply` INNER JOIN `account` ON (`reply`.`replier`=`account`.`id`) WHERE `status`='alive' AND `post`=:postId ORDER BY `datetime` DESC LIMIT $limit[0],$limit[1];";
				$DB->Query($sql);
				if(!$result = $DB->Execute([':postId'=>$postId,])){ return false; } // error
				$row = $DB->FetchAll($result,'assoc');
				if(!$row){ return false; } // not found
				return $row;

			break;default:
				return 'error';
			break;
		}
	}

}
