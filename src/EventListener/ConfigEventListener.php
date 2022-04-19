<?php

/**
 * ConfigEventListener.php
 *
 * Symfony event listener for loading Jaxon config.
 *
 * @package jaxon-core
 * @author Thierry Feuzeu <thierry.feuzeu@gmail.com>
 * @copyright 2022 Thierry Feuzeu <thierry.feuzeu@gmail.com>
 * @license https://opensource.org/licenses/BSD-3-Clause BSD 3-Clause License
 * @link https://github.com/jaxon-php/jaxon-core
 */

namespace Jaxon\Symfony\EventListener;

use Jaxon\Symfony\Jaxon;
use Jaxon\App\AppInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Twig\Environment as TemplateEngine;

use function jaxon;

class ConfigEventListener
{
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
     * The constructor
     *
     * @param KernelInterface $kernel
     * @param LoggerInterface $logger
     * @param TemplateEngine $template
     * @param mixed $session
     * @param array $aOptions
     */
    public function __construct(KernelInterface $kernel, LoggerInterface $logger,
        TemplateEngine $template, $session, array $aOptions)
    {
        $this->kernel = $kernel;
        $this->logger = $logger;
        $this->template = $template;
        $this->session = $session;
        $this->aOptions = $aOptions;
    }

    /**
     * Handler for the kernel.controller event
     *
     * @param ControllerEvent $event
     *
     * @return void
     */
    public function onKernelController(ControllerEvent $event)
    {
        // Check if the route has the jaxon attribute set.
        if(!$event->getRequest()->attributes->get('jaxon', false))
        {
            return;
        }

        jaxon()->di()->set(AppInterface::class, function() {
            return new Jaxon($this->kernel, $this->logger, $this->template, $this->session, $this->aOptions);
        });
        // Load the config
        jaxon()->app()->setup('');
    }
}
