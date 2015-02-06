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
$locate = 'in_news';
if(!User::logged() ||  $user['level'] < 6) { go('/'); }
if(isset($_GET['news_id'])) {
if(isset($_POST['yes'])) {
$db->query("DELETE FROM `news` WHERE `id` = '".abs(intval($_GET['news_id'])) ."'");
go('/news/');
	} elseif(isset($_POST['no'])) {
go('/news/');
	} 
$title = _t('delete');
include_header($title);
$tpl->div('title', _t('delete'));
echo '<form action="/news/delete_news?news_id='.abs(intval($_GET['news_id'])) .'" method="post">
		<div class="menu">
			<b>'. _t('r_sure') .'</b><br/>
			<input name="yes" type="submit" value="'. _t('yyes') .'" /> <input name="no" type="submit" value="'. _t('yno') .'" /><br/>
		</div>
		</form>';
		
$tpl->div('block', NAV .'<a href="/news/">'. _t('news') .'</a><br/>' . HICO .'<a href="/">'. _t('home').'</a>');
include_footer();
} else { go('/'); }
?>