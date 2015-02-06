<?php

if(user()->level() < 5 || !isset($_GET['id']))
	App::redirect('/');

$id = App::filter('int', $_GET['id']);
$db = App::db();

if($db->query("SELECT * FROM `forum` WHERE `id` = '". $id ."'")->rowCount() == 0)
	Site::notFound();

if(isset($_GET['create']))
{
	$name = App::filter('input', $_POST['name']);
	$desc = App::filter('input', $_POST['desc']);
	$ins = $db->prepare("INSERT INTO `forum_c`(`name`, `f_id`, `desc`, `pos`) VALUES(?, ?, ?, ?)");
	$pos = $db->query("SELECT * FROM `forum` WHERE `id` = '". $id ."'")->rowCount() +1;
	$ins->execute([$name, $id, $desc, $pos]);
	App::redirect('/forum/view/'. $id);
	// print_r($db->errorInfo());
}
$title = _t('Add section', 'forum').' - '._t('Forum', 'forum');
Site::header($title);
echo Site::div('title', Site::breadcrumbs(_t('Add section', 'forum')));
echo '<form action="/forum/add_section/'. $id .'?create" method="post">
		<div class="content">
			<input name="name" type="text" placeholder="'. _t('Title') .'"/><br/>
			<input name="desc" type="text" placeholder="'. _t('Description') .'"/><br/>
			<input name="create" type="submit" value="'. _t('Create') .'" /><br/>
		</div>
		</form>';
		
Site::footer();