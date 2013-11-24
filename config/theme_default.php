<?php defined('SYSPATH') OR die('No direct access allowed.');

return array
(
  'regions' => array(
    'header',
    'navigation',
    'sidebar',
    'content',
    'footer',
  ),
  'css' => array(
  	'media/css/style.css' => array(
      'file' => 'media/css/style.css',
      'media' => 'all',
      'weight' => 0,
    ),
  	'media/css/screen.css' => array(
      'file' => 'media/css/screen.css',
      'media' => 'screen',
      'weight' => 1,
    ),
  	'media/css/print.css' => array(
      'file' => 'media/css/print.css',
      'media' => 'screen',
      'weight' => 2,
    ),
  ),
);
