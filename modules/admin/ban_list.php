<?php
if(user()->level() < 5)
    App::redirect('/');

Site::header(_t('Banned users', 'admin'));
echo Site::div('title', Site::breadcrumbs(_t('Banned users', 'admin')));
$users_r = App::db()->query("SELECT * FROM `users` WHERE `ban_time` > '".time()."'")->rowCount();
if($users_r == 0)
{
    echo Site::div('content', _t('No users'));
}
else
{
    $pages = new Pager($users_r, Site::perPage());
    $users = App::db()->query("SELECT * FROM `users` WHERE `ban_time` > '".time()."' ORDER BY id ASC LIMIT ".$pages->start().", ".Site::perPage()."");

    foreach($users as $user)
    {
        echo '<div class="content">';
        echo user()->nick($user['id'], _t('Until').': '.App::date($user['ban_time']));
        echo '</div>';
    }

    $pages->view();
}
Site::footer();