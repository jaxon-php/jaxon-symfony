<?php

namespace Jaxon\AjaxBundle;

use Jaxon\Module\View\Store;
use Jaxon\Module\Interfaces\View as ViewRenderer;

class View implements ViewRenderer
{
    protected $renderer;

    public function __construct($renderer)
    {
        $this->renderer = $renderer;
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
    public function addNamespace($sNamespace, $sDirectory, $sExtension = '')
    {}

    /**
     * Render a view
     * 
     * @param Store         $store        A store populated with the view data
     * 
     * @return string        The string representation of the view
     */
    public function make(Store $store)
    {
        // Render the template
        return trim($this->renderer->render($store->getViewName() . '.html.twig', $store->getViewData()), " \t\n");
    }
}
