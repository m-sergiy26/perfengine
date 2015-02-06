<?php

$dir_id = App::filter('int', $_GET['id']);
$db = App::db();

if($db->query("SELECT * FROM `downloads` WHERE `id` = '". $dir_id ."' AND `type` = '0'")->rowCount() == 0)
	{
		header('location: /downloads/');
		exit;
	}
$ref_id = $db->query("SELECT dir_id FROM `downloads` WHERE `id` = '". $dir_id ."'")->fetchColumn();
$dir_name = $db->query("SELECT name FROM `downloads` WHERE `id` = '". $dir_id ."'")->fetchColumn();
$title = $dir_name.' - '._t('Downloads', 'downloads');
Site::header($title);
echo Site::div('title', Site::breadcrumbs($dir_name));
$downloads_r = $db->query("SELECT * FROM `downloads` WHERE `dir_id` = '$dir_id'")->rowCount();
$pages = new Pager($downloads_r, Site::perPage());
if($downloads_r == 0)
{
	echo Site::div('content', _t('Folder is empty', 'downloads'));
}
else
{
	$downloads_q = $db->query("SELECT * FROM `downloads` WHERE `dir_id` = '".$dir_id."' ORDER BY `type` ASC, `name` DESC LIMIT ".$pages->start().", ".Site::perPage()."");
	echo '<div class="menu_list">';
	foreach($downloads_q as $downloads)
	{
		$downloads_file = $db->query("SELECT * FROM `downloads_files` WHERE `ref_id` = '$dir_id' AND `from_id` = '". $downloads['id'] ."'")->fetch();
		echo '<a href="/downloads/'.($downloads['type'] == 0 ? 'dir/'.$downloads['id'] : 'file/'.$downloads_file['id']).'">
			'.($downloads['type'] == 0 ? Site::icon('folder') : Site::ext($downloads_file['ext'])).' '.($downloads['type'] == 0 ? $downloads['name'] : $downloads_file['name'].' ('.$downloads_file['ext'].')').($downloads['type'] == 0 ? null : ' ['.App::fileSize($downloads_file['size']).']').'
			'.($downloads['type'] == 0 ? '('.Site::counter('downloads', ['dir_id' => $downloads['id'], 'type' => 0]).'/'.Site::counter('downloads', ['dir_id' => $downloads['id'], 'type' => 1]).')' : NULL).'
			<br/>
			'.($downloads['type']== 0 && !empty($downloads['description']) ? '<small>'. $downloads['description'] .'</small>' : ($downloads['type'] == 1 && !empty($downloads_file['description']) ? mb_substr($downloads_file['description'], 0, 100).'...' : NULL)).'
			</a>';
	}
	echo '</div>';
	$pages->view();
}

echo '<div class="action_list">';
if($dir_id != 0 && $db->query("SELECT access FROM `downloads` WHERE `id` = '$dir_id'")->fetchColumn() == 1 && user()->isUser())
{
	echo '<a href="/downloads/add_file/'.$dir_id.'">'.Site::icon('add') .' '. _t('Add file', 'downloads') .'</a>';
}
elseif($dir_id != 0 && $db->query("SELECT access FROM `downloads` WHERE `id` = '$dir_id'")->fetchColumn() == 0 && user()->isUser() && user()->level() > 3)
{
	echo '<a href="/downloads/add_file/'.$dir_id.'">'.Site::icon('add') .' '. _t('Add file', 'downloads') .'</a>';
}
echo (user()->level() >= 4 ? ' <a href="/downloads/add_dir/'.$dir_id.'">'.Site::icon('add') .' '. _t('Create folder', 'downloads') .'</a>' : null)
.($ref_id != 0 ? '<a href="/downloads/dir/'. $ref_id.'">'.Site::icon('folder') .' '.$db->query("SELECT name FROM `downloads` WHERE `dir_id` = '". $ref_id ."'")->fetchColumn().'</a>' : '')

	.'</div>';
Site::footer();