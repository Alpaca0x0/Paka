<?php require('../../init.php'); ?>

<?php
@include_once(Func('loger'));
?>

<?php
// must is login
@include_once(Func('user'));
$User->Update();
if($User->Is('logout')){ $Loger->Resp('warning','is_logout'); }

// must have post data
$needed_datas = ['title','content',];
foreach ($needed_datas as $data){
	if(!isset($_POST[$data]) || !is_string($_POST[$data])){ $Loger->Push('warning','data_missing',$data); }
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
$rules = @include_once(Conf('post'));
if(mb_strlen($title)<$rules['title']['min']){ $Loger->Push('warning','title_too_short'); }
else if(mb_strlen($title)>$rules['title']['max']){ $Loger->Push('warning','title_too_long'); }
if(mb_strlen($content)<$rules['content']['min']){ $Loger->Push('warning','content_too_short'); }
else if(mb_strlen($content)>$rules['content']['max']){ $Loger->Push('warning','content_too_long'); }
if($Loger->Check()){ $Loger->Resp(); }
// $title = "It is a test title";
// $content = "Here is a test content, it can type more text.";
$result = $Post->Create($title, $content);

$warnResps = ['is_logout'];
$errResps = ['error_insert'];
if(in_array($result, $warnResps)){ $Loger->Push('warning', $result); }
else if(in_array($result, $errResps)){ $Loger->Push('error', $result); }
else if(is_array($result)){ $Loger->Push('success','created_post',$result); }
else{ $Loger->Push('error','error',$result); }

$Loger->Resp();