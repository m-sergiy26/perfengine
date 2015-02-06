<?php
if(user()->level() < 5)
    App::redirect('/');

Site::header(_t('System info', 'admin'));
echo Site::div('title', Site::breadcrumbs(_t('System info', 'admin')));
$localVersion = file_get_contents(SYS.DS.'data/version.inf');
$lastVersion = file_get_contents('http://perf-engine.net/.perfdata/last_version.txt');
echo '<div class="content">
'._t('PerfEngine local version', 'admin').': '.$localVersion.'<br/>
'._t('PerfEngine last version', 'admin').': '.$lastVersion.'<br/>
'._t('PHP version', 'admin').': '.PHP_VERSION.'<br/>
'._t('Operating system', 'admin').': '.PHP_OS.'<br/>
'._t('Sever software', 'admin').': '.$_SERVER['SERVER_SOFTWARE'].'<br/>
</div>';
Site::footer();