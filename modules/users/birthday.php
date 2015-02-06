<?php
Site::header(_t('Birthdays'));
echo Site::div('title', Site::breadcrumbs(_t('Birthdays')));
$db = App::db();
$users_a = $db->query("SELECT * FROM `users` WHERE `day` = '".date('d')."' AND `month` = '".date('m')."' AND `year` != '0'")->rowCount();
$pages = new Pager($users_a, Site::perPage());
if($users_a == 0)
{
	echo Site::div('content', _t('No users'));
}
else
{
	$users = $db->query("SELECT * FROM `users` WHERE `day` = '".date('d')."' AND `month` = '".date('m')."' ORDER BY id ASC LIMIT ".$pages->start().", ".Site::perPage()."");
	foreach($users as $user)
	{
		echo Site::div('content', user()->nick($user['id'], '<b>'.(int)((date('Ymd') - date('Ymd', strtotime($user['year'].'.'.$user['month'].'.'.$user['day']))) / 10000).'</b> '._t('years')));
	}
	$pages->view();
}

Site::footer();