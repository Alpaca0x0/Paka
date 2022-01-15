<?php if (!isset($_SESSION)){ session_start(); } ?>
<?php @include_once('../../init.php'); ?>

<?php
@include_once(Func('loger'));
?>

<?php

# If have post data
$needed_datas = ['token'];
foreach ($needed_datas as $data){
    if( !isset($_POST[$data]) || !is_string($_POST[$data]) ){
        $Loger->Resp('warning','data_missing',$data);
        break;
    }
}

# Catch Datas
if(is_string($_POST['token'])){ $token = trim($_POST['token']); }
else{ $token = false; }

# Check
@include_once(Func('user'));
if($User->Get('token',false) !== $token){ $Loger->Resp('warning','token_not_match'); }

# Logout 
$User->Logout();
$Loger->Resp('success','logout_successfully');
