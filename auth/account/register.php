<?php
header('Content-Type: application/json; charset=utf-8');
Inc::clas('resp');
Inc::clas('user');
if(User::isLogin()){ Resp::warning('is_login', '目前是登入狀態，請先登出'); }

# check if data missing
Arr::every($_POST, 'username','password', 'email', 'captcha') or Resp::warning('data_missing', '資料缺失');

# catch variable and convert format
$email = Type::string($_POST['email'], '');
$username = strtolower(trim(Type::string($_POST['username'], '')));
$password = Type::string($_POST['password'], '');
$captcha = trim(Type::string($_POST['captcha'], ''));
$timestamp = time();
$datetime = date("Y-m-d H:i:s", $timestamp);
$ip = trim($_SERVER["REMOTE_ADDR"]);
$token = trim(hash('sha256',bin2hex(random_bytes(16))));

# check captcha
$config = Inc::config('account');
if(!preg_match($config['captcha'], $captcha)){ Resp::warning('captcha_format','驗證碼格式不正確'); }
Inc::clas('captcha');
if(Captcha::check($captcha) !== true){ Resp::warning('captcha_not_match',$captcha, '驗證碼不正確'); }
# check format
if(!preg_match($config['email'], $email)){ Resp::warning('email_format','電子信箱格式不正確'); }
if(!preg_match($config['username'], $username)){ Resp::warning('username_format','帳號格式不正確'); }
if(!preg_match($config['password'], $password)){ Resp::warning('password_format','密碼格式不正確'); }

# transfer
$password = hash('sha256',$password);

# database query
# check if username or email already exist
Inc::clas('db');
DB::connect() or Resp::error('database_cannot_connect', '無法連接資料庫');
$result = DB::query(
    'SELECT MAX(IF(`username`=:username, 1, 0)) AS `username_exist`, MAX(IF(`email`=:email, 1,0)) AS `email_exist` 
	FROM `account` 
	WHERE (`username`=:username2 OR `email`=:email2) AND `status`<>"removed"
    LIMIT 1'
)::execute([':username'=>$username, ':email'=>$email, ':username2'=>$username, ':email2'=>$email, ]);
if(DB::error()){ Resp::error('sql_query_error', 'SQL 語法查詢錯誤'); }

# DB query successfully, check if exist
$row = DB::fetch();
if($row !== false){
    if(!Arr::every($row, 'username_exist', 'email_exist'))
        { Resp::error('sql_result_format', $row, '發生致命錯誤，SQL 查詢返回錯誤格式'); }
    # is exist
	if($row['email_exist']===1){ Resp::warning('email_exist', $email, '該信箱已被註冊'); }
	if($row['username_exist']===1){ Resp::warning('username_exist', $username, '該帳號已被註冊'); }
}

# the datas is acceptable

# add account
DB::beginTransaction();
$sql = 'INSERT INTO `account`(`username`,`password`,`email`,`status`) VALUES(:username, :password, :email, :status);';
$result = DB::query($sql)::execute([
    ':username' =>  $username,
    ':password' =>  $password,
    ':email'    =>  $email, 
    ':status'   =>  'unverified',
]);
if(DB::error()){
    DB::rollback();
    Resp::error('db_insert_error', 'account', '發生錯誤，資料庫無法寫入該帳戶');
}

# add profile
$uid = DB::lastInsertId();
$sql = 'INSERT INTO `profile`(`id`) VALUES(:uid);';
$result = DB::query($sql)::execute([
    ':uid'  =>  $uid,
]);
if(DB::error()){
    DB::rollback();
    Resp::error('db_insert_error', 'profile', '發生錯誤，資料庫無法創建該帳戶的基本資訊');
}

# add event and token
$sql = 'INSERT INTO `account_event`(`uid`, `commit`, `token`, `ip`, `expire`, `datetime`) VALUES(:uid, :commit, :token, :ip, :expire, :datetime);';
$result = DB::query($sql)::execute([
    ':uid'  =>  $uid,
    ':commit'   =>  'register',
    ':token'    =>  $token,
    ':ip'       =>  $ip,
    ':expire'   =>  date("Y-m-d H:i:s", $timestamp+$config['timeout']['verify']),
    ':datetime' =>  $datetime,
]);
if(DB::error()){
    DB::rollback();
    Resp::error('db_insert_error', 'event', '發生錯誤，無法將帳戶憑證寫入資料庫');
}
DB::commit();

# successfully register
# send the email
Inc::clas('email');
if(!Email::init()){ Resp::error('smtp_init', '糟糕，SMTP 伺服器無法連接，暫時無法寄信'); };
//
$url = Protocol.'://'.Domain.':'.Port.Uri::auth('account/verify').'?token='.$token;
$title = 'Verify your email for '.NAME.' !';
$username = htmlentities($username);
$content = "
    Hello <b>{$username}</b> <br>
    Enter the link to verify your email:<br>
    <a href='{$url}' target='_blank'>{$url}</a><hr>
    If you did NOT register any account on this site, please ignore this email.
";
// 
if(!DEV){
    $result = Email::to($email, $username)
        ::subject($title)
        ::content($content)
        ::send();
    if(!$result){
        $msg = DEBUG ? Email::errorMsg() : null;
        Resp::error('smtp_send', $msg, '寄送驗證信時，發生非預期錯誤');
    }
}
// 

Resp::success('successfully', [
    'token' => DEV ? $token : null,
    'username' => $username,
], '註冊成功，請前往信箱驗證帳號！');
