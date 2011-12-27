<?php
namespace Aura\Framework\Web\Hello;
use Aura\Framework\Web\Page as WebPage;
class Page extends WebPage
{
    public function actionWorld()
    {
        $this->view->setInnerView('world');
    }
    
    public function actionAsset()
    {
        $this->view->setInnerView('asset');
    }
}
