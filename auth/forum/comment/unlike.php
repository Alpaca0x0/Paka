<?php
header('Content-Type: application/json; charset=utf-8');
Inc::clas('resp');
Inc::clas('user');
User::isLogin() or Resp::warning('is_logout', '當前尚未登入任何帳戶，可能是 Token 過期了，請重新登入');

# needed datas
$needs = ['commentId'];
Arr::every($_POST, ...$needs) or Resp::warning('data_missing', $needs, '資料缺失');

# convert
$uid = User::get('id', false);
$commentId = Type::int($_POST['commentId'], false);

# check format
if(!$commentId){ Resp::warning('comment_id_format', '留言編號格式錯誤'); }
if(!$uid){ Resp::error('uid_not_found', '發生非預期錯誤，無法獲取帳戶資訊'); }

# check commment exist
Inc::clas('forum');
$commment = Forum::getComment($commentId, $uid);
if($commment === false){ Resp::error('sql_query', 'SQL 查詢時發生錯誤'); }
if(!$commment){ Resp::warning('post_not_found', '找不到該留言'); }
$commment = Arr::nd($commment);

# delete the commment
$result = Forum::unlikeComment($uid, $commentId);
if($result===false){ Resp::error('sql_unlike_post', 'SQL 語法執行失敗'); }
if(is_null($result)){ Resp::success('seems_already_unliked', '看起來尚未按讚過呢'); }
if(!$result){ Resp::error('sql_unlike_post', 'SQL 語法執行失敗，非預期錯誤'); }

Resp::success('successfully', $commentId, '已成功對該留言收回讚');
