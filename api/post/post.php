<?php @include_once('../../init.php'); ?>

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

$ret = $Post->Get('posts',[$skip,$limit]);
if(!is_array($ret)){ $ret = [$ret]; }
foreach ($ret as $key => $val) {
	$temp = $ret[$key]['poster'];
	$ret[$key]['poster'] = Array();
	$ret[$key]['poster']['id'] = $temp;
	$ret[$key]['poster']['username'] = $ret[$key]['poster_username'];
	$ret[$key]['poster']['identity'] = $ret[$key]['poster_identity'];
	$ret[$key]['poster']['nickname'] = $ret[$key]['profile_nickname'];
	$ret[$key]['poster']['gender'] = $ret[$key]['profile_gender'];
	$ret[$key]['poster']['birthday'] = $ret[$key]['profile_birthday'];
	$ret[$key]['poster']['avatar'] = $ret[$key]['profile_avatar'];
	if(!is_null($ret[$key]['poster']['avatar'])){ $ret[$key]['poster']['avatar'] = base64_encode($ret[$key]['poster']['avatar']); }

	unset(
		$ret[$key]['poster_username'],
		$ret[$key]['poster_identity'],
		$ret[$key]['profile_nickname'],
		$ret[$key]['profile_gender'],
		$ret[$key]['profile_birthday'],
		$ret[$key]['profile_avatar'],
	);
}
echo json_encode($ret);
