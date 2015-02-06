<?php
$id = isset($_GET['id']) ? App::filter('int', $_GET['id']) : user()->getId();

if(App::db()->query("SELECT * FROM `users` WHERE `id` = '".$id."'")->rowCount() !=1)
    Site::notFound();

Site::header(_t('Profile').' - '.user()->profile('nick', $id));
echo Site::div('title', Site::breadcrumbs(user()->profile('nick', $id).' ('.user()->levelName(user()->profile('level', $id)).')'));
?>
<div class="content">
    <? if(user()->profile('personal_status', $id) != ''): ?>
    <span class="green"><?=user()->profile('personal_status', $id)?></span><br/>
    <? endif; ?>

    <? if(User::avatar($id)):
        echo User::avatar($id).'<br/>';
        endif;
    ?>
    <?if(user()->profile('name', $id) != ''): ?>
        <?=_t('Name')?>: <?=user()->profile('name', $id)?><br/>
    <?endif;?>
    <?if(user()->profile('surname', $id) != ''): ?>
        <?=_t('Surname')?>: <?=user()->profile('surname', $id)?><br/>
    <?endif;?>
    <?=_t('Gender')?>:
        <? if(user()->profile('gender', $id) == 2):
            echo _t('Female');
           else:
            echo _t('Male');
        endif; ?><br/>
    <? if(user()->profile('year', $id) != 0): ?>
    <?=_t('Birthday')?>: <?=user()->profile('day', $id)?>-<?=user()->profile('month', $id)?>-<?=user()->profile('year', $id)?><br/>
    <? endif; ?>
    <?if(user()->profile('site', $id) != ''): ?>
        <?=_t('Website')?>: <a target="_blank" href="<?=user()->profile('site', $id)?>"><?=user()->profile('site', $id)?></a><br/>
    <?endif;?>
    <?if(user()->profile('info', $id) != ''): ?>
        <?=_t('Personal info')?>: <?=user()->profile('info', $id)?><br/>
    <?endif;?>
</div>
<div class="action_list">
    <? if(user()->getId() != $id && user()->config('allow_messages') == 1): ?>
        <a href="/messages/dialog/<?=$id?>"><?=Site::icon('mail')?> <?=_t('Send message')?></a>
    <? endif; ?>
    <? if(user()->level() > 3 && user()->getId() != $id && user()->profile('level', $id) < 4): ?>
        <a href="/admin/ban/<?=$id?>"><?=Site::icon('arrow-left')?> <?=_t('Block user', 'admin')?></a>
    <? elseif(user()->level() > 3 && user()->getId() != $id && user()->profile('level', $id) < 4 && user()->profile('ban_time', $id) > time()): ?>
        <a href="/admin/ban/<?=$id?>?unblock"><?=Site::icon('arrow-left')?> <?=_t('Unblock user', 'admin')?></a>
    <? endif; ?>
    <? if(user()->getId() == $id || user()->level() >= 5): ?>
    <a href="/user/edit<?=(user()->level() >= 5 ? '/'.$id : '')?>"><?=Site::icon('arrow-left')?> <?=_t('Edit')?></a>
    <? endif; ?>
    <a href="/users/list"><?=Site::icon('arrow-left')?> <?=_t('All users')?></a>
</div>
<? Site::footer();