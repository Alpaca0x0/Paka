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
// can not login
if(!$row){ $Loger->Resp('warning','cannot_verify_your_identity'); }
// not alive
if($row['status']==='alive'){ } // nothing
else if($row['status']==='unverified'){ $Loger->Resp('warning','is_unverified'); }
else if($row['status']==='review'){ $Loger->Resp('warning','is_review'); }
else{ $Loger->Resp('warning','account_not_alive'); }

// login successfully
$_SESSION['account'] = [
    'id' => $row['id'],
    'username' => $row['username'],
    'identity' => $row['identity'],
];
$_SESSION['spawntime'] = time();
$_SESSION['token'] = trim(hash('sha256',bin2hex(random_bytes(16))));

# response
$Loger->Resp('success','login_successfully',$row['username']);

