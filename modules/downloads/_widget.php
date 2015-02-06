<?php

$files = App::db()->query("SELECT * FROM `downloads_files` ORDER BY time DESC LIMIT 5");
if($files->rowCount() == 0)
{
    echo Site::div('content', _t('No files', 'downloads'));
}
else
{
    echo '<div class="menu_list">';
    foreach($files as $file)
    {
        echo '<a href="/downloads/file/'. $file['id'] .'">'.Site::ext($file['ext']).' '. $file['name'] .' ('.$file['ext'].') ['.App::fileSize($file['size']).']<br/>
		 '. ($file['description'] != '' ? mb_substr($file['description'], 0, 100).'...' : '').'</a>';
    }
    echo '</div>';
}
