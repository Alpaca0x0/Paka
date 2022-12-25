<?php
Inc::clas('db');

class User{
	static private $isUpdated = false;
	static private $isCleared = false;
	static private $token = false;
	static private $user = false;

	static function get($key,$replace=false){ self::update(); return isset(self::$user[$key]) ? self::$user[$key] : $replace; }

	static function isLogin(){ self::update(); return self::get('id')?true:false; }

	static function clear($force=false){
		if(self::$isCleared && !$force){ return true; }
		self::$isCleared = true;

		# must connect database
		if(!DB::connect()){ return false; }

		$datetime = date("Y-m-d H:i:s");

		# update accounts info
		# register, unverified and expire
		DB::query(
			'UPDATE `account` 
			LEFT JOIN `account_event` ON(`account`.`id`=`account_event`.`uid`)
			SET `account`.`status`="removed"
			WHERE `account`.`status`="unverified" AND `account_event`.`commit`="register" AND :datetime>`account_event`.`expire`;'
		)::execute([':datetime' => $datetime]);
		# token expire
		DB::query(
			'UPDATE `account_event` 
			SET `expire`=0-`expire` 
			WHERE `expire`>0 AND :datetime>`expire`;'
		)::execute([':datetime' => $datetime]);
	}
	
	static function update($force=false){
		if(self::$isUpdated && !$force){ return true; }
		self::$isUpdated = true;
		
		# clear dead datas
		self::clear();

		# do nothing if no token
		$token = isset($_COOKIE['token']) ? $_COOKIE['token'] : false;
		if(!$token){ return true; }

		$timestamp = time();
		$datetime = date("Y-m-d H:i:s", $timestamp);

		# must connect database
		if(!DB::connect()){ return false; }

		# if user exist
		$result = DB::query(
			'SELECT `account_event`.`id` AS `event_id`, UNIX_TIMESTAMP(`account_event`.`expire`) AS `expire`, UNIX_TIMESTAMP(`account_event`.`datetime`) AS "spawntime",
			`account`.`id`, `account`.`username`, `account`.`identity`, `account`.`email`, `account`.`status` 
			FROM `account_event` 
			LEFT JOIN `account` ON(`account`.`id`=`account_event`.`uid`) 
			WHERE `account`.`status`<>"removed" AND `account_event`.`token`=:token AND :datetime < `account_event`.`expire`
			ORDER BY `account_event`.`id` DESC
			LIMIT 1;'
		)::execute([
			':token' => $token,
			':datetime' => $datetime,
		]);
		if($result::error()){ return false; }
		$user = DB::fetch();
		if(!$user){ self::logout(); return true; }

		# check account status
		if($user['status'] !== 'alive'){ self::logout(); return 'not_alive'; }

		# successfully, update expire
		$rule = Inc::config('account');
		$expireTimestamp = $timestamp + $rule['timeout']['login'];
		$expire = date("Y-m-d H:i:s", $expireTimestamp);

		$result = DB::query(
			'UPDATE `account_event` SET `expire`=:expire WHERE `id`=:event_id;'
		)::execute([':expire'=>$expire, ':event_id'=>$user['event_id'], ]);
		if($result::error()){ return false; }

		setcookie('token', $token, [
			'expires' => $expireTimestamp,
			'path' => Root,
			'domain' => Domain,
			'secure' => false,
			'httponly' => true,
			'samesite' => 'Strict',
		]);
		
		# current datas
		$id = Type::int($user['id'], -1);
		$username = Type::string($user['username'], '');
		$identity = Type::string($user['identity']);
		$email = Type::string($user['email']);
		$status = Type::string($user['status']);
		$spawntime = Type::int($user['spawntime']);

		# try to get profile
		$profile = DB::query(
			"SELECT `nickname`,`gender`,`birthday`,IFNULL(REPLACE(TO_BASE64(`avatar`),'\n',''), NULL) AS `avatar` FROM `profile` 
			WHERE `id`=:id;"
		)::execute([':id' => $id])::fetch();
		if(DB::error() || is_null($profile)){ self::logout(); return false; }

		# get profile
		self::$user = [
			'id' => $id,
			'token' => $token,
			'email' => $email,
			'username' => $username,
			'identity' => $identity,
			'expire' => $expire,
			'status' => $status,
			'spawntime' => $spawntime,
			'nickname' => $profile['nickname'],
			'gender' => $profile['gender'],
			'birthday' => $profile['birthday'],
			'avatar' => $profile['avatar'],
		];
		return true;
	}

	static function logout(){
		self::update();

		// if not login
		$token = self::get('token',false);
		if(!$token){ return true; }

		$timestamp = time();
		$datetime = date("Y-m-d H:i:s", $timestamp);
		$expire = date("Y-m-d H:i:s", $timestamp-1);

		setcookie('token', false, [
			'expires' => $expire,
			'path' => Root,
			'domain' => Domain,
			'secure' => false,
			'httponly' => true,
			'samesite' => 'Strict',
		]);

		# must connect database
		if(!DB::connect()){ return false; }

		# set token expire
		$result = DB::query(
			'UPDATE `account_event` SET `expire`=:expire 
			WHERE `token`=:token AND `expire`>:datetime AND `commit`=:commit;'
		)::execute([
			':datetime' => $expire,
			':token' => $token,
			':datetime' => $datetime,
			':commit' => 'login',
		]);
		return (DB::error()) ? false : true;
	}
}
