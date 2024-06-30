<?php

namespace Jaxon\Symfony;

use Jaxon\App\AbstractApp;
use Jaxon\App\AppInterface;
use Jaxon\Exception\SetupException;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Twig\Environment as TemplateEngine;
use Psr\Log\LoggerInterface;

use function is_a;
use function Jaxon\jaxon;
use function rtrim;

class Jaxon extends AbstractApp
{
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
     * @throws SetupException
     */
    public function __construct(private KernelInterface $kernel, private LoggerInterface $logger,
        private TemplateEngine $template, private $session, private array $aOptions)
    {
        // Setup the Jaxon library.
        parent::__construct();
        $this->setup('');

        // Register this object into the Jaxon container.
        jaxon()->di()->set(AppInterface::class, function() {
            return $this;
        });
    }

    /**
     * @inheritDoc
     * @throws SetupException
     */
    public function setup(string $sConfigFile)
    {
        // Add the view renderer
        $this->addViewRenderer('twig', '.html.twig', function() {
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
        $sJsUrl = isset($_SERVER['SERVER_NAME']) ? '//' . $_SERVER['SERVER_NAME'] . '/jaxon/js' : '/jaxon/js';
        // The application web dir
        $sJsDir = isset($_SERVER['DOCUMENT_ROOT']) ? '//' . $_SERVER['DOCUMENT_ROOT'] . '/jaxon/js' :
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
        // Create and return a Symfony HTTP response
        $httpResponse = new HttpResponse();
        $httpResponse->headers->set('Content-Type', $this->getContentType());
        $httpResponse->setStatusCode($sCode);
        $httpResponse->setContent($this->ajaxResponse()->getOutput());

        return $httpResponse;
    }
}
