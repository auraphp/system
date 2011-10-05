<?php
/**
 * 
 * This file is part of the Aura project for PHP.
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 */
namespace Aura\Framework;
use Aura\Web\Context;
use Aura\View\TwoStep as TwoStepView;
use Aura\Http\Response as HttpResponse;
use Aura\Signal\Manager as SignalManager;
use Aura\Router\Map as RouterMap;
use Aura\Autoload\Loader;
use Aura\Web\ResponseTransfer;

/**
 * 
 * Renders a TwoStepView from a ResponseTransfer.
 * 
 * @package Aura.Framework
 * 
 */
class Renderer implements RendererInterface
{
    /**
     * 
     * A two-step view object to render views and layouts.
     * 
     * @var Aura\View\TwoStep
     * 
     */
    protected $view;
    
    /**
     * 
     * An autoloader to help determine where controller directories are.
     * 
     * @var Aura\Autoload\Loader
     * 
     */
    protected $loader;
    
    /**
     * 
     * Constructor.
     * 
     */
    public function __construct(
        TwoStepView $view,
        Loader $loader
    ) {
        $this->view    = $view;
        $this->loader  = $loader;
    }
    
    /**
     * 
     * Uses the transfer object to render a two-step view.
     * 
     * @return string
     * 
     */
    public function exec(ResponseTransfer $transfer, $accept = null)
    {
        // negoatiate a content type
        $transfer->negotiateContentType($accept);
        
        // only render if content is not already present
        if ($transfer->getContent()) {
            return;
        }
        
        // set the view info
        $this->view->setViewName($transfer->matchView());
        $this->view->setViewData($transfer->getViewData());
        $view_stack = $transfer->getViewStack();
        foreach ($view_stack as $item) {
            list($spec, $subdir) = $item;
            $paths = $this->loader->findDir($spec);
            foreach ($paths as $path) {
                $this->view->addViewPath($path . DIRECTORY_SEPARATOR . $subdir);
            }
        }
        
        // set the layout info
        $this->view->setLayoutName($transfer->matchLayout());
        $this->view->setLayoutData($transfer->getLayoutData());
        $layout_stack = $transfer->getLayoutStack();
        foreach ($layout_stack as $item) {
            list($spec, $subdir) = $item;
            $paths = $this->loader->findDir($spec);
            foreach ($paths as $path) {
                $this->view->addLayoutPath($path . DIRECTORY_SEPARATOR . $subdir);
            }
        }
        $this->view->setLayoutContentVar($transfer->getLayoutContentVar());
        
        // render the content, and done!
        $transfer->setContent($this->view->render());
    }
}
