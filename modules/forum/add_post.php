<?php
if(!user()->isUser() || !isset($_GET['id']))
{
	App::redirect('/forum');
}

$db = App::db();
$topic_id = App::filter('int', $_GET['id']);
if($db->query("SELECT * FROM `forum_t` WHERE `id` = '".$topic_id ."'")->rowCount() == 0)
	Site::notFound();

if($db->query("SELECT `closed` FROM `forum_t` WHERE `id` = '". $topic_id ."'")->fetchColumn() != 0)
{
	App::redirect('/forum/topic/'.$topic_id);
}

$topic = $db->query("SELECT * FROM `forum_t` WHERE `id` = '".$topic_id ."'")->fetch();

if(isset($_GET['create']))
{

	$text = App::filter('input', $_POST['text']);
	if(!empty($text) && App::antiflood('forum_pt', 'text', $text) == false)
	{
		$prevPost = $db->query("SELECT * FROM `forum_pt` WHERE `topic_id` = '".$topic_id."' ORDER BY time DESC LIMIT 1")->fetch();
		if($prevPost['user_id'] == user()->getId() && $prevPost['time'] >= (time()-300))
		{
			$upd = $db->prepare("UPDATE `forum_pt` SET `text` = ?, `edit_time` = ?, `count_edit` = ?, `edit_user_id` = ? WHERE `user_id` = ? AND `topic_id` = ? AND `id` = ?");
			$upd->execute([$prevPost['text']."\n".$text, time(), $prevPost['count_edit']+1, user()->getId(), user()->getId(), $topic_id, $prevPost['id']]);
			$lastPostId = $prevPost['id'];
		}
		else
		{
			$posting = $db->prepare("INSERT INTO `forum_pt` (`text`, `time`, `user_id`, `topic_id`, `cat_id`, `f_id`) VALUES(?, ?, ?, ?, ?, ?)");
			$posting->execute([$text, time(), user()->getId(), $topic_id, $topic['cat_id'], $topic['f_id']]);
			// print_r($db->errorInfo());
			$lastPostId = $db->lastInsertId();
		}


		$db->query("UPDATE `forum_t` SET `time_last_post` = '". time() ."', `user_last_post` = '". user()->getId() ."' WHERE `id` = '".$topic_id."'");
//		$db->query("UPDATE `users` SET `balance` = '".(user()->profile('balance')+1)."' WHERE `id` = '". user()->getId() ."'");

		$file_dir = ROOT .'/files/forum/'.$topic_id.DS;
		if (isset($_FILES['file']) && $_FILES['file']['tmp_name'])
		{
			if(!is_dir($file_dir))
				mkdir($file_dir);

			$patch = pathinfo($_FILES['file']['name']);
			$extension = strtolower($patch['extension']);
			if (!in_array($extension, explode(';', App::config('system/data/config')['filetypes']))) $err = 'File extension not allowed.';
			$name_start = mb_convert_encoding(App::filter('input', $patch['filename']), "UTF-8");
			$name_end = iconv('UTF-8', 'UTF-8//TRANSLIT', $name_start);
			$name = $name_end.'_'.substr(md5(time().$name_end), 0, 8).'.'. $extension;
			if (file_exists($file_dir . $name)) $err = 'This file exists';
	
			if(!isset($err))
			{
				move_uploaded_file($_FILES['file']['tmp_name'], $file_dir . $name);
				$db->query("UPDATE `forum_pt` SET `file` = '". App::filter('input', $name) ."', `file_size` = '". $_FILES['file']['size'] ."' WHERE `id` = '".$lastPostId."'");
				// print_r($db->errorInfo());
			}
		}
		// print_r($db->errorInfo());
		if(isset($_GET['reply_to']))
		{
			$_user_id = App::filter('int', $_GET['reply_to']);
			user()->setNotify($_user_id, user()->getId(), 'Topic reply', '/forum/topic/'.$topic_id.'?page=end', $topic['name']);
		}
		elseif(isset($_GET['quote']))
		{
			$_user_id = $db->query("SELECT user_id FROM `forum_pt` WHERE `topic_id` = '".$topic_id."' AND `id` = '". App::filter('int', $_GET['quote']) ."'")->fetchColumn();
			user()->setNotify($_user_id, user()->getId(), 'Topic reply', '/forum/topic/'.$topic_id.'?page=end#post'.$lastPostId, $topic['name']);
		}

		App::redirect('/forum/topic/'. $topic_id.'?page=end#post'.$lastPostId);
		// print_r($db->errorInfo());
	}
	else 
	{ 
		App::redirect('/forum/topic/'. $topic_id.'?page=end');
	}
}

$title = _t('Add message') .'-'. _t('Forum', 'forum');
Site::header($title);
echo Site::div('title', Site::breadcrumbs(_t('Add message')));
echo '<div class="content">
	<form id="reply" action="/forum/add_post/'. $id .'?create'.(isset($_GET['reply_to']) ? '&reply_to='.App::filter('int', $_GET['reply_to']) : (isset($_GET['quote']) ? '&quote='.App::filter('int', $_GET['quote']) : null)).'" method="post" enctype="multipart/form-data">
	';

if(isset($_GET['quote']))
	$quote = "[quote][i][b]".user()->getNick($db->query("SELECT user_id FROM `forum_pt` WHERE `topic_id` = '". $id ."' AND `id` = '". App::filter('int', $_GET['quote']) ."'")->fetchColumn())."[/b] ".date('d.m.Y, H:i', $db->query("SELECT time FROM `forum_pt` WHERE `topic_id` = '". $id ."' AND `id` = '". App::filter('int', $_GET['quote']) ."'")->fetchColumn())."[/i]:
		".preg_replace("/\[quote\]|\[\/quote\]/i", '', $db->query("SELECT text FROM `forum_pt` WHERE `topic_id` = '". $id ."' AND `id` = '". App::filter('int', $_GET['quote']) ."'")->fetchColumn())."[/quote]";
else
	$quote = '';

Site::textarea((isset($_GET['reply_to']) ? '[b]'.user()->getNick(App::filter('int', $_GET['reply_to'])).'[/b], ' : NULL) . $quote, ['rows' => 6, 'placeholder' => _t('Message'), 'id'=>'message']);
echo '<br/><input name="create" type="submit" value="'. _t('Add') .'" />
	<button id="fileButton" type="button" onclick="document.getElementById(\'fileButton\').style.display=\'none\'; document.getElementById(\'fileForm\').style.display=\'block\';">'._t('Attach file').'</button>
	<input type="file" id="fileForm" name="file" style="display: none;" />
	</form></div>';
		
echo Site::div('action_list', '<a href="/forum/topic/'.$topic_id.'?page=end">'.Site::icon('arrow-left').' '. _t('Back') .'</a>');
Site::footer();