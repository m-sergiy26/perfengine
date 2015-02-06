<?php
$db = App::db();
if(user()->level() < 5)
{
    App::redirect('/');
}
$id = @App::filter('int', $_GET['id']);
#############____ Add folder ____#############
    if(isset($_GET['act']) && $_GET['act'] == 'add_dir')
    {
        if(isset($_POST['send']))
        {
            $name = App::filter('input', $_POST['name']);
            if(!empty($name))
            {
                $db->query("INSERT INTO `smiles` SET `name` = '$name', `type` = '0'");
                App::redirect('/admin/smiles/');
            }
        }

        $title = _t('Add folder', 'admin') .' - '._t('Manage smiles', 'admin');
        Site::header($title);
        echo Site::div('title', Site::breadcrumbs($title));
        echo '<div class="content">
		<form action="/admin/smiles/?act=add_dir" method="post">
		<input placeholder="'._t('Title').'" type="text" name="name" /><br/>
		<input name="send" type="submit" value="'. _t('Add') .'" />
		</form>
	</div>';
        echo Site::div('action_list', '<a href="/admin/smiles/">'.Site::icon('arrow-left').' '. _t('Back') .'</a>');
        Site::footer();
        exit;
    }

#############____ Категории ____#############
    if(isset($_GET['id']) && $db->query("SELECT * FROM `smiles` WHERE `id` = '".$id."' AND `type` = '0'")->rowCount() != 0)
    {
        $title = _t('Manage smiles', 'admin');
        Site::header($title);
        echo Site::div('title', $db->query("SELECT name FROM `smiles` WHERE `id` = '".$id."'")->fetchColumn() .' - '. _t('Manage smiles', 'admin'));
        $smiles_cat = $db->query("SELECT * FROM `smiles` WHERE `cat` = '".$id."' AND `type` = '1'")->rowCount();
        $pages = new Pager($smiles_cat, Site::perPage());
        if($smiles_cat == 0)
        {
            echo Site::div('content', _t('No smiles'));
        }
        else
        {
            $categories = $db->query("SELECT * FROM `smiles` WHERE `cat` = '".$id."' AND `type` = '1' ORDER BY id ASC LIMIT ". $pages->start().", ".Site::perPage()."");
            foreach($categories as $category)
            {
                echo '<div class="content">
			<img src="/files/smiles/'.$category['id'].'.'.$category['ext'].'" alt="." /> '.$category['smile'].'<br />
			[<a href="/admin/smiles/?smile='.$category['id'].'">'. _t('edit') .'</a>]
			</div>';
            }
            $pages->view();
        }
        echo Site::div('action_list', '<a href="/admin/smiles/?add_smile='.$id.'">'.Site::icon('add').' '. _t('Add') .'</a>'. '<a href="/admin/smiles/">'.Site::icon('arrow-left').' '. _t('Back') .'</a>');
        Site::footer();
        exit;
    }

#############____ Удаление категории ____#############
    if(isset($_GET['delete']) && $db->query("SELECT * FROM `smiles` WHERE `id` = '".App::filter('int', $_GET['delete'])."' AND `type` = '0'")->rowCount() != 0)
    {
        if(isset($_POST['delete'])) {
            $db->query("DELETE FROM `smiles` WHERE `id` = '".App::filter('int', $_GET['delete'])."'");
            $db->query("DELETE FROM `smiles` WHERE `cat` = '".App::filter('int', $_GET['delete'])."'");
            App::redirect('/admin/smiles/');
        }

        $title = _t('Manage smiles', 'admin');
        Site::header($title);
        echo Site::div('title',  _t('Manage smiles', 'admin'));
        echo '<div class="content">
<form action="/admin/smiles/?delete='.App::filter('int', $_GET['delete']).'" method="post">
'._t('delete').' <b>'.$db->query("SELECT name FROM `smiles` WHERE `id` = '".App::filter('int', $_GET['delete'])."'")->fetchColumn().'</b> ?<br />
<input name="delete" type="submit" value="'. _t('Delete') .'" />
</form>

</div>';

        echo Site::div('action_list',  '<a href="/admin/smiles/">'.Site::icon('arrow-left').' '. _t('Back') .'</a>');
        Site::footer();
        exit;
    }

