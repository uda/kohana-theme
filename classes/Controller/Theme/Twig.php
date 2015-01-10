<?php defined('SYSPATH') OR die('No direct script access.');

class Controller_Theme_Twig extends Controller_Theme
{
    /**
     * @var string
     */
    protected $engine = 'Twig';

    /**
     * @param Request $request
     * @param Response $response
     * @throws Exception If the Twig module for Kohana is not enabled
     */
    public function __construct(Request $request, Response $response)
    {
        if (!class_exists('Twig')) {
            throw new Exception('Twig module must be enabled in the bootstrap file.');
        }

        parent::__construct($request, $response);

        foreach ($this->_base_config as $key => $value) {
            $this->config_global($key, $value);
        }
    }

    public function before()
    {
        parent::before();

        $controller = $this->request->controller();
        $action = $this->request->action();

        $this->template = strtolower($controller . '/' . $action);

        if ($this->auto_render === true) {
            /** @var Twig $engine */
            $engine = $this->engine;
            $engine::set_global(
              'app',
              array(
                'request' => $this->request,
                'host_name' => $_SERVER['HTTP_HOST'],
              )
            );
            $engine::bind_global('styles', $this->_styles);
            $engine::bind_global('scripts', $this->_scripts);
        }
    }

    public function after()
    {
        if ($this->auto_render === true) {
            /** @var Twig $engine */
            $engine = $this->engine;
            $engine::set_global('site_language'. I18n::$lang);
            $engine::set_global('site_direction', $this->base_config('direction'));
            $engine::set_global('title_separator', $this->base_config('title_separator'));
            $engine::set_global('title', $this->title());
            uasort($this->_styles, array($this, '_sort_weight'));
        }
        parent::after();
    }
}