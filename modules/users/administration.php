<?php
Site::header(_t('Site administration'));
echo Site::div('title', Site::breadcrumbs(_t('Site administration')));
$db = App::db();
$users_r = $db->query("SELECT * FROM `users` WHERE `level` > '2'")->rowCount();

if($users_r == 0)
{
	echo Site::div('content', _t('No users'));
}
else
{
	$users = $db->query("SELECT * FROM `users` WHERE `level` > '2' ORDER BY level DESC");
	foreach($users as $user)
	{
		
		echo Site::div('content', user()->nick($user['id'], user()->levelName(user()->profile('level', $user['id']))));
	}
}

Site::footer();