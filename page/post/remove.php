<?php require('../../init.php'); ?>

<?php
@include_once(Func('loger'));
?>

<?php
// must is login
@include_once(Func('user'));
$User->Update();
if($User->Is('logout')){ $Loger->Resp('warning','is_logout'); }

// remove the comment
if(isset($_POST['commentId']) && is_string($_POST['commentId'])){
	@include_once(Func('post'));
	$commenter = $User->Get('id',false);
	if(!$commenter){ $Loger->Resp('warning','is_logout'); }
	$commentId = (int)$_POST['commentId'];
	if($commentId<1){ $Loger->Resp('warning','comment_id_format_incorrect'); }

	// check access
	$sql = "SELECT `id` FROM `comment` WHERE `id`=:commentId AND `commenter`=:commenter AND `status`='alive' LIMIT 1;";
	$DB->Query($sql);
	$result = $DB->Execute([':commentId'=>$commentId, ':commenter'=>$commenter, ]);
	if(!$result){ $Loger->Resp('error','cannot_select'); }
	$row = $DB->Fetch($result,'assoc');
	if(!$row){ $Loger->Resp('warning','permission_denied'); }

	// start to remove comment
	$result = $Post->RemoveComment($commentId);

	$warnResps = ['comment_id_format_incorrect'];
	if($result==='success'){ $Loger->Push('success','removed_comment'); }
	else if(in_array($result, $warnResps)){ $Loger->Push('warning',$result); }
	else{ $Loger->Push('error','error',[$result]); }
	$Loger->Resp();
}

// remove the post
else if(isset($_POST['postId']) && is_string($_POST['postId'])){
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
	if(!$row){ $Loger->Push('warning','permission_denied'); $Loger->Resp(); }
	// start to remove comment
	$result = $Post->Remove($postId);
	$resps = ['post_id_format_incorrect'];
	if(in_array($result, $resps)){ $Loger->Push('warning',$result); }
	else if($result==='success'){ $Loger->Push('success','removed_post'); }
	else{ $Loger->Push('error','error',[$result]); }
	$Loger->Resp();
}

else{ $Loger->Resp('warning','data_missing'); }

$Loger->Resp();