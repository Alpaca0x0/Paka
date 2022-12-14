<?php
# # # # # # # # # #
# Setting router  #
# # # # # # # # # #
#
# for NginX, put it into configuration file.
#
// location ^~ /router/ {
//     root /var/www/html/router;
//     include fastcgi_params;
//     fastcgi_param SCRIPT_FILENAME $document_root/router.php;
//     fastcgi_pass unix:/run/php/php-fpm.sock;
// }
#
# # # # # # # # # # # # # # # # # # # # # # # # # # # # # # 
#
# DO NOT output any stuff on this page
#
# # # # # # # # # # # # # # # # # # # # # # # # # # # # # #

// init
require('init.php');

// init router
Inc::clas('Router');
Router::init(substr($_SERVER['SCRIPT_NAME'], strlen(Root) - 1));
// ID of Current Page
define('ID', Router::tryFile() === false ? false : ID(Router::tryFile()));

// Router::tryFile() === false ? false : ID(Router::tryFile())

Router::get(['/img/', '/js/', '/css/', '/plugin/'], 'asset');
Router::get('/auth/', 'auth');
Router::get('/api/', 'api');
Router::get('/', 'page');

http_response_code(404);
