<?php

if(user()->level() < 4 || !isset($_GET['id'])) Site::notFound();

$db = App::db();
$id = App::filter('int', $_GET['id']);

if($db->query("SELECT * FROM `forum_pt` WHERE `topic_id` = '". $id ."'")->rowCount() == 0) 
{
	Site::notFound();
}


if(isset($_GET['move']))
{
	$place = App::filter('int', $_POST['place']);
	if ($db->query("SELECT * FROM `forum_c` WHERE `id` = '". $place ."'")->rowCount() != 0)
	{
		$f_id = $db->query("SELECT f_id FROM `forum_c` WHERE `id` = '". $place ."'")->fetchColumn();

		$db->query("UPDATE `forum_t` SET `cat_id` = '". $place ."', `f_id` = '".$f_id."'  WHERE `id` = '". $id ."'");
		$db->query("UPDATE `forum_pt` SET `cat_id` = '". $place ."', `f_id` = '".$f_id."'  WHERE `topic_id` = '". $id ."'");
	}
	App::redirect('/forum/topic/'.$id);
}

Site::header(_t('Move topic', 'forum').' - '._t('Forum', 'forum'));
echo Site::div('title', Site::breadcrumbs(_t('Move topic', 'forum')));
echo '<div class="content">
<form action="/forum/move_topic/'. $id.'?move" method="post">
'. _t('Choose new forum section', 'forum') .':<br/>
<select name="place">';
$places = $db->query("SELECT * FROM `forum_c`");
foreach($places as $place)
{
	$forum_name = $db->query("SELECT name FROM `forum` WHERE `id` = '".$place['f_id']."'")->fetchColumn();
	echo "<option value=\"". $place['id'] ."\" ".($topic['cat_id'] == $place['id'] ? 'selected="selected"' : NULL).">".$forum_name." / ". $place['name'] ."</option>\n";
}
echo '</select><br/>
<input name="edit" type="submit" value="'. _t('Move') .'" /><br/></form>
</div>';
echo Site::div('action_list', '<a href="/forum/topic/'.$id.'?page=end">'.Site::icon('arrow-left').' '. _t('Back').'</a>');
Site::footer();