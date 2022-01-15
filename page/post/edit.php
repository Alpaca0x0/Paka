<?php require('../../init.php'); ?>

<?php
@include_once(Func('loger'));
?>

<?php
// must is login
@include_once(Func('user'));
$User->Update();
if($User->Is('logout')){ $Loger->Push('warning','is_logout'); $Loger->Resp(); }

// edit comment
if(isset($_POST['commentId']) && is_string($_POST['commentId'])){
	// must have data
	$needed_datas = ['postId','commentId','content'];
	foreach ($needed_datas as $data){
		if(!isset($_POST[$data]) || !is_string($_POST[$data])){ $Loger->Push('warning','data_missing',$data); }
	}if($Loger->Check()){ $Loger->Resp(); }

	// get data
	@include_once(Func('post'));
	$postId = (int)trim(@$_POST['postId']);
	$commentId = (int)trim(@$_POST['commentId']);
	$content = trim(@$_POST['content']);

	// filter
	// make some special chars be space " "
	$content = preg_replace('/[\n\r\t]/', ' ', trim($content));
	// remove multiple spaces
	$content = preg_replace('/\s(?=\s)/', '', $content);
	if(strlen($content)<2){ $Loger->Push('warning','content_too_short'); }
	if($Loger->Check()){ $Loger->Resp(); }

	$result = $Post->EditComment($postId, $commentId, $content);
	$warnResps = ['is_logout', 'permission_denied', ];

	if(in_array($result, $warnResps)){ $Loger->Push('warning',$result); }
	else if($result==='chnaged_nothing'){ $Loger->Push('success',$result); }
	else if(is_array($result)){ $Loger->Push('success','edited_comment',$result); }
	else{ $Loger->Push('error','error',$result); }

	$Loger->Resp();
}

// edit post
else{
	// must have post data
	$needed_datas = ['title','content','postId',];
	foreach ($needed_datas as $data){
		if(!isset($_POST[$data]) || !is_string($_POST[$data])){ $Loger->Push('warning','data_missing',$data); }
	}if($Loger->Check()){ $Loger->Resp(); }

	// get data
	@include_once(Func('post'));
	$postId = (int)trim(@$_POST['postId']);
	$title = trim(@$_POST['title']);
	$content = trim(@$_POST['content']);

	// filter
	// make some special chars be space " "
	$title = preg_replace('/[\n\r\t]/', ' ', trim($title)); 
	$content = preg_replace('/[\n\r\t]/', ' ', trim($content));
	// remove multiple spaces
	$title = preg_replace('/\s(?=\s)/', '', $title);
	$content = preg_replace('/\s(?=\s)/', '', $content);
	if(strlen($title)<2){ $Loger->Push('warning','title_too_short'); }
	if(strlen($content)<2){ $Loger->Push('warning','content_too_short'); }
	if($Loger->Check()){ $Loger->Resp(); }


	$result = $Post->Edit($postId, $title, $content);
	$warnResps = ['is_logout', 'permission_denied', ];

	if(in_array($result, $warnResps)){ $Loger->Push('warning',$result); }
	else if($result==='chnaged_nothing'){ $Loger->Push('success',$result); }
	else if(is_array($result)){ $Loger->Push('success','edited_post',$result); }
	else{ $Loger->Push('error','error',$result); }

	$Loger->Resp();
}