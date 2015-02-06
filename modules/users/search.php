<?php
$id = @App::filter('input', $_GET['id']);

Site::header(_t('Search'));
echo Site::div('title', Site::breadcrumbs(_t('Search')));
$db = App::db();

if(isset($_GET['id']))
{
	if(isset($_GET['type']))
	{
		$type = ($_GET['type'] == 0 ? 'nick' : ($_GET['type'] == 1 ? 'name' : ($_GET['type'] == 2 ? 'city' : ($_GET['type'] == 3 ? 'country' : 'name'))));
	}
	else
	{
		$type = 'nick';
	}
	
	if(isset($_GET['order']))
	{
		$order = ($_GET['order'] == 0 ? 'ASC' : ($_GET['type'] == 1 ? 'DESC' : 'DESC'));
	}
	else
	{
		$order = 'ASC';
	}
	
	$sql = (empty($id) ? "SELECT * FROM `users` ORDER BY time, $type $order " : "SELECT * FROM `users` WHERE `".$type."` LIKE '%".$id."%' ORDER BY time $order ");
	
	$users_r = $db->query("SELECT * FROM `users`".(!empty($id) ? " WHERE `".$type."` LIKE '%".$id."%'" : null)."")->rowCount();
	$pages = new Pager($users_r, Site::perPage());
	if($users_r == 0)
	{
		echo Site::div('content', _t('Not found users'));
	}
	else
	{
		$users = $db->query($sql."LIMIT ".$pages->start().", ".Site::perPage()."");
		// print_r($db->errorInfo());
		foreach($users as $user)
		{
			echo Site::div('content', user()->nick($user['id'], user()->levelName(user()->profile('level', $user['id']))));
		}
		$pages->view();
	}
}

echo '<div class="content">'. _t('Search').':
<form action="/users/search" method="get">
<input type="text" name="id"'.(isset($_GET['id']) ? ' value="'.$id.'"' : null).' /><br/>
<select name="type">
<option value="0"'.(isset($_GET['type']) && $_GET['type'] == 0 ? ' selected="selected"' : null).'>'._t('By nickname').'</option>
<option value="1"'.(isset($_GET['type']) && $_GET['type'] == 1 ? ' selected="selected"' : null).'>'._t('By name').'</option>
<option value="2"'.(isset($_GET['type']) && $_GET['type'] == 2 ? ' selected="selected"' : null).'>'._t('By city').'</option>
<option value="3"'.(isset($_GET['type']) && $_GET['type'] == 3 ? ' selected="selected"' : null).'>'._t('By country').'</option>
</select><br />
'._t('Sort').':<br />
<select name="order">
<option value="0"'.(isset($_GET['type']) && $_GET['order'] == 0 ? ' selected="selected"' : null).'>'._t('Ascending').'</option>
<option value="1"'.(isset($_GET['type']) && $_GET['order'] == 1 ? ' selected="selected"' : null).'>'._t('Descending').'</option>
</select><br/>
<input type="submit" value="Go!" />
</form>
</div>';

Site::footer();