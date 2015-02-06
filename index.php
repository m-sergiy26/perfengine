<?php
# debug
error_reporting(E_ALL);
define('DEBUG_TIME', microtime(true));

session_start();

# set encoding and charset
mb_http_input('UTF-8'); 
mb_http_output('UTF-8'); 
mb_internal_encoding("UTF-8");
ini_set( 'default_charset', 'UTF-8' );

# defines
define('ROOT', dirname(__FILE__));
define('SYS', ROOT.'/system');
define('DS', DIRECTORY_SEPARATOR);
define('MODULES', ROOT.'/modules');
define('URL', 'http://'.$_SERVER['HTTP_HOST']);

# check install
if(is_file(SYS.'/data/install.inf') && !is_file(SYS.'/data/db.php'))
{
    # go to install page
    header('Location: /install');
    exit;
}

// include classes
spl_autoload_register(function($class)
{
    include(SYS.'/'.$class.'.php');
});

// initialize app
$app = new App;
// include other init scripts
include('init.php');
$app->run();

echo '<div style="border: dashed 1px #5aacff; margin-top: 10px; color: #07cd16; float: right;">' .round(microtime(true)-DEBUG_TIME, 3).' sec.</div>';