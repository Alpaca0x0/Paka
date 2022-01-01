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

// get data
@include_once(Func('post'));
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
// $title = "It is a test title";
// $content = "Here is a test content, it can type more text.";
$result = $Post->Create($title, $content);


$resps = ['logout', 'error_insert', 'error_select'];

if(in_array($result, $resps)){ $Loger->Push('warning','failed_create_post',$result); }
else if(is_array($result)){ $Loger->Push('success','created_post',$result); }
else{ $Loger->Push('error','error',$result); }

$Loger->Resp();