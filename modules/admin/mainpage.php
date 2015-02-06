<?php
if(user()->level() < 5)
    App::redirect('/');

if(isset($_GET['save']))
{

    file_put_contents(ROOT.DS.'cache/templates/mainpage.htm', trim(htmlspecialchars($_POST['page'], ENT_QUOTES)));
    $page = (string) trim($_POST['page']);
    $patterns = ['/translate\[(.*?)\|(.*?)\]/i',
        '/translate\[(.*?)\]/i',
        '/module_widget\[(.*?)\]/i',
        '/theme_icon\[(.*?)\]/i'];
    $replacements = ["<?=_t('$1', '".trim('$2')."')?>", "<?=_t('$1')?>", "<?=App::moduleWidget('$1')?>", "<?=Site::icon('$1')?>"];

    $page = preg_replace($patterns, $replacements, $page);
    file_put_contents(ROOT.DS.'cache/compiled/mainpage.phtml', $page);
    $_SERVER['alert'] = ['type' => 'notify', 'value' => _t('Main page saved', 'admin')];
    App::redirect('/admin/mainpage');
}

Site::header(_t('Main page', 'admin').' - '._t('Dashboard', 'admin'));
echo Site::div('title', Site::breadcrumbs(_t('Main page', 'admin')));
echo '<div class="content">
<form action="/admin/mainpage?save" method="post">
 <textarea rows="6" name="page"> '.file_get_contents(ROOT.DS.'cache/templates/mainpage.htm').'</textarea><br/>
 <input type="submit" value="'._t('Save').'" />
  </form>
 </div>';
Site::footer();