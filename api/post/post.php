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

$ret = $Post->Get('posts+',[$skip,$limit]);

if(!is_array($ret)){ $ret = [$ret]; }

foreach ($ret as $key => $val) {
	$temp = $ret[$key]['poster'];
	$ret[$key]['poster'] = Array();
	$ret[$key]['poster']['id'] = $temp;
	$ret[$key]['poster']['username'] = $ret[$key]['poster_username'];
	$ret[$key]['poster']['identity'] = $ret[$key]['poster_identity'];
	$ret[$key]['poster']['nickname'] = $ret[$key]['profile_nickname'];
	$ret[$key]['poster']['gender'] = $ret[$key]['profile_gender'];
	$ret[$key]['poster']['avatar'] = $ret[$key]['profile_avatar'];
	$ret[$key]['edited'] = Array();
	$ret[$key]['edited']['times'] = $ret[$key]['post_edited_times'];
	$ret[$key]['edited']['last_time'] = $ret[$key]['post_edited_datetime'];
	if(!is_null($ret[$key]['poster']['avatar'])){ $ret[$key]['poster']['avatar'] = base64_encode($ret[$key]['poster']['avatar']); }

	unset(
		$ret[$key]['poster_username'],
		$ret[$key]['poster_identity'],
		$ret[$key]['profile_nickname'],
		$ret[$key]['profile_gender'],
		$ret[$key]['profile_avatar'],
		$ret[$key]['post_edited_times'],
		$ret[$key]['post_edited_datetime'],
	);
}
echo json_encode($ret);
