<?php
/*
 * This file using for initializing rules
 */
date_default_timezone_set(App::timezone());
// Handler for translation class
function _t($phrase, $container = 'main', $module = null)
{
    $language = new Language($container, $module);
    return $language->translate($phrase);
}

// Alias for User class
function user()
{
    return new User();
}