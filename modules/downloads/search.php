<?php
Site::header(_t('Search'));
echo Site::div('title', Site::breadcrumbs(_t('Search')));
$db = App::db();
if(isset($_GET['q']) && mb_strlen($_GET['q']) >= 3 && isset($_GET['in']))
{
	if($_GET['in'] !== 'name' && $_GET['in'] !== 'description')
	{
		echo Site::div('content', _t('Files not found'));
		echo '<div class="content">
		<form action="/downloads/search?" method="get">
		<input placeholder="'._t('Search query').'" type="text" name="q" /><br/>
		'. _t('Search in') .':<br/>
		<select name="in">
		<option value="name">'._t('File names', 'downloads').'</option>
		<option value="description">'._t('File descriptions', 'downloads').'</option>
		</select><br/>
		<input type="submit" value="'. _t('Search') .'" />
		</form>
		</div>';

		Site::footer();
		exit;
	}


	$search_r = $db->query("SELECT * FROM `downloads_files` WHERE `" . App::filter('input', $_GET['in']) . "` LIKE '%" . App::filter('input', $_GET['q']) . "%'")->rowCount();
	echo Site::div('content', _t('Found items') . ': <b>' . $search_r . '</b>');
	$pages = new Pager($search_r, Site::perPage());
	if ($search_r == 0)
	{
		echo Site::div('content', _t('Files not found'));
	}
	else
	{
		echo '<div class="menu_list">';
		$search = $db->query("SELECT * FROM `downloads_files` WHERE `" . App::filter('input', $_GET['in']) . "` LIKE '%" . App::filter('input', $_GET['q']) . "%' LIMIT " . $pages->start() . ", " . Site::perPage() . "");
		foreach ($search as $file) {
			$file['name'] = str_replace(App::filter('input', $_GET['q']), '<b>' . App::filter('input', $_GET['q']) . '</b>', $file['name']);
			echo '<a href="/downloads/file/' . $file['id'] . '">' . Site::ext($file['ext']) . ' ' . $file['name'] . ' (' . $file['ext'] . ') [' . App::fileSize($file['size']) . ']<br/>
		 ' . ($file['description'] != '' ? mb_substr($file['description'], 0, 100) . '...' : '') . '</a>';
		}
		echo '</div>';
		$pages->view();
	}
}
echo '<div class="content">
		<form action="/downloads/search?" method="get">
		<input placeholder="'._t('Search query').'" type="text" name="q" /><br/>
		'. _t('Search in') .':<br/>
		<select name="in">
		<option value="name">'._t('File names', 'downloads').'</option>
		<option value="description">'._t('File descriptions', 'downloads').'</option>
		</select><br/>
		<input type="submit" value="'. _t('Search') .'" />
		</form>
		</div>';

Site::footer();