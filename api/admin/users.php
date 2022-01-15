<?php @include_once('../../init.php'); ?>

<?php
header('Content-Type: application/json; charset=utf-8');
@include_once(Func('user'));
$User->Update();
if($User->Get('identity',false)!=='admin'){ die(json_encode([])); }
@include_once(Func('admin'));
?>

<?php
// define
$infinity = 2147483647;
$max = [
	'before' => $infinity, // <
	'after' => $infinity-1, // >
	'limit' => 99,
];

$min = [
	'before' => 1, // <
	'after' => 0, // >
	'limit' => 1,
];

$def = [
	'before' => $max['before'], // <
	'after' => $min['after'], // >
	'limit' => 1,
];

// check option
$before = isset($_GET['before']) ? (int)$_GET['before'] : false; 
$after = isset($_GET['after']) ? (int)$_GET['after'] : false; 
$order = isset($_GET['order']) && is_string($_GET['order']) ? strtoupper($_GET['order']) : 'DESC'; 
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 12;

// filter
if($after){ $after = ($after<$min['after']||$after>$max['after']) ? $def['after'] : $after; }
else if($before){ $before = ($before<$min['before']||$before>$max['before']) ? $def['before'] : $before; }
else{ $before=false; $after=$def['after']; }

if($limit<$min['limit'] || $limit>$max['limit']){ $limit = $def['limit']; }

$ret = $Admin->Get('users',[
	'before' => $before?(int)$before:false,
	'after' => $after?(int)$after:false,
	'limit' => $limit,
	'order' => $order,
]);

if(!$ret){ $ret = []; }
else if(!is_array($ret)){ $ret = [$ret]; }
// $ret = array_values($ret);

// foreach ($ret as $key => $val) {
// 	$temp = $ret[$key]['poster'];
// 	$ret[$key]['poster'] = Array();
// 	$ret[$key]['poster']['id'] = $temp;
// 	$ret[$key]['poster']['username'] = $ret[$key]['poster_username'];
// 	$ret[$key]['poster']['identity'] = $ret[$key]['poster_identity'];
// 	$ret[$key]['poster']['nickname'] = $ret[$key]['profile_nickname'];
// 	$ret[$key]['poster']['gender'] = $ret[$key]['profile_gender'];
// 	$ret[$key]['poster']['avatar'] = $ret[$key]['profile_avatar'];
// 	$ret[$key]['edited'] = Array();
// 	$ret[$key]['edited']['times'] = $ret[$key]['post_edited_times'];
// 	$ret[$key]['edited']['last_time'] = $ret[$key]['post_edited_datetime'];
// 	if(!is_null($ret[$key]['poster']['avatar'])){ $ret[$key]['poster']['avatar'] = base64_encode($ret[$key]['poster']['avatar']); }

// 	unset(
// 		$ret[$key]['poster_username'],
// 		$ret[$key]['poster_identity'],
// 		$ret[$key]['profile_nickname'],
// 		$ret[$key]['profile_gender'],
// 		$ret[$key]['profile_avatar'],
// 		$ret[$key]['post_edited_times'],
// 		$ret[$key]['post_edited_datetime'],
// 	);
// }
echo json_encode($ret);
