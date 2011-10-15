<?php
namespace Aura\Framework\Web\Hello;
use Aura\Framework\Web\AbstractPageTest;
class PageTest extends AbstractPageTest
{
    protected $page_name = 'Hello';
    
    public function testActionWorld()
    {
        $page = $this->newPage(array(
            'action' => 'world',
        ));
        $xfer = $page->exec();
        
        $this->assertType('Aura\Web\ResponseTransfer', $xfer);
        $this->assertSame(200, $xfer->getStatusCode());
        $this->assertSame('world', $xfer->getView());
    }
    
    public function testActionAsset()
    {
        $page = $this->newPage(array(
            'action' => 'asset',
        ));
        $xfer = $page->exec();
        
        $this->assertType('Aura\Web\ResponseTransfer', $xfer);
        $this->assertSame(200, $xfer->getStatusCode());
        $this->assertSame('asset', $xfer->getView());
    }
}
