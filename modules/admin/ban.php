<?php
$id = @App::filter('int', $_GET['id']);
if(!isset($_GET['id']) || App::db()->query("SELECT * FROM `users` WHERE `id` = '".$id."'")->rowCount() == 0 || user()->level() < 4 || user()->getId() == $id || user()->level($id) > 3)
    Site::notFound();
if(isset($_GET['ban']))
{
    if($_POST['ban_t'] == 0)
    {
        $ban_time = (time()+3600*App::filter('int', $_POST['ban_time']));
    }
    elseif($_POST['ban_t'] == 1)
    {
        $ban_time = (time()+60*60*24*App::filter('int', $_POST['ban_time']));
    }
    $ban_text = App::filter('input', $_POST['ban_text']);
    $_SERVER['alert'] = ['type' => 'notify', 'value' => _t('User successfully banned')];

    App::db()->query("UPDATE `users` SET  `ban_time` = '". $ban_time."', `ban_text` = '". $ban_text."' WHERE `id` = '". $id ."'");
    App::redirect('/user/profile/'.$id);
}
elseif(isset($_GET['unblock']))
{
    App::db()->query("UPDATE `users` SET  `ban_time` = '0', `ban_text` = '' WHERE `id` = '".$id ."'");
    $_SERVER['alert'] = ['type' => 'notify', 'value' => _t('User unblocked')];
    App::redirect('/user/profile/'.$id);
}

Site::header(_t('Block user', 'admin'));
echo Site::div('title', Site::breadcrumbs(_t('Block user', 'admin').' - '.user()->getNick($id)));
echo '<div class="content">
<form action="/admin/ban/'.$id .'?ban" method="post">
<input type="text" style="width: 30px;" maxlength="3" placeholder="'._t('Duration of ban', 'admin').'" name="ban_time" value="1" /><br/>
<input type="radio" value="0" name="ban_t" checked="checked" /> '. _t('Hours') .'<br />
<input type="radio" value="1" name="ban_t" /> '. _t('Days') .'<br />
<textarea placeholder="'. _t('Reason', 'admin') .'" name="ban_text" rows="5"></textarea><br/>
<input type="submit" name="ban" value="'. _t('Save') .'" /><br/>
</form>
</div>';
echo Site::div('action_list', '<a href="/user/profile/'.$id.'">'.Site::icon('arrow-left').' '._t('Back').'</a>');
Site::footer();