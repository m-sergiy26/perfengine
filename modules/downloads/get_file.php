<?php

$file_id = @App::filter('int', $_GET['id']);
$att_id = @App::filter('int', $_GET['attachment_id']);

$db = App::db();

if(isset($_GET['id']) && $db->query("SELECT * FROM `downloads_files` WHERE `id` = '". $file_id ."'")->rowCount() !=0)
{
	$afile = $db->query("SELECT * FROM `downloads_files` WHERE `id` = '". $file_id ."'")->fetch();
	$root_dir = $db->query("SELECT server_path FROM `downloads` WHERE `id` = '". $afile['ref_id'] ."'")->fetchColumn();
	$db->query("UPDATE `downloads_files` SET `dl_times` = '". ($afile['dl_times']+1) ."' WHERE `id` = '". $file_id ."'");
	App::redirect('/files/downloads/'.$root_dir.'/'.$afile['server_dir'].'/'.$afile['server_name']);
}
elseif(isset($_GET['attachment_id']) && $db->query("SELECT * FROM `downloads_archive` WHERE `id` = '". $att_id ."'")->rowCount() !=0)
{
	$afile = $db->query("SELECT * FROM `downloads_archive` WHERE `id` = '". $att_id ."'")->fetch();
	$ffile = $db->query("SELECT * FROM `downloads_files` WHERE `id` = '". $afile['file_id'] ."'")->fetch();
	$root_dir = $db->query("SELECT server_path FROM `downloads` WHERE `id` = '". abs(intval($ffile['ref_id'])) ."'")->fetchColumn();
	$db->query("UPDATE `downloads_files` SET `dl_times` = '". ($ffile['dl_times']+1) ."' WHERE `id` = '". $ffile['id'] ."'");
	App::redirect('/files/downloads/'.$root_dir.'/'.$ffile['server_dir'].'/'.$afile['server_name']);
}
else
	App::redirect('/downloads');