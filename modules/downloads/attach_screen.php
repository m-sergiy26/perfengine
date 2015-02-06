<?php

$file_id = App::filter('int', $_GET['id']);
$db = App::db();

if(!isset($_GET['id']) && $db->query("SELECT * FROM `downloads_files` WHERE `id` = '$file_id'")->rowCount() == 0 || !user()->isUser())
{
	Site::notFound();
}

if($db->query("SELECT user_id FROM `downloads_files` WHERE `id` = '$file_id'")->fetchColumn() != user()->getId() && user()->level() < 4)
{
	App::redirect('/downloads/');
}

$file = $db->query("SELECT * FROM `downloads_files` WHERE `id` = '". $file_id ."'")->fetch();
if(isset($_GET['act']) && $_GET['act'] == 'save')
{
	if ($_FILES['screen_1']['tmp_name'])
	{
			if(is_file(ROOT.'/files/downloads_thumbs/screen_1_'.$file['server_name'].'.png')) unlink(ROOT.'/files/downloads_thumbs/screen_1_'.$file['server_name'].'.png');
		
			$file_info = pathinfo($_FILES['screen_1']['name']);
			$file_info['extension'] = strtolower($file_info['extension']);
			move_uploaded_file($_FILES['screen_1']['tmp_name'], ROOT.'/tmp/'.$file_info['filename'].'.'.$file_info['extension']);
			$handle = new Jimage();
			$handle->thumb(ROOT.'/tmp/'.$file_info['filename'].'.'.$file_info['extension'], ROOT. '/files/downloads_thumbs/screen_1_'.$file['server_name'].'.png', 128, 160);
			unlink(ROOT.'/tmp/'.$file_info['filename'].'.'.$file_info['extension']);
	}
	if ($_FILES['screen_2']['tmp_name'])
	{
		if(is_file(ROOT.'/files/downloads_thumbs/screen_2_'.$file['server_name'].'.png')) unlink(ROOT.'/files/downloads_thumbs/screen_2_'.$file['server_name'].'.png');
		$file_info = pathinfo($_FILES['screen_2']['name']);
		$file_info['extension'] = strtolower($file_info['extension']);
		move_uploaded_file($_FILES['screen_2']['tmp_name'], ROOT.'/tmp/'.$file_info['filename'].'.'.$file_info['extension']);
		$handle = new Jimage();
		$handle->thumb(ROOT.'/tmp/'.$file_info['filename'].'.'.$file_info['extension'], ROOT. '/files/downloads_thumbs/screen_2_'.$file['server_name'].'.png', 128, 160);
		unlink(ROOT.'/tmp/'.$file_info['filename'].'.'.$file_info['extension']);
	}
	if ($_FILES['screen_3']['tmp_name'])
	{
		if(is_file(ROOT.'/files/downloads_thumbs/screen_3_'.$file['server_name'].'.png')) unlink(ROOT.'/files/downloads_thumbs/screen_3_'.$file['server_name'].'.png');
		$file_info = pathinfo($_FILES['screen_3']['name']);
		$file_info['extension'] = strtolower($file_info['extension']);
		move_uploaded_file($_FILES['screen_3']['tmp_name'], ROOT.'/tmp/'.$file_info['filename'].'.'.$file_info['extension']);
		$handle = new Jimage();
		$handle->thumb(ROOT.'/tmp/'.$file_info['filename'].'.'.$file_info['extension'], ROOT. '/files/downloads_thumbs/screen_3_'.$file['server_name'].'.png', 128, 160);
		unlink(ROOT.'/tmp/'.$file_info['filename'].'.'.$file_info['extension']);
	}
	if ($_FILES['screen_4']['tmp_name'])
	{
		if(is_file(ROOT.'/files/downloads_thumbs/screen_4_'.$file['server_name'].'.png')) unlink(ROOT.'/files/downloads_thumbs/screen_4_'.$file['server_name'].'.png');
		$file_info = pathinfo($_FILES['screen_4']['name']);
		$file_info['extension'] = strtolower($file_info['extension']);
		move_uploaded_file($_FILES['screen_4']['tmp_name'], ROOT.'/tmp/'.$file_info['filename'].'.'.$file_info['extension']);
		$handle = new Jimage();
		$handle->thumb(ROOT.'/tmp/'.$file_info['filename'].'.'.$file_info['extension'], ROOT. '/files/downloads_thumbs/screen_4_'.$file['server_name'].'.png', 128, 160);
		unlink(ROOT.'/tmp/'.$file_info['filename'].'.'.$file_info['extension']);
		}
	if ($_FILES['screen_5']['tmp_name'])
	{
		if(is_file(ROOT.'/files/downloads_thumbs/screen_5_'.$file['server_name'].'.png')) unlink(ROOT.'/files/downloads_thumbs/screen_5_'.$file['server_name'].'.png');
		$file_info = pathinfo($_FILES['screen_5']['name']);
		$file_info['extension'] = strtolower($file_info['extension']);
		move_uploaded_file($_FILES['screen_5']['tmp_name'], ROOT.'/tmp/'.$file_info['filename'].'.'.$file_info['extension']);
		$handle = new Jimage();
		$handle->thumb(ROOT.'/tmp/'.$file_info['filename'].'.'.$file_info['extension'], ROOT. '/files/downloads_thumbs/screen_5_'.$file['server_name'].'.png', 128, 160);
		unlink(ROOT.'/tmp/'.$file_info['filename'].'.'.$file_info['extension']);
	}

	App::redirect('/downloads/attach_screen/'.$file_id);
}
if(isset($_GET['delete']) && $_GET['delete'] >= 1 && $_GET['delete'] <=5)
{
	unlink(ROOT.'/files/downloads_thumbs/screen_'.abs(intval($_GET['delete'])).'_'.$file['server_name'].'.png');
	App::redirect('/downloads/attach_screen/'.$file_id);
}

