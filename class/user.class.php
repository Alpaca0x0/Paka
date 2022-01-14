<?php defined('INIT') or die('NO INIT'); ?>

<?php 
@include_once(Func('db'));
?>

<?php
class User{
	private $isUpdated = false;
	private $timeout = 0;
	
	private $Info = [];

	function __construct(){
		if(!isset($_SESSION)){ $this->__destruct(); return false; }
	}

	function Init($config=false){
		$this->timeout = $config['timeout'];
	}

	// get the user info, info[] or false or "timeout"
	function Get($what, $replace=false){
		$what = strtolower(trim($what));
		if($what==='name'){ $what='username'; }

		switch ($what) {
			case 'status':
				if(!isset($_SESSION['token'])){ return "logout"; }
				else if( time() > $this->Info['expire'] ){
					return "timeout";
				}else{ return "login"; }
				return "error";

			break;case 'id': case 'identity': case 'username': case 'spawntime': case 'email': case 'gender':
				return (isset($this->Info[$what])?$this->Info[$what]:$replace);

			break;case 'token':
				return (isset($_SESSION['token'])?$_SESSION['token']:$replace);

			break;case 'life':
				return (isset($this->Info['expire'])?($this->Info['expire']-time()):$replace);

			// options

			break;case 'nickname': case 'avatar': case 'birthday':
				return (isset($this->Info[$what]) && !is_null($this->Info[$what])?($this->Info[$what]):$replace);

			break;default:
				return 'error:user.class';
			break;
		}
	}

	// check the status
	function Is($what){
		$what = strtolower(trim($what));
		switch ($what) {
			case 'login':
				return ($this->Get('status')=='login');

			case 'logout':
				return ($this->Get('status')!='login');
			
			break;case 'timeout':
				return ($this->Get('status')=='timeout');
			
			break;default:
				return 'error';
			break;
		}
	}

	function Logout(){
		global $DB;
		// catch
		$token = $this->Get('token',false);
		if(!$token){ return false; }
		// destroy session
		@session_destroy();
		// logout token
		$DB->Query("UPDATE `account_event` SET `status`=:status WHERE `target`=:target AND `action`=:action;");
		$result = $DB->Execute([':status'=>'invalid', ':target'=>$token, ':action'=>'login', ]);
		if($result===false){ return false; }
		return true;
	}

	function Update($force=false){
		// check if updated
		if($this->isUpdated && !$force){ return 'updated'; }

		// DB
		global $DB;
		// get token
		$token = $this->Get('token',false);
		$datetime = time();
		// search token
		$DB->Query("
			SELECT `account_event`.`id` AS `event_id`, `account_event`.`account`, `account_event`.`expire`, `account_event`.`datetime` AS 'spawntime',
			`account`.`id`, `account`.`username`, `account`.`identity`, `account`.`email`, `account`.`status` 
			FROM `account_event` 
			JOIN `account` ON(`account`.id=`account_event`.`account`) 
			WHERE `account_event`.`status`=:status AND `account_event`.`target`=:target
			LIMIT 1;
		");
		$result = $DB->Execute([':status'=>'alive', ':target'=>$token]);
		if($result===false){ $this->Logout(); return false; }
		$ac_row = $DB->Fetch($result,'assoc');
		if(!$ac_row){ $this->Logout(); return 'notfound'; }
		// check if timeout
		if( $datetime > $ac_row['expire'] ){ $this->Logout(); return 'timeout'; }
		// check account status
		if(in_array($ac_row['status'], ['removed','review','unverified','invalid'])){ $this->Logout(); return $ac_row['status']; }
		else if($ac_row['status']==='alive'){ }
		else { $this->Logout(); return 'not_alive'; }

		// successfully
		$expire = time()+$this->timeout;
		// update expire
		$DB->Query('UPDATE `account_event` SET `expire`=:expire WHERE `id`=:event_id;');
		$result = $DB->Execute([':expire'=>(int)$expire, ':event_id'=>(int)$ac_row['event_id'], ]);
		if($result===false){ return 'cannot_update'; }

		$id = (int)$ac_row['id'];
		$username = $ac_row['username'];
		$identity = $ac_row['identity'];
		$email = $ac_row['email'];
		$status = $ac_row['status'];
		$spawntime = (int)$ac_row['spawntime'];

		// profile
		$DB->Query("SELECT `nickname`,`gender`,`birthday`,`avatar` FROM `profile` WHERE `id`=:id;");
		$result = $DB->Execute([':id' => $id]);
		if($result===false){ $this->Logout(); return false; }
		$pf_row = $DB->Fetch($result,'assoc');
		if(!$pf_row){
			// if does not exist profile, create it
			$DB->Query("INSERT INTO `profile`(`id`) VALUES(:id);");
			$result = $DB->Execute([':id' => $id]);
			if($result===false){ $this->Logout(); return false; }
			// query again
			$DB->Query("SELECT `id`,`nickname`,`gender`,`birthday`,`avatar` FROM `profile` WHERE `id`=:id;");
			$result = $DB->Execute([':id' => $id]);
			if($result===false){ $this->Logout(); return false; }
			$pf_row = $DB->Fetch($result,'assoc');
			if(!$pf_row){ $this->Logout(); return 'cannot_create'; }
		}
		//
		$this->Info = [
			'id' => $id,
			'email' => $email,
			'username' => $username,
			'identity' => $identity,
			'expire' => $expire,
			'status' => $status,
			'spawntime' => $spawntime,
			'nickname' => $pf_row['nickname'],
			'gender' => $pf_row['gender'],
			'birthday' => $pf_row['birthday'],
			'avatar' => $pf_row['avatar'],
		];
		// $_SESSION['account'] = [
		//     'id' => $ac_row['id'],
		//     'username' => $ac_row['username'],
		//     'identity' => $ac_row['identity'],
		// ];
		// $_SESSION['spawntime'] = time();

		$_SESSION['token'] = $token; // update session time
		$this->isUpdated = true;

		return 'updated';
	}

	function Clear(){
		global $DB;
		$datetime = time();
		//
		$DB->Query("UPDATE `account_event` SET `status`='invalid' WHERE `action`=:action AND :t>`expire`;");
		$result = $DB->Execute([':action'=>'login' ,':t' => $datetime]);
		// if($result===false){ return false; }
		$DB->Query("UPDATE `account` SET `status`='removed' WHERE `action`=:action AND :t>`expire`;");
		$result = $DB->Execute([':action'=>'login' ,':t' => $datetime]);

	}

}
