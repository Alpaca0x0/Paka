<?php @include_once('../../init.php'); ?>

<?php
header('Content-Type: application/json; charset=utf-8');
@include_once(Func('post'));
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
$order = isset($_GET['order']) ? strtoupper($_GET['order']) : 'DESC'; 
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 12;

// filter
if($after){ $after = ($after<$min['after']||$after>$max['after']) ? $def['after'] : $after; }
else if($before){ $before = ($before<$min['before']||$before>$max['before']) ? $def['before'] : $before; }
else{ $before=false; $after=$def['after']; }

if($limit<$min['limit'] || $limit>$max['limit']){ $limit = $def['limit']; }

//

$result = $Post->Get('posts+',[0,$limit]);

if(!$result){ $result = []; }
else if(!is_array($result)){ $result = [$result]; }

$ret = [];
foreach ($result as $key => $val) {
	array_push($ret, [
		'id' => (int)$val['id'],
		'title' => $val['title'],
		'content' => $val['content'],
		'datetime' => $val['datetime'],
		'poster' => [
			'id' => $val['poster'],
			'username' => $val['poster_username'],
			'identity' => $val['poster_identity'],
			'nickname' => $val['profile_nickname'],
			'gender' => $val['profile_gender'],
			'avatar' => !is_null($val['profile_avatar'])?base64_encode($val['profile_avatar']):null,
		],
		'edited' => [
			'times' => $val['post_edited_times'],
			'last_time' => $val['post_edited_datetime'],
		],
	]);

	// $temp = $result[$key]['poster'];
	// $result[$key]['poster'] = Array();
	// $result[$key]['poster']['id'] = $temp;
	// $result[$key]['poster']['username'] = $result[$key]['poster_username'];
	// $result[$key]['poster']['identity'] = $result[$key]['poster_identity'];
	// $result[$key]['poster']['nickname'] = $result[$key]['profile_nickname'];
	// $result[$key]['poster']['gender'] = $result[$key]['profile_gender'];
	// $result[$key]['poster']['avatar'] = $result[$key]['profile_avatar'];
	// $result[$key]['edited'] = Array();
	// $result[$key]['edited']['times'] = $result[$key]['post_edited_times'];
	// $result[$key]['edited']['last_time'] = $result[$key]['post_edited_datetime'];
	// if(!is_null($result[$key]['poster']['avatar'])){ $result[$key]['poster']['avatar'] = base64_encode($result[$key]['poster']['avatar']); }

	// unset(
	// 	$result[$key]['poster_username'],
	// 	$result[$key]['poster_identity'],
	// 	$result[$key]['profile_nickname'],
	// 	$result[$key]['profile_gender'],
	// 	$result[$key]['profile_avatar'],
	// 	$result[$key]['post_edited_times'],
	// 	$result[$key]['post_edited_datetime'],
	// );
}
echo json_encode($ret);
