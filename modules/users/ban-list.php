<?php
/**
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) 2013-2014, Taras Chornyi, Sergiy Mazurenko, Ivan Kotliar
 * @link          http://perf-engine.net
 * @package       PerfEngine
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
$locate = 'in_users';
$title = _t('ban_list');
include_header($title);
$tpl->div('title', _t('ban_list'));

$users_a = $db->query("SELECT * FROM `users` WHERE `ban_time` > '". time() ."'")->rowCount();
$pages = new Paginator($users_a, $ames);

if($users_a == 0) {

$tpl->div('menu', _t('not_users'));

} else {

$users_q = $db->query("SELECT * FROM `users` WHERE `ban_time` > '". time() ."' ORDER BY time DESC LIMIT $start, $ames");

while($users = $users_q->fetch()) {
$tpl->div('menu', nick($users['id'], '<b>'._t('end_ban').'</b>: '.rtime($users['ban_time']).'<br />'. (!empty($users['ban_text']) ? '<b>'._t('ban_text').'</b>: '.$users['ban_text'] : NULL)));

}
$pages->view();
}
$tpl->div('block', NAV .' <a href="/users">'._t('back').'</a><br/>'. HICO .'<a href="/">'. _t('home') .'</a>');
include_footer();
?>