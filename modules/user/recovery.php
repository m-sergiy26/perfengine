<?php
if(user()->isUser()) App::redirect('/');
$db = App::db();

if(isset($_POST['save_pass']) && $_GET['act']== 'change_pass' && isset($_GET['tmphash']) && isset($_GET['email']))
{
    $RecoveryUserData = $db->query("SELECT * FROM `users` WHERE `password` = '". App::filter('input', $_GET['tmphash']) ."' AND `email` = '". App::filter('input', $_GET['email']) ."'")->fetch();
    $pass1 = App::filter('input', $_POST['npass']);
    $pass = App::filter('input', $_POST['pass']);

    if(!empty($pass1) && !empty($pass) && $pass1 == $pass && App::filter('input', $_GET['tmphash']) == $RecoveryUserData['password']) 
    {
        $db->query("UPDATE `users` SET `password` = '". App::hash($pass)."' WHERE `email` = '". App::filter('input', $_GET['email'])."' ");
        // print_r($db->errorInfo());
        App::redirect('/');
    }
    else
    {
        $_SESSION['alert'] = ['type' => 'error', 'value' => _t('An error has occurred')];
        App::redirect('/user/recovery');
    }
}

$title = _t('Recover password');
Site::header($title);
echo Site::div('title', Site::breadcrumbs(_t('Recover password')));
if(!empty($_POST['nick']) && !empty($_POST['email']))
{
    $nick = App::filter('input', $_POST['nick']);
    $mail = App::filter('input', $_POST['email']);
    if($db->query("SELECT * FROM `users` WHERE `nick` = '". $nick ."' AND `email` = '". $mail ."'")->rowCount() == 1)
    {
        $RecoveryUserData = $db->query("SELECT * FROM `users` WHERE `nick` = '". $nick ."' AND `email` = '". $mail ."'")->fetch();

        $_libMail = new Mail('UTF-8');
        $_libMail->From('no-reply@'.$_SERVER['HTTP_HOST']);
        $_libMail->To($nick.';'.$mail);
        $_libMail->Subject(_t('Recover password')." - ".$_SERVER['HTTP_HOST']);
        $_libMail->Body(_t('Hello').", ".$nick."!\n".
            _t('You want recover the password on the site').' <a href="'.URL.'">'.$_SERVER['HTTP_HOST']."</a>\n"._t('To continue, click on the link next') ."<a href=\"".URL."/user/recovery?act=reset&tmphash=".$RecoveryUserData['password']."&email=".$mail."\">"._t('Continue')."</a>\n
"._t('If you did not request to recover your password, then please ignore this email and check your account')."\n"._t('Best regards Administration').' <a href="'.URL.'">'.App::config('system/data/config')['copyright'].'</a>', 'text/html');
        $_libMail->Priority(3);
        $_libMail->Send();
        echo '<div class="content">'._t('A letter with further instructions sent to your email address. Please check inbox or spam').'</div>';
        // print_r($_libMail->Get());
        Site::footer();
        exit;
    }
    else
    {
        $_SESSION['alert'] = ['type' => 'error', 'value' => _t('An error has occurred')];
        App::redirect('/user/recovery');
    }
}
elseif(isset($_GET['act']) && $_GET['act'] == 'reset' && isset($_GET['tmphash']) && isset($_GET['email']))
{
    if($db->query("SELECT * FROM `users` WHERE `password` = '". App::filter('input', $_GET['tmphash']) ."' AND `email` = '". App::filter('input', $_GET['email']) ."'")->rowCount() == 1)
    {
        echo '<div class="content">
        <form action="?act=change_pass&tmphash='.App::filter('input', $_GET['tmphash']).'&amp;email='.App::filter('input', $_GET['email']).'" method="post">

				<b>'. _t('New password') .'</b><br/>
				<input type="text" name="npass"/><br/>
				<b>'. _t('Confirm password') .'</b>:<br/>
				<input type="text" name="pass"/><br/>
				<input type="submit" name="save_pass" value="'. _t('Save') .'" /><br/>
				</form>
				</div>';
        Site::footer();
        exit;
    }
    else
    {
        $_SESSION['alert'] = ['type' => 'error', 'value' => _t('An error has occurred')];
        App::redirect('/user/recovery');
    }
}

echo '<div class="content">
<form action="/user/recovery?" method="post">
		'. _t('Nickname') .':<br/>
		<input type="text" name="nick" /><br/>
		E-mail:<br/>
		<input type="text" name="email" /><br/>
		<input type="submit" value="Ok!" />
	</form>
	</div>';
Site::footer();