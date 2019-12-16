<?php

namespace Jaxon\AjaxBundle;

use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Symfony\Component\HttpKernel\KernelInterface;
use Twig\Environment as TemplateEngine;
use Psr\Log\LoggerInterface;

class Jaxon
{
    use \Jaxon\Features\App;

    /**
     * The template engine
     *
     * @var EngineInterface
     */
    // protected $template;

    /**
     * Create a new Jaxon instance.
     *
     * @param KernelInterface       $kernel
     * @param LoggerInterface       $logger
     * @param TemplateEngine        $template
     * @param array                 $config
     */
    public function __construct(KernelInterface $kernel,
        LoggerInterface $logger, TemplateEngine $template, array $config)
    {
        // The application URL
        $sJsUrl = '//' . $_SERVER['SERVER_NAME'] . '/jaxon/js';
        // The application web dir
        $sJsDir = $_SERVER['DOCUMENT_ROOT'] . '/jaxon/js';
        // Export and minify options
        $bExportJs = $bMinifyJs = !$kernel->isDebug();

        $jaxon = jaxon();
        $di = $jaxon->di();

        $viewManager = $di->getViewManager();
        // Set the default view namespace
        $viewManager->addNamespace('default', '', '.html.twig', 'twig');
        // Add the view renderer
        $viewManager->addRenderer('twig', function() use ($template) {
            return new View($template);
        });

        // Set the session manager
        $di->setSessionManager(function() {
            return new Session();
        });

        // Set the framework service container wrapper
        $di->setAppContainer(new Container($kernel->getContainer()));

        // Set the logger
        $this->setLogger($logger);

        $this->bootstrap()
            ->lib($config['lib'])
            ->app($config['app'])
            // ->uri($sUri)
            ->js($bExportJs, $sJsUrl, $sJsDir, $bMinifyJs)
            ->run();

        // Prevent the Jaxon library from sending the response or exiting
        $jaxon->setOption('core.response.send', false);
        $jaxon->setOption('core.process.exit', false);
    }

    /**
     * Get the HTTP response
     *
     * @param string    $code       The HTTP response code
     *
     * @return mixed
     */
    public function httpResponse($code = '200')
    {
        $jaxon = jaxon();
        // Get the reponse to the request
        $jaxonResponse = $jaxon->di()->getResponseManager()->getResponse();
        if(!$jaxonResponse)
        {
            $jaxonResponse = $jaxon->getResponse();
        }

        // Create and return a Symfony HTTP response
        $httpResponse = new HttpResponse();
        $httpResponse->headers->set('Content-Type', $jaxonResponse->getContentType());
        $httpResponse->setCharset($jaxonResponse->getCharacterEncoding());
        $httpResponse->setStatusCode($code);
        $httpResponse->setContent($jaxonResponse->getOutput());
        return $httpResponse;
    }

    /**
     * Process an incoming Jaxon request, and return the response.
     *
     * @return mixed
     */
    public function processRequest()
    {
        // Process the jaxon request
        jaxon()->processRequest();

        // Return the reponse to the request
        return $this->httpResponse();
    }
}
