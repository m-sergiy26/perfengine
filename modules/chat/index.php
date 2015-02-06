<?php
if(isset($_GET['write']))
{
    $text = substr(App::filter('input', $_POST['text']), 0, 5000);
    if(!empty($text) && App::antiflood('chat', 'text', $text) == false)
    {
        $postIns = App::db()->prepare("INSERT INTO `chat` SET `text` = ?, `time` = ?, `user_id` = ?");
        $postIns->execute([$text, time(), user()->getId()]);
        // print_r($db->errorInfo());
        App::redirect('/chat?'.rand(1, 9999));
    }
}
elseif(isset($_GET['clear']) && user()->level() >= 4)
{
    if(isset($_POST['yes']))
    {
        App::db()->query("TRUNCATE TABLE `chat`");
        App::redirect('/chat?'.rand(1, 9999));
    }
    elseif(isset($_POST['no']))
        App::redirect('/chat?'.rand(1, 9999));

    Site::header(_t('Chat', 'chat'));
    echo Site::div('title', Site::breadcrumbs(_t('Chat', 'chat')));
    echo '<div class="content">
    <form action="/chat?clear" method="post">
    '._t('Are you sure you want to clear the chat?', 'chat').'<br/>
    <input type="submit" name="yes" value="'._t('Yes').'" /> <input type="submit" name="no" value="'._t('No').'" />
     </form>
     </div>';
    Site::footer();
    exit;
}
elseif(isset($_GET['delete'])  && user()->level() >= 3)
{
    $pid = App::filter('int', $_GET['delete']);
    App::db()->query('DELETE FROM `chat` WHERE `id` = "'.$pid.'" LIMIT 1');
    App::redirect('/chat?'.rand(1, 9999));
}

Site::header(_t('Chat', 'chat'));
echo Site::div('title', Site::breadcrumbs(_t('Chat', 'chat')));

if(user()->isUser())
{
    echo '<div class="content">
		<form id="reply" action="/chat?write" method="post">'
        .Site::textarea((isset($_GET['reply_to']) ? '[b]'.user()->getNick(App::filter('int', $_GET['reply_to'])).'[/b], ' : NULL), ['rows' => 6, 'placeholder' => _t('Message'), 'id'=>'message']);
    echo '<br/>
		<input name="create" type="submit" value="'. _t('Add') .'" />
		</form></div>';
}

$chat_r = App::db()->query("SELECT * FROM `chat`")->rowCount();
$pages = new Pager($chat_r, Site::perPage());

if($chat_r == 0)
{
    echo Site::div('content', _t('No messages'));
}
else
{
    $pages = new Pager($chat_r, Site::perPage());
    $posts = App::db()->query("SELECT * FROM `chat` ORDER BY time DESC LIMIT ".$pages->start().", ".Site::perPage()."");
    foreach($posts as $post)
    {
        echo '<div class="content">
            '.(user()->level() >= 4 ? '<span class="float-rt">[<a href="/chat?delete='.$post['id'].'">x</a>]</span>' : null).'
		'.(user()->getId() != $post['user_id'] ? '<span class="float-rt">[<a href="/chat?reply_to='.$post['user_id'].'">'._t('Reply').'</a>]</span>' : null).'
            '.user()->nick($post['user_id'], App::date($post['time'])).'<br/>
            '.Site::output($post['text']).'
        </div>';
    }
    $pages->view();
}
if(user()->level() > 3)
    echo Site::div('action_list', '<a href="/chat?clear">'.Site::icon('arrow-right').' '._t('Clear chat', 'chat').'</a>');

Site::footer();