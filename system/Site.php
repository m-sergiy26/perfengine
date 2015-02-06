<?php
class Site
{
    protected static $title;

    /**
     * @param string $title
     * @return string
     */
    public static function header($title = 'Yet another page')
    {
        $title = $title.' - '.App::config('system/data/config')['site_name'];
        self::$title = $title;
        if(user()->isUser())
        {
            App::db()->query("UPDATE `users` SET `time` = '".time()."' WHERE `id` = '".user()->getId()."'");
        }
        else
        {
            if(App::db()->query("SELECT * FROM `guests` WHERE `ip` = '".$_SERVER['REMOTE_ADDR']."' AND `browser` = '".App::browser()."' LIMIT 1")->rowCount() == 1)
            {
                App::db()->query("UPDATE `guests` SET `time` = '". time() ."' WHERE `ip` = '".$_SERVER['REMOTE_ADDR']."' AND `browser` = '".App::browser()."' LIMIT 1");
            }
            else
            {
                App::db()->query("INSERT INTO `guests` SET `ip` = '".$_SERVER['REMOTE_ADDR']."', `browser` = '".App::browser()."', `time` = '". time() ."'");
            }
        }


        if(is_file(ROOT.'/themes/mobile/'.App::getTheme().'/header.php'))
        {
            require_once(ROOT.'/themes/mobile/'.App::getTheme().'/header.php');
        }
        else
        {
            echo 'Theme not contain header!';
        }


        # user status
        if(user()->isUser() && user()->profile('ban_time') > time())
        {
            echo Site::div('title', _t('You are blocked'));
            echo Site::div('content', _t('Sorry, but you blocked due to violation of certain rules. Please wait for the expiration of blocking or contact the administration')
                .'<br/>'._t('Ban expires').': '.App::date(user()->profile('ban_time')).'<br/>'
                ._t('Reason').': '.user()->profile('ban_text'));
            Site::footer();
            exit;
        }

        # site status
        $_siteCfg = App::config('system/data/config');
        if($_siteCfg['site_status'] == 1 && !user()->isUser() && App::pageId() != 'register' && App::pageId() != 'login')
        {
            echo Site::div('title', _t('Site is closed'));
            echo Site::div('content', _t('Sorry, but the site is available only to authorized users'));
            Site::footer();
            exit;
        }
        elseif($_siteCfg['site_status'] == 2 && user()->level() < 4 && App::pageId() != 'register' && App::pageId() != 'login')
        {
            echo Site::div('title', _t('Site is closed'));
            echo Site::div('content', _t('Sorry, but the site is only available for administration'));
            Site::footer();
            exit;
        }

        # module status
        if(App::isModule())
        {
            $cfg = App::config('modules/'.App::moduleId().'/_module');
            if($cfg['status'] == 1)
            {
                echo Site::div('title', _t('Module is closed'));
                echo Site::div('content', _t('Sorry, but this section of the site temporarily closed'));
                echo Site::div('action_list', '<a href="/">'.Site::icon('arrow-left').' '._t('Back').'</a>');
                Site::footer();
                exit;
            }
            elseif($cfg['access'] == 1 && !user()->isUser())
            {
                echo Site::div('title', _t('Module is closed'));
                echo Site::div('content', _t('Sorry, but this section of the site is available only to authorized users'));
                echo Site::div('action_list', '<a href="/">'.Site::icon('arrow-left').' '._t('Back').'</a>');
                Site::footer();
                exit;
            }
            elseif($cfg['access'] == 2 && user()->level() < 4)
            {
                echo Site::div('title', _t('Module is closed'));
                echo Site::div('content', _t('Sorry, but this section of the site is available only to the administration'));
                echo Site::div('action_list', '<a href="/">'.Site::icon('arrow-left').' '._t('Back').'</a>');
                Site::footer();
                exit;
            }
        }
    }

    /**
     * @return string
     */
    public static function footer()
    {
        if(is_file(ROOT.'/themes/mobile/'.App::getTheme().'/footer.php'))
        {
            include(ROOT.'/themes/mobile/'.App::getTheme().'/footer.php');
        }
        else
        {
            echo 'Theme not contain footer!';
        }
    }

    public static function icon($icon, $ext = '.png')
    {
        if(is_file(ROOT.'/themes/mobile/'.App::getTheme().'/images/'.$icon.$ext))
            return '<img class="icon" src="/themes/mobile/'.App::getTheme().'/images/'.$icon.$ext.'" alt="'.$icon.$ext.'" />';
        else
            return '<img class="icon" src="/themes/_images/'.$icon.$ext.'" alt="'.$icon.$ext.'" />';
    }

    public static function breadcrumbs($title = '', $separator = ' / ')
    {

        $path = array_filter(explode('/', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)));
        $baseUrl = 'http://' . $_SERVER['HTTP_HOST'] . '/';
        $breadcrumbs = '<a href="'.$baseUrl.'">'._t('Homepage').'</a>';

