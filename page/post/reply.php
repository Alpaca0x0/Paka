<?php @include_once('../../init.php'); ?>

<?php
@include_once(Func('loger'));
?>

<?php
// must is login
@include_once(Func('user'));
$User->Update();
if($User->Is('logout')){ $Loger->Push('warning','is_logout'); $Loger->Resp(); }

// must have post data
$needed_datas = ['postId','content','reply'];
foreach ($needed_datas as $data){
	if(!isset($_POST[$data])){ $Loger->Push('warning','data_missing',$data); }
}if($Loger->Check()){ $Loger->Resp(); }

// get data
@include_once(Func('post'));
$postId = (int)(@$_POST['postId']);
$content = trim(@$_POST['content']);
$reply = trim(@$_POST['reply']);

// filter
// make some special chars be space " "
$content = preg_replace('/[\n\r\t]/', ' ', $content);
// remove multiple spaces
$content = preg_replace('/\s(?=\s)/', '', $content);

// format
if(strlen($content)<2){ $Loger->Push('warning','content_too_short'); }
if($Loger->Check()){ $Loger->Resp(); }

// start to reply
$result = $Post->Reply($postId,$content);
$resps = ['logout', 'no_replier', 'error',];
if(in_array($result, $resps)){ $Loger->Push('warning','failed_reply',$result); }
else if(is_array($result)){ $Loger->Push('success','replied',$result); }
else{ $Loger->Push('error','Unexpected error', $result); }

$Loger->Resp();