<?php
header('Content-Type: application/json; charset=utf-8');
Inc::clas('resp');
Inc::clas('user');
User::isLogin() or Resp::warning('is_logout', '當前尚未登入任何帳戶，可能是 Token 過期了，請重新登入');

# needed datas
$needs = ['replyId'];
Arr::every($_POST, ...$needs) or Resp::warning('data_missing', $needs, '資料缺失');

# convert
$uid = User::get('id', false);
$replyId = Type::int($_POST['replyId'], false);

# check format
if(!$replyId){ Resp::warning('reply_id_format', '回覆留言的編號格式錯誤'); }
if(!$uid){ Resp::error('uid_not_found', '發生非預期錯誤，無法獲取帳戶資訊'); }

# check permission
Inc::clas('forum');
$reply = Forum::getreply($replyId, $uid);
if($reply === false){ Resp::error('sql_query', 'SQL 查詢時發生錯誤'); }
if(!$reply){ Resp::warning('reply_not_found', '找不到該回覆留言'); }
$reply = Arr::nd($reply);
if($reply['replier']['id'] !== $uid){ Resp::warning('permission_denied', '您沒有權限刪除此回覆留言'); }

# delete the reply
$result = Forum::deleteReply($replyId);
if(!$result){ Resp::error('sql_delete_reply', $result, 'SQL 語法執行失敗'); }

Resp::success('successfully', $replyId, '已成功刪除該回覆留言');
