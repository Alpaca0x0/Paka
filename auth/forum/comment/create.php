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
$pid = Type::int($_POST['postId'], 0);
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
$commentId = Forum::createComment($uid, $pid, $content);
if($pid === false){ Resp::error('sql_insert', 'SQL 語法執行錯誤'); }

# return new comment
$comment = Forum::getComment($commentId);
if(!$comment){ Resp::error('unexpected', '發生非預期錯誤，無法返回新發布的文章'); }

$comment = Arr::nd($comment);

Resp::success('successfully', $comment, '已成功留言');
