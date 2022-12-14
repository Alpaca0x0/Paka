<?php
header('Content-Type: application/json; charset=utf-8');
Inc::clas('resp');
Inc::clas('user');

if(User::isLogin()){ Resp::warning('is_login', '目前是登入狀態，請先登出'); }

# check if data missing
Arr::every($_POST, 'username','password', 'email', 'captcha') or Resp::warning('data_missing', '資料缺失');

# catch variable and convert format
$email = Type::string($_POST['email'], '');
$username = Type::string($_POST['username'], '');
$password = Type::string($_POST['password'], '');
$captcha = Type::string($_POST['captcha'], '');
$datetime = time();
$ip = trim($_SERVER["REMOTE_ADDR"]);
$token = trim(hash('sha256',bin2hex(random_bytes(16))));

# filter
$username = strtolower(trim($username));
$captcha = trim($captcha);

# check format
$rule = include(Local::Config('account'));
if(!preg_match($rule['email'], $email)){ Roger::push('format','email'); }
if(!preg_match($rule['username'], $username)){ Roger::push('format','username'); }
if(!preg_match($rule['password'], $password)){ Roger::push('format','password'); }
if(!preg_match($rule['captcha'], $captcha)){ Roger::push('format','captcha'); }
Roger::warning();

# check captcha
// isset($Captcha) or include_once(Local::Func('captcha'));
class_exists('Captcha') or include_once(Local::Clas('captcha'));
if(Captcha::check($captcha)!==true){ Roger::warning('captcha_not_match',$captcha); }

# Transform
$password = hash('sha256',$password);

# database query
# check if username or email already exist
class_exists('DB') or include_once(Local::Clas('db'));
DB::connect() or Roger::error('database_cannot_connect');
$result = DB::query(
    'SELECT MAX(IF(`username`=:username, 1, 0)) AS `username_exist`, MAX(IF(`email`=:email, 1,0)) AS `email_exist` 
	FROM `account` 
	WHERE (`username`=:username2 OR `email`=:email2) AND `status` NOT IN ("removed","invalid")
    LIMIT 1'
)::execute([':username'=>$username, ':email'=>$email, ':username2'=>$username, ':email2'=>$email, ]);
if($result===false){ Roger::error('db_query_error'); }

# DB query successfully, check if exist
$row = DB::fetch();
if($row){
    # is exist
	if($row['username_exist']===1){ Roger::push('username_exist',$username,$row); }
	if($row['email_exist']===1){ Roger::push('email_exist',$email); }
} Roger::warning();

# Write into Database
$result = DB::query(
    'INSERT INTO `account`(`username`,`password`,`email`,`status`) VALUES(:username,:password,:email,:status);'
)::execute([':username'=>$username, ':password'=>$password, ':email'=>$email, ':status'=>'unverified']);
if($result===false){ Roger::error('db_insert_error','account'); }

# Create profile
$id = (int)DB::lastInsertId();
$result = DB::query('INSERT INTO `profile`(`id`) VALUES(:id);')::execute([':id'=>$id, ]);
if($result===false){ Roger::error('db_insert_error','profile'); }

# Write account_event
$result = DB::query(
    'INSERT INTO `account_event`(`account`, `action`, `target`, `ip`, `expire`, `datetime`) VALUES(:account, :action, :target, :ip, :expire, :t);'
)::execute([':account'=>$id, ':action'=>'register', ':target'=>$token, ':ip'=>$ip, ':expire'=>$datetime+$rule['verify']['timeout'], ':t'=>$datetime ]);
if($result===false){ Roger::error('db_insert_error','account_event'); }

// Send the email
// isset($Email) or include_once(Local::Func('email'));
class_exists('Email') or require_once(Local::Clas('email'));
$url = (isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) ? 
    'https' : 'http').'://'.DOMAIN.Root::Auth('account/verify').'?token='.$token;
$title = htmlentities('Verify your email in '.Project::Name());
$username = htmlentities($username);
$html_content = "
    Hello <b>{$username}</b> <br>
    Enter the link to verify your email:<br>
    <a href='{$url}' target='_blank'>{$url}</a><hr>
    If you did NOT register any account on this site, please ignore this email.
";

if(DEV){
	Roger::success('successfully', [$email, $username, $title, $html_content]);
}else{
	$result = Email::send($email, $username, $title, $html_content);
	if($result[0]===false){ Roger::error('send_email_error',$result[1]); }
	else if($result[0]===true){ Roger::success('successfully'); }
	else{ Roger::error('send_email_error'); }
}

Roger::error('unexpected_error');

