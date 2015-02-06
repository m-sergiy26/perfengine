<?php
if(user()->level() < 4)
	App::redirect('/news');

$db = App::db();

if(isset($_GET['create']))
{
	$name = App::filter('input', $_POST['name']);
	$text = App::filter('input', $_POST['text']);
	$tags = App::filter('input', $_POST['tags']);

	if(!empty($name) && !empty($text) && !empty($tags))
	{
		$atcl = $db->prepare("INSERT INTO `news`(`name`, `text`, `time`, `user_id`, `tags`) VALUES(?, ?, ?, ?, ?)");
		$atcl->execute([$name, $text, time(), user()->getId(), $tags]);

		$lastId = $db->lastInsertId();
		$file_dir = ROOT .'/files/articles/'.$lastId.DS;
		if (isset($_FILES['file']) && $_FILES['file']['tmp_name'])
		{
			if(!is_dir($file_dir))
				mkdir($file_dir);

			$patch = pathinfo($_FILES['file']['name']);
			$extension = strtolower($patch['extension']);
			if (!in_array($extension, explode(';', 'png;jpg;jpeg;gif'))) $err = 'File extension not allowed.';
			$name_start = mb_convert_encoding(App::filter('input', $patch['filename']), "UTF-8");
			$name_end = iconv('UTF-8', 'UTF-8//TRANSLIT', $name_start);
			$fileName = $name_end.'_'.substr(md5(time().$name_end), 0, 8).'.'. $extension;
			if (file_exists($file_dir . $fileName)) $err = 'This file exists';

			if(!isset($err))
			{
				move_uploaded_file($_FILES['file']['tmp_name'], $file_dir . $fileName);
				$db->query("UPDATE `news` SET `picture` = '".$fileName ."' WHERE `id` = '".$lastId."'");
				// print_r($db->errorInfo());
			}
		}

		App::redirect('/news/article/'.$lastId);
	}
}
Site::header(_t('Write article', 'news'));
echo Site::div('title', _t('Write article', 'news'));
echo '<form action="/news/write_article?create" method="post" enctype="multipart/form-data">
		<div class="content">
			<input name="name" type="text" placeholder="'._t('Title').'"/><br/>
			'.Site::textarea('', ['rows' => 6, 'placeholder' => _t('Message'), 'id'=>'message']);
echo '<br/>
<input name="tags" type="text" placeholder="'._t('Tags').'"/><br/>
<button id="fileButton" type="button" onclick="document.getElementById(\'fileButton\').style.display=\'none\'; document.getElementById(\'fileForm\').style.display=\'block\';">'._t('Attach picture').'</button>
	<input type="file" id="fileForm" name="file" style="display: none;" />
		<input name="create" type="submit" value="'. _t('Add') .'" />
		</div>
		</form>';
Site::footer();