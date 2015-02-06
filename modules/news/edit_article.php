<?php

if(user()->level() < 4 || !isset($_GET['id']))
	Site::notFound();

$news_id = App::filter('int', $_GET['id']);
$db = App::db();
$article = $db->query("SELECT * FROM `news` WHERE `id` =  '".$news_id."'")->fetch();

if(isset($_GET['edit']))
{
	$name = App::filter('input', $_POST['name']);
	$text = App::filter('input', $_POST['text']);
	$tags = App::filter('input', $_POST['tags']);
	if(!empty($text) && !empty($name) && !empty($tags))
	{
		$atcl = $db->prepare("UPDATE `news` SET `name` = ?, `text` = ?, `tags` = ? WHERE `id` =  ?");
		$atcl->execute([$name, $text, $tags, $news_id]);

		$file_dir = ROOT . '/files/articles/' . $news_id . DS;
		if(isset($_FILES['file']) && $_FILES['file']['tmp_name'])
		{
			if($article['picture'] != '')
				@unlink($file_dir.$article['picture']);

			if (!is_dir($file_dir))
				mkdir($file_dir);

			$patch = pathinfo($_FILES['file']['name']);
			$extension = strtolower($patch['extension']);
			if (!in_array($extension, explode(';', 'png;jpg;jpeg;gif'))) $err = 'File extension not allowed.';
			$name_start = mb_convert_encoding(App::filter('input', $patch['filename']), "UTF-8");
			$name_end = iconv('UTF-8', 'UTF-8//TRANSLIT', $name_start);
			$fileName = $name_end . '_' . substr(md5(time() . $name_end), 0, 8) . '.' . $extension;
			if (file_exists($file_dir . $fileName)) $err = 'file error';

			if (!isset($err))
			{
				move_uploaded_file($_FILES['file']['tmp_name'], $file_dir . $fileName);
				$db->query("UPDATE `news` SET `picture` = '" . $fileName . "' WHERE `id` = '" . $news_id . "'");
				// print_r($db->errorInfo());
			}
		}
	}

	// print_r($db->errorInfo());
	App::redirect('/news/article/'.$news_id);
}
elseif(isset($_GET['delete']))
{
	@unlink( ROOT . '/files/articles/' . $news_id . DS.$article['picture']);
	$db->query("DELETE FROM `news` WHERE `id` = '".$news_id."'");
	App::redirect('/news');
}

$title = _t('Edit').' - '.$article['name'];
Site::header($title);
echo Site::div('title', Site::breadcrumbs(_t('Edit').' - '.$article['name']));

echo '<div class="content">
<form action="/news/edit_article/'.$news_id.'?edit" method="post" enctype="multipart/form-data">
			<input name="name" type="text" value="'. $article['name'] .'" placeholder="'._t('Title').'" /><br/>
			'.Site::textarea($article['text'], ['rows' => 6, 'placeholder' => _t('Message'), 'id'=>'message']).'<br/>
			<input name="tags" type="text" value="'.$article['tags'].'" placeholder="'._t('Tags').'"/><br/>
<button id="fileButton" type="button" onclick="document.getElementById(\'fileButton\').style.display=\'none\'; document.getElementById(\'fileForm\').style.display=\'block\';">'._t('Attach picture').'</button>
	<input type="file" id="fileForm" name="file" style="display: none;" />
			<input type="submit" value="'. _t('Save') .'" />
			<a class="button" href="/news/edit_article/'.$news_id.'?delete">'._t('Delete').'</a><br/>
		</form>
		</div>';
echo '<div class="action_list"><a href="/news/article/'.$news_id.'">'.Site::icon('arrow-left').' '._t('Back').'</a></div>';
Site::footer();