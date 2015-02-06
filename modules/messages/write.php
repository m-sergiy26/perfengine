<?php
if(!user()->isUser() || !isset($_GET['id']) || App::filter('int', $_GET['id']) == user()->getId() ||  App::db()->query("SELECT * FROM `users` WHERE `id` = '".App::filter('int', $_GET['id'])."'")->rowCount() == 0)
    Site::notFound();
$id = App::filter('int', $_GET['id']);
$dialog_id = App::db()->query("SELECT id FROM `mail` WHERE (`sender_id` = '".$id."' AND `receiver_id` = '".user()->getId()."') OR (`sender_id` = '".user()->getId()."' AND `receiver_id` = '".$id."')")->fetchColumn();
if(isset($_GET['send']) && isset($_POST['text']))
{
    $text = App::filter('input', $_POST['text']);
    if(!empty($text))
    {
        $db = App::db();
        $time = time();
        $message = $db->prepare("INSERT INTO `mail_dialogs` SET `text` = ?, `sender_id` = ?, `receiver_id` = ?, `time` = ?, `viewed` = ?, `dialog_id` = ?");
        $message->execute([$text, user()->getId(), $id, $time, 0, $dialog_id]);

        $chat = $db->prepare("UPDATE `mail` SET `time_last_message` = ?, `last_message` = ? WHERE `id` = ?");
        $chat->execute([$time, $text, $dialog_id]);
    }
}
else
    echo 'Error';
App::redirect('/messages/dialog/'.$id);