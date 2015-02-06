<?php
if(user()->isUser())
    App::redirect('/user/index');

if(App::config('system/data/config')['registration_status'] != 0)
{
    $_SESSION['alert'] = ['type' => 'error', 'value' => _t('Registration temporary closed')];
    App::redirect('/user/index');
}


if(isset($_GET['do']))
{
    $email = App::filter('input', $_POST['email']);
    $nick = App::filter('input', $_POST['nick']);
    $password = App::filter('input', $_POST['password']);
    $rpassword = App::filter('input', $_POST['rpassword']);
    $gender = App::filter('int', $_POST['gender']) == 2 ? 2 : 1;

    $error = '';
    if(empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL) || App::db()->query("SELECT * FROM `users` WHERE `email` = '".$email."'")->rowCount() > 0)
    {
        $error .= _t('Email wrong or user with this email already exists').'<br/>';
    }

   /* if(empty($nick) || App::db()->query("SELECT * FROM `users` WHERE `nick` = '".$nick."'")->rowCount() > 0)
    {
        $error .= _t('Nickname is empty or user with this nickname already exists').'<br/>';
    }*/

    if(strlen($password) < 6 || $rpassword != $password)
    {
        $error .= _t('Password is very short or passwords don\'t match').'<br/>';
    }

    if($_POST['captcha'] !== $_SESSION['captcha'])
    {
        $error .= _t('Text from image wrong');
    }
    $_SESSION['alert'] = ['type' => 'error', 'value' => $error];

    if($error == '')
    {
        $db = App::db();

        $profile = $db->prepare("INSERT INTO `users` (`email`, `nick`, `password`, `level`, `reg_time`, `time`, `gender`) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $profile->execute([$email, $nick, App::hash($password), 1, time(), time(), $gender]);

        $lastId = $db->lastInsertId();

        $settings = $db->prepare("INSERT INTO `settings` (user_id, language, items, theme_mobile, theme_web, fast_form, show_profile, allow_messages, timezone) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $settings->execute([$lastId, Language::getLanguage(), 20, App::config('system/data/config')['theme_mobile'], App::config('system/data/config')['theme_mobile'], 1, 0, 1, App::timezone()]);

        App::redirect('/user/login?auth&email='.$email.'&password='.$password);
    }
}
Site::header(_t('Register'));
?>
<div class="title"><?=_t('Register');?></div>
<div class="content">
    <form action="/user/register?do" method="post">
        <input type="text" name="email" placeholder="Email"/><br/>
        <input type="text" name="nick" placeholder="<?=_t('Nickname')?>" /><br/>
        <?=_t('Gender')?>:
        <select name="gender">
            <option value="1"><?=_t('Male')?></option>
            <option value="2"><?=_t('Female')?></option>
        </select><br/>
        <input type="text" name="password" placeholder="<?=_t('Password')?>" />
        <input type="text" name="rpassword" placeholder="<?=_t('Repeat password')?>" /><br/>
        <input type="text" name="captcha" placeholder="<?=_t('Enter text from image')?>" /><br/>
        <img style="padding-top: 2px" src="/main/captcha.gif" alt="Image" /><br/>
        <input type="submit" value="<?=_t('Register')?>" />
    </form>
</div>
<?php
Site::footer();