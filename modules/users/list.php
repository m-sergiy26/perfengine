<?php
Site::header(_t('Userlist'));
echo Site::div('title', Site::breadcrumbs(_t('Userlist')));
$users_r = App::db()->query("SELECT * FROM `users`")->rowCount();
if($users_r == 0)
{
    echo Site::div('content', _t('No users'));
}
else
{
    $pages = new Pager($users_r, Site::perPage());
    $users = App::db()->query("SELECT * FROM `users` ORDER BY id ASC LIMIT ".$pages->start().", ".Site::perPage()."");

    foreach($users as $user)
    {
        echo '<div class="content">';
        echo user()->nick($user['id'], user()->levelName($user['level']));
        echo '</div>';
    }

    $pages->view();
}
Site::footer();