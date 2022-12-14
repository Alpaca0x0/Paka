<?php
Inc::clas('user');
if(User::isLogin()){ header('Location: '.Uri::page('account/profile')); die(); }
else{ header('Location: '.Uri::page('account/login')); die(); }
