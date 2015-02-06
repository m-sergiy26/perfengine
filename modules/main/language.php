<?php
if(user()->isUser())
    App::redirect('/user/settings');

if(isset($_GET['set']))
{
    $lang = App::filter('input', $_GET['set']);
    if(is_file(SYS.DS.'locales/'.$lang.'/main.php'))
    {
        setcookie('language', $lang, (time()+3600*24*365), '/');
        $_SESSION['alert'] = ['type' => 'notify', 'value' => _t('Language successfully changed')];
        App::redirect('/');
    }
}

Site::header(_t('Change language'));
echo Site::div('title', Site::breadcrumbs(_t('Change language')));
$languages = scandir(SYS.DS.'locales');
echo '<div class="menu_list">';
foreach($languages as $language)
{
    if($language != '.' && $language !='..')
    {
        echo '<a href="/main/language?set=' . $language . '">' . Site::icon('flags/' . $language) . ' ' . mb_convert_case(_t($language, 'languages'), MB_CASE_TITLE) . '</a>';
    }
}
echo '</div>';
Site::footer();