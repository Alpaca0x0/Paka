<?php @include_once('../../init.php'); ?>

<?php
@include_once(Func('loger'));
?>

<?php
// must is login
@include_once(Func('user'));
$User->Update();
if($User->Is('logout')){ $Loger->Push('warning','is_logout'); $Loger->Resp(); }

// remove the comment
if(isset($_POST['commentId'])){
	@include_once(Func('post'));
	$commenter = $User->Get('id',false);
	if(!$commenter){ $Loger->Push('warning','is_logout'); $Loger->Resp(); }
	$commentId = (int)$_POST['commentId'];
	if($commentId<1){ $Loger->Push('warning','comment_id_format_incorrect'); $Loger->Resp(); }
	// check access
	$sql = "SELECT `id` FROM `comment` WHERE `id`=:commentId AND `commenter`=:commenter AND `status`='alive' LIMIT 1;";
	$DB->Query($sql);
	$result = $DB->Execute([':commentId'=>$commentId, ':commenter'=>$commenter, ]);
	if(!$result){ $Loger->Push('error','cannot_select'); $Loger->Resp(); }
	$row = $DB->Fetch($result,'assoc');
	if(!$row){ $Loger->Push('warning','access_denied'); $Loger->Resp(); }
	// start to remove comment
	$result = $Post->RemoveComment($commentId);
	$resps = ['comment_id_format_incorrect'];
	if(in_array($result, $resps)){ $Loger->Push('warning',$result); }
	else if($result==='success'){ $Loger->Push('success','removed_comment'); }
	else{ $Loger->Push('error','unexpected',[$result]); }
	$Loger->Resp();
}
else if(isset($_POST['postId'])){
	@include_once(Func('post'));
	$poster = $User->Get('id',false);
	if(!$poster){ $Loger->Push('warning','is_logout'); $Loger->Resp(); }
	$postId = (int)$_POST['postId'];
	if($postId<1){ $Loger->Push('warning','post_id_format_incorrect'); $Loger->Resp(); }
	// check access
	$sql = "SELECT `id` FROM `post` WHERE `id`=:postId AND `poster`=:poster AND `status`='alive' LIMIT 1;";
	$DB->Query($sql);
	$result = $DB->Execute([':postId'=>$postId, ':poster'=>$poster, ]);
	if(!$result){ $Loger->Push('error','cannot_select'); $Loger->Resp(); }
	$row = $DB->Fetch($result,'assoc');
	if(!$row){ $Loger->Push('warning','access_denied'); $Loger->Resp(); }
	// start to remove comment
	$result = $Post->Remove($postId);
	$resps = ['post_id_format_incorrect'];
	if(in_array($result, $resps)){ $Loger->Push('warning',$result); }
	else if($result==='success'){ $Loger->Push('success','removed_post'); }
	else{ $Loger->Push('error','unexpected',[$result]); }
	$Loger->Resp();
}
else{ $Loger->Push('warning','data_missing'); $Loger->Resp(); }

$Loger->Resp();