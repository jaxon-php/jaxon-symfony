<?php

namespace Jaxon\Symfony;

use Jaxon\App\AppInterface;
use Jaxon\App\Traits\AppTrait;
use Jaxon\Exception\SetupException;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Twig\Environment as TemplateEngine;
use Psr\Log\LoggerInterface;

use function rtrim;
use function is_a;
use function jaxon;

class Jaxon implements AppInterface
{
    use AppTrait;

    /**
     * @var KernelInterface
     */
    private $kernel;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var TemplateEngine
     */
    private $template;

    /**
     * @var mixed
     */
    private $session;

    /**
     * @var array
     */
    private $aOptions;

    /**
     * The Symfony service locator id
     *
     * @var string
     */
    protected $locatorId = 'jaxon.service_locator';

    /**
     * Create a new Jaxon instance.
     *
     * @param KernelInterface $kernel
     * @param LoggerInterface $logger
     * @param TemplateEngine $template
     * @param mixed $session
     *
     * @param array $aOptions
     */
    public function __construct(KernelInterface $kernel, LoggerInterface $logger,
        TemplateEngine $template, $session, array $aOptions)
    {
        $this->initApp(jaxon()->di());
        $this->kernel = $kernel;
        $this->logger = $logger;
        $this->template = $template;
        $this->session = $session;
        $this->aOptions = $aOptions;
    }

    /**
     * @inheritDoc
     * @throws SetupException
     */
    public function setup(string $sConfigFile)
    {
        // Set the default view namespace
        $this->addViewNamespace('default', '', '.html.twig', 'twig');
        // Add the view renderer
        $this->addViewRenderer('twig', function() {
            return new View($this->template);
        });
        // Set the session manager
        $this->setSessionManager(function() {
            return new Session(is_a($this->session, SessionInterface::class) ?
                $this->session : $this->session->getSession());
        });
        // Set the framework service container wrapper
        $container = $this->kernel->getContainer();
        $locator = $container->get($this->locatorId, ContainerInterface::NULL_ON_INVALID_REFERENCE);
        $this->setContainer(new Container($container, $locator));
        // Set the logger
        $this->setLogger($this->logger);

        // The application URL
        $sJsUrl = isset($_SERVER['SERVER_NAME']) ?
            '//' . $_SERVER['SERVER_NAME'] . '/jaxon/js' : '/jaxon/js';
        // The application web dir
        $sJsDir = isset($_SERVER['DOCUMENT_ROOT']) ?
            '//' . $_SERVER['DOCUMENT_ROOT'] . '/jaxon/js' :
            rtrim($this->kernel->getProjectDir(), '/') . '/public/jaxon/js';
        // Export and minify options
        $bExportJs = $bMinifyJs = !$this->kernel->isDebug();

        $aLibOptions = $this->aOptions['lib'] ?? [];
        $aAppOptions = $this->aOptions['app'] ?? [];

        $this->bootstrap()
            ->lib($aLibOptions)
            ->app($aAppOptions)
            ->asset($bExportJs, $bMinifyJs, $sJsUrl, $sJsDir)
            ->setup();
    }

    /**
     * @inheritDoc
     */
    public function httpResponse(string $sCode = '200')
    {
        // Get the reponse to the request
        $ajaxResponse = $this->ajaxResponse();

        // Create and return a Symfony HTTP response
        $httpResponse = new HttpResponse();
        $httpResponse->headers->set('Content-Type', $ajaxResponse->getContentType());
        $httpResponse->setCharset($this->getCharacterEncoding());
        $httpResponse->setStatusCode($sCode);
        $httpResponse->setContent($ajaxResponse->getOutput());
        return $httpResponse;
    }
}
