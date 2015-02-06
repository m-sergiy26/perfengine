<?php
if(!user()->isUser())
{
    App::redirect('/user/login');
}
if(!isset($_GET['id']) || App::filter('int', $_GET['id']) == user()->getId() || App::db()->query("SELECT * FROM `users` WHERE `id` = '".App::filter('int', $_GET['id'])."'")->rowCount() == 0)
    Site::notFound();

$id = App::filter('int', $_GET['id']);


if(App::db()->query("SELECT * FROM `mail` WHERE (`sender_id` = '".$id."' AND `receiver_id` = '".user()->getId()."') OR (`sender_id` = '".user()->getId()."' AND `receiver_id` = '".$id."')")->rowCount() == 0)
    App::db()->query("INSERT INTO `mail` SET `sender_id` = '".user()->getId()."', `receiver_id` = '".$id."'");

if(user()->config('allow_messages', $id) == 0)
{
    $_SESSION['alert'] = ['type' => 'error', 'value' => _t('User disallow private messages')];
    App::redirect('/user/profile/'.$id);
}


Site::header(_t('Dialog with').' '.user()->getNick($id));
echo Site::div('title', Site::breadcrumbs(_t('Dialog with').' '.user()->getNick($id)));
echo '<div class="content">
		<form id="reply" action="/messages/write/'.$id.'?send" method="post">'
    .Site::textarea('', ['rows' => 6, 'placeholder' => _t('Message'), 'id'=>'message']);
echo '<br/>
		<button type="submit">'. _t('Send') .'</button>
		</form></div>';
$messages_r = App::db()->query("SELECT * FROM `mail_dialogs` WHERE (`sender_id` = '".$id."' AND `receiver_id` = '".user()->getId()."') OR (`sender_id` = '".user()->getId()."' AND `receiver_id` = '".$id."')")->rowCount();
if($messages_r == 0)
{
    echo Site::div('content', _t('No messages'));
}
else
{
    $pages = new Pager($messages_r, Site::perPage());
    ?>
<script>
    $(document).ready(function() {
        setInterval(function() {
            $("#dialog").load("/messages/_dialog", {id: <?=$id?>, start: <?=$pages->start()?>});
        }, 2000);
    });
</script>
<?php
    echo '<div id="dialog">';
    $messages = App::db()->query("SELECT * FROM `mail_dialogs` WHERE (`sender_id` = '" . $id . "' AND `receiver_id` = '" . user()->getId() . "') OR (`sender_id` = '" . user()->getId() . "' AND `receiver_id` = '" . $id . "') ORDER BY time DESC LIMIT " . $pages->start() . ", " . Site::perPage() . "");
    foreach ($messages as $message)
    {
        echo '<div class="content'.($message['viewed'] == 0 ? ' unread' : '').'">
        ' . user()->nick($message['sender_id'], App::date($message['time'])) . '<br/>
        ' . Site::output($message['text']);
        if($message['viewed'] == 0 && $message['receiver_id'] == user()->getId())
            App::db()->query("UPDATE `mail_dialogs` SET `viewed` = '1' WHERE `id` = '".$message['id']."'");

        echo '</div>';
    }
    echo '</div>';
    $pages->view();
}
Site::footer();