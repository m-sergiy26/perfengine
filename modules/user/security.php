<?php
if(!user()->isUser())
    App::redirect('/user/login');
$db = App::db();
if(isset($_GET['change_mail']))
{
    $email = App::filter('email', $_POST['nemail']);

    if(!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL) != false && App::hash($_POST['pass']) == user()->profile('password') && $_POST['cemail'] == user()->profile('email'))
    {
        if($db->query("SELECT * FROM `users` WHERE `email` = '$email'")->rowCount() == 0)
        {
            $upd = $db->prepare("UPDATE `users` SET `email` = ? WHERE `id` = ?");
            $upd->execute([$email, user()->getId()]);
            $_SESSION['alert'] = ['type' => 'notify', 'value' => _t('Email successfully changed')];
            App::redirect('/user/security');
        }
    }
    else
    {
        $_SESSION['alert'] = ['type' => 'error', 'value' => _t('An error has occurred')];
        App::redirect('/user/security');
    }

}

if(isset($_GET['change_pass']))
{
    $pass1 = App::filter('input', $_POST['npass']);
    $pass = App::filter('input', $_POST['pass']);

    if(!empty($pass1) && !empty($pass) && $pass1 == $pass && mb_strlen($pass) > 6 && App::hash(App::filter('input', $_POST['cpass'])) == user()->profile('password'))
    {
        $upd = $db->prepare("UPDATE `users` SET `password` = ? WHERE `id` = ? ");
        $upd->execute([App::hash($pass), user()->getId()]);
        unset($_COOKIE);
        $_SESSION['password'] = App::hash($pass);
        setcookie('password', App::hash($pass), time()+60*60*24*1024, '/');
        $_SESSION['alert'] = ['type' => 'notify', 'value' => _t('Password successfully changed')];
        App::redirect('/');
    }
    else
    {
        $_SESSION['alert'] = ['type' => 'error', 'value' => _t('An error has occurred')];
        App::redirect('/user/security');
    }
}

    $title = _t('Security');
    Site::header($title);
    echo Site::div('title',  _t('Change email'));
    echo '<form action="?change_mail" method="post">
		<div class="content">
		<b>'. _t('Current email') .'</b>:<br/>
		<input type="text" name="cemail" /><br/>
		<b>'. _t('New email') .':</b><br/>
		<input type="text" name="nemail"/><br/>
		<b>'. _t('Current password') .'</b>:<br/>
		<input type="password" name="pass"/><br/>
		<input type="submit" name="save_email" value="'. _t('Save') .'" /><br/>
		</div>
		</form>';
    echo Site::div('title',  _t('Change password'));
    echo '<form action="?change_pass" method="post">
		<div class="content">
		<b>'. _t('Current password') .'</b>:<br/>
		<input type="password" name="cpass" /><br/>
		<b>'. _t('New password') .'</b><br/>
		<input type="text" name="npass"/><br/>
		<b>'. _t('Confirm password') .'</b>:<br/>
		<input type="text" name="pass"/><br/>
		<input type="submit" name="save_pass" value="'. _t('Save') .'" /><br/>
		</div>
		</form>';
Site::footer();