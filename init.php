<?php defined('SYSPATH') or die('No direct script access.');

Route::set('media', 'media(/<file>)', array('file' => '.+'))
  ->defaults(array(
    'controller'    => 'media',
    'action'        => 'index',
    'file'          => NULL,
  ));

$theme_name = Kohana::$config->load('config')->get('theme');
if($theme_name !== NULL)
{
  Kohana::modules(array(
    'theme_' . $theme_name => DOCROOT . 'themes' . DIRECTORY_SEPARATOR . $theme_name,
  ) + Kohana::modules());
}
