<?php

$file_id = App::filter('int', $_GET['id']);
$db = App::db();

if($db->query("SELECT * FROM `downloads_files` WHERE `id` = '". $file_id ."'")->rowCount() == 0)
{
	Site::notFound();
}

$file = $db->query("SELECT * FROM `downloads_files` WHERE `id` = '". $file_id ."'")->fetch();

$file_rating = $db->query("SELECT * FROM `downloads_rating` WHERE `file_id` = '". $file_id ."'")->fetch();

if(isset($_GET['act']) && $_GET['act'] == 'rate')
{
	$rate = (App::filter('int', $_POST['rate']) < 1 ? 1 : (App::filter('int', $_POST['rate']) > 5 ? 5 : App::filter('int', $_POST['rate'])));
	$rating = $db->prepare("INSERT INTO `downloads_rating` SET `rating` = ?, `rated` = ?, `user_id` = ?, `file_id` = ?");
	$rating->execute([($file_rating['rating']+$rate), ($file_rating['rated']+1), user()->getId(), $file_id]);
	App::redirect('/downloads/file/'.$file['id']);
}

$root_dir = $db->query("SELECT server_path FROM `downloads` WHERE `id` = '". $file['ref_id'] ."'")->fetchColumn();
$title = $file['name'].' - '._t('Downloads', 'downloads');
Site::header($title);
echo Site::div('title', Site::breadcrumbs($file['name']));
echo '<div class="content">';
if(preg_match('/png|jpg|gif|jpeg/i', $file['ext']))
{
	if(is_file(ROOT.'/cache/downloads_images/cache_'.$file['server_name']).'.png')
	{
		echo '<img src="/cache/downloads_images/cache_'.$file['server_name'].'.png" alt="Screen" /><br/>';
	}
	else
	{
		echo '<img src="/files/downloads/'.$root_dir.'/'.$file['server_dir'].'/'.$file['server_name'].'" alt="Image" weight="150" width="95"/><br/>';
	}
	$dl_image_info = getimagesize(ROOT.'/files/downloads/'.$root_dir.'/'.$file['server_dir'].'/'.$file['server_name']);
	$type = $dl_image_info['mime'];
	$width = $dl_image_info[0];
	$height = $dl_image_info[1];
	echo '<b>'._t('dl_image_size').'</b>: '.$width.'x'.$height.'<br/>';
}
elseif(preg_match('/mp3/i', $file['ext'])) 
{
	import_lib('audio.class');
	$mp3Info = new AudioFile;
	$mp3Info->loadFile(ROOT.'/files/downloads/'.$root_dir.'/'.$file['server_dir'].'/'.$file['server_name']);
	$mp3Info->printSampleInfo();
	}
elseif(!preg_match('/png|jpg|gif|jpeg|mp3|avi|3gp|mp4/i', $file['ext']))
{
	if(isset($_GET['screen']))
		$screen_id = App::filter('int', $_GET['screen']);
	else
		$screen_id = 1;

	if(is_file(ROOT.'/files/downloads_thumbs/screen_'.$screen_id.'_'.$file['server_name'].'.png'))
		$link = '/files/downloads_thumbs/screen_'.$screen_id.'_'.$file['server_name'].'.png';
	else
		$link = '';

	if(is_file(ROOT.'/files/downloads_thumbs/screen_'.($screen_id-1) .'_'.$file['server_name'].'.png'))
	{
		echo '<a href="/downloads/file/'.$file_id.'?screen='.($screen_id-1) .'">'.Site::icon('arrow-left').'</a>';
	}

	if($link != '')
		echo '<img src="'.$link.'" alt="Capture" />';

	if(is_file(ROOT.'/files/downloads_thumbs/screen_'.($screen_id+1) .'_'.$file['server_name'].'.png'))
	{
		echo '<a href="/downloads/file/'.$file_id.'?screen='.($screen_id+1) .'">'.Site::icon('arrow-right').'</a>';
	}

	echo '<br/>';

}

