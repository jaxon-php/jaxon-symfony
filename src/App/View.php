<?php

namespace Jaxon\Symfony\App;

use Jaxon\App\View\Store;
use Jaxon\App\View\ViewInterface;
use Twig\Environment as TemplateEngine;
use Twig\Loader\FilesystemLoader;

use function ltrim;
use function str_replace;
use function trim;

class View implements ViewInterface
{
    /**
     * @var array
     */
    private array $aExtensions = [];

    /**
     * The constructor
     *
     * @param TemplateEngine $xEngine
     */
    public function __construct(private TemplateEngine $xEngine, private FilesystemLoader $xLoader)
    {}

    /**
     * @inheritDoc
     */
    public function addNamespace(string $sNamespace, string $sDirectory, string $sExtension = ''): void
    {
        $this->aExtensions[$sNamespace] = '.' . ltrim($sExtension, '.');
        $this->xLoader->addPath($sDirectory, $sNamespace);
    }

    /**
     * @inheritDoc
     */
    public function render(Store $store): string
    {
        $sNamespace = $store->getNamespace();
        $sViewName = !$sNamespace || $sNamespace === 'twig' ?
            $store->getViewName() : '@' . $sNamespace . '/' . $store->getViewName();
        $sViewName = str_replace('.', '/', $sViewName);
        if(isset($this->aExtensions[$sNamespace]))
        {
            $sViewName .= $this->aExtensions[$sNamespace];
        }

        // Render the template
        return trim($this->xEngine->render($sViewName, $store->getViewData()), " \t\n");
    }
}
