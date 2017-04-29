<?php

namespace Jaxon\AjaxBundle;

use Jaxon\Module\View\Store;
use Jaxon\Module\Interfaces\View as ViewInterface;

class View implements ViewInterface
{
    protected $renderer;
    protected $namespaces = array();

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
    {
        $this->namespaces[$sNamespace] = array(
            'directory' => $sDirectory,
            'extension' => $sExtension,
        );
    }

    /**
     * Render a view
     * 
     * @param Store         $store        A store populated with the view data
     * 
     * @return string        The string representation of the view
     */
    public function render(Store $store)
    {
        $sExtension = '';
        if(key_exists($store->getNamespace(), $this->namespaces))
        {
            $sExtension = $this->namespaces[$store->getNamespace()]['extension'];
        }
        // Render the template
        return trim($this->renderer->render($store->getViewName() . $sExtension, $store->getViewData()), " \t\n");
    }
}
