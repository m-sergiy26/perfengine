<?php
$dir_id = 0;
Site::header(_t('Downloads', 'downloads'));
echo Site::div('title', Site::breadcrumbs(_t('Downloads', 'downloads')));
$db = App::db();
$downloads_r = $db->query("SELECT * FROM `downloads` WHERE `dir_id` = '0'")->rowCount();
$pages = new Pager($downloads_r, Site::perPage());
if($downloads_r == 0)
{
	echo Site::div('content', _t('Folder is empty', 'downloads'));
}
else
	{
		$downloads_q = $db->query("SELECT * FROM `downloads` WHERE `dir_id` = '0' ORDER BY `type` ASC, `name` DESC LIMIT ".$pages->start().", ".Site::perPage()."");
		echo '<div class="menu_list">';
		foreach($downloads_q as $downloads)
		{
			$downloads_file = $db->query("SELECT * FROM `downloads_files` WHERE `ref_id` = '$dir_id' AND `from_id` = '". $downloads['id'] ."'")->fetch();
			echo '<a href="/downloads/'.($downloads['type'] == 0 ? 'dir/'.$downloads['id'] : 'file/'.$downloads_file['id']).'">
			'.($downloads['type'] == 0 ? Site::icon('folder') : Site::ext($downloads_file['ext'])).' '.($downloads['type'] == 0 ? $downloads['name'] : $downloads_file['name'].' ('.$downloads_file['ext'].')').($downloads['type'] == 0 ? null : ' ['.App::fileSize($downloads_file['size']).']').'
			'.($downloads['type'] == 0 ? '('.Site::counter('downloads', ['dir_id' => $downloads['id'], 'type' => '0']).'/'.Site::counter('downloads', ['dir_id' => $downloads['id'], 'type' => 1]).')' : NULL).'
			<br/>
			'.($downloads['type']== 0 && !empty($downloads['description']) ? '<small>'. $downloads['description'] .'</small>' : ($downloads['type'] == 1 && !empty($downloads_file['description']) ? mb_substr($downloads_file['description'], 0, 100).'...' : NULL)).'
			</a>';
		}
		echo '</div>';
		$pages->view();
	}
echo Site::div('action_list', '<a href="/downloads/search">'.Site::icon('search') .' '. _t('Search') .'</a>'
	. (user()->level() >=4 ? ' <a href="/downloads/add_file?">'.Site::icon('arrow-left').' '. _t('Add file', 'downloads') .'</a>'.
		'<a href="/downloads/add_dir?">'.Site::icon('arrow-left').' '. _t('Create folder', 'downloads') .'</a>' : NULL));
Site::footer();