<?php
header('Content-Type: application/json; charset=utf-8');

$_GET['pid'] = ['1','2',['3','11'], '2,', '2','2','3'];

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
	'fields' => ['post' => 'id'],
];

Inc::clas('resp');
$needs = ['pid'];
Arr::every($_GET, ...$needs) or Resp::warning('data_missing', $needs, '資料缺失');

//
Inc::clas('forum');
Forum::init() or Resp::error('forum_cannot_init', '發生非預期錯，Forum 資料無法被初始化');

// check pids
$pids = $_GET['pid'];
if(!is_array($pids)){ $pids = [$pids]; }
foreach ($pids as $idx => $pid) {
	$pid = is_numeric($pid) ? Type::int($pid, 0) : false; 
	if(!$pid){ unset($pids[$idx]); } 
	else{ $pids[$idx] = $pid; }
} $pids = array_unique($pids);
// check option
$before = isset($_GET['before']) ? Type::int($_GET['before'], $def['before']) : $def['before']; 
$after = isset($_GET['after']) ? Type::int($_GET['after'], $def['after']) : $def['after']; 
$orderBy = isset($_GET['orderBy']) ? strtoupper(Type::string($_GET['orderBy'], $def['orderBy'])) : $def['orderBy']; 
$limit = isset($_GET['limit']) ? Type::int($_GET['limit'], $def['limit']) : $def['limit'];
$fields = isset($_GET['fields']) ? Type::array($_GET['fields'], $def['fields']) : $def['fields'];
if(!isset($fields['post']) || !is_array($fields['post'])){ $fields['post'] = []; }
if(!in_array('id', $fields['post'])){ array_push($fields['post'], 'id'); }

// filter
$fields = Forum::toAllowedFields($fields);
if(!$fields){ Resp::warning('fields_format', '欄位格式錯誤'); }

if($after){ $after = ($after<$min['after']||$after>$max['after']) ? $def['after'] : $after; }
else if($before){ $before = ($before<$min['before']||$before>$max['before']) ? $def['before'] : $before; }
else{ $before=false; $after=$def['after']; }

if(!Arr::includes([$orderBy], 'DESC', 'ASC')){ $orderBy = $def['orderBy']; }

if($limit<$min['limit'] || $limit>$max['limit']){ $limit = $def['limit']; }

$comments = Forum::before($before)
::fields($fields)
::after($after)
::orderBy('post.datetime', $orderBy)
::limit($limit)
::isHtml()
::getComments($pids);

Resp::warning('testing', $comments, '測試');

if($comments === false){ Resp::error('sql_query', 'SQL 語法查詢失敗'); }
if(is_null($comments)){ Resp::success('empty_data', null, '查詢成功，但資料為空'); }
if(!is_array($comments)){ Resp::error('sql_query_return_format', 'SQL 語法查詢返回錯誤格式'); }

Resp::success('successfully', $comments, '成功獲取留言');
