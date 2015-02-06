<?php
if(!isset($_GET['id']))
	Site::notFound();
$id = App::filter('int', $_GET['id']);
$topic_id = App::filter('int', $_GET['topic_id']);

$db = App::db();

$post = $db->query("SELECT * FROM `forum_pt` WHERE `id` = '".$id."'")->fetch();
if(user()->getId() != $post['user_id'] && user()->level() < 2  && user()->level() < 5)
{
	App::redirect('/');
}

if(isset($_GET['delete']))
{
	if(!empty($post['file']))
	{
		unlink(ROOT.'/files/forum/'.$topic_id.DS.$post['file']);
	}

	if($db->query("SELECT `id` FROM `forum_pt` WHERE `topic_id` = '". $topic_id ."' ORDER BY time ASC")->fetchColumn() == $id)
	{
		$d = $db->query("SELECT cat_id FROM `forum_t` WHERE `id` = '".$topic_id."'")->fetchColumn();
		$db->query("DELETE FROM `forum_pt` WHERE `id` = '". $id ."'");
		$db->query("DELETE FROM `forum_t` WHERE `id` = '". $topic_id ."'");
		$db->query("DELETE FROM `forum_pt` WHERE `topic_id` = '". $topic_id ."'");
		App::redirect('/forum/section/'.$d);
	}
	else
	{
		if($db->query("SELECT id FROM `forum_pt` WHERE `id` = '$id' ORDER BY time DESC LIMIT 1")->fetchColumn() == $id)
		{
			$db->query("DELETE FROM `forum_pt` WHERE `id` = '$id' LIMIT 1");
			// print_r($db->errorInfo());
			$_tmp = $db->query("SELECT * FROM `forum_pt` WHERE `topic_id` = '".$topic_id."' ORDER BY time DESC LIMIT 1")->fetch();
			// print_r($db->errorInfo());
			$db->query("UPDATE `forum_t` SET `time_last_post` = '".$_tmp['time']."', `user_last_post` = '".$_tmp['user_id']."' WHERE `id` = '".$topic_id."' LIMIT 1");
		}
		else
		{
			$db->query("DELETE FROM `forum_pt` WHERE `id` = '$id' LIMIT 1");
			// print_r($db->errorInfo());
			$db->query("UPDATE `forum_t` SET `time_last_post` = '".time()."', `user_last_post` = '".user()->getId()."' WHERE `id` = '".$topic_id."' LIMIT 1");
			// print_r($db->errorInfo());
		}
//		var_dump($post);
		App::redirect('/forum/topic/'.$topic_id.'?page=end');
	}
}
