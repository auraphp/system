<?php
namespace Aura\Framework\Web;
use Aura\Web\Page as WebPage;
use Aura\Signal\Manager as SignalManager;
use Aura\Router\Map as RouterMap;

abstract class Page extends WebPage
{
    protected $signal;
    
    protected $router;
    
    public function setSignal(SignalManager $signal)
    {
        $this->signal = $signal;
        $this->signal->handler($this, 'pre_exec', array($this, 'preExec'));
        $this->signal->handler($this, 'pre_action', array($this, 'preAction'));
        $this->signal->handler($this, 'post_action', array($this, 'postAction'));
        $this->signal->handler($this, 'post_exec', array($this, 'postExec'));
    }
    
    public function setRouter(RouterMap $router)
    {
        $this->router = $router;
    }
    
    public function exec()
    {
        $this->signal->send($this, 'pre_exec', $this);
        $this->signal->send($this, 'pre_action', $this);
        $this->action();
        $this->signal->send($this, 'post_action', $this);
        $this->signal->send($this, 'post_exec', $this);
        return $this->response;
    }
}
