<?php
Site::header(_t('Guests'));
echo Site::div('title', Site::breadcrumbs(_t('Guests')));
$guests_r = App::db()->query("SELECT * FROM `guests` WHERE `time` > '".(time()-300)."'")->rowCount();
if($guests_r == 0)
{
    echo Site::div('content', _t('No guests'));
}
else
{
    $pages = new Pager($guests_r, Site::perPage());
    $guests = App::db()->query("SELECT * FROM `guests` WHERE `time` > '".(time()-300)."' ORDER BY id ASC LIMIT ".$pages->start().", ".Site::perPage()."");

    foreach($guests as $guest)
    {
        echo '<div class="content"><b>'._t('Guest').'</b> ('.App::date($guest['time']).')<br/>';
        echo 'IP:'.$guest['ip'].'<br/>'.$guest['browser'];
        echo '</div>';
    }

    $pages->view();
}
echo Site::div('action_list', '<a href="/users/online">'.Site::icon('arrow-left').' '._t('Users online').'</a>');
Site::footer();