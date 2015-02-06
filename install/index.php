<?php
session_name('PSID');
session_start();
error_reporting(E_ALL);
define('SYS', realpath(dirname(__FILE__)).'/system');
$version = file_get_contents('../system/data/version.inf');
function install_header($title = 'Installing PerfEngine')
{
    echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
    <title>'.$title.'</title>
    <link href="/install/style/style.css" rel="stylesheet" type="text/css" />
    </head>
    <body>
    <div class="all">';
}
install_header('Installing PerfEngine v'.$version);
?>
<div class="title">Installing PerfEngine v<?=$version;?></div>
<?php
if(is_file('../system/data/install.inf') && !is_file('../system/data/install.inf'))
{
	echo '<div class="post">
			PerfEngine already installed!
		</div>
		<div class="block">
			<a href="/">Home Page</a>
		</div>';
}
else
{
if(!isset($_GET['lang'])) 
{
	echo '<div class="post">
	Choose Installing language:<br/>';
	$dirs = scandir('lang');
	foreach($dirs as $dir)
	{
		if($dir != '.' && $dir != '..' && !stripos($dir, '.txt'))
		{
			echo '<a href="/install?lang='.str_replace('.ini', '', $dir).'">'.file_get_contents('lang/'.$dir.'.txt').'</a><br/>';
		}
	}
	echo '</div>';
}
else
{
	if(file_exists('lang/'. trim($_GET['lang']).'.ini'))
    {
		$lang = parse_ini_file('lang/'. trim($_GET['lang']).'.ini');
		$lng = trim($_GET['lang']);
	}
    else
    {
		$lang = parse_ini_file('lang/en.ini');
		$lng = 'en';
	}
if(isset($_GET['lang']) && !isset($_GET['act']))
{
    echo '<div class="post">'. $lang['welcome'] .'<br/>
		'.file_get_contents('../LICENSE.txt').'<br/>
		[ <a href="/install?act=start&amp;lang='. $lng .'">'. $lang['agree'] .'</a> | <a href="/install/">'. $lang['nagree'] .'</a> ]</div>';
}
elseif(isset($_GET['lang']) && $_GET['act'] == 'start')
{
		$chmods =  array('../cache/', '../cache/compiled/', '../cache/templates/', '../cache/downloads_jad/', '../files/', '../files/articles/', '../files/avatars/', '../files/forum/', '../files/smiles/', '../files/downloads/', '../files/downloads_thumbs/', '../system/data/', '../system/locales/', '../tmp/');
		echo '<div class="post">
		<table>
		<tr>
		<td><b>'. $lang['fdir'] .'</b></td>
		<td><b>'. $lang['chmods'] .'</b></td>
		</tr>
		<tr>';
		 foreach ($chmods as $chmod) {
        echo '<tr>
        <td>'. str_replace('../', '', $chmod) .'</td>';
        
        if (is_writable(trim($chmod))) {
          echo '<td><span style="color: green"><b>OK (777)</b></span></td>';
		  $err = false;
        } else {
          echo '<td><span style="color: red">'.$lang['must_chmods'].' 777</span></td>';
                    
          $err = TRUE;
        }
        echo '</tr>';
      }
      echo '</tr>
      </table>
      '. ($err == TRUE?'<a href="?act=start&amp;lang='. $lng .'">'.$lang['refresh'].'</a>':'<a href="?act=db&amp;lang='. $lng .'">'.$lang['next'].'</a>') .'    
      </div>
      <div class="block">
      <a href="?lang='. $lng .'">'.$lang['back'].'</a>
      </div>';
}
elseif(isset($_GET['lang']) && $_GET['act'] == 'db')
{
	if (isset($_POST['go']))
    {
        $host = @htmlspecialchars(trim($_POST['host']));
        $user = @htmlspecialchars(trim($_POST['user']));
        $pass = @htmlspecialchars(trim($_POST['pass']));
        $base = @htmlspecialchars(trim($_POST['base']));
        
        if (empty($host)) $err .= $lang['empty_host'].'<br />';
        if (empty($user)) $err .= $lang['empty_user'].'<br />';
        if (empty($base)) $err .= $lang['empty_base'].'<br />';
        if(!isset($err)) 
		{
			try
			{
				$db = new PDO('mysql:dbname='.$base.';host='. $host, $user, $pass);
			} 
			catch (PDOException $e) 
			{
				echo 'Connection failed: ' . $e->getMessage();
			}
		}
        if (!isset($err)) 
		{
          $db->query("SET NAMES utf8");
          $dbCfg = "<?php\nreturn [\n\t'type' => 'mysql',\n\t'host' => '".$host."',\n\t"."'user' => '".$user."',\n\t"."'pass' => '".$pass."',\n\t"."'base' => '".$base."',\n\t'charset' => 'utf8'\n];";
			file_put_contents('../system/data/db.php', $dbCfg);
			file_put_contents('../system/data/__password_salt.txt', substr(md5($_SERVER['HTTP_HOST'].rand(1111, 9999)), 0, 8));
			$dump = file_get_contents('./install.sql');
			$db->query(trim($dump));

			echo '<div class="title">'.$lang['c_create'].'</div>
          <div class="menu">
          '.$lang['after_t'].'<br />
          <a href="?act=admin&amp;lang='. $lng .'">'.$lang['next'].'</a>
          </div>
          
          <div class="block">
          <a href="?act=db&amp;lang='. $lng .'">'.$lang['back'].'</a>
          </div>
          <div class="footer">PerfEngine v'.$version.', '.date('Y').'</div>
</div>
</body>
</html>';
    
          exit();
        }
      }
      
      if (isset($err)) echo '<div class="error">'. $err .'</div>';
    
      echo '<form method="post" action="?act=db&amp;lang='. $lng .'">
      <div class="title">'.$lang['connection'].'</div>
      <div class="menu">
      '.$lang['host'].':<br />
      <input type="text" name="host" value="localhost" /><br />
      '.$lang['user'].':<br />
      <input type="text" name="user" /><br />
      '.$lang['pass'].':<br />
      <input type="password" name="pass" /><br />
      '.$lang['base'].':<br />
      <input type="text" name="base" /><br />
      <input type="submit" name="go" value="'.$lang['send'].'" />
      </div>
      </form>
      
      <div class="block">
      <a href="?act=start&amp;lang='. $lng .'">'.$lang['back'].'</a>
      </div>';
	}
elseif(isset($_GET['lang']) && $_GET['act'] == 'admin')
	{
	if (isset($_POST['reg_admin']))
    {
        $nick = htmlspecialchars(trim($_POST['nick']));
        $name = htmlspecialchars(trim($_POST['name']));
        $email = htmlspecialchars(trim($_POST['email']));
        $password = htmlspecialchars(trim($_POST['password']));
        $password2 = htmlspecialchars(trim($_POST['password2']));
        
        if (empty($nick)) $err .= $lang['no_nick'].'<br />';
        if (empty($name)) $err .= $lang['no_name'].'<br />';
        if (empty($email)) $err .= $lang['no_email'].'<br />';
        if (empty($password)) $err .= $lang['no_pass'].'<br />';
        if (empty($password2)) $err .= $lang['no_pass2'].'<br />';
        
        if (!empty($nick) && (mb_strlen($nick, 'UTF-8') < 3 || mb_strlen($nick, 'UTF-8') > 64)) $err .= $lang['e_nick'].'<br />';
        if (!empty($nick) && !preg_match("#^([A-zА-я0-9\-_ ])+$#ui", $nick)) $err .= $lang['b_nick'].'<br />';
        if (!empty($name) && (mb_strlen($name, 'UTF-8') > 32)) $err .= $lang['e_name'].'<br />';        
        if (!empty($email) && (mb_strlen($email, 'UTF-8') < 3 || mb_strlen($email, 'UTF-8') > 128)) $err .= $lang['b_mail'].'<br />';
        if (!empty($email) && !preg_match('|^([a-z0-9_\.\-]{1,20})@([a-z0-9\.\-]{1,20})\.([a-z]{2,4})$|ius', $email)) $err .= $lang['e_email'].'<br />';        
        if (!empty($password) && (mb_strlen($password, 'UTF-8') < 5 || mb_strlen($password, 'UTF-8') > 128)) $err .= $lang['e_pass'].'<br />';
        if (!empty($password) && !empty($password2) && $password != $password2) $err .= $lang['e_pass2'].'<br />';        
        if (!isset($err)) {

          function crypto($var)
          {
              return crypt(md5(base64_encode(trim($var))), '$1$' . file_get_contents('../system/data/__password_salt.txt') . '$');
          }
        
          # Кодуємо пароль
          $password = crypto($password);
          
          $mysql = include('../system/data/db.php');
			try {
				$db = new PDO('mysql:dbname='.$mysql['base'].';host='. $mysql['host'], $mysql['user'], $mysql['pass']);
			} catch (PDOException $e) {
					echo 'Connection failed: ' . $e->getMessage();
			}
          $db->query("SET NAMES utf8");

            function escape($inp)
	        {
                 if(is_array($inp))
                   return array_map(__METHOD__, $inp);

                if(!empty($inp) && is_string($inp)) {
                    return str_replace(array('\\', "\0", "\n", "\r", "'", '"', "\x1a"), array('\\', '\0', '\n', '\r', "\'", '\"', '\Z'), $inp);
                }

                return $inp;
	        }
		
          # Запит на реєстрацію
          $db->query("INSERT INTO `users` SET `name` = '". escape(trim($name)) ."', `nick` = '". escape(trim($nick)) ."', `password` = '". escape(trim($password)) ."', `reg_time` = '". time() ."', `time` = '". time() ."', `email` = '". escape(trim($email)) ."', `level` = '6', `gender`='0'");
		  $language = $lng;
		  $db->query("INSERT INTO `settings` SET `user_id` = '". $db->lastInsertId() ."', `language` = '". $language ."', `items` = '20', `theme_mobile` = 'default', `theme_web` = 'default', `fast_form` = '1', `show_profile` = '0', `allow_messages` = '1', `timezone` = 'Europe/Kiev'");

		  // print_r($db->errorInfo());
          unlink('../system/data/install.inf');
          session_destroy();
          
          echo '<div class="title">'.$lang['end_i'].'</div>
          <div class="menu">
          '.$lang['end_i_t'].' <b>/install/</b>.<br />
          <a href="/user/login?email='. $email .'&amp;password='. $password2 .'">'.$lang['go_site'].'</a>
          </div>
          
          <div class="block">
          <a href="?act=admin&amp;lang='. $lng .'">'.$lang['back'].'</a>
          </div>          
          </div>
		  <div class="footer">PerfEngine v'.$version.', '.date('Y').'</div>
</div>
</body>
</html>';
    
          exit();
        }         
      }
    
      if (isset($err)) echo '<div class="err">'. $err .'</div>';
      
      echo '<form method="post" action="?act=admin&amp;lang='. $lng .'">
      <div class="title">Administration Registration</div>
      <div class="menu">
      '.$lang['nick'].':<br />
      <input type="text" name="nick" value="'. @htmlspecialchars($_POST['nick']) .'" /><br />
      '.$lang['name'].':<br />
      <input type="text" name="name" value="'. @htmlspecialchars($_POST['name']) .'" /><br />
      E-Mail:<br />
      <input type="text" name="email" value="'. @htmlspecialchars($_POST['email']) .'" /><br />
      '.$lang['password'].':<br />
      <input type="password" name="password" /><br />
      '.$lang['password2'].':<br />
      <input type="password" name="password2" /><br />
      <input type="submit" name="reg_admin" value="'.$lang['sign_up'].'" />
      </div>
      </form>
      
      <div class="block">
      <a href="?act=db&amp;lang='. $lng .'">'.$lang['back'].'</a>
      </div>';

  }
}
}
?>
<div class="footer">PerfEngine v<?=$version;?>, <?=date('Y');?></div>
</div>
</body>
</html>