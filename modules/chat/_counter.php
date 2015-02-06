<?php
$_count_posts = App::db()->query("SELECT * FROM `chat` WHERE `time` > '".(time()-3600*24)."'")->rowCount();
return ($_count_posts > 0 ? '<i class="digits_counter">+'.$_count_posts.'</i>' : null);