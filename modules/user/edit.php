<?php
if(!user()->isUser())
    App::redirect('/');
$id = isset($_GET['id']) ? App::filter('int', $_GET['id']) : user()->getId();

if(App::db()->query("SELECT * FROM `users` WHERE `id` = '".$id."'")->rowCount() !=1)
    Site::notFound();

if(isset($_GET['save']))
{
    $name = App::filter('input', $_POST['name']);
    $surname = App::filter('input', $_POST['surname']);
    $year = substr(App::filter('int', $_POST['year']), 0, 4);
    $month = substr(App::filter('int', $_POST['month']), 0, 2);
    $day = substr(App::filter('int', $_POST['day']), 0, 2);
    $gender = App::filter('int', $_POST['gender']) == 2 ? 2 : 1;
    $level = isset($_POST['level']) && user()->level() > 4 && $_POST['level'] > 0 &&  $_POST['level'] < 6 ? App::filter('int', $_POST['level']) : 1;
    $site = App::filter('input', $_POST['site']);
    $info = App::filter('input', $_POST['info']);
    $status = isset($_POST['personal_status']) && (user()->level() > 4 || user()->level($id) == 2) ? App::filter('input', $_POST['personal_status']) : '';
    $db = App::db();
    $profile = $db->prepare("UPDATE `users` SET `name` = ?, `surname` = ?, `year` = ?, `month` = ?, `day` = ?, `gender` = ?, `site` = ?, `info` = ?, `level` = ?, `personal_status` = ? WHERE `id` = ?");
    $profile->execute([$name, $surname, $year, $month, $day, $gender, $site, $info, $level, $status, $id]);
    $_SESSION['alert'] = ['type' => 'notify', 'value' => 'Saved'];
    App::redirect('/user/edit'.(user()->level() >= 5 ? '/'.$id : ''));
}

Site::header(_t('Edit profile'));
echo Site::div('title', Site::breadcrumbs(_t('Edit profile')));
?>
<div class="content">
    <form action="/user/edit<?=(user()->level() >= 5 ? '/'.$id : '')?>?save" method="post">
        <input type="text" value="<?=user()->profile('name', $id)?>" name="name" placeholder="<?=_t('Name')?>"/><br/>
        <input type="text" value="<?=user()->profile('surname', $id)?>" name="surname" placeholder="<?=_t('Surname')?>"/><br/>
        <?=_t('Gender')?>:<br/>
        <select name="gender">
            <option<?=user()->profile('gender', $id) == 1 ? ' selected="selected"' : ''?> value="1"><?=_t('Male')?></option>
            <option<?=user()->profile('gender', $id) == 2 ? ' selected="selected"' : ''?> value="2"><?=_t('Female')?></option>
        </select><br/>
        <?=_t('Birthday')?>:<br/>
        <select name="year">
            <? for($year = 1950; $year<=(date('Y')-12); $year++): ?>
                <option<?=user()->profile('year', $id) == $year ? ' selected="selected"' : ''?> value="<?=$year?>"><?=$year?></option>
            <? endfor; ?>
        </select>
        <select name="month">
            <? for($month = 1; $month<=12; $month++): ?>
                <option<?=user()->profile('month', $id) == $month ? ' selected="selected"' : ''?> value="<?=$month?>"><?=$month?></option>
            <? endfor; ?>
        </select>
        <select name="day">
            <? for($day = 1; $day<=31; $day++): ?>
                <option<?=user()->profile('day', $id) == $day ? ' selected="selected"' : ''?> value="<?=$day?>"><?=$day?></option>
            <? endfor; ?>
        </select><br/>
        <input value="<?=user()->profile('site', $id)?>" type="url" name="site" placeholder="<?=_t('Website')?>"/><br/>
        <textarea placeholder="<?=_t('Personal info')?>" name="info" rows="5"><?=user()->profile('info', $id)?></textarea><br/>
        <? if(user()->level() > 4 || user()->level($id) == 2): ?>
            <input type="text" name="personal_status" value="<?=user()->profile('personal_status', $id)?>" placeholder="<?=_t('Personal status')?>"/><br/>
        <?endif?>
    <?if(user()->level() > 4 && $id != user()->getId()): ?>
        <?=_t('User level', 'admin')?>:<br/>
        <select name="level">
            <option value="1"<?=(user()->level($id) == 1 ? ' selected="selected"' : '')?>><?=user()->levelName(1)?></option>
            <option value="2"<?=(user()->level($id) == 2 ? ' selected="selected"' : '')?>><?=user()->levelName(2)?></option>
            <option value="3"<?=(user()->level($id) == 3 ? ' selected="selected"' : '')?>><?=user()->levelName(3)?></option>
            <option value="4"<?=(user()->level($id) == 4 ? ' selected="selected"' : '')?>><?=user()->levelName(4)?></option>
            <option value="5"<?=(user()->level($id) == 5 ? ' selected="selected"' : '')?>><?=user()->levelName(5)?></option>
        </select><br/>
        <? endif; ?>
        <input type="submit" value="<?=_t('Save')?>" />
    </form>
</div>
<div class="action_list">
    <a href="/user/profile<?=(user()->level() >= 5 ? '/'.$id : '')?>"><?=Site::icon('arrow-left')?> <?=_t('View profile')?></a>
</div>
<? Site::footer();