<?php
Site::header(_t('New posts', 'forum').' - '._t('Forum', 'forum'));
echo Site::div('title', Site::breadcrumbs(_t('New posts', 'forum')));
$db = App::db();
$topic_r = $db->query("SELECT * FROM `forum_pt` WHERE `time` > '". (time()-60*60*24) ."'")->rowCount();
$pages = new Pager($topic_r, Site::perPage());
if($topic_r == 0) {
	echo Site::div('content', _t('No posts'));
} 
else
{
	$posts = $db->query("SELECT * FROM `forum_pt` WHERE `time` > '". (time()-60*60*24) ."' ORDER BY time DESC LIMIT ".$pages->start().", ".Site::perPage()."");
	foreach($posts as $post)
	{
		echo '<div class="content">';
		$topic = $db->query("SELECT * FROM `forum_t` WHERE `id` = '". $post['topic_id']."' LIMIT 1")->fetch();
		echo Site::output($post['text']).'<br/> [<small>'. user()->getNick($post['user_id']).' / '. App::date($post['time']).' / <a href="/forum/topic/'. $post['topic_id'] .'/?page=end">'. $topic['name'] .'</a></small>]
		</div>';
	}
	$pages->view();
}
Site::footer();