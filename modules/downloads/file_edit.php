<?php

$file_id = App::filter('int', $_GET['id']);

$db = App::db();

if(!isset($_GET['id']) && $db->query("SELECT * FROM `downloads_files` WHERE `id` = '$file_id'")->rowCount() == 0 || !user()->isUser())
{
	App::redirect('/downloads/');
}
	
if($db->query("SELECT user_id FROM `downloads_files` WHERE `id` = '$file_id'")->fetchColumn() != user()->getId() && user()->level() < 4)
{
	App::redirect('/downloads/file/'.$file_id);
}

$file = $db->query("SELECT * FROM `downloads_files` WHERE `id` = '". $file_id ."'")->fetch();
if(isset($_GET['act']) && $_GET['act'] == 'save')
	{
		$name = mb_substr(App::filter('input', $_POST['file_name']), 0, 100);
		$desc = App::filter('input', $_POST['file_desc']);
		$dir = App::filter('int', $_POST['directory']);
		if(!empty($name))
		{
			$db->query("UPDATE `downloads_files` SET `name` = '".$name."', `description` = '".$desc."', `ref_id` = '".$dir."' WHERE `id` = '$file_id'");
			$db->query("UPDATE `downloads` SET `name` = '$name', `description` = '$desc', `dir_id` = '$dir' WHERE `id` = '".$file['from_id']."'");
			if($dir == 0)
				$new_path = '';
			else
				$new_path = $db->query("SELECT server_path FROM `downloads` WHERE `id` = '".$dir."'")->fetchColumn();

			$root_dir = $db->query("SELECT server_path FROM `downloads` WHERE `id` = '". $file['ref_id'] ."'")->fetchColumn();

			if(!is_dir(ROOT.DS.'files/downloads'.$new_path.DS.$file['server_dir']))
				mkdir(ROOT.DS.'files/downloads'.$new_path.DS.$file['server_dir']);

			copy(ROOT.DS.'files/downloads'.$root_dir.DS.$file['server_dir'].DS.$file['server_name'], ROOT.DS.'files/downloads'.$new_path.DS.$file['server_dir'].DS.$file['server_name']);

			App::rrmdir(ROOT.DS.'files/downloads'.$root_dir.DS.$file['server_dir']);

			App::redirect('/downloads/file/'.$file_id);
		}
	}
$title = _t('Edit file', 'downloads').' - '._t('Downloads', 'downloads');
Site::header($title);
echo Site::div('title', Site::breadcrumbs(_t('Edit file', 'downloads')));
echo '<div class="content">
		<form action="/downloads/file_edit/'.$file_id.'?act=save" method="post">
		<input type="text" placeholder="'._t('Title').'" value="'.$file['name'].'" name="file_name" /><br/>
		<textarea placeholder="'._t('Description').'" name="file_desc" rows="5">'.$file['description'].'</textarea><br/>';
$directories = $db->query("SELECT * FROM `downloads` WHERE `type` = '0'".(user()->level() < 5 ? " AND `access` = '1'" : null)."");
if($directories->rowCount() != 0)
{
	echo _t('Move').':<br/>
	<select name="directory">
	'.(user()->level() >=4 ? '<option value="0"'.($file['ref_id'] == 0 ? ' selected="selected"' : null).'>Home folder</option>' : null);
	foreach($directories as $directory)
	{
		echo '<option value="'.$directory['id'].'"'.($file['ref_id'] == $directory['id'] ? ' selected="selected"' : null).'>'.$directory['name'].'</option>';
	}
	echo '</select><br/>';
}
echo '<input type="submit" value="'. _t('Save') .'" />
		</form>
	</div>';
echo Site::div('action_list', ' <a href="/downloads/file/'.$file_id.'">'. Site::icon('arrow-left').' '. _t('Back') .'</a>');
Site::footer();