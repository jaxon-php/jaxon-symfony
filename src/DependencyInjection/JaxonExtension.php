<?php 

namespace Jaxon\Symfony\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class JaxonExtension extends Extension
{
    /**
     * @inheritDoc
     */
    public function load(array $configs, ContainerBuilder $container)
    { 
        $configDir = new FileLocator(__DIR__ . '/../../config');

        // Load the bundle's service declarations 
        $loader = new YamlFileLoader($container, $configDir);
        $loader->load('services.yaml');
    }
}
