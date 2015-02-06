<?php

$db = App::db();

if(!isset($_GET['id']) || $db->query("SELECT * FROM `forum_pt` WHERE `topic_id` = '". App::filter('int', $_GET['id']) ."'")->rowCount() == 0)
{
	Site::notFound();
}
$id = App::filter('int', $_GET['id']);

$topic = $db->query("SELECT * FROM `forum_t` WHERE `id` = '". $id ."' LIMIT 1")->fetch();
$post = $db->query("SELECT * FROM `forum_pt` WHERE `topic_id` = '". $id ."' LIMIT 1")->fetch();

if(user()->level() < 4 && $post['user_id'] != user()->getId()) App::redirect('/forum/topic/'.$id);

if(isset($_GET['edit']))
{
	$name = App::filter('input', $_POST['name']);
	$text = App::filter('input', $_POST['text']);
	$pin = (isset($_POST['pin']) ? 1 : 0);
	if (!empty($name) && !empty($text))
	{
//		var_dump($_FILES);

		$file_dir = ROOT . '/files/forum/' . $id . DS;
		if (isset($_FILES['file']) && $_FILES['file']['tmp_name'])
		{
			$patch = pathinfo($_FILES['file']['name']);
			$extension = strtolower($patch['extension']);
			if (!in_array($extension, explode(';', App::config('system/data/config')['filetypes']))) $err = 'File extension not allowed.';
			$name_f = App::translit(App::filter('input', $patch['filename']));
			if (file_exists($file_dir . $name_f)) $err = 'This file exists';

			if (!isset($err))
			{
				move_uploaded_file($_FILES['file']['tmp_name'], $file_dir . $name);
				$db->query("UPDATE `forum_pt` SET `file` = '" . $name_f . "', `file_size` = '" . $_FILES['file']['size'] . "' WHERE `id` = '" . $post['id'] . "'");
//				 print_r($db->errorInfo());
			}
		}

		$db->query("UPDATE `forum_pt` SET `name` = '".$name."', `text` = '". $text ."', `pin` = '$pin', `edit_time` = '". time() ."', `edit_user_id` = '". user()->getId() ."', `count_edit` = '". ($post['count_edit']+1) ."' WHERE `topic_id` = '". $id ."' LIMIT 1");
		$db->query("UPDATE `forum_t` SET `name` = '".$name."' WHERE `id` = '". $id ."'");
	}
	App::redirect('/forum/topic/'. $id.'?page=end');
	// print_r($db->errorInfo());
}
elseif(isset($_GET['deleteFile']))
{
	unlink(ROOT.'/files/forum/'.$id.DS.$post['file']);
	$db->query("UPDATE `forum_pt` SET `file` = '', `file_size` = '0' WHERE `id` = '". $post['id'] ."'");
	App::redirect('/forum/edit_post/'.$id.'?topic_id='.$topic['id']);
}
Site::header(_t('Edit topic', 'forum').' -'._t('Forum', 'forum'));
echo Site::div('title', Site::breadcrumbs(_t('Edit topic', 'forum')));
echo '<form action="/forum/edit_topic/'. $id .'?edit" method="post" enctype="multipart/form-data">
		<div class="content">
			<input name="name" type="text" placeholder="'._t('Title').'" value="'. $topic['name'] .'" /><br/>
			'.Site::textarea($post['text'], ['rows' => 6, 'placeholder' => _t('Message'), 'id'=>'message']).'
			'.(!empty($post['file']) ? '<br/><a class="button" href="/forum/edit_post/'.$id.'?topic_id='.$post['topic_id'].'&deleteFile">'._t('Delete').' '.$post['file'].'</a>'
			: '<br/><button id="fileButton" type="button" onclick="document.getElementById(\'fileButton\').style.display=\'none\'; document.getElementById(\'fileForm\').style.display=\'block\';">'._t('Attach file').'</button>
			<input type="file" id="fileForm" name="file" style="display: none;" />').'
			<input name="edit" type="submit" value="'. _t('Save') .'" /> '. _t('Pin post') .' <input type="checkbox" name="pin"'.($post['pin'] == 1 ? ' checked="checked"' : null).' />
		</div>
		</form>';
echo Site::div('action_list', '<a href="/forum/topic/'.$post['topic_id'].'?page=end">'.Site::icon('arrow-left').' '. _t('Back').'</a>');
Site::footer();