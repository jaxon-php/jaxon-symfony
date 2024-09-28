<?php

namespace Jaxon\Symfony\App;

use Jaxon\App\AbstractApp;
use Jaxon\App\AppInterface;
use Jaxon\Exception\SetupException;
use Jaxon\Script\JsExpr;
use Jaxon\Script\JxnCall;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Twig\Environment as TemplateEngine;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFilter;
use Twig\TwigFunction;

use function is_a;
use function Jaxon\attr;
use function Jaxon\jaxon;
use function Jaxon\jq;
use function Jaxon\js;
use function Jaxon\pm;
use function Jaxon\rq;
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
     * @param FilesystemLoader $loader
     * @param mixed $session
     * @param array $aOptions
     *
     * @param array $aOptions
     * @throws SetupException
     */
    public function __construct(private KernelInterface $kernel, private LoggerInterface $logger,
        private TemplateEngine $template, private FilesystemLoader $loader, private $session,
        private array $aOptions)
    {
        parent::__construct();
    }

    /**
     * @inheritDoc
     * @throws SetupException
     */
    public function setup(string $_)
    {
        // Register this object into the Jaxon container.
        jaxon()->di()->set(AppInterface::class, function() {
            return $this;
        });

        // Filters for custom Jaxon attributes
        $this->template->addFilter(new TwigFilter('jxnHtml',
            fn(JxnCall $xJxnCall) => attr()->html($xJxnCall), ['is_safe' => ['html']]));
        $this->template->addFilter(new TwigFilter('jxnShow',
            fn(JxnCall $xJxnCall, string $item = '') => attr()->show($xJxnCall, $item), ['is_safe' => ['html']]));
        $this->template->addFilter(new TwigFilter('jxnOn',
            fn(JsExpr $xJsExpr, string|array $on, array $options = []) =>
                attr()->on($on, $xJsExpr, $options), ['is_safe' => ['html']]));
        $this->template->addFilter(new TwigFilter('jxnClick',
            fn(JsExpr $xJsExpr, array $options = []) =>
                attr()->click($xJsExpr, $options), ['is_safe' => ['html']]));

        // Functions for custom Jaxon attributes
        $this->template->addFunction(new TwigFunction('jxnHtml',
            fn(JxnCall $xJxnCall) => attr()->html($xJxnCall), ['is_safe' => ['html']]));
        $this->template->addFunction(new TwigFunction('jxnShow',
            fn(JxnCall $xJxnCall, string $item = '') => attr()->show($xJxnCall, $item), ['is_safe' => ['html']]));
        $this->template->addFunction(new TwigFunction('jxnOn',
            fn(string|array $on, JsExpr $xJsExpr, array $options = []) =>
                attr()->on($on, $xJsExpr, $options), ['is_safe' => ['html']]));
        $this->template->addFunction(new TwigFunction('jxnClick',
            fn(JsExpr $xJsExpr, array $options = []) =>
                attr()->click($xJsExpr, $options), ['is_safe' => ['html']]));
        $this->template->addFunction(new TwigFunction('jxnTarget',
            fn(string $name = '') => attr()->target($name), ['is_safe' => ['html']]));

        $this->template->addFunction(new TwigFunction('jq', fn(...$aParams) => jq(...$aParams)));
        $this->template->addFunction(new TwigFunction('js', fn(...$aParams) => js(...$aParams)));
        $this->template->addFunction(new TwigFunction('rq', fn(...$aParams) => rq(...$aParams)));
        $this->template->addFunction(new TwigFunction('pm', fn() => pm()));

        // Functions for Jaxon js and CSS codes
        $this->template->addFunction(new TwigFunction('jxnCss',
            fn() => jaxon()->css(), ['is_safe' => ['html']]));
        $this->template->addFunction(new TwigFunction('jxnJs',
            fn() => jaxon()->js(), ['is_safe' => ['html']]));
        $this->template->addFunction(new TwigFunction('jxnScript',
            fn() => jaxon()->script(), ['is_safe' => ['html']]));

        // Add the view renderer
        $this->addViewRenderer('twig', '.html.twig', function() {
            return new View($this->template, $this->loader);
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
