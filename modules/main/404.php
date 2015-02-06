<?php
echo 'The page <b>'.App::filter('input', urldecode($_SERVER['REQUEST_URI'])).'</b> was not found';