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
$content = preg_replace('/[\n]/', "\n", $content);
$content = preg_replace('/[\r\t]/', ' ', $content);
$content = preg_replace('/\s(?=\s)/', '', $content);

// format
if(strlen($content)<2){ $Loger->Push('warning','content_too_short'); }
if($Loger->Check()){ $Loger->Resp(); }

// check if access is permission
@include_once(Func('db'));
$sql = "SELECT `id` FROM `comment` WHERE `post`=:postId AND `reply`=:reply AND `status`='alive' LIMIT 1;";
$DB->Query($sql);
$result = $DB->Execute([':postId'=>$postId, ':reply'=>(int)$reply, ]);
if(!$result){ return $Loger->Push('error','cannot_select'); }
$row = $DB->Fetch($result,'assoc');
if(!$row){ $Loger->Push('warning','access_denied'); }
if($Loger->Check()){ $Loger->Resp(); }

// start to reply
$result = $Post->Reply($postId,$content);
$resps = ['logout', 'no_replier', 'error',];
if(in_array($result, $resps)){ $Loger->Push('warning','failed_reply',$result); }
else if(is_array($result)){ $Loger->Push('success','commented',$result); }
else{ $Loger->Push('error','Unexpected error', $result); }

$Loger->Resp();