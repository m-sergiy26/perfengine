<?php 
$new_files = App::db()->query("SELECT * FROM `downloads_files` WHERE `time` > '". (time()-60*60*24) ."'")->rowCount();
$files = App::db()->query("SELECT * FROM `downloads_files`")->rowCount();
return '<i class="digits_counter">'.$files.''.($new_files > 0 ? ' <i class="green">+'.$new_files.'</i>' : NULL).'</i>';