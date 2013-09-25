<?php defined('SYSPATH') OR die('No direct script access.');

class Controller_Theme extends Controller
{
  /**
   * @var  View  page template
   */
  public $template = 'html/html';

  /**
   * @var  boolean  auto render template
   **/
  public $auto_render = TRUE;
  
  protected $_config;

  /**
   * Loads the template [View] object.
   */
  public function __construct(Request $request, Response $response)
  {
    parent::__construct($request, $response);
    
    $this->_config = Kohana::$config->load('theme');
    
    $theme_name = $this->_config->get('name');
    
    
    if ($theme_name === NULL) {
      $theme_name = 'default';
    }
    
    $this->_preset($theme_name);
  }
  
  protected $_regions;
  protected $_title = '';
  protected $_styles = array();
  protected $_scripts = array();
  protected $_links = array(); // used for links like alternate and rss
  
  public function before()
  {
    parent::before();
    
    if ($this->auto_render === TRUE)
    {
      // Load the template
      $this->template = View::factory($this->template);
      
      $theme_styles = $this->_config->get('css');
      if (!empty($theme_styles))
      {
        foreach ($theme_styles as $file => $media)
        {
          $this->style($file, $media);
        }
      }
      
      $theme_scripts = $this->_config->get('js');
      if (!empty($theme_scripts))
      {
        foreach ($theme_scripts as $file)
        {
          $this->script($file);
        }
      }
    }
  }
  
  public function after()
  {
    if ($this->auto_render === TRUE)
    {
      $this->template->title = $this->title();
      
      $styles = array(
        'media/css/style.css' => 'all',
        'media/css/screen.css' => 'screen',
        'media/css/print.css' => 'print',
      );
      $scripts = array(
        'media/js/script.js',
      );
      
      $this->template->styles = array_merge($this->style(), $styles);
      $this->template->scripts = array_merge($this->script(), $scripts);
      
      foreach (array_keys($this->_regions) as $region)
      {
        if (in_array($region, array('header', 'footer')) && empty($this->_regions[$region])) {
          $view = View::factory('html/' . $region);
          if ($region == 'header')
          {
            $view->set('site_name', $this->_config->get('site_name'));
          }
          $this->region($region, $view);
        }
        $this->template->{$region} = $this->region($region);
      }
      
      $this->response->body($this->template->render());
    }
    parent::after();
  }
  
  protected function _preset($theme_name)
  {
    $config_file = 'theme_' . $theme_name;
    $config = Kohana::$config->load($config_file);
    
    $this->_config = $config;
    
    foreach ($config->get('regions') as $region)
    {
      $this->region($region, '');
    }
  }
  
  public function title($title = NULL)
  {
    if ($title === NULL)
    {
      $title = array();
      $site_name = $this->_config->get('site_name');
      if (!empty($site_name))
      {
        $title[] = $site_name;
      }
      if (!empty($this->_title)) {
        $title[] = $this->_title;
      }
      return implode(' :: ', $title);
    }
    $this->_title = $title;
    return $this;
  }
  
  public function region($name, $content = NULL)
  {
    if ($content === NULL) {
      if (!isset($this->_regions[$name]))
      {
        return '';
      }
      
      if (is_object($this->_regions[$name]))
      {
        return $this->_regions[$name]->render();
      }
      
      return $this->_regions[$name];
    }
    
    $this->_regions[$name] = $content;
    return $this;
  }
  
  public function style($file = NULL, $media = NULL)
  {
    if ($file === NULL)
    {
      return $this->_styles;
    }
    
    if ($media === NULL) {
      $media = 'screen';
    }
    
    if (!isset($this->_styles[$file])) {
      $this->_styles[$file] = $media;
    }
    
    return $this;
  }
  
  public function script($file = NULL)
  {
    if ($file === NULL)
    {
      return $this->_scripts;
    }
    
    if (!in_array($file, $this->_scripts)) {
      $this->_scripts[] = $file;
    }
    
    return $this;
  }
}
