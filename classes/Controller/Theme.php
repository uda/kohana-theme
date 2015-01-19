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

  /**
   * @var string
   */
  protected $engine = 'View';

  /**
   * @var Config_Group The initial theme config
   */
  protected $_base_config;

  /**
   * @var Config_Group The selected theme's config
   */
  protected $_config;

  /**
   * @var View[]
   */
  protected $_regions;

  /**
   * @var string
   */
  protected $_title = '';

  /**
   * @var array An array of styles
   */
  protected $_styles = [];

  /**
   * @var array An array of scripts
   */
  protected $_scripts = [];

  /**
   * @var array An array of links
   */
  protected $_links = []; // used for links like alternate and rss

  /**
   * Loads the template [View] object.
   */
  public function __construct(Request $request, Response $response)
  {
    parent::__construct($request, $response);

    $this->_base_config = Kohana::$config->load('config');

    $theme_name = $this->_base_config->get('theme', 'default');

    $this->_preset($theme_name);
  }

  public function after()
  {
    if ($this->auto_render === TRUE)
    {
      uasort($this->template->styles, [$this, '_sort_weight']);
      $this->response->body($this->template->render());
    }
    parent::after();
  }

  protected function _preset($theme_name)
  {
    $config_file = 'theme_' . $theme_name;
    $this->_config = Kohana::$config->load($config_file);

    foreach ($this->config('css', NULL, []) as $file => $file_info)
    {
      $this->style($file, $file_info);
    }

    foreach ($this->config('js', NULL, []) as $file => $file_info)
    {
      $this->script($file, $file_info);
    }
  }

  public function title($title = NULL)
  {
    if ($title === NULL)
    {
      $title = [];
      $site_name = $this->base_config('site_name');
      $title_separator = $this->base_config('title_separator');
      if (!empty($this->_title))
      {
        $title[] = $this->_title;
      }
      if (!empty($site_name))
      {
        $title[] = $site_name;
      }
      return implode(" {$title_separator} ", $title);
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

      if (is_object($this->_regions[$name]) && $this->_regions[$name] instanceof View)
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
          'weight' => $weight,
        );
      }
    }

    return $this;
  }

  public function script($file = NULL, $weight = 0)
  {
    if ($file === NULL)
    {
      return $this->_scripts;
    }

    if (!isset($this->_scripts[$file]))
    {
      if (is_array($weight))
      {
        $this->_scripts[$file] = $weight;
      }
      else {
        $this->_scripts[$file] = [
          'file' => $file,
          'weight' => $weight,
        ];
      }
    }

    return $this;
  }

  /**
   * @param mixed $key
   * @param mixed $value
   * @param mixed $default
   * @return Config_Group|Kohana_Config_Group|mixed|static
   */
  protected function base_config($key = NULL, $value = NULL, $default = NULL)
  {
    if ($key === NULL)
    {
      return $this->_base_config;
    }

    if ($value === NULL)
    {
      return $this->_base_config->get($key, $default);
    }

    $this->_base_config->set($key, $value);
    return $this;
  }

  /**
   * @param mixed $key
   * @param mixed $value
   * @param mixed $default
   * @return Config_Group|Kohana_Config_Group|mixed|static
   */
  protected function config($key = NULL, $value = NULL, $default = NULL)
  {
    if ($key === NULL)
    {
      return $this->_config;
    }

    if ($value === NULL)
    {
      return $this->_config->get($key, $default);
    }

    $this->_config->set($key, $value);
    return $this;
  }

  /**
   * @param $key
   * @param $value
   */
  protected function config_global($key, $value)
  {
    /** @var View|Twig $engine */
    $engine = $this->engine;
    $engine::set_global($key, $value);
  }

  /**
   * Sorts an array by the values of the weight value
   *
   * @param $a
   * @param $b
   * @return int
   */
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
