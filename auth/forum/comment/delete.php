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

# check permission
Inc::clas('forum');
$comment = Forum::getComment($commentId, $uid);
if($comment === false){ Resp::error('sql_query', 'SQL 查詢時發生錯誤'); }
if(!$comment){ Resp::warning('comment_not_found', '找不到該留言'); }
$comment = Arr::nd($comment);
if($comment['commenter']['id'] !== $uid){ Resp::warning('permission_denied', '您沒有權限刪除此留言'); }

# delete the comment
$result = Forum::deleteComment($commentId);
if(!$result){ Resp::error('sql_delete_comment', $result, 'SQL 語法執行失敗'); }

Resp::success('successfully', $commentId, '已成功刪除該留言');
