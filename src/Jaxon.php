<?php

namespace Jaxon\AjaxBundle;

use Jaxon\Config\Yaml as Config;

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
    protected function jaxonSetup()
    {
        // The application URL
        $baseUrl = '//' . $_SERVER['SERVER_NAME'];
        // The application web dir
        $baseDir = $_SERVER['DOCUMENT_ROOT'];

        // Read and set the config options from the config file
        $configFilePath = $this->rootDir . '/app/config/jaxon.yml';
        $this->appConfig = Config::read($configFilePath, 'jaxon_ajax.lib', 'jaxon_ajax.app');

        // Jaxon library default settings
        $this->setLibraryOptions(!$this->debug, !$this->debug, $baseUrl . '/jaxon/js', $baseDir . '/jaxon/js');

        // Jaxon application default settings
        $this->setApplicationOptions($this->rootDir . '/jaxon/Controller', '\\Jaxon\\App');

        // Set the default view namespace
        $this->addViewNamespace('default', '', '', 'twig');
        $this->appConfig->setOption('options.views.default', 'default');

        // Add the view renderer
        $template = $this->template;
        $this->addViewRenderer('twig', function() use($template) {
            return new View($template);
        });

        // Set the session manager
        $this->setSessionManager(function(){
            return new Session();
        });
    }

    /**
     * Set the module specific options for the Jaxon library.
     *
     * This method needs to set at least the Jaxon request URI.
     *
     * @return void
     */
    protected function jaxonCheck()
    {
        // Todo: check the mandatory options
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
        $response->headers->set('Content-Type', $this->jaxonResponse->getContentType());
        $response->setCharset($this->jaxonResponse->getCharacterEncoding());
        $response->setStatusCode($code);
        $response->setContent($this->jaxonResponse->getOutput());
        // prints the HTTP headers followed by the content
        $response->send();
    }
}
