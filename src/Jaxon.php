<?php

namespace Jaxon\AjaxBundle;

use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Twig\Environment as TemplateEngine;
use Psr\Log\LoggerInterface;

class Jaxon
{
    use \Jaxon\Features\App;

    /**
     * The Symfony service locator id
     *
     * @var string
     */
    protected $locatorId = 'jaxon.service_locator';

    /**
     * Create a new Jaxon instance.
     *
     * @param KernelInterface       $kernel
     * @param LoggerInterface       $logger
     * @param TemplateEngine        $template
     * @param mixed                 $session
     * @param array                 $config
     */
    public function __construct(KernelInterface $kernel, LoggerInterface $logger,
        TemplateEngine $template, $session, array $config)
    {
        // The application URL
        $sJsUrl = \array_key_exists('SERVER_NAME', $_SERVER) ?
            '//' . $_SERVER['SERVER_NAME'] . '/jaxon/js' : '/jaxon/js';
        // The application web dir
        $sJsDir = \array_key_exists('DOCUMENT_ROOT', $_SERVER) ?
            '//' . $_SERVER['DOCUMENT_ROOT'] . '/jaxon/js' :
            \rtrim($kernel->getProjectDir(), '/') . '/public/jaxon/js';
        // Export and minify options
        $bExportJs = $bMinifyJs = !$kernel->isDebug();

        $jaxon = \jaxon();
        $di = $jaxon->di();

        $viewManager = $di->getViewManager();
        // Set the default view namespace
        $viewManager->addNamespace('default', '', '.html.twig', 'twig');
        // Add the view renderer
        $viewManager->addRenderer('twig', function() use($template) {
            return new View($template);
        });

        // Set the session manager
        $di->setSessionManager(function() use($session) {
            return new Session(\is_a($session, SessionInterface::class) ? $session : $session->getSession());
        });

        // Set the framework service container wrapper
        $container = $kernel->getContainer();
        $locator = $container->get($this->locatorId, ContainerInterface::NULL_ON_INVALID_REFERENCE);
        // Cannot pass a null parameter to the Container constructor,
        // because PHP versions prior to 7.1 do not support nullable parameters.
        $di->setAppContainer(($locator) ? new Container($container, $locator) : new Container($container));

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
        $jaxon = \jaxon();
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
        \jaxon()->processRequest();

        // Return the reponse to the request
        return $this->httpResponse();
    }
}
