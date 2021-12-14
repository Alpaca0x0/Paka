<?php defined('INIT') or die('NO INIT'); ?>

<?php 
@include_once(Func('db'));
?>

<?php
class User{
	private $isUpdated = false;
	private $timeout = 0;

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

			break;case 'info':
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
			
			break;case 'users':
				return (isset($_SESSION['spawntime'])?($this->timeout-(time()-$_SESSION['spawntime'])):$replace);
			
			break;default:
				return 'error';
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
			$DB->Query("SELECT * FROM `account` WHERE `id`=:id AND `username`=:username;");
			$result = $DB->Execute([':id' => $id, ':username' => $username]);
			if(!$result){ $this->Logout(); return false; }
			$row = $DB->Fetch($result,'assoc');
			if(!$row){ $this->Logout(); return 'notfound'; }
			$_SESSION['account'] = [
			    'id' => $row['id'],
			    'username' => $row['username'],
			    'identity' => $row['identity'],
			];
			$_SESSION['spawntime'] = time();
			$this->isUpdated = true;
		}
		return 'updated';
	}

}
