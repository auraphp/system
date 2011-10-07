<?php
/**
 * 
 * This file is part of the Aura project for PHP.
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 */
namespace Aura\Framework;
use Aura\Signal\Manager as SignalManager;
use Aura\Web\Context;

/**
 * 
 * Takes an incoming web request (Context), then dispatches it, renders
 * content, and returns a response for it.
 * 
 * @package Aura.Framework
 * 
 */
class RequestHandler
{
    /**
     * 
     * The web request context.
     * 
     * @var Aura\Web\Context
     * 
     */
    protected $context;
    
    /**
     * 
     * A handler for dispatching requests.
     * 
     * @var Aura\Framework\Dispatcher
     * 
     */
    protected $dispatcher;
    
    /**
     * 
     * A handler for rendering content.
     * 
     * @var Aura\Framework\Renderer
     * 
     */
    protected $renderer;
    
    /**
     * 
     * A handler for creating an HTTP response.
     * 
     * @var Aura\Framework\Responder
     * 
     */
    protected $responder;
    
    /**
     * 
     * The transfer object returned from the controller.
     * 
     * @var Aura\Web\ResponseTransfer
     * 
     */
    protected $transfer;
    
    /**
     * 
     * The full HTTP response created from the transfer object.
     * 
     * @var Aura\Http\Response
     * 
     */
    protected $response;
    
    /**
     * 
     * Constructor.
     * 
     */
    public function __construct(
        Context       $context,
        SignalManager $signal,
        Dispatcher    $dispatcher,
        Renderer      $renderer,
        Responder     $responder
    ) {
        $this->context    = $context;
        $this->signal     = $signal;
        $this->dispatcher = $dispatcher;
        $this->renderer   = $renderer;
        $this->responder  = $responder;
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
     * @return Aura\Http\Response
     * 
     * @signal pre_dispatch
     * 
     * @signal post_dispatch
     * 
     * @signal pre_render
     * 
     * @signal post_render
     * 
     * @signal pre_response
     * 
     * @signal post_response
     * 
     */
    public function exec()
    {
        // dispatch to a controller and get back a transfer object
        $this->signal->send($this, 'pre_dispatch', $this);
        $path = $this->context->getServer('PATH_INFO', '/');
        $server = $this->context->getServer();
        $this->transfer = $this->dispatcher->exec($path, $server);
        $this->signal->send($this, 'post_dispatch', $this);
        
        // render the view content from the transfer object
        $this->signal->send($this, 'pre_render', $this);
        $accept = $this->context->getAccept();
        $this->content = $this->renderer->exec($this->transfer, $accept);
        $this->signal->send($this, 'post_render', $this);
        
        // convert the tranfer object and content to a response
        $this->signal->send($this, 'pre_response', $this);
        $this->response = $this->responder->exec($this->transfer);
        $this->signal->send($this, 'post_response', $this);
        
        // done!
        return $this->response;
    }
}
