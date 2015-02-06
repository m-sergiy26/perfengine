<?php
if(!user()->isUser())
    App::redirect('/user/login');
if(isset($_GET['save']))
{
    $allow_messages = isset($_POST['pm']) ? 1 : 0;
    $theme_mobile = App::filter('input', $_POST['theme_mobile']);
    $lng = App::filter('input', $_POST['language']);
    $items = App::filter('int', $_POST['items']);
    $timezone = App::filter('input', $_POST['timezone']);

    if(is_dir(ROOT.'/themes/mobile/'.$theme_mobile) && is_dir(SYS.'/locales/'.$lng) && !empty($items))
    {
        $db = App::db();
        $settings = $db->prepare("UPDATE `settings` SET `allow_messages` = ?, `theme_mobile` = ?, `language` = ?, `items` = ?, `timezone` = ? WHERE `user_id` = ?");
        $settings->execute([$allow_messages, $theme_mobile, $lng, $items, $timezone, user()->getId()]);
        $_SESSION['alert'] = ['type' => 'notify', 'value' => _t('Settings have been saved')];
//        var_dump($db->errorInfo());
        App::redirect('/user/settings');
    }
}
Site::header(_t('Settings'));
echo Site::div('title', Site::breadcrumbs(_t('Settings')));
echo '<div class="content">
    <form action="/user/settings?save" method="post">
        <b>'._t('Language').':</b><br/>
        <select name="language">';
        $languages = scandir(SYS.DS.'locales');
        foreach($languages as $language)
        {
            if($language != '.' && $language != '..')
                echo '<option'.(user()->config('language') == $language ? ' selected="selected"' : '').' value="'.$language.'">'._t($language, 'languages').'</option>';
        }
        echo '</select>
        <br/>
        <b>'._t('Items per page').'</b>:<br/>
         <input type="text" name="items" style="width: 5%;" value="'.user()->config('items').'" /><br/>
         <b>'._t('Theme of site').'</b>:<br/>
         <select name="theme_mobile">';
        $themes = scandir(ROOT.'/themes/mobile');
        foreach($themes as $theme)
        {
            if(is_file(ROOT.'/themes/mobile/'.$theme.'/theme.ini'))
            {
                $title = parse_ini_file(ROOT.'/themes/mobile/'.$theme.'/theme.ini')['title'];
                echo '<option'.(user()->config('theme_mobile') == $theme ? ' selected="selected"' : '').' value="'.$theme.'">'.$title.'</option>';
            }
        }
    echo '</select><br/>
         <b>'._t('Site timezone').'</b>:<br/>
         <select name="timezone">';
            $timezones = timezone_identifiers_list();
            foreach($timezones as $timezone)
            {
                echo '<option'.(user()->config('timezone') == $timezone ? ' selected="selected"' : '').' value="'.$timezone.'">'.$timezone.'</option>';
            }
        echo '</select><br/>
        <b>'._t('Allow private messages').'</b>: <input type="checkbox" name="pm" '.(user()->config('allow_messages') == 1 ? 'checked="checked"' : '').'/><br/>
        <input type="submit" value="'._t('Save').'"/>
    </form>
</div>';
Site::footer();