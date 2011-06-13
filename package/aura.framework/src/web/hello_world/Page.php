<?php
namespace Aura\Framework\Web\hello_world;
use Aura\Web\Page as WebPage;
class Page extends WebPage
{
    public function actionIndex()
    {
        $this->response->setView('index');
    }
}
