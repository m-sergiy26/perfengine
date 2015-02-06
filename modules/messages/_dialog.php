<?php
$id = App::filter('int', $_POST['id']);
$start = App::filter('int', $_POST['start']);
$messages = App::db()->query("SELECT * FROM `mail_dialogs` WHERE (`sender_id` = '" . $id . "' AND `receiver_id` = '" . user()->getId() . "') OR (`sender_id` = '" . user()->getId() . "' AND `receiver_id` = '" . $id . "') ORDER BY time DESC LIMIT " . $start . ", " . Site::perPage() . "");
foreach ($messages as $message)
{
    echo '<div class="content'.($message['viewed'] == 0 ? ' unread' : '').'">
        ' . user()->nick($message['sender_id'], App::date($message['time'])) . '<br/>
        ' . Site::output($message['text']);
    if($message['viewed'] == 0 && $message['receiver_id'] == user()->getId())
        App::db()->query("UPDATE `mail_dialogs` SET `viewed` = '1' WHERE `id` = '".$message['id']."'");

    echo '</div>';
}