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

$title = _t('edit_vote');
include_header($title);
Template::div('title', _t('edit_vote'));

if ($db->query("SELECT * FROM `forum_vote` WHERE `topic_id` = '". abs(intval($_GET['id'])) ."' AND `type` = '1'")->rowCount() > 0) {
$vot = abs(intval($_POST['more_answers']));
$votes = $db->query("SELECT * FROM `forum_vote` WHERE `topic_id` = '". abs(intval($_GET['id'])) ."' AND `type` = '2' ORDER BY id");

if ($vot != 0 && $vot < 21) {
if ($vot <= $votes->rowCount()) $vot = $votes->rowCount();
$input_vote = $vot;
} else {
$input_vote = $votes->rowCount();
}

if (isset($_POST['edit'])) {
$question = input($_POST['question']);
if (!empty($question)) {
$db->query("UPDATE `forum_vote` SET  `name` = '". $question."' WHERE `topic_id` = '". abs(intval($_GET['id'])) ."' AND `type` = '1'");
}

foreach($votes as $vote)
		{
		$v = input($_POST['v_e_'.$vote['id'].'']);
		if (!empty($v)) {
		$db->query("UPDATE `forum_vote` SET  `name` = '". $v."' WHERE `id` = '". $vote['id']."' AND `type` = '2'");
		}
		}

for ($i = $votes->rowCount() + 1; $i <= $input_vote; $i++) {
$v = input($_POST['v_'.$i.'']);

if (!empty($v)) {
$db->query("INSERT INTO `forum_vote` SET `name` = '". $v."', `topic_id` = '". abs(intval($_GET['id']))."', `type` = '2'");
}
}

echo '<div class="menu">'._t('succ_save').'<br /><a href="/forum/topic/'.abs(intval($_GET['id'])).'/">'. _t('continue') .'</a></div>';
Template::div('block',  NAV .'<a href="/forum/topic/'.abs(intval($_GET['id'])).'/">'. _t('back') .'</a><br/>' . NAV .'<a href="/forum/">'. _t('forum') .'</a><br/>' . HICO .'<a href="/">'. _t('home').'</a>');
include_footer();
exit;
}

echo '<div class="menu">';
echo '<form action="/forum/vote_edit_topic/'. abs(intval($_GET['id'])) .'" method="post">
'._t('question').':<br/>
<input type="text" maxlength="300" value="'.$db->query("SELECT name FROM `forum_vote` WHERE `topic_id` = '". abs(intval($_GET['id'])) ."' AND `type` = '1'")->fetchColumn().'" name="question"  /><br/>
'._t('reply_vote').':<br/>';

foreach($votes as $vote)
		{
		echo ($i + 1) .'. <input type="text" maxlength="300" value="'.(!empty($_POST['v_e_'.$vote['id'].'']) ? $_POST['v_e_'.$vote['id'].''] : $vote['name']).'" name="v_e_'.$vote['id'].'"  /><br/>';
		++$i;
		}

for ($i = $votes->rowCount() + 1; $i <= $input_vote; $i++) {
echo $i .'. <input type="text" maxlength="300" value="'.input($_POST['v_'.$i.'']).'" name="v_'.$i.'"  /><br/>';
}
echo '<input type="text" size="2" maxlength="2" value="'.$input_vote.'" name="more_answers" /> '._t('input_vote').' <input type="submit" name="more_answers2" value="»" /><br />
<input name="edit" type="submit" value="'. _t('edit') .'" /><br/></form></div>';
} else {
Template::div('block', _t('edit_vote_error'));
}
 
Template::div('block',  NAV .'<a href="/forum/topic/'.abs(intval($_GET['id'])).'/">'. _t('back') .'</a><br/>' . NAV .'<a href="/forum/">'. _t('forum') .'</a><br/>' . HICO .'<a href="/">'. _t('home').'</a>');
include_footer();
?>