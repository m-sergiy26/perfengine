<?php
if(!isset($_GET['id'])) App::redirect('/forum');

$id = App::filter('int', $_GET['id']);

$db = App::db();

//if(user()->isUser())
//{
//	$db->query("UPDATE `notify` SET `read` = '1' WHERE `request_id` = '/forum/topic/".$id."?page=end' AND `user_id` = '".user()->getId()."' AND `type` = 'notify_topic_reply'");
//}
if($db->query("SELECT * FROM `forum_pt` WHERE `topic_id` = '". $id ."'")->rowCount() == 0) 
	Site::notFound();

$topic = $db->query("SELECT * FROM `forum_t` WHERE `id` = '". $id ."'")->fetch();

/*///////////////// Vote //////////////////
$post_vote = $db->query("SELECT * FROM `forum_vote` WHERE `topic_id` = '". $id ."' AND `type` = '1'")->fetch();

if (isset($_POST['votes']) && $db->query("SELECT * FROM `forum_vote_rez` WHERE `topic_id` = '". $id ."' AND `user_id` = '".user()->profile('id')."' ")->rowCount() == 0 && user()->isUser() && $topic['closed'] == 0 && $post_vote['closed'] == 0)
{
	$vote = input($_POST['vote']);
	$vote_u = $db->query("SELECT * FROM `forum_vote` WHERE `id` = '". $vote ."' AND `type` = '2'")->rowCount();
	if (isset($vote) && $vote_u > 0) 
	{
		$db->query("INSERT INTO `forum_vote_rez` SET `user_id` = '". user()->profile('id')."', `topic_id` = '". $id."', `vote` = '".$vote."'");
		$db->query("UPDATE `forum_vote` SET `count` = count + 1 WHERE `id` = '".$vote."'");
	}
	redirect('/forum/topic/'.$id.'?page=end');
	exit;
}
///////////////// Vote //////////////////*/

Site::header($topic['name'] .' - '._t('Forum', 'forum'));
echo Site::div('title', Site::breadcrumbs($topic['name']));

/*////////////////// Vote //////////////////
$top_vote = $db->query("SELECT * FROM `forum_vote` WHERE `topic_id` = '". $id ."' AND `type` = '1'")->rowCount();
if ($top_vote > 0) 
{
	echo '<div class="post">';
	$votes = $db->query("SELECT * FROM `forum_vote` WHERE `topic_id` = '". $id ."' AND `type` = '2' ORDER BY id");
	$cou = $db->query("SELECT * FROM `forum_vote_rez` WHERE `topic_id` = '". $id ."'")->rowCount();

	echo SIte::icon('rating.png').'<b>'.$post_vote['name'] .'</b> '.($topic['closed'] == 1 || $post_vote['closed'] == 1 ? '('._t('vote_close').')' : null).'<br />';

	if ($db->query("SELECT * FROM `forum_vote_rez` WHERE `topic_id` = '". $id ."' AND `user_id` = '".user()->profile('id')."' ")->rowCount() == 0 && user()->isUser() && $topic['closed'] == 0 && $post_vote['closed'] == 0)
	{

		echo '<form action="/forum/topic/'.$id.'/" method="post">';
		foreach($votes as $vote)
		{
			echo '<input type="radio" value="'.$vote['id'].'" name="vote" /> '.$vote['name'].'<br />';
		}
		echo '<input type="submit" name="votes" value="'. _t('vote') .'" /></form>';
	}
	else
	{
		foreach($votes as $vote)
		{
			$count = $cou ? round(100 / $cou * $vote['count'], 1) : 0;
			echo $vote['name'].' ('.$db->query("SELECT * FROM `forum_vote_rez` WHERE `topic_id` = '". $id ."' AND `vote` = '".$vote['id']."'")->rowCount().')<br />
			<div class="rang"><span style="width: '.$count.'%"><center>'.$count.'%</center></span></div>';
		}
	}

	echo  _t('user_vote') . ' <b>'.$cou .'</b> '. _t('user_vot') . '<br />
	<small>------------<br />'. _t('add_votes') .' <b><a href="/user/profile/'.$post_vote['user_id'] .'">'.user()->getNick($post_vote['user_id']).'</a></b>, '.rtime($post_vote['time']) .'</small>';		
	echo '</div>';
}
///////////////// Vote //////////////////*/

