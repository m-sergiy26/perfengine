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
if(isset($_GET['act']) && $_GET['act'] == 'delete')
	{
		if(isset($_POST['yes']))
			{
				rrmdir(ROOT.'/files/downloads/'.$diri['server_path']);
				$db->query("DELETE FROM `downloads_files` WHERE `ref_id` = '". $dir_id ."'");
				$db->query("DELETE FROM `downloads` WHERE `id` = '". $dir_id ."'");
				$db->query("DELETE FROM `downloads` WHERE `dir_id` = '". $dir_id ."'");
				// print_r($db->errorInfo());
				header('location: /downloads/');
				exit;
			}
		elseif(isset($_POST['no']))
			{
				header('location: /downloads/file/'.$dir_id);
				exit;
			}
	}
				
$title = _t('dl_edit_file').' | '._t('downloads');
include_header($title);
$tpl->div('title', _t('dl_edit_file'));
echo '<div class="post">
		<form action="/downloads/delete_dir/'.$dir_id.'?act=delete" method="post">
		'._t('dl_dir_delete_attention').' <b>'.$diri['name'].'</b>?<br/>
		<input type="submit" name="yes" value="'. _t('yyes') .'" /> <input type="submit" name="no" value="'. _t('yno') .'" />
		</form>
	</div>';
$tpl->div('block', img('download.png') . ' <a href="/downloads/">'. _t('downloads') .'</a><br/>'
				. HICO .' <a href="/">'. _t('home') .'</a>');
include_footer();
?>