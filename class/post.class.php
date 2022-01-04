<?php defined('INIT') or die('NO INIT'); ?>

<?php 
@include_once(Func('db'));
@include_once(Func('user'));
?>

<?php
class Post{

	function __construct(){ }

	function Init($config=false){ }

	function Create($title, $content){
		global $DB, $User;
		if($User->Is('logout')){ return 'logout'; }
		$poster = $User->Get('id',false);
		$poster_username = $User->Get('username',false);
		$poster_identity = $User->Get('identity',false);
		$poster_nickname = $User->Get('nickname',null);
		$poster_gender = $User->Get('gender',false);
		$poster_birthday = $User->Get('birthday',null);
		$poster_avatar = $User->Get('avatar',null);
		$datetime = time();
		
		// start to create post
		$sql = "INSERT INTO `post`(`title`, `content`, `poster`, `datetime`) VALUES (:title, :content, :poster, :t)";
		$DB->Query($sql);
		$result = $DB->Execute([':title' => $title, ':content' => $content, ':poster' => $poster, 't' => $datetime,]);
		if($result===false){ return 'error_insert'; }

		return [
			'id' => $DB->Connect->lastInsertId(),
			'title' => $title,
			'content' => $content,
			'poster' => [
				"id" => $poster,
				"username" => $poster_username,
				"identity" => $poster_identity,
				"nickname" => $poster_nickname,
				"gender" => $poster_gender,
				"birthday" => $poster_birthday,
				"avatar" => is_null($poster_avatar)?null:base64_encode($poster_avatar),
			],
			'datetime' => $datetime,
		];
	}

	function Edit($postId, $title, $content){
		global $DB, $User;
		if($User->Is('logout')){ return 'is_logout'; }
		$editor = $User->Get('id',false);
		$postId = (int)$postId;
		$datetime = time();
		if($postId<1){ return false; }
		// check access and get the old data
		$sql = "SELECT `title`,`content` FROM `post` WHERE `id`=:postId AND `poster`=:editor AND `status`='alive' LIMIT 1;";
		$DB->Query($sql);
		$result = $DB->Execute([':postId'=>$postId, ':editor'=>$editor, ]);
		if($result===false){ return false; }
		$row = $DB->Fetch($result,'assoc');
		if(!$row){ return 'access_denied'; }
		// if title and content is same as old
		if($title===$row['title'] && $content===$row['content']){ return 'chnaged_nothing'; }
		// log old post
		$sql = "INSERT INTO `post_edited`(`editor`, `post`, `title`, `content`, `datetime`) VALUES (:editor, :postId, :title, :content, :t)";
		$DB->Query($sql);
		$result = $DB->Execute([':editor' => $editor, ':postId' => $postId, ':title' => $row['title'], ':content' => $row['content'], ':t' => $datetime,]);
		if($result===false){ return false; }
		// update post
		$sql = "UPDATE `post` SET `title`=:title, `content`=:content WHERE `id`=:postId AND `poster`=:editor AND `status`='alive';";
		$DB->Query($sql);
		$result = $DB->Execute([':postId'=>$postId, ':editor'=>$editor, ':title'=>$title, ':content'=>$content, ]);
		if($result===false){ return false; }
		//
		return [
			'postId' => $postId,
			'title' => $title,
			'content' => $content,
			'last_time' => $datetime,
		];
	}

