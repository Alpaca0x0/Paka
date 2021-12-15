<?php @include_once('../init.php'); ?>

<?php
header('Content-Type: application/json; charset=utf-8');
@include_once(Func('post'));
?>

<?php
// $Loger->Push($Post->Get('posts',[0,5]));
// $Loger->Resp();

// check option
$skip = isset($_GET['skip']) ? (int)$_GET['skip'] : 0; 
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 8; 

// filter
if($skip<0 || $skip>2147483647){ $skip = 0; }
if($limit<1 || $limit>99){ $limit = 8; }


$ret = $Post->Get('posts_poster',[$skip,$limit]);
if(!is_array($ret)){ $ret = [$ret]; }
echo json_encode($ret);
