<?php

class User
{
    protected $id;

    protected $level;

    protected $nick;

    /*
     * @var int user id
     */
    public function __construct()
    {
        $this->authorize();
    }

    private function authorize()
    {
        // authorization by cookies
        if(isset($_COOKIE['authorized']))
        {
            // decode cookie data
            $auth = explode('::', base64_decode($_COOKIE['authorized']));
            // search user in database
            if(App::db()->query("SELECT * FROM `users` WHERE `id` = '". App::filter('int', $auth[0]) ."' AND `password` = '". App::filter('input', $auth[1]) ."'")->rowCount() == 1)
            {
                $user = App::db()->query("SELECT * FROM `users` WHERE `id` = '". App::filter('int', $auth[0]) ."' AND `password` = '". App::filter('input', $auth[1]) ."'")->fetch();
                $this->setId($user['id']);
                $this->setNick($user['nick']);
                $this->setLevel($user['level']);

            }
        }
        // authorization by php session
        elseif(isset($_SESSION['user_id'], $_SESSION['password']))
        {
//            var_dump();
            // search user in database
            if(App::db()->query("SELECT COUNT(*) FROM `users` WHERE `id` = '". App::filter('int', $_SESSION['user_id']) ."' AND `password` = '". App::filter('int', $_SESSION['password']) ."'")->fetchColumn() == 1)
            {
                $user = App::db()->query("SELECT * FROM `users` WHERE `id` = '". App::filter('int', $_SESSION['user_id']) ."' AND `password` = '". App::filter('int', $_SESSION['password']) ."'")->fetch();
                $this->setId($user['id']);
                $this->setNick($user['nick']);
                $this->setLevel($user['level']);
            }
        }
    }

    protected function setId($id)
    {
       $this->id = $id;
    }

    protected function setNick($nick)
    {
       $this->nick = $nick;
    }

    protected function setLevel($level)
    {
       $this->level = $level;
    }

    /**
     * User login
     * @param $email
     * @param $password
     * @param string $type
     * @return bool
     */
    public function login($email, $password, $type = 'cookie')
    {
        $email = App::filter('email', $email);
        $password = App::hash(App::filter('input', $password));
        $db = App::db();

//        var_dump("SELECT * FROM `users` WHERE `email` = '" . $email . "' AND `password` = '" . $password . "'");

        if ($db->query("SELECT * FROM `users` WHERE `email` = '" . $email . "' AND `password` = '" . $password . "'")->rowCount() == 1)
        {
            $id = $db->query("SELECT id FROM `users` WHERE `email` = '" . $email . "' AND `password` = '" . $password . "'")->fetchColumn();

            if ($type == 'cookie')
            {
                setcookie('authorized', base64_encode($id . '::' . $password), time() + 1209600, '/');
            }
            else
            {
                $_SESSION['user_id'] = $id;
                $_SESSION['password'] = $password;
            }
//            var_dump($_SESSION);
        }
        else
        {
            return false;
        }
    }

    /**
     * Close session
     */
    public function logout()
    {
        setcookie('authorized', '', (time()-3600), '/');
        $_SESSION['user_id'] = false;
        $_SESSION['password'] = false;
        session_destroy();
    }

