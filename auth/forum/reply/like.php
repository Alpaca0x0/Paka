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
if(!$replyId){ Resp::warning('comment_id_format', '回覆編號格式錯誤'); }
if(!$uid){ Resp::error('uid_not_found', '發生非預期錯誤，無法獲取帳戶資訊'); }

# check reply exist
Inc::clas('forum');
$reply = Forum::getReply($replyId);
if($reply === false){ Resp::error('sql_query', 'SQL 查詢時發生錯誤'); }
if(!$reply){ Resp::warning('comment_not_found', '找不到該回覆'); }
$reply = Arr::nd($reply);

# like reply
$result = Forum::likeComment($uid, $replyId);
if($result===false){ Resp::error('sql_like_comment', 'SQL 語法執行失敗'); }
if(is_null($result)){ Resp::success('seems_already_liked', '看起來已經按讚過囉'); }
if(!$result){ Resp::error('sql_like_comment', 'SQL 語法執行失敗，非預期錯誤'); }

Resp::success('successfully', $replyId, '已成功對該回覆按讚');
