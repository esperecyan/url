<?php
namespace esperecyan\url\lib;

class URLUtilsReadOnlyTest extends \PHPUnit_Framework_TestCase
{
    /** @var HTMLAnchorElement */
    private $anchor;
    
    protected function setUp()
    {
        $this->anchor = new HTMLAnchorElement();
        (new \DOMDocument())->appendChild($this->anchor);
    }
    
    /**
     * @param string $propertyName
     * @expectedException \LogicException
     * @expectedExceptionMessage Cannot write to readonly public property esperecyan\url\lib\WorkerLocation::
     * @dataProvider readonlyPropertyNameProvider
     */
    public function testReadonly($propertyName)
    {
        $workerLocation = new WorkerLocation('http://url.test/');
        $workerLocation->{$propertyName} = 'http://url.test';
    }
    
    public function readonlyPropertyNameProvider()
    {
        return [
            ['href'],
            ['origin'],
            ['protocol'],
            ['host'],
            ['hostname'],
            ['port'],
            ['pathname'],
            ['search'],
            ['hash'],
        ];
    }
}
