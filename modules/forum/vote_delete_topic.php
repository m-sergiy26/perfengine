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
$locate = 'in_forum';
$user_topics = $db->query("SELECT * FROM `forum_pt` WHERE `cat_id` != '0' AND `topic_id` = '". abs(intval($_GET['id'])) ."'")->fetch();
if(user()->Id() != $user_topics['user_id'] && user()->level() < 2  && user()->level() < 5 || !isset($_GET['id'])) { go('/'); exit;}
if($db->query("SELECT * FROM `forum_pt` WHERE `topic_id` = '". abs(intval($_GET['id'])) ."'")->rowCount() == 0) 
{
	go('/forum/');
	exit;
}
$id = abs(intval($_GET['id']));
if(isset($_POST['yes'])) 
	{
	$db->query("DELETE FROM `forum_vote` WHERE `topic_id` = '". $id ."'");
	$db->query("DELETE FROM `forum_vote_rez` WHERE `topic_id` = '". $id ."'");
	redirect('/forum/topic/'.$id.'');
	}
	elseif(isset($_POST['no'])) 
	{
		redirect('/forum/topic/'.$id.'');
	}

$title = _t('delete_vote');
include_header($title);
Template::div('title', _t('delete_vote'));

if ($db->query("SELECT * FROM `forum_vote` WHERE `topic_id` = '". abs(intval($_GET['id'])) ."' AND `type` = '1'")->rowCount() > 0) {

echo '<form action="/forum/vote_delete_topic/'. $id .'/" method="post">
		<div class="menu">
		<u>'. _t('votes') .'</u>: '.$db->query("SELECT name FROM `forum_vote` WHERE `topic_id` = '". abs(intval($_GET['id'])) ."' AND `type` = '1'")->fetchColumn().'<br />
			<b>'. _t('r_sure') .'</b><br/>
			<input name="yes" type="submit" value="'. _t('yes') .'" /> <input name="no" type="submit" value="'. _t('no') .'" /><br/>
		</div>
		</form>';
} else {
Template::div('block', _t('edit_vote_error'));
}
Template::div('block',  NAV .'<a href="/forum/topic/'.abs(intval($_GET['id'])).'/">'. _t('back') .'</a><br/>' . NAV .'<a href="/forum/">'. _t('forum') .'</a><br/>' . HICO .'<a href="/">'. _t('home').'</a>');
include_footer();
?>