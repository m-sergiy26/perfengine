<!DOCTYPE HTML>
<html>
<head>
    <title><?=$title;?></title>
    <meta name="charset" content="utf-8" />
    <meta name="keywords" content="<?=App::config('system/data/config')['keywords']?>"/>
    <meta name="description" content="<?=App::config('system/data/config')['description']?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <script type="text/javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false); function hideURLbar(){ window.scrollTo(0,1); } </script>
    <link href="/themes/mobile/default/css/style.css" rel="stylesheet" type="text/css" media="all"/>
    <link href="/themes/mobile/default/css/dropdown.css" rel="stylesheet" type="text/css" media="all"/>
    <script type="text/javascript" src="/themes/_js/jquery.js"></script>
    <link type="text/css" href="/themes/mobile/default/css/mmenu.css" rel="stylesheet" media="all" />
    <script type="text/javascript" src="/themes/mobile/default/js/jquery.mmenu.min.js"></script>
    <script type="text/javascript">
        $(function() {
            $('nav#sidebar').mmenu();
        });
    </script>
</head>
<body>
<div class="main">
<div class="wrap">
    <div class="header">
        <div class="header_top">
            <div class="profile_details">
                <div class="menu-ico"><a href="#sidebar"></a></div>
                <a href="/"><img class="logo" src="/themes/mobile/default/images/logo.jpg" alt="" /></a>
                <a href="#profile" class="toggleMenu"></a>
                <div id="profile" class="menu">
                    <ul class="nav">
                        <?php if(user()->isUser()): ?>
                            <?php if(user()->level() > 4): ?>
                                  <li><a href="/admin"><?=_t('Dashboard', 'admin')?> </a></li>
                            <? endif; ?>
                        <li><a href="/user"><?=_t('Settings')?> </a></li>
                        <li><a href="/messages"><?=_t('Messages')?> <?=Site::countNewMessages()?></a></li>
                        <? else: ?>
                        <li><a href="/user/login"><?=_t('Login')?> </a></li>
                        <li><a href="/user/register"><?=_t('Register')?> </a></li>
                        <? endif; ?>
                        <div class="clear"></div>
                    </ul>
                </div>
                <script type="text/javascript" src="/themes/mobile/default/js/responsive-menu.js"></script>
                <div class="clear"></div>
            </div>
            <div class="clear"></div>
        </div>
    </div>
    <? include('_menu.php') ?>
</div>
<?php
if(Site::advLinks('top') !== false): ?>
    <div class="adv">
        <?=Site::advLinks('top')?>
    </div>
<? endif;
Site::alerts();
?>