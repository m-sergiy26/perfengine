<?php

if(!isset($_GET['id']))
	Site::notFound();

$id = App::filter('int', $_GET['id']);

$db = App::db();

$post = $db->query("SELECT * FROM `forum_pt` WHERE `id` = '".$id."'")->fetch();

if((user()->getId() != $post['user_id'] || $post['time'] < (time()-300)) && user()->level() < 4)
{
	App::redirect('/forum/topic/'.$post['topic_id'].'?page=end');
}


if($db->query("SELECT * FROM `forum_pt` WHERE `id` = '".$id."'")->rowCount() == 0) 
{
	Site::notFound();
}

if($db->query("SELECT id FROM `forum_pt` WHERE `topic_id` = '".$post['topic_id']."' ORDER BY time ASC")->fetchColumn() == $post['id'])
{
	App::redirect('/forum/edit_topic/'.$post['topic_id']);
}

if(isset($_GET['edit']))
{
	$text = App::filter('input', $_POST['text']);
	if(!empty($text))
	{
		$file_dir = ROOT .'/files/forum/'.$post['id'];
		if (isset($_FILES['file']) && $_FILES['file']['tmp_name'])
		{
			if(!is_dir($file_dir))
				mkdir($file_dir);

			$patch = pathinfo($_FILES['file']['name']);
			$extension = strtolower($patch['extension']);
			if (!in_array($extension, explode(';', App::config('system/data/config')['filetypes']))) $err = 'File extension not allowed.';
			$name = App::translit(App::filter('input', $patch['filename']));
			if (file_exists($file_dir . $name)) $err = 'This file exists';
	
			if(!isset($err))
			{
				move_uploaded_file($_FILES['file']['tmp_name'], $file_dir . $name);
				$db->query("UPDATE `forum_pt` SET `file` = '". $name ."', `file_size` = '". $_FILES['file']['size'] ."' WHERE `id` = '".$id."'");
				// print_r($db->errorInfo());
			}
		}
		$db->query("UPDATE `forum_pt` SET `text` = '". $text ."', `edit_time` = '". time() ."', `edit_user_id` = '". user()->getId() ."', `count_edit` = '". ($post['count_edit']+1) ."' WHERE `id` = '".$id."'");
		App::redirect('/forum/topic/'.$post['topic_id'].'?page=end');
		// print_r($db->errorInfo());
	}
}
elseif(isset($_GET['deleteFile']))
{
	unlink(ROOT.'/files/forum/'.$id.DS.$post['file']);
	$db->query("UPDATE `forum_pt` SET `file` = '', `file_size` = '0' WHERE `id` = '". $post['id'] ."'");
	App::redirect('/forum/edit_post/'.$id.'?topic_id='.$post['topic_id']);
}

$title = _t('Edit post', 'forum').' - '._t('Forum', 'forum');
Site::header($title);
echo Site::div('title', Site::breadcrumbs(_t('Edit post', 'forum')));
echo '<form action="/forum/edit_post/'.$id.'?topic_id='.$post['topic_id'].'&edit" method="post" enctype="multipart/form-data">
		<div class="content">
			'.Site::textarea($post['text'], ['rows' => 6, 'placeholder' => _t('Message'), 'id'=>'message']).'
			'.(!empty($post['file']) ? '<a class="button" href="/forum/edit_post/'.$id.'?topic_id='.$post['topic_id'].'&deleteFile">'._t('Delete').' '.$post['file'].'</a>'
			: '<br/><button id="fileButton" type="button" onclick="document.getElementById(\'fileButton\').style.display=\'none\'; document.getElementById(\'fileForm\').style.display=\'block\';">'._t('Attach file').'</button>
			<input type="file" id="fileForm" name="file" style="display: none;" />').'
			<input name="edit" type="submit" value="'. _t('Save') .'" /><br/>
		</div>
		</form>';
		
echo Site::div('action_list', '<a href="/forum/topic/'.$post['topic_id'].'?page=end">'.Site::icon('arrow-left').' '. _t('Back').'</a>');
Site::footer();