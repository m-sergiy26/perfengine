<?php
if(user()->level() < 5 || !isset($_GET['id'])) App::redirect('/');

$db = App::db();
$id = App::filter('int', $_GET['id']);

if($db->query("SELECT * FROM `forum` WHERE `id` = '".$id."'")->rowCount() == 0)
	App::redirect('/forum/');


$forum = $db->query("SELECT * FROM `forum` WHERE `id` = '".$id."'")->fetch();

if(isset($_GET['edit']))
{
	$name = App::filter('input', $_POST['name']);
	$desc = App::filter('input', $_POST['desc']);

	$db->query("UPDATE `forum` SET `name` = '".$name."', `desc` = '".$desc."' WHERE `id` = '".$id."'");
	App::redirect('/forum/view/'.$id);
//	 print_r($db->errorInfo());
}
elseif(isset($_GET['delete']))
{
	$db->query("DELETE FROM `forum` WHERE `id` = '".$id."'");
	$db->query("DELETE FROM `forum_c` WHERE `f_id` = '".$id."'");
	$db->query("DELETE FROM `forum_t` WHERE `f_id` = '".$id."'");
	$db->query("DELETE FROM `forum_pt` WHERE `f_id` = '".$id."'");
	App::redirect('/forum');
}

Site::header(_t('Edit').' - '.$forum['name'].' - '._t('Forum'));

echo Site::div('title', Site::breadcrumbs(_t('Edit').' - '.$forum['name']));
echo '<form action="/forum/edit_forum/'.$id.'/?edit" method="post">
		<div class="content">
			<input type="text" placeholder="'._t('Forum name', 'forum').'" name="name" value="'. $forum['name'] .'" /><br/>
			<input type="text" placeholder="'._t('Forum description', 'forum').'" name="desc" value="'. $forum['desc'] .'" /><br/>
			<input name="edit" type="submit" value="'. _t('Edit') .'" />
			<a class="button" href="/forum/edit_forum/'.$id.'/?delete">'._t('Delete').'</a>
		</div>
		</form>';
Site::footer();