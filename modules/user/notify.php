<?php
if(!user()->isUser())
    App::redirect('/user/login');
Site::header(_t('Notifications'));
echo Site::div('title', Site::breadcrumbs(_t('Notifications')));
$notifications_r = App::db()->query("SELECT * FROM `notify` WHERE `user_id` = '". user()->getId()."'")->rowCount();
if($notifications_r == 0)
{
    echo Site::div('content', _t('No notifications'));
}
else
{
    $pages = new Pager($notifications_r, Site::perPage());
    $notifications = App::db()->query("SELECT * FROM `notify` WHERE `user_id` = '". user()->getId()."' ORDER BY time DESC LIMIT ".$pages->start().", ".Site::perPage()."");
    echo '<div class="menu_list">';
    foreach($notifications as $notify)
    {
        echo '<a'.($notify['read'] == 0 ? ' class="unread"' : '').' href="'.$notify['request_id'].'">'._t($notify['type']).' '.($notify['request_value'] != '' ? '('.$notify['request_value'].')' : '').'<br/>
        <small>['.user()->getNick($notify['from_id']).'/'.App::date($notify['time']).']</small>
        </a>';
        if($notify['read'] == 0)
            App::db()->query("UPDATE `notify` SET `read` = '1' WHERE `id` = '".$notify['id']."'");
    }
    echo '</div>';
    $pages->view();
}
Site::footer();