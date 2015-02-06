<?php
if(!user()->isUser())
{
    App::redirect('/user/login');
}

Site::header(_t('Private messages'));
echo Site::div('title', Site::breadcrumbs(_t('Private messages')));
$messages_r = App::db()->query("SELECT * FROM `mail` WHERE `sender_id` = '".user()->getId()."' OR `receiver_id` = '".user()->getId()."'")->rowCount();
if($messages_r == 0)
{
    echo Site::div('content', _t('No messages'));
}
else
{
    $pages = new Pager($messages_r, Site::perPage());
    $dialogs = App::db()->query("SELECT * FROM `mail` WHERE `sender_id` = '".user()->getId()."' OR `receiver_id` = '".user()->getId()."' ORDER BY time_last_message DESC LIMIT ".$pages->start().", ".Site::perPage()."");
    echo '<div class="menu_list">';
    foreach($dialogs as $dialog)
    {
        if($dialog['sender_id'] == user()->getId())
            $user_id = $dialog['receiver_id'];
        else
            $user_id = $dialog['sender_id'];

        if(mb_strlen($dialog['last_message']) > 150)
            $last_message = mb_substr($dialog['last_message'], 0, 150).'...';
        else
            $last_message = $dialog['last_message'];

        echo '<a href="/messages/dialog/'.$user_id.'">
        <i class="messages_img">'.User::avatar($user_id, true).'</i>'.user()->getNick($user_id).' ['.App::date($dialog['time_last_message']).']<br/>
        <small>'.$last_message.'</small></a>';
    }
    echo '</div>';
    $pages->view();
}
Site::footer();