if($file_rating['rating'] != 0)
{
		$rating = ($file_rating['rating']/$file_rating['rated']);
}
else
{
		$rating = 0;
}

echo (!empty($file['description']) ? '<b>'._t('Description').'</b>:
		'.Site::output($file['description']).'<br/>' : false).'
		'._t('Rating').': '. round($rating, 1) .'<br/>
				'.(user()->isUser() && $db->query("SELECT * FROM `downloads_rating` WHERE `user_id` = '". user()->getId() ."' AND `file_id` = '". $file['id'] ."'")->rowCount() == 0 ?
				'<form action="/downloads/file/'.$file['id'].'?act=rate" method="post">
				<select style="width: 7%;" name="rate">
				<option value="1">1</option>
				<option value="2">2</option>
				<option value="3">3</option>
				<option value="4">4</option>
				<option value="5">5</option>
				</select>
				<input type="submit" value="'._t('vote').'" />
				</form>' : NULL).'
</div>';
echo '<div class="content">
'.Site::icon('down').' '._t('Author').': '.App::date($file['time']).' (<a href="/user/profile/'.$file['user_id'].'">'.user()->getNick($file['user_id']).'</a>)<br/>
'.Site::icon('info').' '._t('Downloaded', 'downloads').': '.$file['dl_times'].'<br/>
'.Site::ext($file['ext']).' <a href="/downloads/get_file/'.$file['id'].'">'.(preg_match('/jar|sis|apk|exe|zip|ipa|sisx/i', $file['ext']) ? $file['name'] : _t('dl_download_file')).' ('.strtoupper($file['ext']).')</a> ['.App::fileSize($file['size']).'] '.($file['ext'] == 'jar' ? '[<a href="/downloads/jad/'.$file['id'].'">jad</a>]' : NULL).'<br/>';
if($db->query("SELECT * FROM `downloads_archive` WHERE `file_id` = '". $file['id'] ."'")->rowCount() != 0)
{
	$attachments = $db->query("SELECT * FROM `downloads_archive` WHERE `file_id` = '". $file['id'] ."'");
	foreach($attachments as $attachment)
	{
		echo Site::ext($attachment['ext']).' <a href="/downloads/get_file/attachment/'.$attachment['id'].'">'.$attachment['name'].' ('.strtoupper($attachment['ext']).')</a> ('.App::fileSize($attachment['size']).') [<a href="/downloads/delete_attachment/'.$attachment['id'].'?delete">x</a>] '.($attachment['ext'] == 'jar' ? '[<a href="/downloads/jad/attachment/'.$attachment['id'].'">jad</a>]' : NULL).'<br/>';
	}
}
echo '</div>';
echo '
'.(user()->level() >= 4 || user()->getId() == $file['user_id'] ? '<div class="action_list">'.
	'<a href="/downloads/file_edit/'.$file['id'].'">'.Site::icon('edit') .' '. _t('Edit') .'</a>'.
	'<a href="/downloads/delete_file/'.$file['id'].'">'.Site::icon('delete') .' '. _t('Delete') .'</a>'.
	(!preg_match('/png|jpg|gif|jpeg|mp3|avi|3gp|mp4/i', $file['ext']) ? '<a href="/downloads/attach_files/'.$file['id'].'">'.Site::icon('add') .' '. _t('Attach files', 'downloads') .'</a>'.
	'<a href="/downloads/attach_screen/'.$file['id'].'">'.Site::icon('add') .' '. _t('Attach screenshot', 'downloads') .'</a>' : NULL).'</div>' : NULL);
echo Site::div('action_list', '<a href="/downloads/comments/'.$file['id'].'">'.Site::icon('comments').' '._t('Comments').' ('.Site::counter('comments', ['object_id' => $file['id'], 'object_name' => 'downloads']).')</a> '
	.($file['ref_id'] != 0 ? '<a href="/downloads/dir/'. $file['ref_id'].'">'.Site::icon('folder').' '.$db->query("SELECT name FROM `downloads` WHERE `id` = '". $file['ref_id'] ."'")->fetchColumn().'</a>' : ''));
Site::footer();