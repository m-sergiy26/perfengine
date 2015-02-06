<?php

if(user()->level() < 5 || !isset($_GET['id'])) App::redirect('/');
$id = App::filter('int', $_GET['id']);

$db = App::db();

if($db->query("SELECT * FROM `forum_c` WHERE `id` = '".$id."'")->rowCount() == 0)
	Site::notFound();

$section = $db->query("SELECT * FROM `forum_c` WHERE `id` = '".$id."'")->fetch();
if(isset($_GET['edit']))
{
	$name = App::filter('input', $_POST['name']);
	$desc = App::filter('input', $_POST['desc']);
	if(!empty($name))
	{
		$db->query("UPDATE `forum_c` SET `name` = '". $name ."', `desc` = '$desc' WHERE `id` = '".$id."'");
		App::redirect('/forum/section/'.$id);
		// print_r($db->errorInfo());
	}
}
elseif(isset($_GET['delete']))
{
	$db->query("DELETE FROM `forum_c` WHERE `id` = '".$id."'");
	$db->query("DELETE FROM `forum_t` WHERE `cat_id` = '".$id."'");
	$db->query("DELETE FROM `forum_pt` WHERE `cat_id` = '".$id."'");
	App::redirect('/forum/view/'.$section['f_id']);
}

Site::header(_t('Edit').' - '.$section['name'].' - '._t('Forum'));

echo Site::div('title', Site::breadcrumbs(_t('Edit').' - '.$section['name']));
echo '<form action="/forum/edit_section/'.$id.'/?edit" method="post">
		<div class="content">
			<input type="text" placeholder="'._t('Title').'" name="name" value="'. $section['name'] .'" /><br/>
			<input type="text" placeholder="'._t('Description').'" name="desc" value="'. $section['desc'] .'" /><br/>
			<input name="edit" type="submit" value="'. _t('Edit') .'" />
			<a class="button" href="/forum/edit_section/'.$id.'/?delete">'._t('Delete').'</a>
		</div>
		</form>';
Site::footer();