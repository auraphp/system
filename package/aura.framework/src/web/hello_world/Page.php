<?php
namespace aura\framework\web\hello_world;
use aura\web\Page as WebPage;
class Page extends WebPage
{
    public function actionIndex()
    {
        $this->response->setView('index');
    }
}
