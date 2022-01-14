<?php @include_once('../../init.php'); ?>

<?php
@include_once(Func('loger'));
@include_once(Func('captcha'));
?>

<?php
# If have post data
$needed_datas = ['username','password','email','captcha'];
foreach ($needed_datas as $data){
    if( !isset($_POST[$data]) ){
        $Loger->Resp('warning','data_missing',$data);
        break;
    }
}

# Catch Datas
$username = trim($_POST['username']);
$password = $_POST['password'];
$email = trim($_POST['email']);
$captcha = trim($_POST['captcha']);
$datetime = time();
$ip = trim($_SERVER["REMOTE_ADDR"]);
$token = trim(hash('sha256',bin2hex(random_bytes(16))));
// $gender = @trim($_POST['gender']);
// $username = 'alpaca';
// $password = 'passw0rd';
// $email = 'alpaca0x0@gmail.com';

# Check
// if(!$Captcha->Check($captcha)){ $Loger->Resp('warning','captcha_incorrect'); }
$rule = include(Conf('account')); // Setting Rules
if(!preg_match($rule['email'], $email)){ $Loger->Push('warning','email_format_not_match'); }
if(!preg_match($rule['username'], $username)){ $Loger->Push('warning','username_format_not_match'); }
if(!preg_match($rule['password'], $password)){ $Loger->Push('warning','password_format_not_match'); }
// if (!in_array($gender,['male','female','secret'])) { }
if($Loger->Check()){ $Loger->Resp(); } // if have one of [unknown, error, warning], response

# Transform
$password = hash('sha256',$password);

# Start to using database
@include_once(Func('db'));

# Check if the user is not exist
$block_list = @require(Conf('account')); 
$block_list = "'".implode("','",$block_list['no_status']['register'])."'";
// $Loger->Resp('error','db_cannot_query',[$block_list]);
$sql = "
	SELECT MAX(IF(`username`=:username, 1, 0)) AS `username_exist`, MAX(IF(`email`=:email, 1,0)) AS `email_exist` 
	FROM `account` 
	WHERE (`username`=:username2 OR `email`=:email2) AND `status` IN (${block_list})
	LIMIT 1;
";

$DB->Query($sql);

$result = $DB->Execute([':username'=>$username, ':email'=>$email, ':username2'=>$username, ':email2'=>$email, ]);
if($result===false){ $Loger->Resp('error','db_cannot_query'); }
# DB query successfully
$row = $DB->Fetch($result,'assoc');
// if(in_array($username, array_column($row,'username'))){ $Loger->Push('warning','username_exist'); }
// if(in_array($email, array_column($row,'email'))){ $Loger->Push('warning','email_exist'); }
if($row){
	if($row['username_exist']!=0){ $Loger->Push('warning','username_exist'); }
	if($row['email_exist']!=0){ $Loger->Push('warning','email_exist'); }
}
if($Loger->Check()){ $Loger->Resp(); }

# Write into Database
$DB->Query("INSERT INTO `account`(`username`,`password`,`email`,`status`) VALUES(:username,:password,:email,:status);");
$result = $DB->Execute([':username'=>$username, ':password'=>$password, ':email'=>$email, ':status'=>'unverified']);
if($result===false){ $Loger->Push('error','db_cannot_insert','account'); }
if($Loger->Check()){ $Loger->Resp(); }

$id = (int)$DB->Connect->lastInsertId();

// Write Profile
$DB->Query("INSERT INTO `profile`(`id`) VALUES(:id);");
$result = $DB->Execute([':id'=>$id, ]);
if(!$result){ $Loger->Push('error','db_cannot_insert','profile'); }
if($Loger->Check()){ $Loger->Resp(); }

// Write account_event
$DB->Query("INSERT INTO `account_event`(`account`, `action`, `target`, `ip`, `expire`, `datetime`) VALUES(:account, :action, :target, :ip, :expire, :t);");
$result = $DB->Execute([':account'=>$id, ':action'=>'register', ':target'=>$token, ':ip'=>$ip, ':expire'=>$datetime+$rule['verify']['timeout'], ':t'=>$datetime ]);
if(!$result){ $Loger->Push('error','db_cannot_insert','account_event'); }
if($Loger->Check()){ $Loger->Resp(); }

// send the email
@include_once(Func('email'));
$url = (isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS'])?'https':'http').'://'.$_SERVER['SERVER_NAME'].Page('account/verify').'?token='.$token;
$title = 'Verify your email';
$html_content = "
Hello ${username}<br>
Enter the link to verify:<br>
<a href='${url}' target='_blank'>${url}</a>
";

if(DEV){
	$Loger->Push('success','successfully', [$email, $username, $title, $html_content]);
}else{
	$result = $Email->Send($email, $username, $title, $html_content);
	if($result[0]===false){ $Loger->Push('error','cannot_send_email',$result[1]); }
	else if($result[0]===true){ $Loger->Push('success','successfully'); }
	else{ $Loger->Push('error','error_send_email'); }
}
$Loger->Resp();

