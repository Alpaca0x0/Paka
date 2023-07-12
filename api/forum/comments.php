<?php
header('Content-Type: application/json; charset=utf-8');

Inc::clas('resp');

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

$needs = ['postId'];
Arr::every($_GET, ...$needs) or Resp::warning('data_missing', '資料缺失');

//
Inc::clas('forum');
Forum::init() or Resp::error('forum_cannot_init', '發生非預期錯，Forum 資料無法被初始化');

Inc::clas('User');
$uid = User::get('id', 0);
// check pids
$pids = $_GET['postId'];
if(!is_array($pids)){ $pids = [$pids]; }
foreach ($pids as $idx => $pid) {
	$pid = is_numeric($pid) ? Type::int($pid, 0) : false; 
	if(!$pid){ unset($pids[$idx]); } 
	else{ $pids[$idx] = $pid; }
} $pids = array_unique($pids);
if(count($pids) > 16){ Resp::warning('post_too_many', '請求留言的文章過多'); }

// check option
$before = isset($_GET['before']) ? Type::int($_GET['before'], $def['before']) : $def['before']; 
$after = isset($_GET['after']) ? Type::int($_GET['after'], $def['after']) : $def['after']; 
$orderBy = isset($_GET['orderBy']) ? strtoupper(Type::string($_GET['orderBy'], $def['orderBy'])) : $def['orderBy']; 
$limit = isset($_GET['limit']) ? Type::int($_GET['limit'], $def['limit']) : $def['limit'];

// filter
if($after){ $after = ($after<$min['after']||$after>$max['after']) ? $def['after'] : $after; }
else if($before){ $before = ($before<$min['before']||$before>$max['before']) ? $def['before'] : $before; }
else{ $before=false; $after=$def['after']; }

if(!in_array($orderBy, ['DESC', 'ASC'])){ $orderBy = $def['orderBy']; }
if($limit<$min['limit'] || $limit>$max['limit']){ $limit = $def['limit']; }

$comments = Forum::before($before)
				::after($after)
				::orderBy('`comment`.`datetime`', $orderBy)
				::limit($limit)
				::getComments($pids, $uid);
// 
if($comments === false){ Resp::error('sql_query', 'SQL 語法查詢失敗'); }
if(!$comments){ Resp::success('empty_data', null, '查詢成功，但資料為空'); }
if(!is_array($comments)){ Resp::error('sql_query_return_format', 'SQL 語法查詢返回錯誤格式'); }

foreach($comments as $idx => $comment){
	$comments[$idx] = Arr::nd($comment);
	$comments[$idx]['content'] = htmlentities($comment['content']);
}

Resp::success('successfully', $comments, '成功獲取留言');
