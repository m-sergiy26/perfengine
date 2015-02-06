<?php
if(!user()->isUser())
    Site::notFound();
if(isset($_GET['save']))
{
    if($_FILES['avatar']['tmp_name'])
    {
        @unlink(ROOT.DS.'files/avatars/'.user()->getId().'.jpg');
        @unlink(ROOT.DS.'files/avatars/'.user()->getId().'_mini.jpg');

        $file_info = pathinfo($_FILES['avatar']['name']);
        $file_info['extension'] = strtolower($file_info['extension']);
        if(!in_array($file_info['extension'], explode(';', 'png;jpg;jpeg;gif')))
        {
            $_SESSION['alert'] = ['type' => 'error', 'value' => 'Disallowed file extension'];
            App::redirect('/user/avatar');
        }

        move_uploaded_file($_FILES['avatar']['tmp_name'], ROOT.'/tmp/'.$file_info['filename'].'.'.$file_info['extension']);

        $avatar = new Jimage();
        $avatar->thumb(ROOT.'/tmp/'.$file_info['filename'].'.'.$file_info['extension'], ROOT.'/files/avatars/'.user()->getId().'.jpg', 130, 165);
        $avatar->thumb(ROOT.'/tmp/'.$file_info['filename'].'.'.$file_info['extension'], ROOT.'/files/avatars/'.user()->getId().'_mini.jpg', 40, 40);

        unlink(ROOT.'/tmp/'.$file_info['filename'].'.'.$file_info['extension']);
    }
    App::redirect('/user/avatar');
}
elseif(isset($_GET['delete']))
{
    unlink(ROOT.DS.'files/avatars/'.user()->getId().'.jpg');
    unlink(ROOT.DS.'files/avatars/'.user()->getId().'_mini.jpg');
    App::redirect('/user/avatar');
}
Site::header(_t('User avatar'));
echo Site::div('title', Site::breadcrumbs(_t('User avatar')));
echo '<div class="content">
'.User::avatar(user()->getId()).'<br/>
<b>'._t('Choose image').':</b><br/>
<form action="/user/avatar?save" method="post" enctype="multipart/form-data">
    <input type="file" name="avatar" /><br/>
    <input type="submit" value="'._t('Save').'" />
    '.(User::avatar(user()->getId()) ? '<a href="/user/avatar?delete" class="button">'._t('Delete').'</a>' : '').'
</form>
</div>';
