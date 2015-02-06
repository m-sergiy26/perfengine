<?php
if(!user()->isUser() || !isset($_GET['id']))
{
	App::redirect('/');
}

$id = App::filter('int', $_GET['id']);
$db = App::db();
if($db->query("SELECT * FROM `forum_c` WHERE `id` = '". $id ."'")->rowCount() == 0)
	Site::notFound();

if(isset($_GET['create']))
{
	$err = '';

	$name = App::filter('input', $_POST['name']);
	$text = App::filter('input', $_POST['text']);

	if(empty($_POST['name']))
	{

		$err .= "Title is empty\n";
	}
	
	if(empty($_POST['text']))
	{
		$err .= 'Message is empty';
	}

	$pin = isset($_POST['pin']) ? 1 : 0;
	
	if($err == '' && App::antiflood('forum_pt', 'text', $text) == false)
	{
		$f_id = $db->query("SELECT f_id FROM `forum_c` WHERE `id` = '". $id ."'")->fetchColumn();

		$t_ins = $db->prepare("INSERT INTO `forum_t`(`name`, `cat_id`, `f_id`, `time_last_post`, `user_last_post`, `attach`, `closed`) VALUES(?, ?, ?, ?, ?, ?, ?)");
		// print_r($db->errorInfo());
		$t_ins->execute([$name, $id, $f_id, time(), user()->getId(), '0', '0']);

		$lastTopicId = $db->lastInsertId();

		$p_ins = $db->prepare("INSERT INTO `forum_pt`(`name`, `text`, `time`, `user_id`, `cat_id`, `topic_id`, `file`, `file_size`, `edit_time`, `edit_user_id`, `count_edit`, `pin`, `f_id`) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
		// print_r($db->errorInfo());
		$p_ins->execute([$name, $text, time(), user()->getId(), $id, $lastTopicId, '', '0', '0', '0', '0', $pin, $f_id]);
		$lastPostId = $db->lastInsertId();

		$file_dir = ROOT .'/files/forum/'.$lastTopicId.DS;
		if(isset($_FILES['file']) && $_FILES['file']['tmp_name']) 
		{
			if(!is_dir($file_dir))
				mkdir($file_dir);

			$patch = pathinfo($_FILES['file']['name']);
			$extension = strtolower($patch['extension']);
			if (!in_array($extension, explode(';', App::config('system/data/config')['filetypes']))) $ferr = 'File extension not allowed.';
			$name_end = App::translit(App::filter('input', $patch['filename']));
			$name = $name_end.'_'.substr(md5(time().$name_end), 0, 8).'.'. $extension;
			if (file_exists($file_dir . $name)) $ferr = 'This file exists';
			
			if(!$ferr)
			{
				move_uploaded_file($_FILES['file']['tmp_name'], $file_dir . $name);
				$db->query("UPDATE `forum_pt` SET `file` = '". $name ."', `file_size` = '". $_FILES['file']['size'] ."' WHERE `id` = '".$lastPostId."'");
//				$db->query("UPDATE `users` SET `balance` = '".(user()->profile('balance')+1)."' WHERE `id` = '". user()->Id() ."'");
				// print_r($db->errorInfo());
			}
 		}
		App::redirect('/forum/topic/'. $lastTopicId);
		// print_r($db->errorInfo());
	}
	else
	{
		$_SESSION['alert'] = ['type'=>'error', 'value'=> $err];
	}
}

$title = _t('Add topic', 'forum').' - '._t('Forum', 'forum');
Site::header($title);
echo Site::div('title', Site::breadcrumbs(_t('Add topic', 'forum')));
?>
<div class="content">
	<form action="/forum/add_topic/<?=$id?>/?create" method="post" enctype="multipart/form-data">
		<input name="name" type="text" placeholder="<?=_t('Title')?>"/><br/>
		<textarea name="text" rows="5" cols="26" placeholder="<?=_t('Message')?>"></textarea><br/>
		<?=_t('Attach file')?>: <br/>
		<input type="file" name="file"><br/>
		<input type="checkbox" name="pin" /> <?=_t('Pin post', 'forum')?> <br/>
		<input type="submit" value="<?=_t('Create')?>" />
	</form>
</div>
<div class="action_list">
	<a href="/forum/section/<?=$id?>"><?=Site::icon('arrow-left')?> <?=_t('Back')?></a>
</div>
<? Site::footer();