<?php
class App
{
    private static $route;

    private static $db;

    /**
     *
     */
    public function __construct()
    {
        // parse route
        $route = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        // assign module and page
        $route = explode('/', $route);

        array_shift($route);

        if (end($route) == '')
            array_pop($route);

        if (!isset($route[0])) {
            $route[0] = 'main';
        }


        if (!isset($route[1])) {
            $route[1] = 'index';
        }

        self::$route = $route;

        /*
         * Database connection
         */
        // parse database config
        $dbc = self::config('system/data/db');

        // connect to server
        self::$db = new PDO($dbc['type'] . ':dbname=' . $dbc['base'] . ';host=' . $dbc['host'] . ';charset=' . $dbc['charset'], $dbc['user'], $dbc['pass'], [
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES '.$dbc['charset'],
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
        /*
         * Database connection
         */
    }

    private static function route()
    {
        return self::$route;
    }

    public static function isModule($moduleName = null)
    {
        if(is_file(ROOT.DS.'modules/'.($moduleName ? $moduleName : self::route()[0]).'/_module.php'))
            return true;
        else
            return false;
    }

    public static function moduleId()
    {
        if(self::route()[0])
            return self::route()[0];
        else
            return false;
    }

    public static function pageId()
    {
        if(self::route()[1])
            return self::route()[1];
        else
            return false;
    }

    /**
     *
     */
    public function run()
    {
        // var_dump($route);

        if (is_dir(ROOT . '/modules/' . self::route()[0]) && is_file(ROOT . '/modules/' . self::route()[0] . '/' . self::route()[1] . '.php'))
        {
            include(ROOT . '/modules/' . self::route()[0] . '/' . self::route()[1] . '.php');
        }
        else
        {
            if(is_file(ROOT.DS.'themes/mobile/'.self::getTheme().'/404.php'))
                $file = ROOT.DS.'themes/mobile/'.self::getTheme().'/404.php';
            else
                $file = ROOT . '/modules/main/404.php';
            include($file);
        }
    }

    /**
     * @param $file
     * @return mixed|string
     */
    public static function config($file)
    {
        if (is_file(ROOT . '/' . $file . '.php')) {
            return require(ROOT . '/' . $file . '.php');
        } else {
            echo 'No config found';
            return false;
        }
    }

    public static function writeConfig($file, array $data)
    {
        if(is_array(self::config($file)) && is_array($data))
        {
            $config = array_merge(self::config($file), $data);

            $string = "<?php\nreturn [\n";
            foreach($config as $key => $value)
            {
                $string .= "\t'".$key."' => ".(is_int($value) ? $value : "'".$value."'").",\n";
            }
            $string .= '];';

            $cfg = fopen(ROOT.DS.$file.'.php', 'wt');
            fwrite($cfg, $string);
            fclose($cfg);
        }
    }

    /**
     * @return PDO
     */
    public static function db()
    {
        // returning instance
        return self::$db;
    }

    /**
     * @param string $type
     * @param mixed $data
     * @param array $options
     * @return int|string
     */
    public static function filter($type = 'input', $data, $options = [])
    {
        switch ($type) {
            case 'input': // filtering string
                return trim(filter_var($data, FILTER_SANITIZE_FULL_SPECIAL_CHARS, $options));
                break;

            case 'int': // filtering numbers
                return (int) trim(filter_var($data, FILTER_SANITIZE_NUMBER_INT, $options));
                break;

            case 'url': // filtering url
                return trim(filter_var($data, FILTER_SANITIZE_URL, $options));
                break;

            case 'email': // filtering email
                return trim(filter_var($data, FILTER_SANITIZE_EMAIL, $options));
                break;

            default:
                return trim(filter_var($data, FILTER_SANITIZE_SPECIAL_CHARS, $options));
                break;
        }
    }

    /**
     * @param $input
     * @param string $salt
     * @return string
     */
    public static function hash($input, $saltStr = '')
    {
        if(is_file(SYS.DS.'data/__password_salt.txt'))
            $salt = file_get_contents(SYS.DS.'data/__password_salt.txt');
        elseif($saltStr != '')
            $salt = $saltStr;
        else
            $salt = 'abcd1234';

        return crypt(md5(base64_encode(trim($input))), '$1$' . $salt . '$');
    }

    /**
     * @return string
     */
    public static function getTheme()
    {
        $user = new User;
        if ($user->isUser()) {
            return $user->config('theme_mobile');
        } else {
            return self::config('system/data/config')['theme_mobile'];
        }
    }

    /**
     * @param string $url
     */
    public static function redirect($url)
    {
        header('Location: ' . $url);
        exit;
    }

    public static function timezone()
    {
        if (user()->isUser()) {
            $user = new User;
            return $user->config('timezone');
        } else {
            return self::config('system/data/config')['timezone'];
        }
    }

    public static function antiflood($table, $row, $value, $time = 15, $params = array('time_row' => 'time', 'user_row' => 'user_id'))
    {
        $db = App::db();
        if ($db->query("SELECT * FROM `" . $table . "` WHERE `" . $params['time_row'] . "` > '" . (time() - $time) . "' AND `" . $row . "` = '" . $value . "' AND `" . $params['user_row'] . "` = '" . user()->getId() . "'")->rowCount() == 0) {
            return false;
        } else {
            return true;
        }
    }

    public static function date($timestamp = null)
    {
        if (!$timestamp)
            $timestamp = time();

        $date = date('d.m.Y, H:i', $timestamp);

        if (date('d.m.Y', $timestamp) == date('d.m.Y'))
            $date = date(_t('\T\o\d\a\y') . ', H:i', $timestamp);
        elseif (date('d.m.Y', $timestamp) == date('d.m.Y', time() - 60 * 60 * 24))
            $date = date(_t('\Y\e\s\t\e\r\d\a\y') . ', H:i', $timestamp);

        return $date;
    }

    public static function browser_type()
    {
        $useragent = $_SERVER['HTTP_USER_AGENT'];
        if (preg_match('/android|avantgo|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|msie|mmp|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i', $useragent) || preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|e\-|e\/|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(di|rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|xda(\-|2|g)|yas\-|your|zeto|zte\-/i', substr($useragent, 0, 4))) {
            return 'mobile';
        } else {
            return 'desktop';
        }
    }

    public static function fileSize($bytes)
    {
        if ($bytes >= 1073741824) return round($bytes / 1073741824, 2) . ' GiB';
        else if ($bytes >= 1048576) return round($bytes / 1048576, 2) . ' MiB';
        elseif ($bytes >= 1024) return round($bytes / 1024, 2) . ' KiB';
        else return round($bytes) . ' b';
    }

    public static function translit($string)
    {
        $string = strtr(mb_strtolower($string), array("а" => "a", "б" => "b",
            "в" => "v", "г" => "g", "д" => "d", "е" => "e", "ж" => "zh",
            "з" => "z", "и" => "i", "й" => "j", "к" => "k", "л" => "l",
            "м" => "m", "н" => "n", "о" => "o", "п" => "p", "р" => "r",
            "с" => "s", "т" => "t", "у" => "u", "ф" => "f", "х" => "h",
            "ц" => "ts", "ч" => "ch", "ш" => "sh", "щ" => "sch", "ъ" => "",
            "ы" => "y", "ь" => "", "э" => "e", "ю" => "ju", "я" => "ja",
            "і" => "i", "ї" => "ji", "ґ" => "g", "ё" => "e", "є" => "je",
            " " => "_"));
        return preg_replace('/[^a-z0-9_-]/ui', '', $string);
    }

    public static function rrmdir($dir)
    {
        if (is_dir($dir))
        {
            $objects = scandir($dir);
            foreach ($objects as $object)
            {
                if ($object != "." && $object != "..")
                {
                    if (filetype($dir."/".$object) == "dir") self::rrmdir($dir."/".$object); else unlink($dir."/".$object);
                }
            }
            reset($objects);
            rmdir($dir);
        }
    }

    public static function rcopy($sourceDir, $targetDir)
    {
        if (!file_exists($sourceDir)) return false;
        if (!is_dir($sourceDir)) return copy($sourceDir, $targetDir);
        if (!mkdir($targetDir)) return false;
        foreach (scandir($sourceDir) as $item) {
            if ($item == '.' || $item == '..') continue;
            if (!self::rcopy($sourceDir.DIRECTORY_SEPARATOR.$item, $targetDir.DIRECTORY_SEPARATOR.$item)) return false;
        }
        return true;
    }

    public static function moduleWidget($module)
    {
        if(is_file(ROOT.DS.'modules/'.$module.'/_widget.php'))
        {
            include(ROOT.DS.'modules/'.$module.'/_widget.php');
        }
        else
            return false;
    }

    public static function moduleCounter($module)
    {
        if(is_file(ROOT.DS.'modules/'.$module.'/_counter.php'))
        {
            return include(ROOT.DS.'modules/'.$module.'/_counter.php');
        }
        else
            return false;
    }

    public static function browser($agent = '')
    {
        $subtok = function ($string,$chr,$pos,$len = NULL)
        {
            return implode($chr,array_slice(explode($chr,$string),$pos,$len));
        };
        
        if(empty($agent))
        {
            $agent = $_SERVER['HTTP_USER_AGENT'];
        }

        if (stripos($agent, 'Avant Browser') !== false)
        {
            return 'Avant Browser';
        }
        elseif (stripos($agent, 'Acoo Browser') !== false)
        {
            return 'Acoo Browser';
        }
        elseif (stripos($agent, 'MyIE2') !== false)
        {
            return 'MyIE2';
        }
        elseif (preg_match('|Iron/([0-9a-z\.]*)|i', $agent, $pocket))
        {
            return 'SRWare Iron ' . $subtok($pocket[1], '.', 0, 2);
        }
        elseif(preg_match('|OPR/([0-9a-z\.]*)|i', $agent, $pocket))
        {
            return 'Opera ' . $subtok($pocket[1], '.', 0, 3);
        }
        elseif(preg_match('|Yandex/([0-9a-z\.]*)|i', $agent, $pocket))
        {
            return 'Yandex Browser ' . $subtok($pocket[1], '.', 0, 3);
        }
        elseif(preg_match('|Vivaldi/([0-9a-z\.]*)|i', $agent, $pocket))
        {
            return 'Vivaldi Browser ' . $subtok($pocket[1], '.', 0, 3);
        }
        elseif (preg_match('|Chrome/([0-9a-z\.]*)|i', $agent, $pocket))
        {
            return 'Chrome ' . $subtok($pocket[1], '.', 0, 3);
        }
        elseif (preg_match('#(Maxthon|NetCaptor)( [0-9a-z\.]*)?#i', $agent, $pocket))
        {
            return $pocket[1] . $pocket[2];
        }
        elseif (stripos($agent, 'Safari') !== false && preg_match('|Version/([0-9]{1,2}.[0-9]{1,2})|i', $agent, $pocket))
        {
            return 'Safari ' . $subtok($pocket[1], '.', 0, 3);
        }
        elseif (preg_match('#(NetFront|K-Meleon|Netscape|Galeon|Epiphany|Konqueror|Safari|Opera Mini|Opera Mobile/Opera Mobi)/([0-9a-z\.]*)#i', $agent, $pocket))
        {
            return $pocket[1] . ' ' . $subtok($pocket[2], '.', 0, 2);
        }
        elseif (stripos($agent, 'Opera') !== false && preg_match('|Version/([0-9]{1,2}.[0-9]{1,2})|i', $agent, $pocket))
        {
            return 'Opera ' . $pocket[1];
        }
        elseif (preg_match('|Opera[/ ]([0-9a-z\.]*)|i', $agent, $pocket))
        {
            return 'Opera ' . $subtok($pocket[1], '.', 0, 2);
        }
        elseif (preg_match('|Orca/([ 0-9a-z\.]*)|i', $agent, $pocket))
        {
            return 'Orca ' . $subtok($pocket[1], '.', 0, 2);
        }
        elseif (preg_match('#(SeaMonkey|Firefox|GranParadiso|Minefield|Shiretoko)/([0-9a-z\.]*)#i', $agent, $pocket))
        {
            return $pocket[1] . ' ' . $subtok($pocket[2], '.', 0, 3);
        }
        elseif (preg_match('|rv:([0-9a-z\.]*)|i', $agent, $pocket) && strpos($agent, 'Mozilla/') !== false)
        {
            return 'Mozilla ' . $subtok($pocket[1], '.', 0, 2);
        }
        elseif (preg_match('|Lynx/([0-9a-z\.]*)|i', $agent, $pocket))
        {
            return 'Lynx ' . $subtok($pocket[1], '.', 0, 2);
        }
        elseif (preg_match('|MSIE ([0-9a-z\.]*)|i', $agent, $pocket))
        {
            return 'IE ' . $subtok($pocket[1], '.', 0, 2);
        }
        elseif (preg_match('|Googlebot/([0-9a-z\.]*)|i', $agent, $pocket))
        {
            return 'Google Bot ' . $subtok($pocket[1], '/', 0, 2);
        }
        elseif (preg_match('|Yandex|i', $agent))
        {
            return 'Yandex Bot ';
        }
        elseif (preg_match('|Nokia([0-9a-z\.\-\_]*)|i', $agent, $pocket))
        {
            return 'Nokia '.$pocket[1];
        }
        else
        {
            $agent = preg_replace('|http://|i', '', $agent);
            $agent = strtok($agent, '/ ');
            $agent = substr($agent, 0, 22);
            $agent = $subtok($agent, '.', 0, 2);

            if (!empty($agent))
            {
                return $agent;
            }
        }
        return 'Unknown';
    }
}