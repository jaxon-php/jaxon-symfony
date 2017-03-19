<?php

namespace Jaxon\AjaxBundle;

use Jaxon\Module\View\Store;
use Jaxon\Module\View\Facade;

class View extends Facade
{
    protected $renderer;

    public function __construct($renderer)
    {
        parent::__construct();
        $this->renderer = $renderer;
    }

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
        return trim($this->renderer->render($store->getViewPath() . '.html.twig', $store->getViewData()), " \t\n");
    }
}
