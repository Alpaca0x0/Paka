<?php
header('Content-Type: application/json; charset=utf-8');
Inc::clas('resp');
Inc::clas('user');
User::isLogin() or Resp::warning('is_logout', '當前尚未登入任何帳戶，可能是 Token 過期了，請重新登入');

# needed datas
$needs = ['replyId', 'content'];
Arr::every($_POST, ...$needs) or Resp::warning('data_missing', $needs, '資料缺失');

# convert
$uid = User::get('id', false);
$replyId = Type::int($_POST['replyId'], 0);
$content = Type::string($_POST['content'], '');

# check format
if(!$uid){ Resp::error('uid_not_found', '發生非預期錯誤，無法獲取帳戶資訊'); }
// 
$config = Inc::config('forum/reply');
$content = preg_replace('/[\f\r\t]+/', ' ', $content);
$content = preg_replace('/\n[\s+]*\n+/', "\n", $content);
$content = trim($content);
# count chinese as one length
$preContent = preg_replace("#[^\x{00}-\x{ff}]#u", '?', $content);
// 
if(!preg_match($config['content'], $preContent)){ Resp::warning('content_format', '內文的格式錯誤'); }

# create the comment
Inc::clas('forum');
$replyId = Forum::createReply($uid, $replyId, $content);
if($replyId === false){ Resp::error('sql_insert', 'SQL 語法執行錯誤'); }
if(is_null($replyId)){ Resp::error('sql_insert_null', 'SQL 寫入留言時發生錯誤'); }

# return new comment
$reply = Forum::getComment($replyId);
if(!$reply){ Resp::error('unexpected', '發生非預期錯誤，無法返回新的回覆'); }

$reply = Arr::nd($reply);

Resp::success('successfully', $reply, '已成功回應留言');
