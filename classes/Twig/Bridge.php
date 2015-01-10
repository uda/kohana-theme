<?php defined('SYSPATH') or die('No direct script access.');

class Twig_Bridge
{
    public static function html($method, $arguments = array())
    {
        call_user_func_array(array('HTML', $method), $arguments);
    }

    public static function i18n($method, $arguments = array())
    {
        call_user_func_array(array('I18n', $method), $arguments);
    }
}
