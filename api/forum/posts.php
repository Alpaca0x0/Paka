<?php
header('Content-Type: application/json; charset=utf-8');

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
	'limit' => 8,
	'orderBy' => 'DESC',
];

// check option
$before = isset($_GET['before']) ? Type::int($_GET['before'], $def['before']) : $def['before']; 
$after = isset($_GET['after']) ? Type::int($_GET['after'], $def['after']) : $def['after']; 
$orderBy = isset($_GET['orderBy']) ? strtoupper(Type::string($_GET['orderBy'], $def['orderBy'])) : $def['orderBy']; 
$limit = isset($_GET['limit']) ? Type::int($_GET['limit'], $def['limit']) : $def['limit'];

// filter
if($after){ $after = ($after<$min['after']||$after>$max['after']) ? $def['after'] : $after; }
else if($before){ $before = ($before<$min['before']||$before>$max['before']) ? $def['before'] : $before; }
else{ $before=false; $after=$def['after']; }

if(!Arr::includes([$orderBy], 'DESC', 'ASC')){ $orderBy = $def['orderBy']; }

if($limit<$min['limit'] || $limit>$max['limit']){ $limit = $def['limit']; }

//
Inc::clas('resp');
Inc::clas('forum');
// 
Forum::init() or Resp::error('forum_cannot_init', '發生非預期錯，Forum 資料無法被初始化');
$posts = Forum::before($before)
			::after($after)
			::orderBy('post.datetime', $orderBy)
			::limit($limit)
			::getPosts();

if($posts === false){ Resp::error('sql_query', 'SQL 語法查詢失敗'); }
if(is_null($posts)){ Resp::success('empty_data', null, '查詢成功，但資料為空'); }
if(!is_array($posts)){ Resp::error('sql_query_return_format', 'SQL 語法查詢返回錯誤格式'); }

$ret = [];
foreach ($posts as $key => $val) {
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

Resp::success('successfully', $ret, '成功獲取文章');
