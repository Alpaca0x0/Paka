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

		$datetime = time();

		# update accounts info
		# register, unverified and expire
		DB::query(
			'UPDATE `account` 
			LEFT JOIN `event` ON(`account`.`id`=`event`.`uid`)
			SET `account`.`status`="removed"
			WHERE `account`.`status`="unverified" AND `event`.`action`="register" AND :datetime>`event`.`expire`;'
		)::execute([':datetime' => $datetime]);
		# token expire
		DB::query(
			'UPDATE `event` 
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
		$datetime = time();

		# must connect database
		if(!DB::connect()){ return false; }

		# if user exist
		$result = DB::query(
			'SELECT `event`.`id` AS `event_id`, `event`.`expire`, `event`.`datetime` AS "spawntime",
			`account`.`id`, `account`.`username`, `account`.`identity`, `account`.`email`, `account`.`status` 
			FROM `event` 
			JOIN `account` ON(`account`.`id`=`event`.`uid`) 
			WHERE `account`.`status`<>"removed" AND `event`.`token`=:token
			ORDER BY `event`.`id` DESC
			LIMIT 1;'
		)::execute([':token' => $token]);
		if(!$result){ return false; }
		$user = DB::fetch();
		if(!$user){ self::logout(); return true; }
		
		# check if timeout
		if( $datetime > $user['expire'] ){ self::logout(); return 'timeout'; }
		# check account status
		if($user['status'] !== 'alive'){ self::logout(); return 'not_alive'; }

		# successfully, update expire
		$rule = Inc::config('account');
		$expire = time() + $rule['timeout']['login'];
		$result = DB::query(
			'UPDATE `event` SET `expire`=:expire WHERE `id`=:event_id;'
		)::execute([':expire'=>$expire, ':event_id'=>$user['event_id'], ]);
		if(!$result){ return false; }
		
		# current datas
		$id = Type::int($user['id'], -1);
		$username = Type::string($user['username'], '');
		$identity = Type::string($user['identity']);
		$email = Type::string($user['email']);
		$status = Type::string($user['status']);
		$spawntime = Type::int($user['spawntime']);

		# try to get profile
		$profile = DB::query(
			'SELECT `nickname`,`gender`,`birthday`,`avatar` FROM `profile` 
			WHERE `id`=:id;'
		)::execute([':id' => $id])::fetch();
		if(!$profile){ self::logout(); return false; }

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
			'birthday' => ( is_null($profile['birthday']) ? null : date('Y-m-d', $profile['birthday']) ),
			'avatar' => ( is_null($profile['avatar']) ? null : base64_encode($profile['avatar']) ),
		];
		return true;
	}

	static function logout(){
		self::update();

		// if not login
		$token = self::get('token',false);
		if(!$token){ return true; }

		$datetime = time();
        setcookie('token',false,$datetime-1, Root);

		# must connect database
		if(!DB::connect()){ return false; }

		# set token expire
		$result = DB::query(
			'UPDATE `event` SET `expire`=:datetime 
			WHERE `token`=:token AND `expire`>:datetime2 AND `commit`=:commit;'
		)::execute([
			':datetime' => $datetime,
			':token' => $token,
			':datetime2' => $datetime,
			':commit' => 'login',
		]);
		return (!$result) ? false : true;
	}
}
