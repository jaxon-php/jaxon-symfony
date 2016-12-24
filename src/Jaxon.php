<?php

namespace Jaxon\AjaxBundle;

use Symfony\Component\HttpFoundation\Response as HttpResponse;

class Jaxon
{
    use \Jaxon\Module\Traits\Module;

    /**
     * The application root dir
     * 
     * @var string
     */
    protected $rootDir;

    /**
     * The application debug option
     * 
     * @var bool
     */
    protected $debug;

    /**
     * The template engine
     * 
     * @var \Symfony\Component\Templating\EngineInterface
     */
    protected $template;

    /**
     * Create a new Jaxon instance.
     *
     * @return void
     */
    public function __construct($kernel, $template, $debug)
    {
        $this->template = $template;
        // The application root dir
        $this->rootDir = realpath($kernel->getRootDir() . '/..');
        // The application debug option
        $this->debug = $debug;
    }

    /**
     * Set the module specific options for the Jaxon library.
     *
     * @return void
     */
    protected function setup()
    {
        // The application URL
        $baseUrl = $_SERVER['SERVER_NAME'];
        // The application web dir
        $baseDir = $_SERVER['DOCUMENT_ROOT'];

        // Read and set the config options from the config file
        $jaxon = jaxon();
        $this->appConfig = $jaxon->readConfigFile($this->rootDir . '/app/config/jaxon.yml', 'jaxon_ajax.lib', 'jaxon_ajax.app');

        // Jaxon library settings
        // Default values
        if(!$jaxon->hasOption('js.app.extern'))
        {
            $jaxon->setOption('js.app.extern', !$this->debug);
        }
        if(!$jaxon->hasOption('js.app.minify'))
        {
            $jaxon->setOption('js.app.minify', !$this->debug);
        }
        if(!$jaxon->hasOption('js.app.uri'))
        {
            $jaxon->setOption('js.app.uri', '//' . $baseUrl . '/jaxon/js');
        }
        if(!$jaxon->hasOption('js.app.dir'))
        {
            $jaxon->setOption('js.app.dir', $baseDir . '/jaxon/js');
        }

        // Jaxon application settings
        // Default values
        if(!$this->appConfig->hasOption('controllers.directory'))
        {
            $this->appConfig->setOption('controllers.directory', $this->rootDir . '/src/Jaxon/App/Controllers');
        }
        if(!$this->appConfig->hasOption('controllers.namespace'))
        {
            $this->appConfig->setOption('controllers.namespace', '\\Jaxon\\App');
        }
        if(!$this->appConfig->hasOption('controllers.protected') || !is_array($this->appConfig->getOption('protected')))
        {
            $this->appConfig->setOption('controllers.protected', array());
        }
        // Jaxon controller class
        $this->setControllerClass('\\Jaxon\\AjaxBundle\\Controller');
    }

    /**
     * Set the module specific options for the Jaxon library.
     *
     * This method needs to set at least the Jaxon request URI.
     *
     * @return void
     */
    protected function check()
    {
        // Todo: check the mandatory options
    }

    /**
     * Return the view renderer.
     *
     * @return void
     */
    protected function view()
    {
        if($this->viewRenderer == null)
        {
            $this->viewRenderer = new View($this->template);
        }
        return $this->viewRenderer;
    }

    /**
     * Wrap the Jaxon response into an HTTP response.
     *
     * @param  $code        The HTTP Response code
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function httpResponse($code = '200')
    {
        // Create and return a Symfony HTTP response
        $response = new HttpResponse();
        $response->headers->set('Content-Type', $this->response->getContentType());
        $response->setCharset($this->response->getCharacterEncoding());
        $response->setStatusCode($code);
        $response->setContent($this->response->getOutput());
        // prints the HTTP headers followed by the content
        $response->send();
    }
}
