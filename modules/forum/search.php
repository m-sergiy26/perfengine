<?php
Site::header(_t('Search'));
echo Site::div('title', Site::breadcrumbs(_t('Search')));
$db = App::db();

if(isset($_GET['q']) && mb_strlen($_GET['q']) >= 3 && $_GET['in'] === 'topic_names')
{
	$search_r = $db->query("SELECT * FROM `forum_t` WHERE `name` LIKE '%".App::filter('input', $_GET['q'])."%'")->rowCount();

	echo Site::div('menu', _t('Found items').': <b>'.$search_r.'</b>');
	$pages = new Pager($search_r, Site::perPage());
	if($search_r == 0) 
	{
		echo Site::div('content', _t('Topics not found', 'forum'));
	}
	else
	{
		echo '<div class="menu_list">';
		$topics = $db->query("SELECT * FROM `forum_t` WHERE `name` LIKE '%".App::filter('input', $_GET['q'])."%' LIMIT ".$pages->start().", ".Site::perPage()."");
		foreach($topics as $topic)
		{
			$topic['name'] = str_replace(App::filter('input', $_GET['q']), '<b>'.App::filter('input', $_GET['q']).'</b>', $topic['name']);
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

		$pages->view();
	}
	echo '<div class="content">
		<form action="/forum/search?" method="get">
		<input placeholder="'._t('Search query').'" type="text" name="q" /><br/>
		'. _t('Search in') .':<br/>
		<select name="in">
		<option value="topic_names">'._t('Names of topics', 'forum').'</option>
		<option value="topic_posts">'._t('Posts of topics', 'forum').'</option>
		</select><br/>
		<input type="submit" value="'. _t('Search') .'" />
		</form>
		</div>';

	Site::footer();
	exit;
}
elseif(isset($_GET['q']) && mb_strlen($_GET['q']) >= 3 && $_GET['in'] === 'topic_posts')
{
	$search_r = $db->query("SELECT * FROM `forum_pt` WHERE `text` LIKE '%".App::filter('input', $_GET['q'])."%'")->rowCount();
	echo Site::div('menu', _t('Found items').': <b>'.$search_r.'</b>');
	$pages = new Pager($search_r, Site::perPage());
	if($search_r == 0) 
	{
		echo Site::div('content', _t('Topics not found', 'forum'));
	}
	else
	{
		$posts = $db->query("SELECT * FROM `forum_pt` WHERE `text` LIKE '%".App::filter('input', $_GET['q'])."%' ORDER BY time DESC LIMIT  ".$pages->start().", ".Site::perPage()."");
		foreach($posts as $post)
		{
			$post['text'] = str_replace(App::filter('input', $_GET['q']), '<b>'.App::filter('input', $_GET['q']).'</b>', $post['text']);
			echo '<div class="content">';
			$search_u = $db->query("SELECT * FROM `forum_t` WHERE `id` = '". $post['topic_id']."' LIMIT 1")->fetch();
			echo user()->nick($post['user_id']).'<br/> '. Site::output($post['text']).'<br/> [<small class="gray">'. App::date($post['time']).' / <a href="/forum/topic/'. $post['topic_id'] .'?page=end">'. $search_u['name'] .'</a> </small>]
		</div>';
		}
		$pages->view();
	}
}
echo '<div class="content">
		<form action="/forum/search?" method="get">
		<input placeholder="'._t('Search query').'" type="text" name="q" /><br/>
		'. _t('Search in') .':<br/>
		<select name="in">
		<option value="topic_names">'._t('Names of topics', 'forum').'</option>
		<option value="topic_posts">'._t('Posts of topics', 'forum').'</option>
		</select><br/>
		<input type="submit" value="'. _t('Search') .'" />
		</form>
		</div>';

Site::footer();