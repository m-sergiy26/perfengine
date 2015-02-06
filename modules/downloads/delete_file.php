<?php

$file_id = App::filter('int', $_GET['id']);
$db = App::db();

if(!isset($_GET['id']) && $db->query("SELECT * FROM `downloads_files` WHERE `id` = '$file_id'")->rowCount() == 0 || !user()->isUser())
{
	App::redirect('/downloads/');
}
if($db->query("SELECT user_id FROM `downloads_files` WHERE `id` = '$file_id'")->fetchColumn() != user()->getId() && user()->level() < 4)
{
	App::redirect('/downloads/');
}
$file = $db->query("SELECT * FROM `downloads_files` WHERE `id` = '". $file_id ."'")->fetch();
$root_dir = $db->query("SELECT `server_path` FROM `downloads` WHERE `id` = '". $file['ref_id'] ."'")->fetchColumn();
if(isset($_GET['act']) && $_GET['act'] == 'delete')
{
	if(isset($_POST['yes']))
	{
		App::rrmdir(ROOT.'/files/downloads/'.$root_dir.'/'.$file['server_dir']);
		$db->query("DELETE FROM `downloads_files` WHERE `id` = '". $file_id ."'");
		$db->query("DELETE FROM `downloads_comms` WHERE `downloads_id` = '". $file_id ."'");
		$db->query("DELETE FROM `downloads` WHERE `id` = '". $file['from_id'] ."'");
		App::redirect('/downloads/dir/'.$file['dir_id']);
	}
	elseif(isset($_POST['no']))
	{
		App::redirect('/downloads/file/'.$file_id);
	}
}
				
$title = _t('Delete file', 'downloads').' - '._t('Downloads', 'downloads');
Site::header($title);
echo Site::div('title', Site::breadcrumbs(_t('Delete file', 'downloads')));
echo '<div class="content">
		<form action="/downloads/delete_file/'.$file_id.'?act=delete" method="post">
		'._t('Do you really want to delete this file?').' <b>'.$file['name'].'</b>?<br/>
		<input type="submit" name="yes" value="'. _t('Yes') .'" /> <input type="submit" name="no" value="'. _t('No') .'" />
		</form>
	</div>';
echo Site::div('action_list', '<a href="/downloads/file/'.$file_id.'">'.Site::icon('arrow-left').' '._t('Back').'</a>');
Site::footer();