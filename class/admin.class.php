<?php defined('INIT') or die('NO INIT'); ?>

<?php 
@include_once(Func('db'));
?>

<?php
class Admin{
	private $isUpdated = false;
	private $User; // user id or username 
	private $Users = [];
	private $Count = [];

	function __construct(){	}

	function Init($config=false){ }

	function User($id){
		$this->User = $id;
		return $this;
	}

	// get the user info, info[] or false or "timeout"
	function Get($what){
		$what = strtolower(trim($what));
		$args = array_values(func_get_args());
		switch ($what) {
			case 'id':
				//
			break;case 'users':
				$config = [
					'order' => 'DESC',
					'limit' => '1',
					'before' => false,
					'after' => false,
				];
				$change = $args[1]?$args[1]:[];
				foreach ($change as $key => $val) { $config[$key] = $val; }
				//
				$order = strtoupper($config['order']);
				$order = in_array($order, ['DESC','ASC'])?$order:'DESC';
				$limit = $config['limit'];
				$before = $config['before'];
				$after = $config['after'];
				$compare = [
					'sign' => $before!==false?'<':'>',
					'value' => $before!==false?(int)$before:(int)$after,
				];
				//
				global $DB;
				$sql = "
					SELECT `account`.`id`, `account`.`username`, `account`.`email`, `account`.`identity`, `account`.`status`,
					`profile`.`nickname`, `profile`.`gender`, `profile`.`birthday`, `profile`.`avatar`,
					`account_event`.`datetime` as `register_time`  
					FROM `account` 
					LEFT JOIN `profile` ON(`account`.`id`=`profile`.`id`) 
					LEFT JOIN `account_event` ON(`account`.`id`=`account_event`.`account` AND `action`='register') 
					WHERE `account`.`id` $compare[sign] :comare_value
					ORDER BY `account`.`id` $order
					LIMIT $limit
				;";
				$DB->Query($sql);
				$result = $DB->Execute([':comare_value'=>$compare['value'], ]);
				if($result===false){ return false; }
				$rows = $DB->FetchAll($result,'assoc');
				if(!$rows){ return false; }
				// push
				$temp = [];
				foreach ($rows as $row) {
					$temp[$row['id']] = [
						'id' => $row['id'],
						'status' => $row['status'],
						'email' => $row['email'],
						'username' => $row['username'],
						'identity' => $row['identity'],
						'nickname' => $row['nickname'],
						'gender' => $row['gender'],
						'birthday' => $row['birthday'],
						'register_time' => $row['register_time'],
						'avatar' => $row['avatar']===null?null:base64_encode($row['avatar']),
					];
				}return $temp;
			
			break;default:
				return 'error:admin.class';
			break;
		}
	}

	// get data count
	function Count(){
		global $DB;
		$DB->Query("
			SELECT COUNT(`account`.`id`) as `total`, COUNT(if(`account`.`identity`='admin',true,null)) as `admin`,
				COUNT(if(`account`.`identity`='member',true,null)) as `member`, COUNT(if(`account`.`identity`='vip',true,null)) as `vip`
			FROM `account` 
			LEFT JOIN `profile` ON(`account`.`id`=`profile`.`id`) 
			LEFT JOIN `account_event` ON(`account`.`id`=`account_event`.`account` AND `action`='register') 
			WHERE 1;
		");
		$result = $DB->Execute();
		if($result===false){ return false; }
		$rows = $DB->Fetch($result,'assoc');
		if(!$rows){ return false; }
		return $rows;
	}

	// check the status
	// function Is($what){
	// 	$what = strtolower(trim($what));
	// 	switch ($what) {
	// 		case 'login':
	// 			return ($this->Get('status')=='login');

	// 		case 'logout':
	// 			return ($this->Get('status')!='login');
			
	// 		break;case 'timeout':
	// 			return ($this->Get('status')=='timeout');
			
	// 		break;default:
	// 			return 'error';
	// 		break;
	// 	}
	// }

	function Update($force=false){
		// update from database
		if(!$this->isUpdated || $force){
			// get users
			array_push($this->Users, $this->Get('Users'));
			$this->isUpdated = true;
		}
		return 'updated';
	}

}
