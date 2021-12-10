<?php @include_once('../../init.php'); ?>

<?php
@include_once(Func('loger'));
?>

<?php
# If have post data
$needed_datas = ['username','password','email','gender'];
foreach ($needed_datas as $data){
    if( !isset($_POST[$data]) ){
        $Loger->Push('warning','data_missing',$data);
        $Loger->Resp();
        break;
    }
}

# Catch Datas
$username = @trim($_POST['username']);
$password = @$_POST['password'];
$email = @trim($_POST['email']);
$gender = @trim($_POST['gender']);
// $username = 'alpaca';
// $password = 'passw0rd';
// $email = 'alpaca0x0@gmail.com';

# Check
$regex = @include_once(Conf('account/regex')); // Setting Rules
if(!preg_match($regex['email'], $email)){ $Loger->Push('warning','email_format_not_match'); }
if(!preg_match($regex['username'], $username)){ $Loger->Push('warning','username_format_not_match'); }
if(!preg_match($regex['password'], $password)){ $Loger->Push('warning','password_format_not_match'); }
if($Loger->Check()){ $Loger->Resp(); } // if have one of [unknown, error, warning], response

# Transform
$password = sha1($password);

# Start to using database
@include_once(Func('db'));

# Check if the user is not exist
$DB->Query("SELECT `username`,`email` FROM `account` WHERE `username`=:username OR `email`=:email;");
$result = $DB->Execute([':username'=>$username, ':email'=>$email]);
if(!$result){ $Loger->Push('error','db_cannot_query'); $Loger->Resp(); }
# DB query successfully
$row = $DB->FetchAll($result,'assoc');
if(in_array($username, array_column($row,'username'))){ $Loger->Push('warning','username_exist'); }
if(in_array($email, array_column($row,'email'))){ $Loger->Push('warning','email_exist'); }
if($Loger->Check()){ $Loger->Resp(); } // exist

# Write into Database
$DB->Query("INSERT INTO `account`(`username`,`password`,`email`) VALUES(:username,:password,:email);");
$result = $DB->Execute([':username'=>$username, ':password'=>$password, ':email'=>$email]);
if(!$result){ $Loger->Push('error','db_cannot_insert'); }
else{ $Loger->Push('success','db_insert_successfully'); }
$Loger->Resp(); 

