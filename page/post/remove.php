<?php @include_once('../../init.php'); ?>

<?php
@include_once(Func('loger'));
?>

<?php
// must is login
@include_once(Func('user'));
$User->Update();
if($User->Is('logout')){ $Loger->Push('warning','is_logout'); $Loger->Resp(); }
$remover = $User->Get('id','-');

// must have post data
$needed_datas = ['postId',];
foreach ($needed_datas as $data){
	if(!isset($_POST[$data])){ $Loger->Push('warning','data_missing',$data); }
}
if($Loger->Check()){ $Loger->Resp(); }

// check access
@include_once(Func('post'));
$postId = (int)$_POST['postId'];
if($Post->Get('poster',$postId)['id'] !== $remover){ $Loger->Push('warning','no_access',$postId); $Loger->Resp(); };

// start to write
@include_once(Func('post'));
if($Post->Remove($postId)){ $Loger->Push('success','removed_post'); }
else{ $Loger->Push('error','failed_remove_post'); }

$Loger->Resp();