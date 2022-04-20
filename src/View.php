<?php

namespace Jaxon\Symfony;

use Jaxon\App\View\Store;
use Jaxon\App\View\ViewInterface;
use Twig\Environment;

use function trim;

class View implements ViewInterface
{
    /**
     * The Twig template renderer
     *
     * @var Environment
     */
    protected $xRenderer;

    /**
     * The view namespaces
     *
     * @var array
     */
    protected $aNamespaces = [];

    /**
     * The constructor
     *
     * @param Environment $xRenderer
     */
    public function __construct(Environment $xRenderer)
    {
        $this->xRenderer = $xRenderer;
    }

    /**
     * Add a namespace to this view renderer
     *
     * @param string        $sNamespace         The namespace name
     * @param string        $sDirectory         The namespace directory
     * @param string        $sExtension         The extension to append to template names
     *
     * @return void
     */
    public function addNamespace(string $sNamespace, string $sDirectory, string $sExtension = '')
    {
        $this->aNamespaces[$sNamespace] = [
            'directory' => $sDirectory,
            'extension' => $sExtension,
        ];
    }

    /**
     * Render a view
     *
     * @param Store         $store        A store populated with the view data
     *
     * @return string        The string representation of the view
     */
    public function render(Store $store): string
    {
        $sExtension = '';
        if(isset($this->aNamespaces[$store->getNamespace()]))
        {
            $sExtension = $this->aNamespaces[$store->getNamespace()]['extension'];
        }

        // Render the template
        return trim($this->xRenderer->render($store->getViewName() . $sExtension, $store->getViewData()), " \t\n");
    }
}
