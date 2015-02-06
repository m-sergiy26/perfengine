<?php
if(user()->level() < 5)
    App::redirect('/');

if(isset($_GET['save']))
{
    $data = [];
    $data['theme_mobile'] = App::filter('input', $_POST['theme_mobile']);
    $data['language'] = App::filter('input', $_POST['language']);
    $data['items'] = App::filter('int', $_POST['items']);
    $data['timezone'] = App::filter('input', $_POST['timezone']);
    $data['site_status'] = ($_POST['site_status'] == 2 ? 2 : ($_POST['site_status'] == 1 ? 1 : 0));
    $data['registration_status'] = ($_POST['registration_status'] == 1 ? 1 : 0);

    if(is_dir(ROOT.'/themes/mobile/'.$data['theme_mobile']) && is_dir(SYS.'/locales/'.$data['language']) && !empty($data['items']))
    {
        App::writeConfig('system/data/config', $data);
        $_SESSION['alert'] = ['type' => 'notify', 'value' => _t('Saved')];
        App::redirect('/admin/settings');
    }
}

Site::header(_t('Basic Settings', 'admin').' - '._t('Dashboard', 'admin'));
echo Site::div('title', Site::breadcrumbs(_t('Basic Settings', 'admin')));
$config = App::config('system/data/config');
echo '<div class="content">
<form action="/admin/settings?save" method="post">
    <b>'._t('Language').':</b><br/>
     <select name="language">';
    $languages = scandir(SYS.DS.'locales');
    foreach($languages as $language)
    {
        if($language != '.' && $language != '..')
          echo '<option'.($config['language'] == $language ? ' selected="selected"' : '').' value="'.$language.'">'._t($language, 'languages').'</option>';
    }
echo '</select>
 <br/>
        <b>'._t('Items per page').'</b>:<br/>
         <input type="text" name="items" style="width: 5%;" value="'.$config['items'].'" /><br/>
         <b>'._t('Theme of site').'</b>:<br/>
         <select name="theme_mobile">';
$themes = scandir(ROOT.'/themes/mobile');
foreach($themes as $theme)
{
    if(is_file(ROOT.'/themes/mobile/'.$theme.'/theme.ini'))
    {
        $title = parse_ini_file(ROOT.'/themes/mobile/'.$theme.'/theme.ini')['title'];
        echo '<option'.($config['theme_mobile'] == $theme ? ' selected="selected"' : '').' value="'.$theme.'">'.$title.'</option>';
    }
}
echo '</select><br/>
         <b>'._t('Site timezone').'</b>:<br/>
         <select name="timezone">';
$timezones = timezone_identifiers_list();
foreach($timezones as $timezone)
{
    echo '<option'.($config['timezone'] == $timezone ? ' selected="selected"' : '').' value="'.$timezone.'">'.$timezone.'</option>';
}
echo '</select><br/>
<b>'._t('Site available for', 'admin').':</b><br/>
<select name="site_status">
    <option'.($config['site_status'] == 0 ? ' selected="selected"' : '').' value="0">'._t('For all', 'admin').'</option>
    <option'.($config['site_status'] == 1 ? ' selected="selected"' : '').' value="1">'._t('Only for authorized', 'admin').'</option>
    <option'.($config['site_status'] == 2 ? ' selected="selected"' : '').' value="2">'._t('Only for administration', 'admin').'</option>
</select><br/>
<b>'._t('Registration status', 'admin').':</b><br/>
<select name="registration_status">
    <option'.($config['registration_status'] == 0 ? ' selected="selected"' : '').' value="0">'._t('Opened', 'admin').'</option>
    <option'.($config['registration_status'] == 1 ? ' selected="selected"' : '').' value="1">'._t('Closed', 'admin').'</option>
</select><br/>
<input type="submit" value="'._t('Save').'" />
</form>
</div>';
Site::footer();