<?php
header('Content-Type: application/json; charset=utf-8');
Inc::clas('resp');
Inc::clas('user');
User::isLogin() or Resp::warning('is_logout', '當前尚未登入任何帳戶，可能是 Token 過期了，請重新登入');

# needed datas
$needs = ['postId', 'content'];
Arr::every($_POST, ...$needs) or Resp::warning('data_missing', $needs, '資料缺失');

# convert
$uid = User::get('id', false);
$postId = Type::int($_POST['postId'], 0);
$content = Type::string($_POST['content'], '');

# check format
if(!$uid){ Resp::error('uid_not_found', '發生非預期錯誤，無法獲取帳戶資訊'); }
// 
$config = Inc::config('forum/comment');
$content = preg_replace('/[\f\r\t]+/', ' ', $content);
$content = preg_replace('/\n[\s+]*\n+/', "\n", $content);
$content = trim($content);
# count chinese as one length
$preContent = preg_replace("#[^\x{00}-\x{ff}]#u", '?', $content);
// 
if(!preg_match($config['content'], $preContent)){ Resp::warning('content_format', '內文的格式錯誤'); }

# create the comment
Inc::clas('forum');
Forum::init() or Resp::error('cannot_init_forum', '初始化 Forum 時發生錯誤');

$commentId = Forum::createComment($uid, $postId, $content);
if($commentId === false){ Resp::error('sql_insert', 'SQL 語法執行錯誤'); }
if(is_null($commentId)){ Resp::error('sql_insert_null', 'SQL 寫入留言時發生錯誤'); }

# return new comment
$newComment = Forum::getComment($commentId, $uid);
if($newComment === false){ Resp::error('sql_query', 'SQL 查詢新留言的資訊時發生錯誤'); }
if(!$newComment){ Resp::error('unexpected', '發生非預期錯誤，無法返回新發布的留言'); }

$newComment = Arr::nd($newComment);

Resp::success('successfully', $newComment, '已成功留言');
