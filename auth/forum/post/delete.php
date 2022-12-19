<?php
header('Content-Type: application/json; charset=utf-8');
Inc::clas('resp');
Inc::clas('user');
User::isLogin() or Resp::warning('is_logout', '尚未登入');

# needed datas
$needs = ['pid'];
Arr::every($_POST, ...$needs) or Resp::warning('data_missing', $needs, '資料缺失');

# convert
$uid = User::get('id', false);
$pid = Type::int($_POST['pid'], false);

# check format
if(!$pid){ Resp::warning('post_id_format', '文章編號格式錯誤'); }
if(!$uid){ Resp::error('uid_not_found', '發生非預期錯誤，無法獲取帳戶資訊'); }

# check permission
Inc::clas('forum');
$post = Forum::getPost($pid);
if($post === false){ Resp::error('sql_query', 'SQL 查詢時發生錯誤'); }
if(is_null($post)){ Resp::warning('post_not_found', '找不到該文章'); }
if($post['poster']['id'] !== $uid){ Resp::warning('permission_denied', '您沒有權限刪除此文章'); }

# delete the post
$result = Forum::deletePost($pid);
if(!$result || $result < 1){ Resp::error('sql_delete_post', 'SQL 語法執行失敗'); }
if($result > 1){ Resp::error('sql_delete_many_post', 'SQL 語法執行異常，刪除了多篇文章'); }

Resp::success('successfully', $pid, '已成功刪除該文章');
