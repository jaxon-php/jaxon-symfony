<?php

namespace Jaxon\AjaxBundle;

use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Twig\Environment as TemplateEngine;
use Psr\Log\LoggerInterface;

use function rtrim;
use function is_a;
use function jaxon;

class Jaxon
{
    use \Jaxon\App\AppTrait;

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
     * @param array                 $aOptions
     */
    public function __construct(KernelInterface $kernel, LoggerInterface $logger,
        TemplateEngine $template, $session, array $aOptions)
    {
        $this->jaxon = jaxon();

        // Set the default view namespace
        $this->addViewNamespace('default', '', '.html.twig', 'twig');
        // Add the view renderer
        $this->addViewRenderer('twig', function() use($template) {
            return new View($template);
        });
        // Set the session manager
        $this->setSessionManager(function() use($session) {
            return new Session(is_a($session, SessionInterface::class) ? $session : $session->getSession());
        });
        // Set the framework service container wrapper
        $container = $kernel->getContainer();
        $locator = $container->get($this->locatorId, ContainerInterface::NULL_ON_INVALID_REFERENCE);
        $this->setAppContainer(new Container($container, $locator));
        // Set the logger
        $this->setLogger($logger);

        // The application URL
        $sJsUrl = isset($_SERVER['SERVER_NAME']) ?
            '//' . $_SERVER['SERVER_NAME'] . '/jaxon/js' : '/jaxon/js';
        // The application web dir
        $sJsDir = isset($_SERVER['DOCUMENT_ROOT']) ?
            '//' . $_SERVER['DOCUMENT_ROOT'] . '/jaxon/js' :
            rtrim($kernel->getProjectDir(), '/') . '/public/jaxon/js';
        // Export and minify options
        $bExportJs = $bMinifyJs = !$kernel->isDebug();

        $aLibOptions = $aOptions['lib'] ?? [];
        $aAppOptions = $aOptions['app'] ?? [];
        $this->bootstrap()
            ->lib($aLibOptions)
            ->app($aAppOptions)
            // ->uri($sUri)
            ->js($bExportJs, $sJsUrl, $sJsDir, $bMinifyJs)
            ->setup();
    }

    /**
     * @inheritDoc
     */
    public function httpResponse(string $sCode = '200')
    {
        // Get the reponse to the request
        $jaxonResponse = $this->jaxon->getResponse();

        // Create and return a Symfony HTTP response
        $httpResponse = new HttpResponse();
        $httpResponse->headers->set('Content-Type', $jaxonResponse->getContentType());
        $httpResponse->setCharset($this->getCharacterEncoding());
        $httpResponse->setStatusCode($sCode);
        $httpResponse->setContent($jaxonResponse->getOutput());
        return $httpResponse;
    }
}