$posts_n = $db->query("SELECT * FROM `forum_pt` WHERE `topic_id` = '". $id ."'")->rowCount();
$pages = new Pager($posts_n, Site::perPage());
if($posts_n == 0)
{
	echo Site::div('content', _t('No posts'));
} 
else 
{
	if(isset($_GET['page']) && $_GET['page'] != 1 && $posts_n > Site::perPage())
	{
		$pinned = $db->query("SELECT * FROM `forum_pt` WHERE `topic_id` = '".$id."' ORDER BY time ASC LIMIT 1")->fetch();
		if($pinned['pin'] == 1)
		{
			echo '<div class="list" id="post'.$pinned['id'].'">
		'.  user()->nick($pinned['user_id'], '<a href="/forum/post/'.$pinned['id'].'"><small>'. App::date($pinned['time']).'</small></a>') . Site::output($pinned['text']) .
				($pinned['count_edit'] > 0 ? '<br/><span class="edited">'. _t('Edited by').' <b>'. user()->getNick($pinned['edit_user_id']) .'</b> ('. App::date($pinned['edit_time']) .') ['. $pinned['count_edit'] .']</span>':NULL)
				.(user()->isUser() ? '<span class="float-rt">'.(user()->getId() != $pinned['user_id'] && $pinned['closed'] == 0 ? '[<a href="/forum/topic/'.$pinned['topic_id'].'?page=end&amp;reply_to='.$pinned['user_id'].'#reply">'. _t('Reply').'</a>
			| <a href="/forum/'.(user()->isUser() && user()->config('fast_form') == 1 ? 'topic/'.$pinned['topic_id'].'?page=end&amp;' : 'add_post/'.$pinned['topic_id'].'?'). 'quote='. $pinned['id'] .'#reply">'. _t('Quote').'</a>]': NULL).'
					'. (user()->level() >= 3 || (user()->getId() == $pinned['user_id'] && $pinned['time'] > (time()-300)) ? ' [<a href="/forum/delete_post/'.$pinned['id'].'?topic_id='.$pinned['topic_id'].'&delete">x</a> | <a href="/forum/edit_post/'.$pinned['id'].'?topic_id='.$pinned['topic_id'].'">+</a>]' : NULL).'</span>':NULL).'
		'.(!empty($pinned['file']) ? '<br/><div style="border: dotted 1px;"><a href="/files/forum/'.$id.DS.$pinned['file'].'">'.$pinned['file'].'</a> ('.App::fileSize($pinned['file_size']).')</div>' : NULL).'
		</div>';
		}
	}
	$posts = $db->query("SELECT * FROM `forum_pt` WHERE `topic_id` = '". $id ."' ORDER BY time ASC LIMIT ".$pages->start().", ".Site::perPage()."");
	foreach($posts as $post)
	{
		echo '<div class="content" id="post'.$post['id'].'">
		'.(user()->isUser() ? '<span class="float-rt">'.(user()->getId() != $post['user_id'] && $topic['closed'] == 0 ? '[<a href="/forum/topic/'.$post['topic_id'].'?page=end&amp;reply_to='.$post['user_id'].'#reply">'. _t('Reply').'</a>
			| <a href="/forum/topic/'.$post['topic_id'].'?page=end&amp;quote='. $post['id'] .'#reply">'. _t('Quote').'</a>]': NULL)
		. (user()->level() >= 3 || (user()->getId() == $post['user_id'] && $post['time'] > (time()-300)) ? ' [<a href="/forum/delete_post/'.$post['id'].'?topic_id='.$post['topic_id'].'&delete">x</a> | <a href="/forum/edit_post/'.$post['id'].'?topic_id='.$post['topic_id'].'">+</a>]' : NULL).'</span>':NULL)
		.  user()->nick($post['user_id'], '<a href="/forum/post/'.$post['id'].'">'. App::date($post['time']).'</a><br/>') . Site::output($post['text'])
			.(!empty($post['file']) ? '<br/><span class="file"><a href="/files/forum/'.$id.DS.$post['file'].'">'.$post['file'].'</a> ('.App::fileSize($post['file_size']).')</span>' : NULL).'
		'.($post['count_edit'] > 0 ? '<br/><span class="edited">
	'. _t('Edited by').' <b>'. user()->getNick($post['edit_user_id']) .'</b> ('. App::date($post['edit_time']) .') ['. $post['count_edit'] .']</span>':NULL) .'
		</div>';
	}
	$pages->view();
}

