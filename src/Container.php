<?php

namespace Jaxon\AjaxBundle;

use Psr\Container\ContainerInterface as PsrContainerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ServiceLocator;

/**
 * Container.php - Dependency injection gateway
 *
 * @package jaxon-core
 * @author Thierry Feuzeu <thierry.feuzeu@gmail.com>
 * @copyright 2016 Thierry Feuzeu <thierry.feuzeu@gmail.com>
 * @license https://opensource.org/licenses/BSD-3-Clause BSD 3-Clause License
 * @link https://github.com/jaxon-php/jaxon-core
 */

class Container implements PsrContainerInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var ServiceLocator
     */
    protected $locator;

    /**
     * The constructor
     *
     * @param ContainerInterface $container
     * @param ServiceLocator $locator
     */
    public function __construct(ContainerInterface $container, ?ServiceLocator $locator = null)
    {
        $this->container = $container;
        $this->locator = $locator;
    }

    /**
     * Check if a given class is defined in the container
     *
     * @param string    $sClass     A full class name
     *
     * @return bool
     */
    public function has($sClass)
    {
        return ($this->locator !== null && $this->locator->has($sClass)) || $this->container->has($sClass);
    }

    /**
     * Get a class instance
     *
     * @param string    $sClass     A full class name
     *
     * @return mixed
     */
    public function get($sClass)
    {
        if($this->locator !== null && $this->locator->has($sClass))
        {
            return $this->locator->get($sClass);
        }
        return $this->container->get($sClass, ContainerInterface::NULL_ON_INVALID_REFERENCE);
    }
}