    /**
     * check if user
     * @return bool
     */
    public function isUser()
    {
        if($this->id)
            return true;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getNick($id = null)
    {
        if($id)
            return $this->profile('nick', $id);
        else
            return $this->nick;
    }

    /**
     * @return int
     */
    public function level($id = null)
    {
        if($id)
            return $this->profile('level', $id);
        else
            return $this->level;
    }

    /**
     * @param string $item
     * @return string
     */
    public function config($item, $user_id = null)
    {
        if($user_id)
            $uid = $user_id;
        else
            $uid = $this->id;
        return App::db()->query("SELECT ".$item." FROM `settings` WHERE `user_id` = '".$uid."'")->fetchColumn();
    }

    /**
     * @param $item
     * @return string
     */
    public function profile($item, $user_id = null)
    {
        if($user_id)
            $uid = $user_id;
        else
            $uid = $this->id;

        return App::db()->query("SELECT ".$item." FROM `users` WHERE `id` = '".$uid."'")->fetchColumn();
    }

    public static function nick($id, $string = '', $no_pic = false)
    {
        $id = App::filter('int', $id);
        $db = App::db();

        if($db->query("SELECT * FROM `users` WHERE `id` = '$id'")->rowCount() == 1)
        {
            $user = $db->query("SELECT * FROM `users` WHERE `id` = '$id'")->fetch();
            if($no_pic == true)
            {
                return '<a href="/user/profile/'.$user['id'].'">'. (isset($user['color_nick']) ? '<span style="color: #'.$user['color_nick'].';">'. $user['nick'] .'</span>' : $user['nick']) .'</a> '. ($user['level'] >= 5 ? '<span style="color:#ff211c;">[Adm]</span>' : ($user['level'] > 2 && $user['level'] <= 4 ? '<span style="color: #22b14c;">[Mod]</span>' : null)) .' '.($user['time'] > (time()-300) ? '<i class="green">'.Site::icon('on').' </i>' : '<i class="green">'.Site::icon('off').'</i>').($string != '' ? '<br/><small>'.$string.'</small>' : false);
            }
            else
            {
                return self::avatar($id, true).'&nbsp;<a href="/user/profile/'.$user['id'].'">'. (isset($user['color_nick']) ? '<span style="color: #'.$user['color_nick'].';">'. $user['nick'] .'</span>' : $user['nick']) .'</a> '. ($user['level'] >= 5 ? '<span style="color:#ff211c;">[Adm]</span>' : ($user['level'] > 2 && $user['level'] <= 4 ? '<span style="color: #22b14c;">[Mod]</span>' : null)) .' '.($user['time'] > (time()-300) ? '<i class="green">'.Site::icon('on').' </i>' : '<i class="green">'.Site::icon('off').'</i>').($string != '' ? '<br/><small>&nbsp;'.$string.'</small>' : false);
            }
        }
        else
        {
            return 'Unknown user';
        }
    }

    public static function avatar($id, $small = null)
    {
        if ($small)
            if(is_file(ROOT.DS.'files/avatars/'.$id.'_mini.jpg'))
                return '<img class="profile_img" src="/files/avatars/'.$id.'_mini.jpg" alt="'.user()->getId().'.jpg" />';
            else
                return Site::icon('avatar');
        else
        {
            if(is_file(ROOT.DS.'files/avatars/'.$id.'.jpg'))
                return '<img src="/files/avatars/'.$id.'.jpg" alt="'.$id.'.jpg" />';
        }
    }

    public static function setNotify($user_id, $from_id, $type, $request_page = '', $request_value = '')
    {
        if($user_id != $from_id)
            App::db()->query("INSERT INTO `notify` SET `user_id` = '". $user_id ."', `from_id` = '". $from_id ."', `request_id` = '".$request_page."', `request_value` = '".$request_value."', `type` = '".$type."', `read` = '0', `time` = '". time() ."'");
    }

    public function levelName($levelId)
    {
        switch($levelId)
        {
            case '1':
                return _t('User');
                break;

            case '2':
                return _t('VIP-User');
                break;

            case '3':
                return _t('Moderator');
                break;

            case '4':
                return _t('Super moderator');
                break;

            case '5':
                return _t('Administrator');
                break;

            case '6':
                return _t('Super administrator');
                break;

            default:
                return 'None';
            break;
        }
    }

    public function getOnline($type)
    {
        if($type == 'users')
        {
            return App::db()->query("SELECT * FROM `users` WHERE `time` > '".(time()-300)."'")->rowCount();
        }
        elseif($type == 'guests')
        {
            return App::db()->query("SELECT * FROM `guests` WHERE `time` > '". (time()-300) ."'")->rowCount();
        }
    }
}