$title = _t('Attach screenshot', 'downloads').' - '.$file['name'].' - '._t('downloads');
Site::header($title);
echo Site::div('title', Site::breadcrumbs(_t('Attach screenshot', 'downloads').' - '.$file['name']));
echo '<div class="content">
		<form action="/downloads/attach_screen/'.$file_id.'?act=save" method="post" enctype="multipart/form-data">
		'.(is_file(ROOT.'/files/downloads_thumbs/screen_1_'.$file['server_name'].'.png') ?
			'<img src="/files/downloads_thumbs/screen_1_'.$file['server_name'].'.png" weight="150" width="100" alt="Screen" /><br/>
			<a href="/downloads/attach_screen/'.$file_id.'?delete=1">'._t('Delete').'</a><br/>
			' : NULL).'
			'._t('Choose image').':<br/>
			<input type="file" name="screen_2" /><br/>
		'.(is_file(ROOT.'/files/downloads_thumbs/screen_2_'.$file['server_name'].'.png') ?
			'<img src="/files/downloads_thumbs/screen_2_'.$file['server_name'].'.png" weight="150" width="100" alt="Screen" /><br/>
			<a href="/downloads/attach_screen/'.$file_id.'?delete=2">'._t('Delete').'</a><br/>' : NULL).'
			'._t('Choose image').':<br/>
			<input type="file" name="screen_2" /><br/>
		'.(is_file(ROOT.'/files/downloads_thumbs/screen_3_'.$file['server_name'].'.png') ?
			'<img src="/files/downloads_thumbs/screen_3_'.$file['server_name'].'.png" weight="150" width="100" alt="Screen" /><br/>
			<a href="/downloads/attach_screen/'.$file_id.'?delete=3">'._t('Delete').'</a><br/>' : NULL).'
			'._t('Choose image').':<br/>
			<input type="file" name="screen_3" /><br/>
		'.(is_file(ROOT.'/files/downloads_thumbs/screen_4_'.$file['server_name'].'.png') ?
			'<img src="/files/downloads_thumbs/screen_4_'.$file['server_name'].'.png" weight="150" width="100" alt="Screen" /><br/>
			<a href="/downloads/attach_screen/'.$file_id.'?delete=4">'._t('Delete').'</a><br/>' : NULL).'
			'._t('Choose image').':<br/>
			<input type="file" name="screen_4" /><br/>
		'.(is_file(ROOT.'/files/downloads_thumbs/screen_5_'.$file['server_name'].'.png') ?
			'<img src="/files/downloads_thumbs/screen_5_'.$file['server_name'].'.png" weight="150" width="100" alt="Screen" /><br/>
			<a href="/downloads/attach_screen/'.$file_id.'?delete=5">'._t('Delete').'</a><br/>' : NULL).'
			'._t('Choose image').':<br/>
			<input type="file" name="screen_5" /><br/>
		<input type="submit" value="'. _t('Save') .'" />
		</form>
	</div>';
echo Site::div('action_list', '<a href="/downloads/file/'.$file_id.'">'. Site::icon('arrow-left').' '. _t('Back') .'</a>');
Site::footer();