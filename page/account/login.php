<?php if (!isset($_SESSION)){ session_start(); } ?>
<?php @include_once('../../init.php'); ?>

<?php
@include_once(Func('loger'));
?>

<?php

# If have post data
$needed_datas = ['username','password'];
foreach ($needed_datas as $data){
    if( !isset($_POST[$data]) ){
        $Loger->Resp('warning','data_missing',$data);
        break;
    }
}

# Catch Datas
$username = @trim($_POST['username']);
$password = @$_POST['password'];
// $username = 'alpaca';
// $password = 'passw0rd';
// $email = 'alpaca0x0@gmail.com';

# Check
$regex = @include_once(Conf('account/regex')); // Setting Rules
if(!preg_match($regex['username'], $username)){ $Loger->Push('warning','username_format_not_match'); }
if(!preg_match($regex['password'], $password)){ $Loger->Push('warning','password_format_not_match'); }
if($Loger->Check()){ $Loger->Resp(); } // if have one of [unknown, error, warning], response

# Transform
$password = hash('sha256',$password);

# Start to using database
@include_once(Func('db'));
# Check if the user is not exist
$DB->Query("SELECT `id`,`username`,`identity`,`status` FROM `account` WHERE `username`=:username AND `password`=:password AND `status`!=:status;");
$result = $DB->Execute([':username'=>$username, ':password'=>$password, ':status'=>'removed']);
if(!$result){ $Loger->Resp('error','db_cannot_query'); }
# DB query successfully
$row = $DB->Fetch($result,'assoc');
// can not verify
if(!$row){ $Loger->Resp('warning','cannot_verify_your_identity'); }
// not alive
if($row['status']==='alive'){ } // nothing
else if($row['status']==='unverified'){ $Loger->Resp('warning','is_unverified'); }
else if($row['status']==='review'){ $Loger->Resp('warning','is_review'); }
else{ $Loger->Resp('warning','account_not_alive'); }

// login successfully

// $_SESSION['account'] = [
//     'id' => $row['id'],
//     'username' => $row['username'],
//     'identity' => $row['identity'],
// ];
// $_SESSION['spawntime'] = time();

$user_regex = @include_once(Conf('user')); // Setting Rules

$token = trim(hash('sha256',bin2hex(random_bytes(16))));
$datetime = time();
$expire = $datetime + $user_regex['timeout'];
$ip = trim($_SERVER["REMOTE_ADDR"]);

# Write into Database
$DB->Query("INSERT INTO `account_event`(`account`,`action`,`target`,`ip`,`expire`,`datetime`,`status`) 
	VALUES(:account, :action, :target, :ip, :expire, :t, :status);");
$result = $DB->Execute([':account'=>(int)$row['id'], ':action'=>'login', ':target'=>$token, ':ip'=>$ip, ':expire'=>$expire, ':t'=>$datetime, ':status'=>'alive' ]);
if($result===false){ $Loger->Push('error','db_cannot_insert','account_event'); }
if($Loger->Check()){ $Loger->Resp(); }

$_SESSION['token'] = $token;

# response
$Loger->Resp('success','login_successfully',$row['username']);

