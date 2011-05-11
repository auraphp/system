<?php
/**
 * 
 * This file is part of the Aura project for PHP.
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 */
namespace aura\framework;
use aura\web\ControllerFactory;
use aura\web\Context;
use aura\view\TwoStep as TwoStepView;
use aura\http\Response as HttpResponse;
use aura\signal\Manager as SignalManager;
use aura\router\Map as RouterMap;
use aura\autoload\Loader;

/**
 * 
 * Dispatches a Route to a Controller, then uses the returned ResponseTransfer
 * to render a TwoStepView into an HttpResponse.
 * 
 * @package aura.framework
 * 
 */
class Dispatcher
{
    /**
     * 
     * The web request context.
     * 
     * @var aura\web\Context
     * 
     */
    protected $context;
    
    /**
     * 
     * A factory to create web controllers.
     * 
     * @var aura\web\ControllerFactory
     * 
     */
    protected $controller_factory;
    
    /**
     * 
     * A two-step view object to render views and layouts.
     * 
     * @var aura\view\TwoStep
     * 
     */
    protected $view;
    
    /**
     * 
     * An HTTP response object for sending the response.
     * 
     * @var aura\http\Response
     * 
     */
    protected $http_response;
    
    /**
     * 
     * A signal manager for events.
     * 
     * @var aura\signal\Manager
     * 
     */
    protected $signal;
    
    /**
     * 
     * An autoloader to help determine where controller directories are.
     * 
     * @var aura\autoload\Loader
     * 
     */
    protected $loader;
    
    /**
     * 
     * A ResponseTransfer object from the Controller.
     * 
     * @var aura\web\ResponseTransfer
     * 
     */
    protected $transfer;
    
    protected $router;
    
    protected $content;
    
    /**
     * 
     * Constructor.
     * 
     */
    public function __construct(
        Context           $context,
        RouterMap         $router,
        ControllerFactory $controller_factory,
        TwoStepView       $view,
        HttpResponse      $http_response,
        SignalManager     $signal,
        Loader            $loader
    ) {
        $this->context            = $context;
        $this->router             = $router;
        $this->controller_factory = $controller_factory;
        $this->view               = $view;
        $this->http_response      = $http_response;
        $this->signal             = $signal;
        $this->loader             = $loader;
    }
    
    /**
     * 
     * Magic read-only access to properties.
     * 
     * @param string $key The property to retrieve.
     * 
     * @return mixed
     * 
     */
    public function __get($key)
    {
        return $this->$key;
    }
    
    /**
     * 
     * Dispatches a Route to a web controller, renders a view into the
     * ReponseTransfer, and returns an HTTP response.
     * 
     * @param aura\router\Route $route A Route to dispatch.
     * 
     * @return aura\http\Response
     * 
     * @signal pre_dispatch
     * 
     * @signal post_dispatch
     * 
     * @signal pre_render
     * 
     * @signal post_render
     * 
     * @signal pre_transfer
     * 
     * @signal post_transfer
     * 
     */
    public function exec()
    {
        // dispatch to the controller
        $this->signal->send($this, 'pre_dispatch', $this);
        $this->transfer = $this->dispatch();
        $this->signal->send($this, 'post_dispatch', $this);
        
        // render the view into the transfer object
        $this->signal->send($this, 'pre_render', $this);
        $this->content = $this->render();
        $this->signal->send($this, 'post_render', $this);
        
        // transfer to the http response
        $this->signal->send($this, 'pre_transfer', $this);
        $this->transfer();
        $this->signal->send($this, 'post_transfer', $this);
        
        // done!
        return $this->http_response;
    }
    
    /**
     * 
     * Creates and executes a web controller.
     * 
     * @return aura\web\ResponseTransfer
     * 
     */
    protected function dispatch()
    {
        // set the route from the context
        $this->route = $this->router->match(
            $this->context->getServer('PATH_INFO', '/'),
            $this->context->getServer()
        );
        
        // was there a match?
        if ($this->route) {
            // retain info
            $controller = $this->route->values['controller'];
            $params     = $this->route->values;
        } else {
            // no match
            $controller = null;
            $params     = array();
        }
        
        // create controller
        $this->controller = $this->controller_factory->newInstance(
            $controller,
            $params
        );
        
        // execute and return data transfer object
        return $this->controller->exec();
    }
    
    /**
     * 
     * Uses the transfer object to render a two-step view.
     * 
     * @return string
     * 
     */
    protected function render()
    {
        // only render if content is not already present
        $content = $this->transfer->getContent();
        if ($content) {
            return $content;
        }
        
        // negoatiate a content type for rendering
        $accept = $this->context->getAccept('type');
        $this->transfer->negotiateContentType($accept);
        
        // get the override paths from the transfer object
        $view_paths   = $this->transfer->getViewPaths();
        $layout_paths = $this->transfer->getLayoutPaths();
        
        // add the default paths for the controller
        $subdirs = $this->loader->getSubdirs(get_class($this->controller));
        foreach ($subdirs as $dir) {
            $view_paths[]   = $dir . DIRECTORY_SEPARATOR . 'View';
            $layout_paths[] = $dir . DIRECTORY_SEPARATOR . 'Layout';
        }
        
        // set values for the view and layout in the two-step view
        $this->view->setViewName($this->transfer->matchView());
        $this->view->setViewData($this->transfer->getViewData());
        $this->view->setViewPaths($view_paths);
        $this->view->setLayoutName($this->transfer->matchLayout());
        $this->view->setLayoutData($this->transfer->getLayoutData());
        $this->view->setLayoutPaths($layout_paths);
        $this->view->setContentVar($this->transfer->getLayoutContentVar());
        
        // render the content
        return $this->view->render();
    }
    
    /**
     * 
     * Moves the ResponseTransfer data into the HTTP response.
     * 
     * @return void
     * 
     */
    protected function transfer()
    {
        $this->http_response->setVersion($this->transfer->getVersion());
        $this->http_response->setStatusCode($this->transfer->getStatusCode());
        $this->http_response->setStatusText($this->transfer->getStatusText());
        $this->http_response->headers->setAll($this->transfer->getHeaders());
        $this->http_response->cookies->setAll($this->transfer->getCookies());
        $this->http_response->setContent($this->content);
    }
}
