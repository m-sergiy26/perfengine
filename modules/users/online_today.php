<?php
Site::header(_t('Visitors today'));
echo Site::div('title', Site::breadcrumbs(_t('Visitors today')));
$db = App::db();

$users_a = $db->query("SELECT * FROM `users` WHERE `time` > '". strtotime('now 00:00:00') ."'")->rowCount();
$pages = new Pager($users_a, Site::perPage());

if($users_a == 0)
{
    echo Site::div('content', _t('No users'));
}
else
{
    $users = $db->query("SELECT * FROM `users` WHERE `time` > '". strtotime('now 00:00:00') ."' ORDER BY time DESC LIMIT ".$pages->start().", ".Site::perPage()."");
    foreach($users as $user)
    {
        echo Site::div('content', user()->nick($user['id'], '<b>'._t('Last visit').'</b>: '.App::date($user['time'])));
    }
    $pages->view();
}

Site::footer();