#############____ Редактирование категории ____#############
    if(isset($_GET['edit']) && $db->query("SELECT * FROM `smiles` WHERE `id` = '".App::filter('int', $_GET['edit'])."' AND `type` = '0'")->rowCount() != 0)
    {
        if(isset($_POST['edit'])) {
            $name = App::filter('input', $_POST['name']);
            if(!empty($name))
            {
                $db->query("UPDATE `smiles` SET `name` = '$name' WHERE `id` = '".App::filter('int', $_GET['edit'])."'");
                App::redirect('/admin/smiles/');
                exit;
            }
            App::redirect('/admin/smiles/');
        }

        $title = _t('Manage smiles', 'admin');
        Site::header($title);
        echo Site::div('title',  _t('Manage smiles', 'admin'));
        echo '<div class="content">
<form action="/admin/smiles/?edit='.App::filter('int', $_GET['edit']).'" method="post">
'._t('name').':<br/>
<input type="text" name="name" value="'.$db->query("SELECT name FROM `smiles` WHERE `id` = '".App::filter('int', $_GET['edit'])."'")->fetchColumn().'" /><br/>
<input name="edit" type="submit" value="'. _t('edit') .'" />
</form>
</div>';
        echo Site::div('action_list',  '<a href="/admin/smiles/">'.Site::icon('arrow-left').' '. _t('Back') .'</a>');
        Site::footer();
        exit;
    }

