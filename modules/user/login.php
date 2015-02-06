<?php
if(user()->isUser())
    App::redirect('/user/index');

if(isset($_GET['auth'], $_REQUEST['email'], $_REQUEST['password']))
{
    $user = new User;
    if($user->login($_REQUEST['email'], $_REQUEST['password'], (isset($_REQUEST['remember']) ? 'cookie' : 'session')) !== false)
    {
        App::redirect('/');
    }
    else
    {
//        var_dump();
        $_SESSION['alert'] = ['type' => 'error', 'value' => _t('E-mail or password is incorrect')];
    }
}
Site::header(_t('Login'));
?>
<div class="title"><?=_t('Login')?></div>
<div class="content">
    <form action="/user/login?auth" method="post">
        <input type="text" name="email" placeholder="Email" /><br/>
        <input type="password" name="password" placeholder="<?=_t('Password')?>" /><br/>
        <input type="submit" value="<?=_t('Login')?>" /> <input type="checkbox" name="remember" checked="checked" /> <?=_t('Remember me')?>
    </form>
</div>
<?=Site::div('action_list', '<a href="/user/recovery">'.Site::icon('arrow-left').' '._t('Recover password').'</a>
<a href="/user/register">'.Site::icon('arrow-left').' '._t('Register').'</a>');
Site::footer();