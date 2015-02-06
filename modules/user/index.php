<?php
if(!user()->isUser())
    App::redirect('/user/login');

Site::header(_t('User panel'));
echo Site::div('title', Site::breadcrumbs(_t('User panel')));
?>
<div class="menu_list">
    <a href="/user/edit"><?=Site::icon('arrow-right')?> <?=_t('Edit profile')?></a>
    <a href="/user/profile"><?=Site::icon('arrow-right')?> <?=_t('View profile')?></a>
    <a href="/user/avatar"><?=Site::icon('arrow-right')?> <?=_t('Avatar')?></a>
    <a href="/user/settings"><?=Site::icon('arrow-right')?> <?=_t('Settings')?></a>
    <a href="/user/security"><?=Site::icon('arrow-right')?> <?=_t('Security')?></a>
    <a href="/messages"><?=Site::icon('arrow-right')?> <?=_t('Private messages')?></a>
    <a href="/user/notify"><?=Site::icon('arrow-right')?> <?=_t('Notifications')?></a>
    <a href="/user/logout"><?=Site::icon('arrow-right')?> <?=_t('Logout')?></a>
</div>
<?=Site::footer();