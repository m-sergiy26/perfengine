<?php
$new_posts = App::db()->query("SELECT * FROM `forum_pt` WHERE `time` > '". (time()-60*60*24) ."'")->rowCount();
return '<i class="digits_counter">'.Site::counter('forum_t').'/'.Site::counter('forum_pt').($new_posts > 0 ? ' <i class="green">+'.$new_posts.'</i>' : NULL).'</i>';