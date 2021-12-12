<?php defined('INIT') or die('NO INIT'); ?>

<?php
class User{
	private $Info = false;

	function __construct(){
		if(!isset($_SESSION)){ $this->__destruct(); return false; }
	}

	function Init(){
		$this->Info = $this->Get('info');
	}

	// get the user info, info[] or false or "timeout"
	function Get($what, $replace=false){
		switch (strtolower(trim($what))) {
			case 'status':
				if(!isset($_SESSION['account']) || !isset($_SESSION['timeout']) ){
					return "logout";
				}else if( (time()-$_SESSION['timeout']) > (60*60*16) ){
					return "timeout";
				}else{ return "login"; }
				return "error";

			break;case 'info':
				return (isset($_SESSION['account'])?$_SESSION['account']:$replace);

			break;case 'id':
				return (isset($this->Info['id'])?$this->Info['id']:$replace);
			
			break;case 'identity':
				return (isset($this->Info['identity'])?$this->Info['identity']:$replace);
			
			break;case 'username':case 'name':
				return (isset($this->Info['username'])?$this->Info['username']:$replace);

			break;case 'token':
				return (isset($_SESSION['token'])?$_SESSION['token']:$replace);
			
			break;default:
				return 'Error';
			break;
		}
	}

	// check the status
	function Is($what){
		switch (strtolower(trim($what))) {
			case 'login':
				return ($this->Get('status')=='login');

			case 'logout':
				return ($this->Get('status')!='login');
			
			break;case 'timeout':
				return ($this->Get('status')=='timeout');
			
			break;default:
				return 'Error';
			break;
		}
	}

	function Logout(){ session_destroy(); return true; }

}
