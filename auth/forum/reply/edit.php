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
$replyId = Type::int($_POST['replyId'], false);
$content = Type::string($_POST['content'], '');

# check format
if(!$uid){ Resp::error('uid_not_found', '發生非預期錯誤，無法獲取帳戶資訊'); }
if(!$replyId){ Resp::warning('reply_id_format', '留言 ID 格式錯誤'); }
// 
$config = Inc::config('forum/reply');
$content = preg_replace('/[\f\r\t]+/', ' ', $content);
$content = preg_replace('/\n[\s+]*\n+/', "\n", $content);
$content = trim($content);
# count chinese as one length
$preContent = preg_replace("#[^\x{00}-\x{ff}]#u", '?', $content);
// 
if(!preg_match($config['content'], $preContent)){ Resp::warning('content_format', '內文的格式錯誤'); }

Inc::clas('forum');
Forum::init() or Resp::error('forum_cannot_init', 'Forum 無法初始化');

# check permission
$reply = Forum::getReply($replyId);
if($reply === false){ Resp::error('sql_query', 'SQL 語法執行錯誤'); }
if(!$reply){ Resp::warning('reply_not_found', '找不到該留言'); }
$reply = Arr::nd($reply);
if($reply['replier']['id'] !== $uid){ Resp::warning('permission_denied', '您沒有權限編輯該留言'); }

# check if nothing change
if($reply['content'] === $content){ Resp::success('nothing_changed', '沒有改變任何內容'); }

# edit the reply
$reply = Forum::editReply($uid, $replyId, $content);
if($reply === false){ Resp::error('sql_insert', 'SQL 語法執行錯誤'); }
if(!$reply){ Resp::error('sql_insert', '無法編輯該留言'); }

$reply = Arr::nd($reply);
$reply['content'] = htmlentities($reply['content']);

Resp::success('successfully', $reply, '已成功編輯留言');

