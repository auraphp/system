<?php
namespace Aura\Framework\Web\Hello;
use Aura\Web\Page as WebPage;
class Page extends WebPage
{
    public function actionWorld()
    {
        $this->response->setView('world');
    }
    
    public function actionAsset()
    {
        $this->response->setView('asset');
    }
}
