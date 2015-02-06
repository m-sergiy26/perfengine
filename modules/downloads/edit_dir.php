<?php
/**
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) 2013, Taras Chornyi, Sergiy Mazurenko, Ivan Kotliar
 * @link          http://perf-engine.net
 * @package       PerfEngine
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

$locate	= 'in_downloads';
$dir_id = abs(intval($_GET['id']));
if(!isset($dir_id) && $db->query("SELECT * FROM `downloads` WHERE `id` = '$dir_id'")->rowCount() == 0 || $user['level'] < 4)
	{
		header('location: /downloads/');
		exit;
	}
$diri = $db->query("SELECT * FROM `downloads` WHERE `id` = '". $dir_id ."'")->fetch();
if(isset($_GET['act']) && $_GET['act'] == 'save')
	{
		$name = mb_substr(input($_POST['dir_name']), 0, 100);
		$desc = input($_POST['dir_desc']);
		$access = (num($_POST['access']) == 0 || num($_POST['access']) == 1 ? num($_POST['access']) : 0);
		if(!empty($name))
			{
				$db->query("UPDATE `downloads` SET `name` = '$name', `description` = '$desc', `access` = '$access' WHERE `id` = '$dir_id'");
				// print_r($db->errorInfo());
				header('location: /downloads/dir/'.$dir_id);
				exit;
			}
	}
$title = _t('dl_edit_dir').' | '._t('downloads');
include_header($title);
$tpl->div('title', _t('dl_edit_dir'));
echo '<div class="post">
		<form action="/downloads/edit_dir/'.$dir_id.'?act=save" method="post">
		'._t('dl_dir_name').':<br/>
		<input type="text" value="'.$diri['name'].'" name="dir_name" /><br/>
		'._t('dl_dir_desc').':<br/>
		<textarea name="dir_desc" rows="5" cols="25">'.$diri['description'].'</textarea><br/>
		'. _t('access_upload') .':<br/>
		<select name="access">
		<option value="0"'.($diri['access'] == 0 ? ' selected="selected"' : false).'>'. _t('access_admins') .'</option>
		<option value="1"'.($diri['access'] == 1 ? ' selected="selected"' : false).'>'. _t('access_all') .'</option>
		</select><br/>
		<input type="submit" value="'. _t('save') .'" />
		</form>
	</div>';
$tpl->div('block', img('nav.png') . ' <a href="/downloads/dir/'.$dir_id.'">'. _t('back') .'</a><br/>' 
				. img('download.png') . ' <a href="/downloads/">'. _t('downloads') .'</a><br/>'
				. HICO .' <a href="/">'. _t('home') .'</a>');
include_footer();
?>