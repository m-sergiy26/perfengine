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
$fileAtt = $db->query("SELECT * FROM `downloads_archive` WHERE `id` = '". $file_id ."'")->fetch();
$file = $db->query("SELECT * FROM `downloads_files` WHERE `id` = '". $fileAtt['file_id'] ."'")->fetch();
$root_dir = $db->query("SELECT `server_path` FROM `downloads` WHERE `id` = '". $file['ref_id']."'")->fetchColumn();
if(isset($_GET['delete']))
{
	unlink(ROOT.'/files/downloads/'.$root_dir.'/'.$file['server_dir'].'/'.$fileAtt['server_name']);
	$db->query("DELETE FROM `downloads_archive` WHERE `id` = '". $file_id ."'");
	App::redirect('/downloads/file/'.$file_id);
}