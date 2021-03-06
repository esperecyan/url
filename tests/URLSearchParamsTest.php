<?php
namespace esperecyan\url;

class URLSearchParamsTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @param string[][]|string[]|string $init
     * @param string|false $serialized
     * @dataProvider urlProvider
     */
    public function testConstruct($init, $serialized)
    {
        if ($serialized === false) {
            $this->expectException('esperecyan\webidl\TypeError');
        }
        $this->assertSame($serialized, (string)new URLSearchParams($init));
    }
    
    public function urlProvider()
    {
        return [
            [[['name', 'value1'], ['name', 'value2']]                      , 'name=value1&name=value2'  ],
            [[['name', 'value']]                                           , 'name=value'               ],
            [[['name', 'value', 'value']]                                  , false                      ],
            [[['name']]                                                    , false                      ],
            [['name']                                                      , false                      ],
            [['name1' => 'value1', 'name2' => 'value2']                    , 'name1=value1&name2=value2'],
            [['value', 2 => 'value']                                       , '0=value&2=value'          ],
            ['name=value&name=値'                                          , 'name=value&name=%E5%80%A4'],
            [new URLSearchParams('name=value1&name=value2')                , 'name=value1&name=value2'  ],
            [(object)['name1' => 'value1', 'name2' => 'value2']            , 'name1=value1&name2=value2'],
            [new \ArrayIterator(['name1' => 'value1', 'name2' => 'value2']), 'name1=value1&name2=value2'],
        ];
    }
    
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
        $params = new URLSearchParams('?pear=%F0%9F%8D%90&pear=%E6%A2%A8&%2520=+&=');
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
    
    public function testSort()
    {
        $params = new URLSearchParams([
            'a'  => 'U+0061',
            'α' => 'U+03B1',
            '〜' => 'U+301C',
            '！' => 'U+FF01',
            '𩸽' => 'U+29E3D',
        ]);
        
        $params->sort();
        
        // assertEquals does not check key order
        $this->assertSame([
            'a'  => 'U+0061',
            'α' => 'U+03B1',
            '〜' => 'U+301C',
            '𩸽' => 'U+29E3D',
            '！' => 'U+FF01',
        ], iterator_to_array($params));
    }
    
    public function testUpdateSteps()
    {
        $url = new URL('http://anchor.test/?name=value');
        $url->searchParams->append('name', 'value');
        $url->searchParams->append('name2', 'value2');
        $url->searchParams->append('name2', 'value2');
        $url->searchParams->append('name3', 'value3');
        $this->assertSame(
            'http://anchor.test/?name=value&name=value&name2=value2&name2=value2&name3=value3',
            $url->href
        );
        
        $url->searchParams->delete('name', 'value');
        $this->assertSame('http://anchor.test/?name2=value2&name2=value2&name3=value3', $url->href);
        
        $url->searchParams->set('name2', 'value4');
        $this->assertSame('http://anchor.test/?name2=value4&name3=value3', $url->href);
        
        $url->search = 'pear=%F0%9F%8D%90&pear=%E6%A2%A8&%2520=+&=';
        $this->assertSame([
            'pear' => '梨',
            '%20' => ' ',
            '' => '',
        ], iterator_to_array($url->searchParams));
        
        $queryTestURL = new URL('https://query.test/?name=value');
        $queryTestURL->searchParams->delete('name');
        $this->assertSame('https://query.test/', $queryTestURL->href);
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
