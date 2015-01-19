<?php defined('SYSPATH') OR die('No direct script access.');

class Controller_Theme_View extends Controller_Theme
{
    protected $engine = 'View';

    /**
     * @param Request $request
     * @param Response $response
     * @throws Exception If the Twig module for Kohana is not enabled
     */
    public function __construct(Request $request, Response $response)
    {
        parent::__construct($request, $response);

        foreach ($this->base_config('regions', NULL, array()) as $region => $enabled)
        {
            $this->region($region, '');
        }
    }

    public function before()
    {
        parent::before();

        if ($this->auto_render === true) {
            // Load the template
            $this->template = View::factory($this->template);
            $this->template->set(
              'app',
              array(
                'request' => $this->request,
                'host_name' => $_SERVER['HTTP_HOST'],
              )
            );
        }
    }

    public function after()
    {
        if ($this->auto_render === true) {
            $this->template->set('site_direction', $this->base_config('direction'));
            $this->template->set('title_separator', $this->base_config('title_separator'));
            $this->template->set('title', $this->title());
            $this->template->set('styles', $this->style());
            $this->template->set('scripts', $this->script());

            foreach (array_keys($this->_regions) as $region) {
                if (in_array($region, array('header', 'footer')) && empty($this->_regions[$region])) {
                    $view = View::factory('html/' . $region);
                    if ($region == 'header') {
                        $view->set('site_name', $this->config('site_name'));
                    }
                    $this->region($region, $view);
                }
                $this->template->{$region} = $this->region($region);
            }
        }
        parent::after();
    }
}