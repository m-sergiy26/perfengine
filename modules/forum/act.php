<?php
$db = App::db();
$id = App::filter('int', $_GET['id']);

if(user()->isUser() && isset($_GET['id']) && $db->query("SELECT * FROM `forum_t` WHERE `id` = '". $id ."'")->rowCount() == 1)
{
	if((user()->level()>=4 || user()->getId() == $db->query("SELECT user_id FROM `forum_pt` WHERE `topic_id` = '". $id."' ORDER BY time ASC LIMIT 1")->fetchColumn()) && $_GET['act'] == 'close')
	{
		$db->query("UPDATE `forum_t` SET `closed` = '1' WHERE `id` = '". $id ."'");
		App::redirect('/forum/topic/'.$id.'?page=end');
	}
	elseif((user()->level()>=4 || user()->getId() == $db->query("SELECT user_id FROM `forum_pt` WHERE `topic_id` = '". $id."' ORDER BY time ASC LIMIT 1")->fetchColumn()) && $_GET['act'] == 'open')
	{
		$db->query("UPDATE `forum_t` SET `closed` = '0' WHERE `id` = '". $id ."'");
		App::redirect('/forum/topic/'.$id.'?page=end');
	}
	
	if(user()->level()>=5 && $_GET['act'] == 'attach')
	{
		$db->query("UPDATE `forum_t` SET `attach` = '1' WHERE `id` = '". $id ."'");
		App::redirect('/forum/topic/'.$id.'?page=end');
	}
	
	if(user()->level()>=5 && $_GET['act'] == 'unpin')
	{
		$db->query("UPDATE `forum_t` SET `attach` = '0' WHERE `id` = '". $id ."'");
		App::redirect('/forum/topic/'.$id.'?page=end');
	}
	///////////////// Vote //////////////////

	if((user()->level() >= 4|| user()->getId() == $db->query("SELECT user_id FROM `forum_pt` WHERE `topic_id` = '". $id."' ORDER BY time ASC LIMIT 1")->fetchColumn()) && $_GET['act'] == 'close_vote')
	{
		$db->query("UPDATE `forum_vote` SET `closed` = '1' WHERE `topic_id` = '". $id ."'");
		App::redirect('/forum/topic/'.$id.'?page=end');
	}
	if((user()->level() >= 4  || user()->getId() == $db->query("SELECT user_id FROM `forum_pt` WHERE `topic_id` = '". $id."' ORDER BY time ASC LIMIT 1")->fetchColumn()) && $_GET['act'] == 'open_vote')
	{
		$db->query("UPDATE `forum_vote` SET `closed` = '0' WHERE `topic_id` = '". $id ."'");
		App::redirect('/forum/topic/'.$id.'?page=end');
	}
	///////////////// Vote //////////////////

}
else
{
	App::redirect('/forum/');
}