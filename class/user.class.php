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
		switch ($what) {
			case 'status':
				if(!isset($_SESSION['account']) || !isset($_SESSION['spawntime']) ){
					return "logout";
				}else if( (time()-$_SESSION['spawntime']) > ($this->timeout) ){
					return "timeout";
				}else{ return "login"; }
				return "error";

			break;case 'session':
				return (isset($_SESSION['account'])?$_SESSION['account']:$replace);

			break;case 'id': case 'identity':
				return (isset($_SESSION['account'][$what])?$_SESSION['account'][$what]:$replace);

			break;case 'username': case 'name':
				return (isset($_SESSION['account']['username'])?$_SESSION['account']['username']:$replace);

			break;case 'token':
				return (isset($_SESSION['token'])?$_SESSION['token']:$replace);

			break;case 'spawntime':
				return (isset($_SESSION['spawntime'])?$_SESSION['spawntime']:$replace);

			break;case 'life':
				return (isset($_SESSION['spawntime'])?($this->timeout-(time()-$_SESSION['spawntime'])):$replace);
			
			// break;case 'users':
			// 	return (isset($_SESSION['spawntime'])?($this->timeout-(time()-$_SESSION['spawntime'])):$replace);

			break;case 'email':
				return (isset($this->Info['email'])?($this->Info['email']):$replace);

			break;case 'nickname':
				return (isset($this->Info['nickname']) && !is_null($this->Info['nickname'])?($this->Info['nickname']):$replace);

			break;case 'avatar':
				return (isset($this->Info['avatar']) && !is_null($this->Info['avatar'])?($this->Info['avatar']):$replace);

			break;case 'gender':
				return (isset($this->Info['gender'])?($this->Info['gender']):$replace);

			break;case 'birthday':
				return (isset($this->Info['birthday'])?($this->Info['birthday']):$replace);
			
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

	function Logout(){ @session_destroy(); return true; }

	function Update($force=false){
		// check if timeout
		if($this->Is('timeout')){ $this->Logout(); return 'timeout'; }
		// check $DB
		global $DB;
		if(!isset($DB)){ die('user.class: need to include DB class'); }
		// update from database
		if(!$this->isUpdated || $force){
			$id = $this->Get('id','');
			$username = $this->Get('username','');

			// account
			$DB->Query("SELECT `id`,`username`,`email`,`identity` FROM `account` WHERE `id`=:id AND `username`=:username;");
			$result = $DB->Execute([':id' => $id, ':username' => $username]);
			if($result===false){ $this->Logout(); return false; }
			$ac_row = $DB->Fetch($result,'assoc');
			if(!$ac_row){ $this->Logout(); return 'notfound'; }

			// profile
			$DB->Query("SELECT `id`,`nickname`,`gender`,`birthday`,`avatar` FROM `profile` WHERE `id`=:id;");
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
				'id' => $ac_row['id'],
				'email' => $ac_row['email'],
				'username' => $ac_row['username'],
				'identity' => $ac_row['identity'],
				'nickname' => $pf_row['nickname'],
				'gender' => $pf_row['gender'],
				'birthday' => $pf_row['birthday'],
				'avatar' => $pf_row['avatar'],
			];
			$_SESSION['account'] = [
			    'id' => $ac_row['id'],
			    'username' => $ac_row['username'],
			    'identity' => $ac_row['identity'],
			];
			$_SESSION['spawntime'] = time();
			$this->isUpdated = true;
		}
		return 'updated';
	}

}