if(user()->isUser() && $topic['closed'] == 0)
{
	echo '<div class="content">
	<form id="reply" action="/forum/add_post/'. $id .'?create'.(isset($_GET['reply_to']) ? '&reply_to='.App::filter('int', $_GET['reply_to']) : (isset($_GET['quote']) ? '&quote='.App::filter('int', $_GET['quote']) : null)).'" method="post" enctype="multipart/form-data">
	';

	if(isset($_GET['quote']))
		$quote = "[quote][i][b]".user()->getNick($db->query("SELECT user_id FROM `forum_pt` WHERE `topic_id` = '". $id ."' AND `id` = '". App::filter('int', $_GET['quote']) ."'")->fetchColumn())."[/b] ".date('d.m.Y, H:i', $db->query("SELECT time FROM `forum_pt` WHERE `topic_id` = '". $id ."' AND `id` = '". App::filter('int', $_GET['quote']) ."'")->fetchColumn())."[/i]:
".preg_replace("/\[quote\]|\[\/quote\]/i", '', $db->query("SELECT text FROM `forum_pt` WHERE `topic_id` = '". $id ."' AND `id` = '". App::filter('int', $_GET['quote']) ."'")->fetchColumn())."[/quote]";
	else
		$quote = '';

	echo Site::textarea((isset($_GET['reply_to']) ? '[b]'.user()->getNick(App::filter('int', $_GET['reply_to'])).'[/b], ' : NULL) . $quote, ['rows' => 6, 'placeholder' => _t('Message'), 'id'=>'message']);
	echo '<br/>
	<input type="file" id="fileForm" name="file" style="display: none;" />
	<input name="create" type="submit" value="'. _t('Add') .'" />
	<button id="fileButton" type="button" onclick="document.getElementById(\'fileButton\').style.display=\'none\'; document.getElementById(\'fileForm\').style.display=\'block\';">'._t('Attach file').'</button>
	<br/>
	</form></div>';
}
else
{
	echo '<div class="content"><span class="red">'. _t('Topic is closed', 'forum') .'</span></div>';
}
echo '<div class="action_list">';
if(user()->level()>=3)
{
	echo '<a href="/forum/move_topic/'. $topic['id'].'">'.Site::icon('arrow-right').' '._t('Move').'</a>';

	if($topic['closed'] == 0)
	{
		echo '<a href="/forum/act/'. $topic['id'].'?act=close">'.Site::icon('arrow-right').' '. _t('Close') .'</a>';
	}
	else
	{
		echo '<a href="/forum/act/'. $topic['id'].'?act=open">'.Site::icon('arrow-right').' '. _t('Open') .'</a>';
	}
}

/*///////////////// Vote //////////////////
$user_topics = $db->query("SELECT * FROM `forum_pt` WHERE `cat_id` != '0' AND `topic_id` = '". $id ."'")->fetch();
if (user()->level()>=5 || user()->level() == 3 || $user_topics['user_id'] == user()->profile('id'))
{
	if ($top_vote == 0 && $topic['closed'] == 0) 
	{
		echo NAV . '<a href="/forum/vote_topic/'. $topic['id'].'">'. _t('add_vote') .'</a><br/>';
	}
	
	if ($top_vote != 0) 
	{
		echo NAV . '<a href="/forum/vote_edit_topic/'. $topic['id'].'">'. _t('edit_vote') .'</a><br/>';
		echo NAV . '<a href="/forum/vote_delete_topic/'. $topic['id'].'">'. _t('delete_vote') .'</a><br/>';
		
		if($post_vote['closed'] == 0)
		{
			echo NAV . '<a href="/forum/act/'. $topic['id'].'?act=close_vote">'. _t('close_vote') .'</a><br/>';
		}
		elseif($post_vote['closed'] == 1)
		{
			echo NAV . '<a href="/forum/act/'. $topic['id'].'?act=open_vote">'. _t('open_vote') .'</a><br/>'; 
		}
	}
}
///////////////// Vote //////////////////*/

if(user()->level()>=4 && $topic['attach'] == 0)
{
	echo '<a href="/forum/act/'. $topic['id'].'?act=attach">'.Site::icon('arrow-right').' '. _t('Pin topic') .'</a>';
}
elseif(user()->level()>=4 && $topic['attach'] == 1)
{
	echo '<a href="/forum/act/'. $topic['id'].'?act=unpin">'.Site::icon('arrow-right').' '. _t('Unpin topic') .'</a>';
}
$postNav = $db->query("SELECT * FROM `forum_c` WHERE `id` = '". $topic['cat_id']."'")->fetch();
echo '<a href="/forum/section/'. $topic['cat_id'] .'">'.Site::icon('arrow-left').' '.$postNav['name'].'</a>'.
'<a href="/forum/view/'. $topic['f_id'] .'">'.Site::icon('arrow-left').' '. $db->query("SELECT name FROM `forum` WHERE `id` = '". $postNav['f_id'] ."'")->fetchColumn().'</a>
</div>';

Site::footer();