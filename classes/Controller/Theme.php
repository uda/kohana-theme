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
  
  private $_base_config;
  private $_config;

  /**
   * Loads the template [View] object.
   */
  public function __construct(Request $request, Response $response)
  {
    parent::__construct($request, $response);
    
    $this->_base_config = Kohana::$config->load('theme');
    
    $theme_name = $this->_base_config->get('name');
    
    
    if ($theme_name === NULL)
    {
      $theme_name = 'default';
    }
    
    $this->_preset($theme_name);
  }
  
  private $_regions;
  private $_title = '';
  private $_styles = array();
  private $_scripts = array();
  private $_links = array(); // used for links like alternate and rss
  
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
        foreach ($theme_styles as $file => $file_info)
        {
          $this->style($file, $file_info);
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
      $this->template->set('site_direction', $this->_config->get('direction'));
      $this->template->set('title', $this->title());
      $this->template->set('styles', $this->style());
      uasort($this->template->styles, array($this, '_sort_weight'));
      $this->template->set('scripts', $this->script());
      
      foreach (array_keys($this->_regions) as $region)
      {
        if (in_array($region, array('header', 'footer')) && empty($this->_regions[$region]))
        {
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
    $this->_config = Kohana::$config->load($config_file);
    
    foreach ($this->_config->get('regions') as $region)
    {
      $this->region($region, '');
    }
    
    foreach ($this->_config->get('css', array()) as $file => $file_info)
    {
      $this->style($file, $file_info);
    }
    
    foreach ($this->_config->get('js', array()) as $file)
    {
      $this->script($file);
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
      if (!empty($this->_title))
      {
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
  
  public function style($file = NULL, $media = NULL, $weight = 0)
  {
    if ($file === NULL)
    {
      return $this->_styles;
    }
    
    if ($media === NULL)
    {
      $media = 'screen';
    }
    
    if (!isset($this->_styles[$file]))
    {
      if (is_array($media))
      {
        $this->_styles[$file] = $media;
      }
      else
      {
        $this->_styles[$file] = array(
          'file' => $file,
          'media' => $media,
          'wieght' => $weight,
        );
      }
    }
    
    return $this;
  }
  
  public function script($file = NULL)
  {
    if ($file === NULL)
    {
      return $this->_scripts;
    }
    
    if (!in_array($file, $this->_scripts))
    {
      $this->_scripts[] = $file;
    }
    
    return $this;
  }
  
  protected function _sort_weight($a, $b)
  {
    $a_weight = (is_array($a) && isset($a['weight'])) ? $a['weight'] : 0;
    $b_weight = (is_array($b) && isset($b['weight'])) ? $b['weight'] : 0;
    if ($a_weight == $b_weight)
    {
      return 0;
    }
    return ($a_weight < $b_weight) ? -1 : 1;
  }
}
