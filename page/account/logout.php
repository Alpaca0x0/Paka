<?php if (!isset($_SESSION)){ session_start(); } ?>
<?php @include_once('../../init.php'); ?>

<?php
@include_once(Func('loger'));
?>

<?php

# If have post data
$needed_datas = ['token'];
foreach ($needed_datas as $data){
    if( !isset($_POST[$data]) ){
        $Loger->Push('warning','data_missing',$data);
        $Loger->Resp();
        break;
    }
}

# Catch Datas
$token = @trim($_POST['token']);

# Check
@include_once(Func('user'));
if($User->Get('token') !== $token){ $Loger->Push('warning','token_not_match',$token); }
if($Loger->Check()){ $Loger->Resp(); } // if have one of [unknown, error, warning], response

# Logout 
$User->Logout();
$Loger->Push('success','logout_successfully');
$Loger->Resp(); 

