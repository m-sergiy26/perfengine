<?php
if(user()->level() < 5)
    App::redirect('/');

if(isset($_GET['create']))
{
    $name = App::filter('input', $_POST['title']);
    $link = App::filter('input', $_POST['link']);
    $image = App::filter('input', $_POST['image']);
    $html = App::filter('input', $_POST['html']);
    $type = ($_POST['type'] == 'counter' ? 'counter' : ($_POST['type'] == 'top' ? 'top' : 'bottom'));

    if(!empty($name) && (!empty($link) || !empty($html)))
    {
        $adv = App::db()->prepare("INSERT INTO `adv` SET `name` = ?, `link` = ?, `image` = ?, `html` = ?, `type` = ?");
        $adv->execute([$name, $link, $image, $html, $type]);
        $_SESSION['alert'] = ['type' => 'notify', 'value' => _t('Link created')];
        App::redirect('/admin/adv?'.$type);
    }
}

Site::header(_t('Add link', 'admin'));
echo Site::div('title', Site::breadcrumbs(_t('Add link', 'admin')));
echo '<div class="content">
<form action="/admin/adv_add?create" method="post">
    <input type="text" placeholder="'._t('Title').'" name="title" /><br/>
    <input type="url" placeholder="URL" name="link"/><br/>
    <input type="text" placeholder="'._t('Image link (optional)', 'admin').'" name="image"/><br/>
    <input type="text" placeholder="'._t('HTML Code (optional)', 'admin').'" name="html"/><br/>
    '._t('Choose type', 'admin').':<br/>
    <select name="type">
        <option value="top">'._t('Top links', 'admin').'</option>
        <option value="bottom">'._t('Bottom links', 'admin').'</option>
        <option value="counter">'._t('Counters', 'admin').'</option>
    </select><br/>
    <input type="submit" value="'._t('Save').'" />
</form>
</div>
<div class="action_list">
    <a href="/admin/adv">'.Site::icon('arrow-left').' '._t('Back').'</a>
</div>';
Site::footer();