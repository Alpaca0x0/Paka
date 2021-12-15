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
$needed_datas = ['title','content',];
foreach ($needed_datas as $data){
	if(!isset($_POST[$data])){ $Loger->Push('warning','data_missing',$data); }
}if($Loger->Check()){ $Loger->Resp(); }
// start to write
@include_once(Func('post'));
$title = @$_POST['title'];
$content = @$_POST['content'];
// $title = "It is a test title";
// $content = "Here is a test content, it can type more text.";
$result = $Post->Create($title, $content);

$resps = ['title_too_short', 'logout', 'content_too_short', 'error_insert', ];

if(in_array($result, $resps)){ $Loger->Push('warning','failed_create_post',$result); }
else if(is_array($result)){ $Loger->Push('success','created_post',$result); }
else{ $Loger->Push('error','error',$result); }

$Loger->Resp();