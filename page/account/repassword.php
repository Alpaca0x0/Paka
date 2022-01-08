<?php if (!isset($_SESSION)){ session_start(); } ?>
<?php @include_once('../../init.php'); ?>

<?php
@include_once(Func('loger'));
@include_once(Func('user'));
$User->Update();
if($User->Is('logout')){ $Loger->Resp('warning','is_logout'); }
?>

<?php

# If have post data
$needed_datas = ['old_password','new_password'];
foreach ($needed_datas as $data){
    if( !isset($_POST[$data]) ){
        $Loger->Resp('warning','data_missing',$data);
        break;
    }
}


# Catch Datas
$old_password = $_POST['old_password'];
$new_password = $_POST['new_password'];

# Check
$regex = @include_once(Conf('account/regex')); // Setting Rules
if(!preg_match($regex['password'], $new_password)){ $Loger->Push('warning','password_format_not_match'); }
if($Loger->Check()){ $Loger->Resp(); } // if have one of [unknown, error, warning], response

# Transform
$old_password = hash('sha256',$old_password);
$new_password = hash('sha256',$new_password);

# Start to using database
@include_once(Func('db'));
$id = (int)$User->Get('id',0);
# Check if the user is not exist
$DB->Query("UPDATE `account` SET `password`=:new_password WHERE `id`=:id AND `password`=:old_password");
$result = $DB->Execute([':new_password'=>$new_password, ':id'=>$id, ':old_password'=>$old_password, ]);
if($result===false){ $Loger->Resp('error','db_cannot_update'); }
else if($DB->rowCount()===0){ $Loger->Resp('warning','nothing_changed'); }

# response
$Loger->Resp('success','update_successfully');

