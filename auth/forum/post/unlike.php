<?php
header('Content-Type: application/json; charset=utf-8');
Inc::clas('resp');
Inc::clas('user');
User::isLogin() or Resp::warning('is_logout', '當前尚未登入任何帳戶，可能是 Token 過期了，請重新登入');

# needed datas
$needs = ['postId'];
Arr::every($_POST, ...$needs) or Resp::warning('data_missing', $needs, '資料缺失');

# convert
$uid = User::get('id', false);
$postId = Type::int($_POST['postId'], false);

# check format
if(!$postId){ Resp::warning('post_id_format', '文章編號格式錯誤'); }
if(!$uid){ Resp::error('uid_not_found', '發生非預期錯誤，無法獲取帳戶資訊'); }

# check post exist
Inc::clas('forum');
$post = Forum::getPost($postId, $uid);
if($post === false){ Resp::error('sql_query', 'SQL 查詢時發生錯誤'); }
if(!$post){ Resp::warning('post_not_found', '找不到該文章'); }
$post = Arr::nd($post);

# delete the post
$result = Forum::unlikePost($uid, $postId);
if($result===false){ Resp::error('sql_unlike_post', 'SQL 語法執行失敗'); }
if(is_null($result)){ Resp::success('seems_already_unliked', '看起來尚未按讚過呢'); }
if(!$result){ Resp::error('sql_unlike_post', 'SQL 語法執行失敗，非預期錯誤'); }

Resp::success('successfully', $postId, '已成功對該文章收回讚');