        if(isset($path[2]))
            $breadcrumbs .= $separator.'<a href="/'.$path[1].'">'._t(ucfirst($path[1]), $path[1]).'</a>';
        $title = (!empty($title) ? $title : self::$title);
        $breadcrumbs .= $separator.$title;
        return $breadcrumbs;
    }

    public static function div($name, $content)
    {
        return '<div class="'.$name.'">'.$content.'</div>';
    }

    public static function counter($table, $params = null, $row = '*')
    {
        if(is_array($params))
        {
            $data = '';
            foreach($params as $key => $value)
            {
                if(end($params) == $value && prev($params) != $value)
                    $data .= "`".$key."` = '".$value."' ";
                else
                    $data .= "`".$key."` = '".$value."' AND ";
            }

            if(substr($data, -4) == 'AND ')
                $data = substr($data, 0, -4);
        }
//        print_r("SELECT ".$row." FROM `".$table."`".($params ? ' WHERE '.$data : '')."");
        return App::db()->query("SELECT ".$row." FROM `".$table."`".($params ? ' WHERE '.$data : '')."")->rowCount();
    }

    public static function notFound()
    {
        require_once(MODULES . DS . 'main/404.php');
        exit;
    }

    public static function perPage()
    {
        $user = new User;
        if(user()->isUser())
            return $user->config('items');
        else
            return App::config('system/data/config')['items'];
    }

    public static function textarea($content = '', $data = null)
    {
        if(!user()->isUser()) return false;

        ob_start();
        ob_implicit_flush(false);
        echo '[<a href="/info/tags">'._t('BB Tags').'</a> | <a href="/info/smiles">'._t('Smiles').'</a>]<br/>';
        if(App::browser_type() == 'desktop'):
            ?>
            <script src="/themes/_js/bbtags.js"></script>
            <button type="button" onclick="bbTag('b', '<?=$data['id']?>');"><?=self::icon('bb/bold')?></button>
            <button type="button" onclick="bbTag('i', '<?=$data['id']?>');"><?=self::icon('bb/italic')?></button>
            <button type="button" onclick="bbTag('u', '<?=$data['id']?>');"><?=self::icon('bb/underline')?></button>
            <button type="button" onclick="bbUrl('<?=$data['id']?>');"><?=self::icon('bb/link')?></button>
            <button type="button" onclick="bbTag('code', '<?=$data['id']?>');"><?=self::icon('bb/php')?></button>
            <br/>
        <?php
        endif;
        if(!isset($data['name']))
            $data['name'] = 'text';
        $params = '';
        foreach($data as $key=>$value)
        {
            $params .= ' '.$key.'="'.$value.'"';
        }
        echo '<textarea'.$params.'>'.(!empty($content) ? $content : null).'</textarea>';
        return ob_get_clean();
    }

    public static function output($text)
    {
        $output = new Output();
        return nl2br($output->smiles($output->bbtags(trim($text))));
    }

    public static function ext($ext)
    {
        if(preg_match('/gif|png|jpg|jpeg|bmp|ico|tiff/i', $ext))
        {
            return self::icon('picture');
        }
        elseif(preg_match('/mp3|mid|aac|midi|amr|wav|m4u/i', $ext))
        {
            return self::icon('music');
        }
        elseif(preg_match('/mp4|avi|3gp|wmv/i', $ext))
        {
            return self::icon('play');
        }
        elseif(preg_match('/apk|apt|ipa|jar|jad|sis|sisx/i', $ext))
        {
            return self::icon('app-mobile');
        }
        elseif(preg_match('/exe|deb|run/i', $ext))
        {
            return self::icon('app-desktop');
        }
        elseif(preg_match('/zip|rar|cab|tar|gz|bz|7z/i', $ext))
        {
            return self::icon('archive');
        }
        else
        {
            return self::icon('file');
        }
    }

    public static function countNewMessages()
    {
        $count = App::db()->query("SELECT * FROM `mail_dialogs` WHERE  `receiver_id` = '" . user()->getId() . "' AND `viewed` = '0'")->rowCount();
        if($count > 0)
        {
            return '<i class="digits_notify">'.$count.'</i>';
        }
    }

    public static function advLinks($type)
    {
        if(in_array($type, explode(';', 'top;bottom;counter')))
        {
            $links = App::db()->query("SELECT * FROM `adv` WHERE `type` = '".$type."'");
            if($links->rowCount() > 0)
            {
                ob_start();
                ob_implicit_flush(false);

                foreach($links as $link)
                {
                    if($link['html'] != '')
                        echo $link['html'];
                    elseif($link['image'] != '')
                        echo '<a href="'.$link['link'].'"><img src="'.$link['image'].'" alt="'.$link['name'].'" /></a>'."<br/>\n";
                    else
                        echo '<a href="'.$link['link'].'">'.$link['name'].'</a>'."\n";
                }
                return ob_get_clean();
            }
            else
                return false;
        }
        else
            return false;
    }


    public static function alerts()
    {
        if (isset($_SESSION['alert']))
        {
            echo '<div class="' . $_SESSION['alert']['type'] . '">' . nl2br($_SESSION['alert']['value']) . '</div>';
        }
        $_SESSION['alert'] = null;

        $notifications = App::db()->query("SELECT * FROM `notify` WHERE `user_id` = '". user()->getId() ."' AND `read` = '0'")->rowCount();
        if($notifications > 0)
        {
            echo '<div class="menu_list txt-center"><a href="/user/notify">'._t('Notification').' <i class="digits_notify">'.$notifications.'</i></a></div>';
        }
    }
}