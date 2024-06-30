<?php

namespace Jaxon\Symfony;

use Jaxon\App\View\Store;
use Jaxon\App\View\ViewInterface;
use Jaxon\App\View\ViewTrait;
use Twig\Environment as Twig;

use function trim;

class View implements ViewInterface
{
    use ViewTrait;

    /**
     * The constructor
     *
     * @param Twig $xRenderer
     */
    public function __construct(protected Twig $xRenderer)
    {}

    /**
     * @inheritDoc
     */
    public function render(Store $store): string
    {
        $sViewName = $store->getViewName();
        $sNamespace = $store->getNamespace();
        // For this view renderer, the view name doesn't need to be prepended with the namespace.
        $nNsLen = strlen($sNamespace) + 2;
        if(substr($sViewName, 0, $nNsLen) === $sNamespace . '::')
        {
            $sViewName = substr($sViewName, $nNsLen);
        }

        // View namespace
        $this->setCurrentNamespace($sNamespace);

        // Render the template
        return trim($this->xRenderer->render($sViewName . $this->sExtension, $store->getViewData()), " \t\n");
    }
}
