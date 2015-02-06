<?php
if(user()->level() < 5)
    App::redirect('/');

Site::header(_t('Advertising links', 'admin'));
echo Site::div('title', Site::breadcrumbs(_t('Advertising links', 'admin')));
echo '<div class="menu_list">
<a href="/admin/adv?top">'._t('Top links', 'admin').'</a>
</div>';
if(isset($_GET['top']))
{
    echo '<div class="content">';
    $top = App::db()->query("SELECT * FROM `adv` WHERE `type` = 'top'");
    foreach($top as $link)
    {
        echo '<a href="/admin/adv_link/'.$link['id'].'">'.$link['name'].'</a><br/>';
    }
    echo '[<a href="/admin/adv_add">'._t('Add link', 'admin').'</a>]</div>';
}

echo '<div class="menu_list">
<a href="/admin/adv?bottom">'._t('Bottom links', 'admin').'</a>
</div>';
if(isset($_GET['bottom']))
{
    echo '<div class="content">';
    $bottom = App::db()->query("SELECT * FROM `adv` WHERE `type` = 'bottom'");
    foreach($bottom as $link)
    {
        echo '<a href="/admin/adv_link/'.$link['id'].'">'.$link['name'].'</a><br/>';
    }
    echo '[<a href="/admin/adv_add">'._t('Add link', 'admin').'</a>]</div>';
}

echo '<div class="menu_list">
<a href="/admin/adv?counters">'._t('Counters', 'admin').'</a>
</div>';
if(isset($_GET['counters']))
{
    echo '<div class="content">';
    $counter = App::db()->query("SELECT * FROM `adv` WHERE `type` = 'counter'");
    foreach($counter as $link)
    {
        echo '<a href="/admin/adv_link/'.$link['id'].'">'.$link['name'].'</a><br/>';
    }
    echo '[<a href="/admin/adv_add">'._t('Add link', 'admin').'</a>]</div>';
}
Site::footer();

