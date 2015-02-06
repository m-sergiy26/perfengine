<?php
if(user()->level() < 5)
	App::redirect('/');

if(isset($_GET['create']))
{
	$name = App::filter('input', $_POST['name']);
	$desc = App::filter('input', $_POST['desc']);

	$forum = App::db()->prepare("INSERT INTO `forum`(`name`, `desc`, `pos`) VALUES(?, ?, ?)");
	$position = App::db()->query("SELECT COUNT(*) FROM `forum`")->fetchColumn() +1;
	$forum->execute([$name, $desc, $position]);
	App::redirect('/forum/');
}

Site::header(_t('Create forum', 'forum'));
echo Site::div('title', Site::breadcrumbs(_t('Create forum', 'forum')));
echo '<form action="/forum/add_forum?create" method="post">
		<div class="content">
			<input name="name" type="text" placeholder="'._t('Forum name', 'forum').'"/><br/>
			<input name="desc" type="text" placeholder="'._t('Forum description (optional)', 'forum').'"/><br/>
			<input type="submit" value="'. _t('Create') .'" /><br/>
		</div>
		</form>';