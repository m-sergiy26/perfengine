<?php

$dir_id = (isset($_GET['id']) ? App::filter('int', $_GET['id']) : 0);
$db = App::db();

if($dir_id != 0 && $db->query("SELECT * FROM `downloads` WHERE `id` = '$dir_id'")->rowCount() == 0)
{
	Site::notFound();
}

if(!user()->isUser())
{
	App::redirect('/downloads/dir/'.$dir_id);
}
if(($dir_id != 0 && $db->query("SELECT access FROM `downloads` WHERE `id` = '$dir_id'")->fetchColumn() == 0) && user()->level() < 4)
{
	App::redirect('/downloads/dir/'.$dir_id);
}

if(isset($_GET['add']))
{
	if($_POST['type'] == 0 && $_FILES['dl_file']['tmp_name']) 
	{	
		$name = mb_substr(App::filter('input', $_POST['file_name']), 0, 100);
		$desc = App::filter('input', $_POST['file_desc']);
		$trans_name = App::translit(App::filter('input', $_POST['file_name']));
		$root_dir = ($dir_id == 0 ? '' : $db->query("SELECT server_path FROM `downloads` WHERE `id` = '". $dir_id ."'")->fetchColumn());
		$file_info = pathinfo($_FILES['dl_file']['name']);
		$file_info['extension'] = strtolower($file_info['extension']);

		if (!in_array($file_info['extension'], explode(';', App::config('system/data/config')['filetypes']))) 
		{ 
			$err = 'File extension not allowed';
		}
		
		$servname = App::translit($file_info['filename']).'.'.$file_info['extension'];
		if (is_file(ROOT.'/files/downloads/'.$root_dir.'/'.$trans_name.'/'.$servname))
		{ 
			$err = 'This is file exists<br />'; 
		}
			
		if(!isset($err) && !empty($name))
		{
			mkdir(ROOT.'/files/downloads/'.$root_dir.'/'.$trans_name);
			move_uploaded_file($_FILES['dl_file']['tmp_name'], ROOT.'/files/downloads/'.$root_dir.'/'.$trans_name.'/'.$servname);
			$insert_dl = $db->prepare("INSERT INTO `downloads` SET `name` = ?, `description` = ?, `type` = ?, `dir_id` = ?, `server_path` = ?");
			$insert_dl->execute([$name, $desc, '1', $dir_id, '']);
			// print_r($db->errorInfo());
			$insertId = $db->lastInsertId();
					
			$insert_fl = $db->prepare("INSERT INTO `downloads_files` SET `name` = ?, `description` = ?, `server_name` = ?, `server_dir` = ?, `ext` = ?, `user_id` = ?, `time` = ?, `ref_id` = ?, `from_id` = ?, `size` = ?, `dl_times` = ?");
			// print_r($db->errorInfo());
			$insert_fl->execute([$name, $desc, $servname, $trans_name, $file_info['extension'], user()->getId(), time(), $dir_id, $insertId, $_FILES['dl_file']['size'], '0']);
			$lastId = $db->lastInsertId();
			if(preg_match('/png|jpg|jpeg|gif/i', $file_info['extension'])) 
			{
				copy(ROOT.'/files/downloads/'.$root_dir.'/'.$trans_name.'/'.$servname, ROOT.'/files/downloads_screens/'.$servname);
				$handle = new Jimage();
				$handle->thumb(ROOT.'/files/downloads_screens/'.$servname, ROOT.'/cache/downloads_thumbs/'.$servname.'.png', 128, 160);
				unlink(ROOT.'/files/downloads_screens/'.$servname);
			}
			App::redirect('/downloads/file/'.$lastId);
		}
		else
		{
			$_SESSION['alert'] = ['type' => 'error', 'value' => $err];
			App::redirect('/downloads/add_file/'.$dir_id);
		}
	}
	elseif($_POST['type'] == 1 && !empty($_POST['file'])) 
	{
		$name = mb_substr(App::filter('input', $_POST['file_name']), 0, 100);
		$desc = App::filter('input', $_POST['file_desc']);
		$_name = App::translit(App::filter('input', $_POST['file_name']));
		$trans_name = preg_replace('/[^а-яА-Яa-zA-Z0-9_-]/isU', '', strtolower($_name));
		$root_dir = ($dir_id == 0 ? '' : $db->query("SELECT server_path FROM `downloads` WHERE `id` = '". $dir_id ."'")->fetchColumn());
	
		$headerInfo = get_headers(App::filter('input', $_POST['file']), 1);
		// print_r($headerInfo);
		// exit;
	
		if($headerInfo[0] !='HTTP/1.1 200 OK')
		{
			$err = 'File Not Found';
		}
		
		$fileTypes = array(
		'audio/amr',
		'audio/x-wav',
		'application/x-tar',
		'image/jpeg',
		'image/jpg',
		'image/gif',
		'image/png',
		'image/bmp',
		'application/java-archive',
		'application/vnd.symbian.install',
		'audio/wav',
		'audio/midi',
		'audio/rmf',
		'video/x-msvideo',
		'audio/mpeg',
		'video/flv',
		'application/x-shockwave-flash',
		'video/mp4',
		'video/mpeg',
		'video/3gpp', 
		'application/zip',
		'application/apk',
		// 'text/plain',
		'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
		);
				
		if(!in_array($headerInfo['Content-Type'], $fileTypes))
		{
			$err = 'Content-Type not allowed';
		}
			
		$urlinfo = pathinfo(parse_url(App::filter('input', $_POST['file']), PHP_URL_PATH));
		$urlinfo['extension'] = strtolower($urlinfo['extension']);

		if (!in_array($urlinfo['extension'], explode(';', App::config('system/data/config')['filetypes'])))
		{
			$err = 'File extension not allowed';
		}
		$serv_name = App::translit($urlinfo['filename']);
		$servname = $serv_name.'.'.$urlinfo['extension'];
		if (is_file(ROOT.'/files/downloads/'.$root_dir.'/'.$trans_name.'/'.$servname))
		{ 
			$err = 'This file exists';
		}
								
		if(isset($err) && !empty($name))
		{
			mkdir(ROOT.'/files/downloads/'.$root_dir.'/'.$trans_name);
			copy(App::filter('input', $_POST['file']), ROOT.'/files/downloads/'.$root_dir.'/'.$trans_name.'/'.$servname);
			$filesize = filesize(ROOT.'/files/downloads/'.$root_dir.'/'.$trans_name.'/'.$servname);
			$insert_dl = $db->prepare("INSERT INTO `downloads` SET `name` = ?, `description` = ?, `type` = ?, `dir_id` = ?, `server_path` = ?");
			$insert_dl->execute([$name, $desc, '1', $dir_id, '']);
			// print_r($db->errorInfo());
			$insertId = $db->lastInsertId();
				
			$db->prepare("INSERT INTO `downloads_files` SET `name` = ?, `description` = ?, `server_name` = ?, `server_dir` = ?, `ext` = ?, `user_id` = ?, `time` = ?, `ref_id` = ?, `from_id` = ?, `size` = ?, `dl_times` = ?");
			$insert_fl->execute([$name, $desc, $servname, $trans_name, $urlinfo['extension'], user()->getId(), time(), $dir_id, $insertId, $filesize, '0']);
			// print_r($db->errorInfo());
			$lastId = $db->lastInsertId();
			if(preg_match('/png|jpg|jpeg|gif/i', $urlinfo['extension'])) 
			{
				copy(ROOT.'/files/downloads/'.$root_dir.'/'.$trans_name.'/'.$servname, ROOT.'/tmp/'.$servname);
				$handle = new Jimage();
				$handle->thumb(ROOT.'/tmp/'.$servname, ROOT.'/cache/downloads_thumbs/cache_'.$servname.'_'.$lastId.'.png', 128, 160);
				unlink(ROOT.'/tmp/'.$servname);
			}
			App::redirect('/downloads/file/'.$lastId);
		}
		else
		{
			$_SESSION['alert'] = ['type' => 'error', 'value' => $err];
			App::redirect('/downloads/add_file/'.$dir_id);
		}
	}
			// print_r($_POST);
}
		
		Site::header(_t('Add file', 'downloads'));
		echo '<div class="title">'.Site::breadcrumbs(_t('Add file', 'downloads')).'</div>';
		echo '<div class="content">
		<form action="/downloads/add_file?add'.($dir_id != 0 ? '&amp;id='.$dir_id : NULL).'" method="post" enctype="multipart/form-data">
		<input type="text" name="file_name" placeholder="'._t('Title').'"/><br/>
		<input type="radio" name="type" value="0" checked="checked" />'. _t('Choose file') .':<br/>
			<input name="dl_file" type="file" /><br/>
		<input type="radio" name="type" value="1" /> '. _t('Import from Internet') .':<br/>
			<input name="file" type="text" value="http://" /><br/>
		<textarea name="file_desc" placeholder="'._t('Description').'" rows="5" cols="25"></textarea><br/>
		<input type="submit" value="'. _t('Add') .'" />
		</form>
		</div>';
		
		echo '<div class="action_list">'.($dir_id != 0 ? '<a href="/downloads/dir/'. $dir_id.'">'.Site::icon('folder') .' '.$db->query("SELECT name FROM `downloads` WHERE `id` = '". $dir_id ."'")->fetchColumn().'</a>' : '').'
				</div>';
Site::footer();