	function Comment($postId, $content, $reply=null){
		global $DB, $User;
		if($User->Is('logout')){ return 'logout'; }
		$commenter = $User->Get('id',false);
		$commenter_username = $User->Get('username',false);
		$commenter_identity = $User->Get('identity',false);
		$commenter_nickname = $User->Get('nickname',null);
		$commenter_gender = $User->Get('gender',false);
		$commenter_birthday = $User->Get('birthday',null);
		$commenter_avatar = $User->Get('avatar',null);
		$datetime = time();
		$reply = $reply;
		//check
		if(!$commenter) { return 'no_commenter'; }
		// start to reply
		$sql = "INSERT INTO `comment`(`post`, `content`, `reply`, `commenter`, `datetime`) VALUES (:postId, :content, :reply, :commenter, :t)";
		$DB->Query($sql);
		$result = $DB->Execute([':postId' => $postId, ':content' => $content, ':reply' => $reply, ':commenter' => $commenter, ':t' => $datetime,]);
		if($result===false){ return false; } // error

		return [
			'id' => (int)$DB->Connect->lastInsertId(),
			'post' => $postId,
			'content' => $content,
			'reply' => $reply,
			'commenter' => [
				"id" => $commenter,
				"username" => $commenter_username,
				"identity" => $commenter_identity,
				"nickname" => $commenter_nickname,
				"gender" => $commenter_gender,
				"birthday" => $commenter_birthday,
				"avatar" => is_null($commenter_avatar)?null:base64_encode($commenter_avatar),
			],
			'datetime' => $datetime,
		];
	}

	function Reply($postId, $replyTarget, $content){
		// remove the $postId, it should be auto search
		global $DB, $User;
		if($User->Is('logout')){ return 'logout'; }
		$commenter = $User->Get('id',false);
		$commenter_username = $User->Get('username',false);
		$commenter_identity = $User->Get('identity',false);
		$commenter_avatar = $User->Get('avatar',null);
		$datetime = time();
		$reply = (int)($replyTarget);
		//check
		if(!$commenter) { return 'no_commenter'; }
		// start to reply
		$sql = "INSERT INTO `comment`(`post`, `content`, `reply`, `commenter`, `datetime`) VALUES (:postId, :content, :reply, :commenter, :t)";
		$DB->Query($sql);
		$result = $DB->Execute([':postId' => $postId, ':content' => $content, ':reply' => $reply, ':commenter' => $commenter, ':t' => $datetime,]);
		if($result===false){ return 'unexpected'; } // error
		return [
			'id' => $DB->Connect->lastInsertId(),
			'post' => $postId,
			'content' => $content,
			'reply' => $reply,
			'datetime' => $datetime,
			'commenter' => $commenter,
			'commenter_username' => $commenter_username,
			'commenter_identity' => $commenter_identity,
			'commenter_avatar' => is_null($commenter_avatar)?null:base64_encode($commenter_avatar),
		];
	}

	function Remove($postId){
		global $DB;
		$postId = (int)$postId;
		if($postId<1){ return 'post_id_format_incorrect'; }
		// remove post
		$sql = "UPDATE `post` SET `status`='removed' WHERE `id`=:postId;";
		$DB->Query($sql);
		$result = $DB->Execute([':postId' => $postId,]);
		if($result===false){ return false; }
		// remove comment
		$sql = "UPDATE `comment` SET `status`='removed' WHERE `post`=:postId;";
		$DB->Query($sql);
		$result = $DB->Execute([':postId'=>$postId,]);
		if($result===false){ return false; }
		return 'success';
	}

