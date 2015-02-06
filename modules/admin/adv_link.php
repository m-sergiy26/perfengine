<?php
if(user()->level() < 5 || !isset($_GET['id']) || App::db()->query("SELECT * FROM `adv` WHERE `id` = '".App::filter('int', $_GET['id'])."'")->rowCount() == 0)
    App::redirect('/');

$id = App::filter('int', $_GET['id']);
$link = App::db()->query("SELECT * FROM `adv` WHERE `id` = '".App::filter('int', $_GET['id'])."'")->fetch();

if(isset($_GET['save']))
{
    $name = App::filter('input', $_POST['title']);
    $link = App::filter('input', $_POST['link']);
    $image = App::filter('input', $_POST['image']);
    $html = App::filter('input', $_POST['html']);
    $type = ($_POST['type'] == 'counter' ? 'counter' : ($_POST['type'] == 'top' ? 'top' : 'bottom'));

    if(!empty($name) && (!empty($link) || !empty($html)))
    {
        $adv = App::db()->prepare("UPDATE `adv` SET `name` = ?, `link` = ?, `image` = ?, `html` = ?, `type` = ? WHERE `id` = ?");
        $adv->execute([$name, $link, $image, $html, $type, $id]);
        $_SESSION['alert'] = ['type' => 'notify', 'value' => _t('Link saved')];
        App::redirect('/admin/adv_link/'.$id);
    }
}
elseif(isset($_GET['delete']))
{
    App::db()->query("DELETE FROM `adv` WHERE `id` = '".$id."'");
    $_SESSION['alert'] = ['type' => 'notify', 'value' => _t('Link deleted', 'admin')];
    App::redirect('/admin/adv?'.$link['type']);
}

Site::header(_t('Add link', 'admin'));
echo Site::div('title', Site::breadcrumbs(_t('Add link', 'admin')));
echo '<div class="content">
<form action="/admin/adv_link/'.$id.'?save" method="post">
    <input type="text" placeholder="'._t('Title').'" name="title" value="'.$link['name'].'" /><br/>
    <input type="url" placeholder="URL" name="link" value="'.$link['link'].'"/><br/>
    <input type="text" placeholder="'._t('Image link (optional)', 'admin').'" name="image" value="'.$link['image'].'"/><br/>
    <input type="text" placeholder="'._t('HTML Code (optional)', 'admin').'" name="html" value="'.$link['html'].'"/><br/>
    '._t('Choose type', 'admin').':<br/>
    <select name="type">
        <option value="top"'.($link['type'] == 'top' ? ' selected="selected"' : null).'>'._t('Top links', 'admin').'</option>
        <option value="bottom"'.($link['type'] == 'bottom' ? ' selected="selected"' : null).'>'._t('Bottom links', 'admin').'</option>
        <option value="counter"'.($link['type'] == 'counter' ? ' selected="selected"' : null).'>'._t('Counters', 'admin').'</option>
    </select><br/>
    <input type="submit" value="'._t('Save').'" />
    <a class="button" href="/admin/adv_link/'.$id.'?delete">'._t('Delete').'</a>
</form>
</div>
<div class="action_list">
    <a href="/admin/adv">'.Site::icon('arrow-left').' '._t('Back').'</a>
</div>';
Site::footer();