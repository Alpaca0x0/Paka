<?php
header('Content-Type: application/json; charset=utf-8');
Inc::clas('resp');
Inc::clas('user');
if(User::isLogin()){ Resp::success('is_login','當前已經是登入狀態了'); }

# check if data missing
Arr::every($_POST, 'username','password') or Resp::warning('data_missing', '資料缺失');

// # type
$datetime = time();
$username = Type::string($_POST['username'], '');
$password = Type::string($_POST['password'], '');

// # filter
$username = strtolower(trim($username));

// # check
$config = Inc::config('account');
if(!preg_match($config['username'], $username) && 
!preg_match($config['email'], $username)){ Resp::warning('format_not_match','username','Username 或 E-Mail 格式不正確'); }
if(!preg_match($config['password'], $password)){ Resp::warning('format_not_match','password', 'Password 格式不正確'); }

# database
Inc::clas('db');
DB::connect() or Resp::error('db_cannot_connect', '無法連接資料庫');
# check user if it is exist
$result = DB::query(
    'SELECT `id`,`username`,`password`,`status` FROM `account` 
    WHERE (`username`=:username OR `email`=:email) AND `status`<>"removed" LIMIT 1;'
)::execute([
    ':username'=> $username,
    ':email' => $username,
]);
if($result::error()){ Resp::error('sql_query', 'SQL 語法執行錯誤'); }
$user = DB::fetch();
if(!$user){ Resp::warning('not_found_user', $username, "找不到帳號 \"{$username}\""); }

# check status
if($user['status'] === 'unverified'){ Resp::warning('user_status_unverifie', $username, "帳號 \"{$username}\" 尚未驗證信箱"); }
else if($user['status'] !== 'alive'){ Resp::warning('user_status', $username, "帳號 \"{$username}\" 暫時無法使用"); }

# found user, check if have too many requests
# tried times limit, more than 3 times since 15 minutes, needs captcha
$loginMaxTimes = 3;
$result = DB::query(
    'SELECT COUNT(`id`) AS `count` FROM `account_event` 
    WHERE `commit`=:commit AND `uid`=:uid AND (:datetime-`datetime`)<15*60  # 15 mins
    LIMIT 1;'
)::execute([
    ':commit' => "login_failed",
    ':uid' => Type::int($user['id'], -1),
    ':datetime' => $datetime,
]);
if($result::error()){ Resp::error('sql_query', 'event_login_failed', 'SQL 語法執行錯誤'); }
$row = $result::fetch();
$row['count'] = Type::int($row['count'], 0);
# if needs captcha
if($row['count'] > $loginMaxTimes){
    if(!isset($_POST['captcha'])){ Resp::warning('needs_captcha', '您的帳戶當前需要輸入驗證碼以完成登入程序'); }
    $captcha = Type::string($_POST['captcha'], '');
    Inc::clas('captcha');
    if(Captcha::check($captcha)!==true){ Resp::warning('captcha_not_match', $captcha, '驗證碼不正確'); }
}

# check if password is not correct
# log event
$ip = Type::string(trim($_SERVER["REMOTE_ADDR"]));
if($user['password'] !== hash('sha256',$password)){ 
    $result = DB::query(
        'INSERT INTO `account_event`(`uid`,`commit`,`ip`,`datetime`) 
        VALUES(:uid, :commit, :ip, :datetime);'
    )::execute([
        ':uid' => $user['id'], 
        ':commit' => 'login_failed',
        ':ip' => $ip, 
        ':datetime' => $datetime, 
    ]);
    if($result::error()){ Resp::error('db_cannot_insert','account_event_login_failed','資料庫無法寫入資料'); }
    Resp::warning('password_not_match',($loginMaxTimes-$row['count']>0)?$loginMaxTimes-$row['count']:0, '密碼錯誤');
}

# login success
# write into account event
$token = hash('sha256',bin2hex(random_bytes(16)));
$expire = $datetime + $config['timeout']['login'];
$result = DB::query(
    'INSERT INTO `account_event`(`uid`,`commit`,`token`,`ip`,`expire`,`datetime`) 
    VALUES(:uid, :commit, :token, :ip, :expire, :datetime);'
)::execute([
    ':uid' => $user['id'], 
    ':commit' => 'login', 
    ':token' => $token, 
    ':ip' => $ip, 
    ':expire' => $expire, 
    ':datetime' => $datetime, 
]);
if($result::error()){ Resp::error('db_cannot_insert','account_event_login', '資料庫無法寫入資料'); }
setcookie('token', $token, [
    'expires' => $expire,
    'path' => Root,
    'domain' => Domain,
    'secure' => false,
    'httponly' => true,
    'samesite' => 'Strict',
]);
Resp::success('successfully_login', [$user['id'], $user['username']], "登入成功");
