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
		$poster = $User->Get('id',false);
		if($User->Is('logout') || !$poster){ return false; }
		// filter
		// make some special chars be space " "
		$title = preg_replace('/[\n\r\t]/', ' ', trim($title)); 
		$content = preg_replace('/[\n\r\t]/', ' ', trim($content));
		// remove multiple spaces
		$title = preg_replace('/\s(?=\s)/', '', $title);
		$content = preg_replace('/\s(?=\s)/', '', $content);
		if(strlen($title)<2){ return false; }
		if(strlen($content)<2){ return false; }
		// start to create post
		$sql = "INSERT INTO `post`(`title`, `content`, `poster`, `datetime`) VALUES (:title, :content, :poster, :t)";
		$DB->Query($sql);
		$result = $DB->Execute([':title' => $title, ':content' => $content, ':poster' => $poster, 't' => time(),]);
		if(!$result){ return false; }
		return true;
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
				$limit = $args[1];
				$sql = "SELECT `id`,`title`,`content`,`poster`,`datetime` FROM `post` WHERE `status`='alive' ORDER BY `datetime` DESC LIMIT 5;";
				$DB->Query($sql);
				$result = $DB->Execute();
				if(!$result){ return false; } // error
				$row = $DB->FetchAll($result,'assoc');
				if(!$row){ return false; } // not found
				return $row;

			break;case 'posts_poster':
				$limit = $args[1];
				$sql = "SELECT `post`.`id`,`post`.`title`,`post`.`content`,`post`.`poster`,`post`.`datetime`,`account`.`username`as`poster_username`, `account`.`identity`as`poster_identity` FROM `post` INNER JOIN `account` ON (`post`.`poster`=`account`.`id`) WHERE `status`='alive' ORDER BY `post`.`datetime` DESC LIMIT 5;";
				$DB->Query($sql);
				$result = $DB->Execute();
				if(!$result){ return false; } // error
				$row = $DB->FetchAll($result,'assoc');
				if(!$row){ return false; } // not found
				return $row;
			
			break;default:
				return 'error';
			break;
		}
	}

}
