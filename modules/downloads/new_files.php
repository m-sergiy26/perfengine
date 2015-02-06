<?php
Site::header(_t('New files', 'downloads'));
echo Site::div('title', Site::breadcrumbs(_t('New files', 'downloads')));
$db = App::db();
$files_r = $db->query("SELECT * FROM `downloads_files` WHERE `time` > '". (time()-60*60*24) ."'")->rowCount();
$pages = new Pager($files_r, Site::perPage());
if($files_r == 0)
{
	echo Site::div('content', _t('No files', 'downloads'));
} 
else
{
	echo '<div class="menu_list">';
	$files = $db->query("SELECT * FROM `downloads_files` WHERE `time` > '". (time()-60*60*24) ."' ORDER BY time DESC LIMIT ".$pages->start().", ".Site::perPage()."");
	foreach($files as $file)
	{
		echo ' <a href="/downloads/file/'. $file['id'] .'">'.Site::ext($file['ext']).' '. $file['name'] .' ('.$fils['ext'].') ['.App::fileSize($file['size']).']<br/>
		 '. mb_substr($file['description'], 0, 100).'...</a>';
	}
	echo '</div>';
	$pages->view();
}