<?php

/**
 * Container.php
 *
 * @package jaxon-core
 * @author Thierry Feuzeu <thierry.feuzeu@gmail.com>
 * @copyright 2016 Thierry Feuzeu <thierry.feuzeu@gmail.com>
 * @license https://opensource.org/licenses/BSD-3-Clause BSD 3-Clause License
 * @link https://github.com/jaxon-php/jaxon-core
 */

namespace Jaxon\Symfony\App;

use Psr\Container\ContainerInterface as PsrContainerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ServiceLocator;

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
     * @param ServiceLocator|null $locator
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
    public function has(string $sClass): bool
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
    public function get(string $sClass)
    {
        if($this->locator !== null && $this->locator->has($sClass))
        {
            return $this->locator->get($sClass);
        }
        return $this->container->get($sClass, ContainerInterface::NULL_ON_INVALID_REFERENCE);
    }
}
