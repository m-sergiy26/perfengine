<?php
Site::header(_t('Users online'));
echo Site::div('title', Site::breadcrumbs(_t('Users online')));
$users_r = App::db()->query("SELECT * FROM `users` WHERE `time` > '".(time()-300)."'")->rowCount();
if($users_r == 0)
{
    echo Site::div('content', _t('No users'));
}
else
{
    $pages = new Pager($users_r, Site::perPage());
    $users = App::db()->query("SELECT * FROM `users` WHERE `time` > '".(time()-300)."' ORDER BY id ASC LIMIT ".$pages->start().", ".Site::perPage()."");

    foreach($users as $user)
    {
        echo '<div class="content">';
        echo user()->nick($user['id'], user()->levelName($user['level']));
        echo '</div>';
    }

    $pages->view();
}
echo Site::div('action_list', '<a href="/main/guests">'.Site::icon('arrow-left').' '._t('Guests').'</a>');
Site::footer();