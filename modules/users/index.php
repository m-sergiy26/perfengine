<?php

Site::header(_t('Users'));
echo Site::div('title', Site::breadcrumbs(_t('Users')));
$db = App::db();
echo Site::div('menu_list',
'<a href="/users/list">'.Site::icon('users').' '._t('Userlist').' <i class="digits_counter">'.Site::counter('users').'</i></a>
<a href="/users/administration">'.Site::icon('administration').' '._t('Site administration').' <i class="digits_counter">'.$db->query("SELECT * FROM `users` WHERE `level` > '2'")->rowCount().'</i></a>
<a href="/users/birthday">'.Site::icon('birth').' '._t('Birthdays').' <i class="digits_counter">'.$db->query("SELECT * FROM `users` WHERE `day` = '".date('d')."' AND `month` = '".date('m')."' AND `year` != '0'")->rowCount().'</i></a>
<a href="/users/online_today">'.Site::icon('status_online').' '._t('Visitors today').' <i class="digits_counter">'.$db->query("SELECT * FROM `users` WHERE `time` > '". strtotime('now 00:00:00') ."'")->rowCount().'</i></a>
<a href="/users/search">'.Site::icon('search').' '._t('Search').'</a>');

Site::footer();