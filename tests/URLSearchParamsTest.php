<?php
namespace esperecyan\url;

class URLSearchParamsTest extends \PHPUnit_Framework_TestCase
{
    public function testAppend()
    {
        $params = new URLSearchParams();
        $params->append('pear', '🍐');
        $params->append('pear', '梨');
        $params->append('%20', '#/ !"#$%&\'()*+,-./:;<=>?@[\\]^_`{|}~');
        $params->append('', '');
        
        $this->assertSame('pear=%F0%9F%8D%90&pear=%E6%A2%A8&%2520=%23%2F+%21%22%23%24%25%26%27%28%29*%2B%2C-.%2F%3A%3B%3C%3D%3E%3F%40%5B%5C%5D%5E_%60%7B%7C%7D%7E&=', (string)$params);
        $this->assertSame([
            'pear' => '梨',
            '%20' => '#/ !"#$%&\'()*+,-./:;<=>?@[\\]^_`{|}~',
            '' => '',
        ], iterator_to_array($params));
    }

    public function testDelete()
    {
        $params = new URLSearchParams('pear=%F0%9F%8D%90&pear=%E6%A2%A8&%2520=+&=');
        $params->delete('pear');
        
        $this->assertSame('%2520=+&=', (string)$params, var_export($params, true));
        $this->assertSame([
            '%20' => ' ',
            '' => '',
        ], iterator_to_array($params));
    }

    public function testGet()
    {
        $params = new URLSearchParams('pear=%F0%9F%8D%90&pear=%E6%A2%A8&%2520=+&=');
        $this->assertSame('🍐', $params->get('pear'));
        $this->assertNull($params->get('test'));
    }

    public function testGetAll()
    {
        $params = new URLSearchParams('pear=%F0%9F%8D%90&pear=%E6%A2%A8&%2520=+&=');
        $this->assertSame(['🍐', '梨'], $params->getAll('pear'));
        $this->assertSame([], $params->getAll('test'));
    }

    public function testHas()
    {
        $params = new URLSearchParams('pear=%F0%9F%8D%90&pear=%E6%A2%A8&%2520=+&=');
        $this->assertTrue($params->has('pear'));
        $this->assertFalse($params->has('test'));
    }
    
    public function testSet()
    {
        $params = new URLSearchParams();
        $params->append('%20', ' ');
        $params->append('pear', '🍐');
        $params->append('', '');
        $params->append('pear', '梨');
        $params->set('pear', '');
        $params->set('test', '');
        
        $this->assertSame('%2520=+&pear=&=&test=', (string)$params);
        $this->assertSame([
            '%20' => ' ',
            'pear' => '',
            '' => '',
            'test' => '',
        ], iterator_to_array($params));
    }
    
    public function testUpdateSteps()
    {
        $url = new URL('http://url.test/');
        $url2 = new URL('http://url2.test/');
        $anchor = new lib\HTMLAnchorElement();
        (new \DOMDocument())->appendChild($anchor);
        
        $anchor->searchParams = $url->searchParams = $url2->searchParams;
        $this->assertSame($anchor->searchParams, $url->searchParams);
        
        $anchor->href = 'http://anchor.test/?name=value';
        $this->assertSame('', $url->search);
        
        $anchor->search = 'pear=%F0%9F%8D%90&pear=%E6%A2%A8&%2520=+&=';
        $this->assertSame([
            'pear' => '梨',
            '%20' => ' ',
            '' => '',
        ], iterator_to_array($url->searchParams));
        $this->assertSame($anchor->search, $url->search);
        $this->assertSame('http://url.test/?pear=%F0%9F%8D%90&pear=%E6%A2%A8&%2520=+&=', $url->href);
        $this->assertSame('http://url2.test/?pear=%F0%9F%8D%90&pear=%E6%A2%A8&%2520=+&=', $url2->href);
    }
    
    public function testIterable()
    {
        $params = new URLSearchParams('key1=value1&key2=value2&key3=value3&key4=value4');
        $this->assertCount(4, $params);
        
        $i = 0;
        foreach ($params as $value) {
            foreach ($params as $value) {
                $i++;
            }
        }
        $this->assertSame(16, $i);
        
        foreach ($params as $key => $value) {
            $params->delete($key);
            $values[$key] = $value;
        }
        $this->assertSame([
            'key1' => 'value1',
            'key3' => 'value3',
        ], $values);
    }
}
