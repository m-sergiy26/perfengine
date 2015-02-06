<nav id="sidebar">
    <ul>
        <?php
        $modules = scandir(MODULES);
        foreach($modules as $module)
        {
            if(App::isModule($module))
            {
                echo '<a href="/'.$module.'">'.Site::icon($module).' '._t(ucfirst($module), $module).' '.App::moduleCounter($module).'</a>';
            }
        }
        ?>
    </ul>
</nav>