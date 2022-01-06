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
$postId = (int)($_POST['postId']);
$content = trim($_POST['content']);
$reply = (isset($_POST['reply']) && (int)(@$_POST['reply'])>0) ? (int)(@$_POST['reply']) : null;

// filter
// make some special chars be space " "
$content = preg_replace('/[\n\r\t]/', ' ', $content);
// remove multiple spaces
$content = preg_replace('/\s(?=\s)/', '', $content);

// format
if(strlen($content)<2){ $Loger->Push('warning','content_too_short'); }
if($Loger->Check()){ $Loger->Resp(); }

// check if access is permission
@include_once(Func('db'));
$sql = "SELECT `id` FROM `post` WHERE `id`=:postId AND `status`='alive' LIMIT 1;";
$DB->Query($sql);
$result = $DB->Execute([':postId'=>$postId, ]);
if($result===false){ $Loger->Resp('error','cannot_select'); }
$row = $DB->Fetch($result,'assoc');
if(!$row){ $Loger->Resp('warning','permission_denied'); } 

// check access if it is reply
if(!is_null($reply)){
	$sql = "SELECT `id` FROM `comment` WHERE `id`=:reply AND `post`=:postId AND `status`='alive' LIMIT 1;";
	$DB->Query($sql);
	$result = $DB->Execute([':reply'=> $reply,':postId'=>$postId ]);
	if($result===false){ $Loger->Resp('error','cannot_select'); }
	$row = $DB->Fetch($result,'assoc');
	if(!$row){ $Loger->Resp('warning','permission_denied'); } 
}

// start to reply
$result = $Post->Comment($postId,$content,$reply);

$resps = ['logout', 'no_replier', 'error',];
if(in_array($result, $resps)){ $Loger->Push('warning','failed_reply',$result); }
else if(is_array($result)){ $Loger->Push('success','commented',$result); }
else{ $Loger->Push('error','unexpected_error', $result); }

$Loger->Resp();