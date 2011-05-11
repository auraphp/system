<?php
namespace aura\framework\example;
use aura\web\Page;
class HelloWorld extends Page
{
    public function actionIndex()
    {
        $this->response->setView('index');
    }
}
