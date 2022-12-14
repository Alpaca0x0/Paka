<?php
header('Content-Type: application/json; charset=utf-8');
Inc::clas('resp');

# If have post data
Arr::every($_GET, 'token') or Resp::warning('data_missing', '需要 Token 驗證');

# Data format
$token = Type::string($_GET['token'],false);
strlen($token) === 64 or Resp::warning('token_format_wrong','Token 格式不正確');

# Check if token match
Inc::clas('user');
User::isLogin() or Resp::warning('not_login', '尚未登入');
if(User::get('token',false) !== $token){ Resp::warning('token_not_match', 'Token 不一致'); }

# Logout 
User::logout();
Resp::success('logout_successfully', '成功登出');
