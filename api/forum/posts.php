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
	'fields' => ['post' => 'id'],
];

// $_GET['fields'] = [
// 	'post' => ['id','content'],
// 	'poster' => ['id','username'],
// ];

//
Inc::clas('resp');
Inc::clas('forum');
Forum::init() or Resp::error('forum_cannot_init', '發生非預期錯，Forum 資料無法被初始化');

// check option
$before = isset($_GET['before']) ? Type::int($_GET['before'], $def['before']) : $def['before']; 
$after = isset($_GET['after']) ? Type::int($_GET['after'], $def['after']) : $def['after']; 
$orderBy = isset($_GET['orderBy']) ? strtoupper(Type::string($_GET['orderBy'], $def['orderBy'])) : $def['orderBy']; 
$limit = isset($_GET['limit']) ? Type::int($_GET['limit'], $def['limit']) : $def['limit'];
$fields = isset($_GET['fields']) ? Type::array($_GET['fields'], $def['fields']) : $def['fields'];
if(!isset($fields['post']) || !is_array($fields['post'])){ $fields['post'] = []; }
if(!in_array('id', $fields['post'])){ array_push($fields['post'], 'id'); }

// filter
$fieldsWhitelist = Forum::getWhitelist();
$preFields = [];
foreach ($fields as $table => $columns) {
	$table = Type::string($table, '_');
	$columns = Type::array($columns, []);
	$preFields[$table] = isset($preFields[$table]) ? $preFields[$table] : [];
	foreach ($columns as $column) {
		Type::string($column, '?');
		if(!array_key_exists($table, $fieldsWhitelist)){ Resp::warning('tables_out_of_range', '資料表超出範圍'); }
		if(!array_key_exists($column, $fieldsWhitelist[$table])){ Resp::warning('fields_out_of_range', [$table, $column], '欄位超出範圍'); }
		$preFields[$table][$column] = $fieldsWhitelist[$table][$column];
	}
}

if($after){ $after = ($after<$min['after']||$after>$max['after']) ? $def['after'] : $after; }
else if($before){ $before = ($before<$min['before']||$before>$max['before']) ? $def['before'] : $before; }
else{ $before=false; $after=$def['after']; }

if(!Arr::includes([$orderBy], 'DESC', 'ASC')){ $orderBy = $def['orderBy']; }

if($limit<$min['limit'] || $limit>$max['limit']){ $limit = $def['limit']; }

//
$fields = $preFields;
if(isset($preFields['post'])){ $preFields[''] = $preFields['post']; unset($preFields['post']); }

$posts = Forum::before($before)
::after($after)
::orderBy('post.datetime', $orderBy)
::limit($limit)
::setFields($preFields)
::getPosts();

if($posts === false){ Resp::error('sql_query', 'SQL 語法查詢失敗'); }
if(is_null($posts)){ Resp::success('empty_data', null, '查詢成功，但資料為空'); }
if(!is_array($posts)){ Resp::error('sql_query_return_format', 'SQL 語法查詢返回錯誤格式'); }

# encode
foreach($posts as $idx => $post){
	if(isset($fields['content'])){
		$posts[$idx]['content'] = htmlentities($post['content']);
		$posts[$idx]['content'] = nl2br($posts[$idx]['content']);
	}
}

Resp::success('successfully', $posts, '成功獲取文章');
