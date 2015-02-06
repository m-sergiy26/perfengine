<?php
class Language
{
    /*
     * @var array $data
     */
    protected $data = [];
    /**
     * @param $container
     * @param $module
     */
    public function __construct($container, $module)
    {
        $path = $module && is_dir(MODULES . DS . $module) ? MODULES . DS . $module : SYS;
		$lng = is_dir($path. DS .'locales'.DS.$this->getLanguage()) ? $this->getLanguage() : App::config('system/data/config')['language'];

        $main = include(SYS.DS.'locales'.DS. $lng .DS.'main.php');
        if(is_file($path.DS.'locales'.DS.$lng.DS.$container.'.php'))
        {
            $data = include($path.DS.'locales'.DS.$lng.DS.$container.'.php');
            $this->data = array_merge($data, $main);
        }
        else
        {
            $this->data = $main;
        }
    }

    /*
     * Translate phrase
     * @param string $phrase
     * @return string
     */
    public function translate($phrase)
    {
        // check exists of phrase
        if(isset($this->data[$phrase]))
        {
            // return translated
            return $this->data[$phrase];
        }
        else
        {
            //
            return $phrase;
        }
    }

    /*
     * Parser of language code
     * @return string
     */
    public static function getLanguage()
    {
        // get browser language
        $accept = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) : App::config('system/data/config')['language'];
        // get default config language

        // check cookie and file exists
        if(isset($_COOKIE['language']))
        {
            // return this value
            return $_COOKIE['language'];
        }
        elseif($accept)
        {
            return $accept;
        }
    }
}