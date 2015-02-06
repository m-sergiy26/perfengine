<?php
//var_dump(user()->level());
// if does not active forum id go back
if(!isset($_GET['id'])) App::redirect('/forum/');

$f_id = App::filter('int', $_GET['id']);

// if forum do not exists show alert
if(App::db()->query("SELECT * FROM `forum` WHERE `id` = '". $f_id ."'")->rowCount() == 0)
	Site::notFound();

// forum array
$forumf = App::db()->query("SELECT * FROM `forum` WHERE `id` = '". $f_id ."'")->fetch();
//title of forum

// showing header
Site::header($forumf['name'] .' - '._t('Forum', 'forum'));
echo Site::div('title', Site::breadcrumbs($forumf['name']));
// count categories
$forum_r = App::db()->query("SELECT * FROM `forum_c` WHERE `f_id` = '". $f_id ."'")->rowCount();
// if no categories show alert
if($forum_r == 0) 
{
	echo Site::div('content', _t('No sections yet', 'forum'));
} 
else // else show subforums
{
	$forum_q = App::db()->query("SELECT * FROM `forum_c` WHERE `f_id` = '". $f_id ."' ORDER BY pos");
	while($forum = $forum_q->fetch()) 
	{
		echo Site::div('menu_list', ' <a href="/forum/section/'. $forum['id'] .'">'.Site::icon('list').' '. $forum['name'] .'
		('.Site::counter('forum_t', ['cat_id' => $forum['id']]).' / '.Site::counter('forum_pt', ['cat_id' => $forum['id']]).')
		<br/>
		<small>'. $forum['desc'] .'</small></a>');
	}
}
if(user()->level() >=5)
{
	echo '<div class="action_list">
	<a href="/forum/edit_forum/'.$f_id.'">'.Site::icon('arrow-right').' '._t('Edit forum', 'forum').'</a>
	<a href="/forum/add_section/'.$f_id.'">'.Site::icon('arrow-left').' '._t('Add section', 'forum').'</a>
	</div>';
}

Site::footer();