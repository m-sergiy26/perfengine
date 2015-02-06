<?php

if(!isset($_GET['id'])) App::redirect('/forum/');

$id = App::filter('int', $_GET['id']);
$db = App::db();

if($db->query("SELECT * FROM `forum_c` WHERE `id` = '". $id ."'")->rowCount() == 0)
	Site::notFound();

$section = $db->query("SELECT * FROM `forum_c` WHERE `id` = '". $id ."'")->fetch();

Site::header($section['name'] .' - '._t('Forum', 'forum'));
echo Site::div('title', Site::breadcrumbs($section['name']));

$topic_r = $db->query("SELECT * FROM `forum_t` WHERE `cat_id` = '". $id ."'")->rowCount();
$pages = new Pager($topic_r, Site::perPage());

if($topic_r == 0)
{
	echo Site::div('content', _t('Topics are not yet created', 'forum'));
} 
else
{
	$topics = $db->query("SELECT * FROM `forum_t` WHERE `cat_id` = '". $id ."' ORDER BY attach DESC, time_last_post DESC LIMIT ".$pages->start().", ".Site::perPage()."");
	foreach($topics as $topic)
	{
		echo '<div class="menu_list">';
		if ($topic['closed'] == 1 && $topic['attach'] == 1) $icon = Site::icon('pin-closed');
		elseif ($topic['attach'] == 1) $icon = Site::icon('pin');
        elseif ($topic['closed'] == 1) $icon = Site::icon('topic_closed');
        else $icon = Site::icon('topic');
		$topic_a = $db->query("SELECT * FROM `forum_pt` WHERE `topic_id` = '". $topic['id']."' ORDER BY time DESC LIMIT 1")->fetch();
		echo '<a href="/forum/topic/'. $topic['id'] .'">'.$icon.' '. $topic['name'] .'  ('. Site::counter('forum_pt', ['topic_id' => $topic['id']]).')
		<br/> &nbsp;[<small class="gray">'. user()->getNick($topic_a['user_id']).' / '. App::date($topic_a['time']).'</small>]
		</a></div>';
	}
	$pages->view();
}
?>
<div class="action_list">
	<? if(user()->isUser()): ?>
		<? if(user()->level() >=5): ?>
			<a href="/forum/edit_section/<?=$id?>"><?=Site::icon('arrow-right')?> <?=_t('Edit section', 'forum')?></a>
		<? endif; ?>
		<a href="/forum/add_topic/<?=$id?>"><?=Site::icon('arrow-left')?> <?=_t('Add topic', 'forum')?></a>
	<? endif; ?>
</div>
<? Site::footer();