<?php
Router::new(Path::asset);

Router::view(Router::uri());

http_response_code(404);