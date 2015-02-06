<?php
if(user()->level() < 5)
    App::redirect('/');

Site::header(_t('Admin', 'admin'));
echo Site::div('title', Site::breadcrumbs(_t('Admin', 'admin')));
echo '<div class="menu_list">
<a href="/admin/settings">'.Site::icon('arrow-right').' '._t('Basic Settings', 'admin').'</a>
<a href="/admin/advanced">'.Site::icon('arrow-right').' '._t('Advanced Settings', 'admin').'</a>
<a href="/admin/modules">'.Site::icon('arrow-right').' '._t('Modules', 'admin').'</a>
<a href="/admin/mainpage">'.Site::icon('arrow-right').' '._t('Main page', 'admin').'</a>
<a href="/admin/adv">'.Site::icon('arrow-right').' '._t('Advertising links', 'admin').'</a>
<a href="/admin/ban_list">'.Site::icon('arrow-right').' '._t('Banned users', 'admin').'</a>
<a href="/admin/smiles">'.Site::icon('arrow-right').' '._t('Manage smiles', 'admin').'</a>
<a href="/admin/info">'.Site::icon('arrow-right').' '._t('System info', 'admin').'</a>
</div>';
Site::footer();