#############____ Изменения смайла ____#############
    if(isset($_GET['smile']) && $db->query("SELECT * FROM `smiles` WHERE `id` = '".App::filter('int', $_GET['smile'])."' AND `type` = '1'")->rowCount() != 0)
    {
        $smile = $db->query("SELECT * FROM `smiles` WHERE `id` = '".App::filter('int', $_GET['smile'])."'")->fetch();

        if(isset($_POST['edit']))
        {
            $smile_nam = App::filter('input', $_POST['smile']);
            if(!empty($smile_nam))
            {
                $db->query("UPDATE `smiles` SET  `smile` = '". $smile_nam."' WHERE `id` = '".$smile['id'] ."'");

                App::redirect('/admin/smiles/'.$smile['cat'].'');
            }
        }

        if(isset($_POST['delete']))
        {
            $db->query("DELETE FROM `smiles` WHERE `id` = '".$smile['id'] ."'");
            unlink(ROOT.'/files/smiles/'.$smile['id'].'.'.$smile['ext']);
            App::redirect('/admin/smiles/'.$smile['cat'].'');
        }

        $title = _t('Manage smiles', 'admin');
        Site::header($title);
        echo Site::div('title',  _t('Manage smiles', 'admin'));

        echo '<div class="content">
<form action="/admin/smiles/?smile='.App::filter('int', $_GET['smile']).'" method="post">
<img src="/files/smiles/'.$smile['id'].'.'.$smile['ext'].'" alt="." /><br />
'._t('mark').': <input type="text" size="2" name="smile" value = "'.$smile['smile'].'" /><br/>
<input name="edit" type="submit" value="'. _t('edit') .'" />
<input name="delete" type="submit" value="'. _t('delete') .'" />
</form>

</div>';
        echo Site::div('action_list', '<a href="/admin/smiles/'.$smile['cat'].'">'.Site::icon('arrow-left').' '. _t('Back') .'</a> 
        <a href="/admin/smiles/">'.Site::icon('arrow-left').' '. _t('Manage smiles', 'admin') .'</a>');
        Site::footer();
        exit;
    }


#############____ Добавление смайла ____#############
    if(isset($_GET['add_smile']) && isset($_GET['add_smile']) && $db->query("SELECT * FROM `smiles` WHERE `id` = '".App::filter('int', $_GET['add_smile'])."' AND `type` = '0'")->rowCount() != 0)
    {

        if(isset($_POST['add'])) {
            $smile = App::filter('input', $_POST['smile']);
            if($_FILES['file']['tmp_name'] && !empty($smile))
            {

                $file_info = pathinfo($_FILES['file']['name']);
                $file_info['extension'] = strtolower($file_info['extension']);
                $db->query("INSERT INTO `smiles` SET `smile` = '".$smile."', `ext` = '".$file_info['extension']."', `cat` = '".App::filter('int', $_GET['add_smile'])."', `type` = '1'");
                // print_r($db->errorInfo());
                $servname = $db->lastInsertId().'.'.$file_info['extension'];

                move_uploaded_file($_FILES['file']['tmp_name'], ROOT.'/files/smiles/'.$servname);

                $sm = [];
                $smiles = App::db()->query("SELECT * FROM `smiles` WHERE `type` = '1'");
                foreach($smiles as $smile)
                {
                    $sm[$smile['smile']] = '<img src="/files/smiles/'.$smile['id'].'.'.$smile['ext'].'" alt="'.$smile['smile'].'" />';
                }
                App::writeConfig('system/data/smiles', $sm);

                App::redirect('/admin/smiles/'.App::filter('int', $_GET['add_smile']).'');
            }

        }
        $title = _t('add_smile');
        Site::header($title);
        echo Site::div('title',  _t('add_smile'));

        echo '<div class="content">
<form action="/admin/smiles/?add_smile='.App::filter('int', $_GET['add_smile']).'" method="post" enctype="multipart/form-data">	
<b>'. _t('image_smile') .'</b>:<br/>
<input name="file" type="file"  accept="image/*"/><br/>	
'._t('mark').': <input type="text" size="2" name="smile" /><br/>
<input name="add" type="submit" value="'. _t('add') .'" />
</form>
</div>';

        echo Site::div('action_list', '<a href="/admin/smiles/'.App::filter('int', $_GET['add_smile']).'">'.Site::icon('arrow-left').' '. _t('Back') .'</a> 
        <a href="/admin/smiles/">'.Site::icon('arrow-left').' '. _t('Manage smiles', 'admin') .'</a>');
        Site::footer();
        exit;
    }

#############____ Главная страница подарков ____#############
    $title = _t('Manage smiles', 'admin');
    Site::header($title);
    echo Site::div('title',  _t('Manage smiles', 'admin'));
    $smiles_cat = $db->query("SELECT * FROM `smiles` WHERE `type` = '0'")->rowCount();
    $pages = new Pager($smiles_cat, Site::perPage());
    if($smiles_cat == 0)
    {
        echo Site::div('menu', _t('dl_dir_empty'));
    }
    else
    {
        $categories = $db->query("SELECT * FROM `smiles` WHERE `type` = '0' ORDER BY id ASC LIMIT ".$pages->start().", ".Site::perPage()."");
        foreach($categories as $category)
        {
            echo Site::div('content', '<a href="/admin/smiles/'. $category['id'] .'">'.Site::icon('folder').' '. _t($category['name']) .'</a> ('.$db->query("SELECT * FROM `smiles` WHERE  `cat` = '". $category['id'] ."' AND `type` = '1'")->rowCount().') <span style="float: right;">[<a href="/admin/smiles/?delete='. $category['id'] .'">x</a> | <a href="/admin/smiles/?edit='. $category['id'] .'">+</a>]</span>');
        }
        $pages->view();

    }
    echo Site::div('action_list', '<a href="/admin/smiles/?act=add_dir">'.Site::icon('add').' '. _t('Add folder') .'</a>');
    Site::footer();