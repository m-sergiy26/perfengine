<?php

$dir_id = (isset($_GET['id']) ? App::filter('int', $_GET['id']) : 0);
$db = App::db();
//var_dump($dir_id);
if($dir_id != 0 && $db->query("SELECT * FROM `downloads` WHERE `id` = '$dir_id'")->rowCount() == 0 || user()->level() < 4)
	Site::notFound();

if(isset($_GET['add']))
{
	$err = null;
	$name = App::filter('input', $_POST['dir_name']);
	$desc = App::filter('input', $_POST['dir_desc']);
	$trans_name = App::translit($name);
	$access = (App::filter('int', $_POST['access']) == 1 ? 1 : 0);
	$serverPath = ($dir_id !=0 ? $db->query("SELECT server_path FROM `downloads` WHERE `id` = '$dir_id'")->fetchColumn() : false).'/'.strtolower($trans_name);
		if($db->query("SELECT * FROM `downloads` WHERE `server_path` = '$trans_name' AND 'type' = '0'")->rowCount() > 0)
		{
				$err = 'Folder already exists';
		}
		if(is_dir(ROOT.'/files/downloads/'.$serverPath)) $err = 'Can\'t create folder';
		if(!$err && !empty($name))
		{
			$db->query("INSERT INTO `downloads` SET `name` = '$name', `server_path` = '$serverPath', `description` = '$desc', `type` = '0', `dir_id` = '$dir_id', `access` = '$access'");
			mkdir(ROOT.'/files/downloads/'.$serverPath);
			App::redirect('/downloads/dir/'.$dir_id);
		}
		elseif($err)
		{
			$_SESSION['alert'] = ['type' => 'error', 'value' => $err];
		}
}

Site::header(_t('Create folder', 'downloads').' - '._t('Downloads', 'downloads'));
echo Site::div('title', Site::breadcrumbs(_t('Create folder', 'downloads')));
echo '<div class="content">
		<form action="/downloads/add_dir/'.($dir_id != 0 ? $dir_id : NULL).'?add" method="post">
		<input type="text" name="dir_name" placeholder="'._t('Title').'"/><br/>
		<input type="text" name="dir_desc" placeholder="'._t('Description').'"/><br/>
		'. _t('Right to unloading', 'downloads') .':<br/>
		<select name="access">
		<option value="0">'. _t('All users', 'downloads') .'</option>
		<option value="1">'. _t('Only administration', 'downloads') .'</option>
		</select><br/>
		<input type="submit" value="'. _t('Create') .'" />
		</form>
	</div>';
echo Site::div('action_list', ($dir_id != 0 ? '<a href="/downloads/dir/'. $dir_id.'">'. Site::icon('arrow-left').' '.$db->query("SELECT name FROM `downloads` WHERE `id` = '". $dir_id ."'")->fetchColumn().'</a>' : '' ));
Site::footer();