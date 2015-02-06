<?='<?xml version="1.0" encoding="UTF-8"?>'?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="<?=Core::language();?>" xml:lang="<?=Core::language();?>">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title><?=(!empty($title) ? $title : 'Untitled Page'); ?> | <?=Core::config('title');?></title>
		<link rel="stylesheet" href="<?=Core::themeUrl();?>/style.css" type="text/css"/>
		<link rel="shortcut icon" href="<?=Core::themeUrl();?>/favicon.ico" type="image/x-icon" />
		<?=(Core::config('description') ? '<meta name="description" content="'.Core::config('description').'" />' : NULL); ?>
		<?=(Core::config('keywords') ? '<meta name="keywords" content="'.Core::config('keywords').'" />' : NULL); ?>
		</head>
<body>
<div class="main">
<div class="logo"><a href="/"><img src="<?=Core::themeUrl();?>/img/logo.png" alt="" /></a></div>
<div class="panel">
<?=user_panel();?>
</div>
<?=notifications();?>
<?=Stat::adsHeader();?>