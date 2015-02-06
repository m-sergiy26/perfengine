<?php 
$topics = App::db()->query("SELECT * FROM `forum_t` ORDER BY time_last_post DESC LIMIT 0, 6");
if($topics->rowCount() == 0)
{
	echo Site::div('content', _t('Topics are not yet created', 'forum'));
}
else
{
	echo '<div class="menu_list">';
	foreach($topics as $topic)
	{
		if ($topic['closed'] == 1 && $topic['attach'] == 1) $icon = Site::icon('pin-closed');
		elseif ($topic['attach'] == 1) $icon = Site::icon('pin');
		elseif ($topic['closed'] == 1) $icon = Site::icon('topic_closed');
		else $icon = Site::icon('topic');
		$topic_a = App::db()->query("SELECT * FROM `forum_pt` WHERE `topic_id` = '". $topic['id']."' ORDER BY time DESC LIMIT 1")->fetch();
		echo '<a href="/forum/topic/'. $topic['id'] .'">'.$icon.' '. $topic['name'] .'  ('. Site::counter('forum_pt', ['topic_id' => $topic['id']]).')
		<br/> &nbsp;[<small class="gray">'. user()->getNick($topic_a['user_id']).' / '. App::date($topic_a['time']).'</small>]
		</a>';
	}
	echo '</div>';
}