	function RemoveComment($commentId){
		global $DB;
		$commentId = (int)$commentId;
		if($commentId<1){ return 'comment_id_format_incorrect'; }
		// remove comment
		$sql = "UPDATE `comment` SET `status`='removed' WHERE `id`=:commentId OR `reply`=:commentId2;";
		$DB->Query($sql);
		$result = $DB->Execute([':commentId'=>$commentId, ':commentId2'=>$commentId]);
		if($result===false){ return false; }
		return 'success';
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
				if($result===false){ return false; } // error
				$row = $DB->Fetch($result,'assoc');
				if(!$row){ return false; } // not found
				return $row;

			break;case 'poster':
				$postId = $args[1];
				$sql = "SELECT `id`,`username`,`identity`,`email` FROM `account` WHERE `id`=(SELECT `poster` FROM `post` WHERE `id`=:postId AND `status`='alive' LIMIT 1)";
				$DB->Query($sql);
				$result = $DB->Execute([':postId' => $postId]);
				if($result===false){ return false; } // error
				$row = $DB->Fetch($result,'assoc');
				if(!$row){ return false; } // not found
				return $row;

			break;case 'posts':
				if(isset($args[1])){ $limit = $args[1]; }
				else{ $limit = [0,5]; }
				$sql = "SELECT `id`,`title`,`content`,`poster`,`datetime` FROM `post` WHERE `status`='alive' ORDER BY `datetime` ASC LIMIT $limit[0],$limit[1];";
				$DB->Query($sql);
				$result = $DB->Execute();
				if($result===false){ return false; } // error
				$row = $DB->FetchAll($result,'assoc');
				if(!$row){ return false; } // not found
				return $row;

			break;case 'posts+':
				if(isset($args[1])){ $limit = $args[1]; }
				else{ $limit = [0,5]; }
				$sql = "SELECT `post`.`id`,`post`.`title`,`post`.`content`,`post`.`poster`,`post`.`datetime`,
				`account`.`username`as`poster_username`, `account`.`identity`as`poster_identity`,
				`profile`.`nickname`as`profile_nickname`, `profile`.`gender`as`profile_gender`, `profile`.`avatar`as`profile_avatar`,
				COUNT(`post_edited`.`id`)as`post_edited_times`, MAX(`post_edited`.`datetime`)as`post_edited_datetime` 
				FROM `post` 
				JOIN `account` ON (`post`.`poster`=`account`.`id`) 
				JOIN `profile` ON (`post`.`poster`=`profile`.`id`) 
				LEFT JOIN `post_edited` ON (`post`.`id`=`post_edited`.`post`) 
				WHERE `status`='alive' 
				GROUP BY `post`.`id`
				ORDER BY `post`.`datetime` DESC 
				LIMIT $limit[0],$limit[1];"; 
				$DB->Query($sql);
				$result = $DB->Execute();
				if($result===false){ return false; } // error
				$row = $DB->FetchAll($result,'assoc');
				if(!$row){ return false; } // not found
				return $row;

			break;case 'comment':
				$postId = $args[1];
				if(isset($args[2])){ $limit = $args[2]; }
				else{ $limit = [0,5]; }
				$sql = "SELECT `comment`.`id`, `comment`.`reply`, `comment`.`content`, `comment`.`commenter`, `comment`.`datetime`, `comment`.`post`,
				`account`.`username`as`commenter_username`, `account`.`identity`as`commenter_identity`,
				`profile`.`nickname`as`profile_nickname`, `profile`.`gender`as`profile_gender`, `profile`.`birthday`as`profile_birthday`, `profile`.`avatar`as`profile_avatar`
				FROM `comment` 
				JOIN `account` ON (`comment`.`commenter`=`account`.`id`) 
				JOIN `profile` ON (`comment`.`commenter`=`profile`.`id`) 
				WHERE `status`='alive' AND `post`=:postId ORDER BY `datetime` ASC LIMIT $limit[0],$limit[1];";
				$DB->Query($sql);
				$result = $DB->Execute([':postId'=>$postId,]);
				if($result===false){ return false; } // error
				$row = $DB->FetchAll($result,'assoc');
				if(!$row){ return false; } // not found
				return $row;

			break;case 'reply':
				$commentId = $args[1];
				if(isset($args[2])){ $limit = $args[2]; }
				else{ $limit = [0,5]; }
				$sql = "SELECT `comment`.`id`, `comment`.`reply`, `comment`.`content`, `comment`.`commenter`, `comment`.`datetime`, `comment`.`post`,
				`account`.`username`as`commenter_username`, `account`.`identity`as`commenter_identity`,
				`profile`.`nickname`as`profile_nickname`, `profile`.`gender`as`profile_gender`, `profile`.`birthday`as`profile_birthday`, `profile`.`avatar`as`profile_avatar`
				FROM `comment` 
				JOIN `account` ON (`comment`.`commenter`=`account`.`id`) 
				JOIN `profile` ON (`comment`.`commenter`=`profile`.`id`) 
				WHERE `status`='alive' AND `reply`=:commentId ORDER BY `datetime` ASC LIMIT $limit[0],$limit[1];";
				$DB->Query($sql);
				$result = $DB->Execute([':commentId'=>$commentId,]);
				if($result===false){ return false; } // error
				$row = $DB->FetchAll($result,'assoc');
				if(!$row){ return false; } // not found
				return $row;

			break;default:
				return 'error';
			break;
		}
	}

}
