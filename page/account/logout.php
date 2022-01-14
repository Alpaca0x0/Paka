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
        $Loger->Resp('warning','data_missing',$data);
        break;
    }
}

# Catch Datas
$token = trim(@$_POST['token']);

# Check
@include_once(Func('user'));
if($User->Get('token') !== $token){ $Loger->Push('warning','token_not_match',[$User->Get('token'),$token]); }
if($Loger->Check()){ $Loger->Resp(); } // if have one of [unknown, error, warning], response

# Logout 
$User->Logout();
$Loger->Resp('success','logout_successfully');
