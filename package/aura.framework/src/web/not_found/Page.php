<?php
namespace aura\framework\web\not_found;
use aura\web\Page as WebPage;
class Page extends WebPage
{
    // force the action to "index"
    public function preExec()
    {
        $this->action = 'index';
    }
    
    public function actionIndex()
    {
        $uri = htmlspecialchars(
            var_export($this->context->getServer('REQUEST_URI'), true)
        );
        
        $path = htmlspecialchars(
            var_export($this->context->getServer('PATH_INFO', '/'), true)
        );
        
        $html = <<<HTML
<html>
    <head>
        <title>Not Found</title>
    </head>
    <body>
        <h1>404 Not Found</h1>
        <p>No controller found for <code>$uri</code></p>
        <p>Please check that your config has:</p>
        <ol>
            <li>An <code>aura\\router\\Map</code> route for the path <code>$path</code></li>
            <li>A <code>['values']['controller']</code> value for the mapped route</li>
            <li>A <code>\$di->params['aura\web\ControllerFactory']['map']</code> entry for the controller value.</li>
        </ol>
    </body>
</html>
HTML;

        $this->response->setContent($html);
        $this->response->setStatusCode(404);
    }
}
