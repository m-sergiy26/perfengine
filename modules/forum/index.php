<?php
Site::header(_t('Forum', 'forum'));
echo '<div class="title">'.Site::breadcrumbs(_t('Forum', 'forum')).'</div>';

$forums_r = App::db()->query("SELECT * FROM `forum`")->rowCount();
if($forums_r == 0)
{
	echo Site::div('content', _t('No forums yet'));
}
else
{
	$forums = App::db()->query("SELECT * FROM `forum` ORDER BY pos");
	foreach($forums as $forum)
	{
		echo Site::div('menu_list', ' <a href="/forum/view/'. $forum['id'] .'">'.Site::icon('list').' '. $forum['name'] .'
		('.Site::counter('forum_c', ['f_id' => $forum['id']]).' / '.Site::counter('forum_t', ['f_id' => $forum['id']]).' / '.Site::counter('forum_pt', ['f_id' => $forum['id']]).')
		<br/>
		<small>&nbsp;'.$forum['desc'].'</small>
		</a>');
	}
}
?>
<div class="action_list">
	 <a href="/forum/new_posts"><?=Site::icon('arrow-left')?> <?=_t('New posts', 'forum')?></a>
	 <a href="/forum/search"><?=Site::icon('search')?> <?=_t('Search')?></a>
	<? if(user()->level() >= 5): ?>
	<a href="/forum/add_forum"><?=Site::icon('arrow-right')?> <?=_t('Add forum', 'forum')?></a>
	<? endif; ?>
</div>
<? Site::footer();