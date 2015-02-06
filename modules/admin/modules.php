<?php
if(user()->level() < 5)
    App::redirect('/');

if(isset($_GET['save']))
{
    if(App::isModule(App::filter('input', $_GET['save'])))
    {
        $data = [];
        $data['status'] = $_POST['status'] == 1 ? 1 : 0;
        $data['access'] = ($_POST['access'] == 2 ? 2 : ($_POST['access'] == 1 ? 1 : 0));

        App::writeConfig('modules/'.App::filter('input', $_GET['save']).'/_module', $data);
        $_SERVER['alert'] = ['type' => 'notify', 'value' => _t('Module settings saved')];
        App::redirect('/admin/modules#data-'.App::filter('input', $_GET['save']));
    }
}

Site::header(_t('Advanced Settings', 'admin').' - '._t('Dashboard', 'admin'));
echo Site::div('title', Site::breadcrumbs(_t('Advanced Settings', 'admin')));

$modules = scandir(ROOT.DS.'modules');
foreach($modules as $module)
{
    if (App::isModule($module))
    {
        $config = App::config('modules/'.$module.'/_module');
        echo '<div class="content">';
        echo '<a href="#" onclick="$(\'#data-'.$module.'\').toggle();">'._t(ucfirst($module), $module).'</a>
        <span style="display:none;" id="data-'.$module.'">
            <form action="/admin/modules?save='.$module.'" method="post">
                <b>'._t('Module status', 'admin').'</b>:<br/>
                <select name="status">
                <option'.($config['status'] == 0 ? ' selected="selected"' : '').' value="0">'._t('Opened', 'admin').'</option>
                <option'.($config['status'] == 1 ? ' selected="selected"' : '').' value="1">'._t('Closed', 'admin').'</option>
                </select><br/>
                <b>'._t('Module available for', 'admin').'</b>:<br/>
                <select name="access">
                    <option'.($config['access'] == 0 ? ' selected="selected"' : '').' value="0">'._t('For all', 'admin').'</option>
                    <option'.($config['access'] == 1 ? ' selected="selected"' : '').' value="1">'._t('Only for authorized', 'admin').'</option>
                    <option'.($config['access'] == 2 ? ' selected="selected"' : '').' value="2">'._t('Only for administration', 'admin').'</option>
                </select>
                <br/>
                <input type="submit" value="'._t('Save').'" />
            </form>
        </span>
        </div>';
    }
}
Site::footer();