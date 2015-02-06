<?php

$id = App::filter('int', $_GET['id']);
$db = App::db();
if($db->query("SELECT * FROM `news` WHERE `id` = '". $id ."'")->rowCount() == 0)
	Site::notFound();

$news_t = $db->query("SELECT * FROM `news` WHERE `id` = '". $id ."'")->fetch();

if(user()->isUser() && isset($_GET['create']))
{
	$text = App::filter('input', $_POST['text']);
	if(!empty($text))
	{
		$comm = $db->prepare("INSERT INTO `comments` SET `object_name` = ?, `object_id` = ?, `user_id` = ?, `time` = ?, `text` = ?");
		$comm->execute(['news', $id, user()->getId(), time(), $text]);
		if(isset($_GET['reply_to']))
		{
			$_user_id = App::filter('int', $_GET['reply_to']);
			user()->setNotify($_user_id, user()->getId(), 'Topic reply', '/news/comments/'.$id.'#comment'.$db->lastInsertId());
		}
		App::redirect('/news/comments/'.$id);
	}
}
elseif(isset($_GET['delete']))
{
	$delete_id = App::filter('int', $_GET['delete']);
	if(user()->level() >=3 || $db->query("SELECT `user_id` FROM `comments` WHERE `id` = '".$delete_id."'")->fetchColumn() == user()->getId())
	{
		$_SESSION['alert'] = ['type' => 'notify', 'value' => _t('Comment deleted')];
		$db->query("DELETE FROM `comments` WHERE `id` = '".$delete_id."' LIMIT 1");
		App::redirect('/news/comments/'.$id);
	}
}
$title = _t('Comments') .' - '. $news_t['name'] .' - '._t('News', 'news');
Site::header($title);
echo Site::div('title', Site::breadcrumbs(_t('Comments') .' - '. $news_t['name']));

$comments_r = $db->query("SELECT * FROM `comments` WHERE `object_name` = 'news' AND `object_id` = '".$id."'")->rowCount();

if(user()->isUser())
{
	echo '<div class="content">
		<form id="reply" action="/news/comments/'. $id .'?create'.(isset($_GET['reply_to']) ? '&reply_to='.App::filter('int', $_GET['reply_to']) : NULL).'" method="post">'
		.Site::textarea((isset($_GET['reply_to']) ? '[b]'.user()->getNick(App::filter('int', $_GET['reply_to'])).'[/b], ' : NULL), ['rows' => 6, 'placeholder' => _t('Message'), 'id'=>'message']);
	echo '<br/>
		<input name="create" type="submit" value="'. _t('Add') .'" />
		</form></div>';
}

if($comments_r == 0)
	echo Site::div('content', _t('No comments'));
else
{
	$pages = new Pager($comments_r, Site::perPage());

	$comments = $db->query("SELECT * FROM `comments` WHERE `object_name` = 'news' AND `object_id` = '".$id."' ORDER BY time DESC LIMIT ".$pages->start().", ".Site::perPage()."");

	foreach($comments as $comment)
	{
		echo '<div class="content" id="comment'.$comment['id'].'">
			'.user()->nick($comment['user_id'], '<small>'.App::date($comment['time'])).'
			<span class="float-rt">
			'.(user()->getId() != $comment['user_id'] ? '[<a href="/news/comments/'.$id.'?reply_to='.$comment['user_id'].'">'._t('Reply').'</a>]' : '').'
			'.(user()->level() >=3 || $comment['user_id'] == user()->getId() ? '[<a href="/news/comments/'.$id.'?delete='.$comment['id'].'">'._t('Delete').'</a>]' : '').'
				</span><br/>
			'. Site::output($comment['text']).'
		</div>';
	}
}

echo Site::div('action_list', '<a href="/news/article/'. $id .'/">'.Site::icon('arrow-left').' '. _t('Back') .'</a>');

Site::footer();