<?php
Router::new(Path::page);
// Router::equal(Router::root(), function () {
//     Router::redirect(Router::uri().'forum');
// });

Router::equal('/', function () { Router::redirect('/forum/'); });
Router::view();

http_response_code(404);