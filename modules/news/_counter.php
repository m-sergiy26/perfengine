<?php
$_new = App::db()->query("SELECT * FROM `news` WHERE `time` > '".(time()-60*60*24)."'")->rowCount();
$_news = App::db()->query("SELECT * FROM `news`")->rowCount();
return '<i class="digits_counter">'.($_new > 0 ? '<i class="green">+'.$_new.'</i>' : $_news).'</i>';