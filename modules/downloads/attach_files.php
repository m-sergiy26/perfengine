<?php
$db = App::db();
if(!isset($_GET['id']) && $db->query("SELECT * FROM `downloads_files` WHERE `id` = '".App::filter('int', $_GET['id'])."'")->rowCount() == 0 || !user()->isUser())
{
	App::redirect('/downloads/');
}

$file_id = App::filter('int', $_GET['id']);

if($db->query("SELECT user_id FROM `downloads_files` WHERE `id` = '$file_id'")->fetchColumn() != user()->getId() && user()->level() < 5)
{
	App::redirect('/downloads/');
}
$filei = $db->query("SELECT * FROM `downloads_files` WHERE `id` = '". $file_id ."'")->fetch();
$root_dir = $db->query("SELECT server_path FROM `downloads` WHERE `id` = '". $filei['ref_id'] ."'")->fetchColumn();
if(isset($_POST['upload']))
{
	$numf = substr(abs(intval($_POST['dl_num_files'])), 0, 2);
	$err = false;
	for($i=1;$i<=$numf;$i++)
	{
		if($_FILES['dl_num_file_'.$i]['tmp_name'] && !empty($_POST['dl_name_file_'.$i]))
		{
			$namef = App::filter('input', $_POST['dl_name_file_'.$i]);
			$file_info = pathinfo($_FILES['dl_num_file_'.$i]['name']);
			$file_info['extension'] = strtolower($file_info['extension']);
			$servname = App::translit($file_info['filename']).'.'.$file_info['extension'];
			if (!in_array($file_info['extension'], explode(';', App::config('system/data/config')['filetypes']))) $err = 'File extension not allowed.';
			if($err == false)
			{
				move_uploaded_file($_FILES['dl_num_file_'.$i]['tmp_name'], ROOT.'/files/downloads'.$root_dir.'/'.$filei['server_dir'].'/'.$servname);
				$insert_fl = $db->prepare("INSERT INTO `downloads_archive` SET `name` = ?, `file_id` = ?, `server_name`=?, `size` = ?, `ext` = ?");
				$insert_fl->execute([$namef, $filei['id'], $servname, $_FILES['dl_num_file_'.$i]['size'], $file_info['extension']]);
				$db->query("UPDATE `downloads_files` SET `time` = '". time() ."' WHERE `id` = '".$filei['id']."'");
				App::redirect('/downloads/file/'.$file_id);
			}
			else
			{
				$_SESSION['alert'] = ['type' => 'error', 'value' => $err];
			}
		}
	}
}
$title = _t('Attach file', 'downloads').' | '._t('Downloads', 'downloads');
Site::header($title);
echo Site::div('title', Site::breadcrumbs(_t('Attach file', 'downloads')));
echo '<div class="content">
		<form action="/downloads/attach_files/'.$file_id.'?" method="post" enctype="multipart/form-data">
		'._t('Number of files', 'downloads').': <input type="text" style="width: 2%;" value="1" name="dl_num_files" />
		<input type="submit" value="Go!" /><br/>';
		if(isset($_POST['dl_num_files']))
		{
			$num_files = substr(App::filter('int', $_POST['dl_num_files']), 0, 1);
			for($i=1;$i<=$num_files;$i++)
			{
				echo _t('Title').' '.$i.':<br/>
				<input type="text" name="dl_name_file_'.$i.'" /><br/>
				'._t('Choose file').' '.$i.':<br/>
				<input type="file" name="dl_num_file_'.$i.'" /><br/>';
			}
			echo ' 	<input name="upload" type="submit" value="'. _t('add') .'" />';
		}
		echo '</form>';
echo	'</div>';
echo Site::div('action_list', '<a href="/downloads/file/'.$file_id.'">'. Site::icon('arrow-left').' '. _t('back') .'</a>');

Site::footer();