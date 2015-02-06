<?php
if(user()->level() < 5)
    App::redirect('/');

if(isset($_GET['save']))
{
    $data = [];
    $data['site_name'] = App::filter('input', $_POST['site_name']);
    $data['copyright'] = App::filter('input', $_POST['copyright']);
    $data['filetypes'] = App::filter('input', $_POST['filetypes']);
    $data['keywords'] = App::filter('input', $_POST['keywords']);
    $data['description'] = App::filter('input', $_POST['description']);

    App::writeConfig('system/data/config', $data);
    $_SESSION['alert'] = ['type' => 'notify', 'value' => _t('Saved')];
    App::redirect('/admin/advanced');
}

Site::header(_t('Advanced Settings', 'admin').' - '._t('Dashboard', 'admin'));
echo Site::div('title', Site::breadcrumbs(_t('Advanced Settings', 'admin')));
$config = App::config('system/data/config');
echo '<div class="content">
    <form action="/admin/advanced?save" method="post">
        <b>'._t('Site title', 'admin').'</b>:<br/>
        <input type="text" name="site_name" value="'.$config['site_name'].'" /><br/>
        <b>'._t('Copyright', 'admin').'</b>:<br/>
        <input type="text" name="copyright" value="'.$config['copyright'].'" /><br/>
        <b>'._t('Allowed extension of files', 'admin').'</b>:<br/>
        <input type="text" name="filetypes" value="'.$config['filetypes'].'" /><br/>
        <b>'._t('Keywords (for search engines)', 'admin').'</b>:<br/>
        <input type="text" name="keywords" value="'.$config['keywords'].'" /><br/>
        <b>'._t('Description (for search engines)', 'admin').'</b>:<br/>
       <textarea name="description">'.$config['description'].'</textarea><br/>
       <input type="submit" value="'._t('Save').'" />
    </form>
</div>';
Site::footer();