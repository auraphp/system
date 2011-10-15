<?php
/**
 * 
 * This file is part of the Aura project for PHP.
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 */
namespace Aura\Framework;
use Aura\Http\Response;
use Aura\Web\ResponseTransfer;

/**
 * 
 * Dispatches a Route to a Controller, then uses the returned ResponseTransfer
 * to render a TwoStepView into an HttpResponse.
 * 
 * @package Aura.Framework
 * 
 */
class Responder implements ResponderInterface
{
    /**
     * 
     * An HTTP response object for sending the response.
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
    public function __construct(Response $response)
    {
        $this->response = $response;
    }
    
    /**
     * 
     * Moves the ResponseTransfer data into the HTTP response.
     * 
     * @return void
     * 
     */
    public function exec(ResponseTransfer $transfer)
    {
        $this->response->setVersion($transfer->getVersion());
        $this->response->setStatusCode($transfer->getStatusCode());
        $this->response->setStatusText($transfer->getStatusText());
        $this->response->headers->setAll($transfer->getHeaders());
        $this->response->cookies->setAll($transfer->getCookies());
        $this->response->setContent($transfer->getContent());
        return $this->response;
    }
}
