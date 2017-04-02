<?php
namespace esperecyan\url\lib;

class URLTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param string $url
     * @param boolean $special
     * @param boolean $local
     * @dataProvider schemeProvider
     */
    public function testIsSpecial($url, $special, $local)
    {
        $this->assertSame($special, URL::parseURL($url)->isSpecial());
    }
    /**
     * @param string $url
     * @param boolean $special
     * @param boolean $local
     * @dataProvider schemeProvider
     */
    public function testIsLocal($url, $special, $local)
    {
        $this->assertSame($local, URL::parseURL($url)->isLocal());
    }
    
    public function schemeProvider()
    {
        return [
            ['ftp://url.test/'       , true , false],
            ['file://url.test/'      , true , false],
            ['gopher://url.test/'    , true , false],
            ['http://url.test/'      , true , false],
            ['https://url.test/'     , true , false],
            ['ws://url.test/'        , true , false],
            ['wss://url.test/'       , true , false],
            ['about://url.test/'     , false, true ],
            ['blob://url.test/'      , false, true ],
            ['data://url.test/'      , false, true ],
            ['filesystem://url.test/', false, true ],
            ['javascript://url.test/', false, false],
            ['chrome://url.test/'    , false, false],
            ['tftp://url.test/'      , false, false],
        ];
    }

    /**
     * @param string $url
     * @param boolean $includingCredentials
     * @dataProvider credentialsProvider
     */
    public function testIsIncludingCredentials($url, $includingCredentials)
    {
        $this->assertSame($includingCredentials, URL::parseURL($url)->isIncludingCredentials());
    }
    
    public function credentialsProvider()
    {
        return [
            ['http://url.test/'                  , false],
            ['http://username:password@url.test/', true ],
            ['http://username@url.test/'         , true ],
            ['http://:password@url.test/'        , true ],
            ['http://username:@url.test/'        , true ],
            ['http://:@url.test/'                , true ],
            ['http://0@url.test/'                , true ],
            ['http://:0@url.test/'               , true ],
        ];
    }

    /**
     * @param string $url
     * @param string[] $path
     * @dataProvider pathProvider
     */
    public function testShortenPath($url, $path)
    {
        $parsedURL = URL::parseURL($url);
        $parsedURL->shortenPath();
        $this->assertSame($path, $parsedURL->path);
    }

    /**
     * @param string $url
     * @param string[] $path
     * @dataProvider pathProvider
     */
    public function testPopPath($url, $path)
    {
        $parsedURL = URL::parseURL($url);
        $parsedURL->popPath();
        $this->assertSame($path, $parsedURL->path);
    }
    
    public function pathProvider()
    {
        return [
            ['http://url.test/'           , []                 ],
            ['http://url.test/foo'        , []                 ],
            ['http://url.test/foo/'       , ['foo']            ],
            ['http://url.test/foo/bar'    , ['foo']            ],
            ['file:///directory/file'     , ['directory']      ],
            ['file:///C:\\directory\\file', ['C:', 'directory']],
            ['file:///C:'                 , ['C:']             ],
            ['file:///C:\\'               , ['C:']             ],
            ['http://url.test/c:'         , []                 ],
        ];
    }
    
    /**
     * @param string $input
     * @param URL|null $base
     * @param string $encodingOverride
     * @param URL|null $url
     * @param string $stateOverride
     * @param array|false $expectedComponents
     * @param string|null $message
     * @dataProvider basicUrlProvider
     */
    public function testParseURL($input, $base, $encodingOverride, $url, $stateOverride, $expectedComponents, $message = null)
    {
        if ($message) {
            $this->markTestSkipped($message);
        }
        
        $returnValue = URL::parseURL($input, $base, $encodingOverride);
        
        if ($expectedComponents === false) {
            $this->assertFalse($returnValue, $message);
        } else {
            if (is_null($url)) {
                $this->assertInstanceOf(__NAMESPACE__ . '\\URL', $returnValue);
                $url = $returnValue;
            } else {
                $this->assertNull($returnValue, $message);
            }
            
            foreach ($expectedComponents as $name => $value) {
                $components[$name] = $url->{$name};
            }
            $this->assertEquals($expectedComponents, $components, $message);
        }
    }
    
    /**
     * @param string $input
     * @param URL|null $base
     * @param string $encodingOverride
     * @param URL|null $url
     * @param string $stateOverride
     * @param array|false $expectedComponents
     * @param string|null $message
     * @dataProvider basicUrlProvider
     */
    public function testParseBasicURL($input, $base, $encodingOverride, $url, $stateOverride, $expectedComponents, $message = null)
    {
        if ($message) {
            $this->markTestSkipped($message);
        }
        
        $returnValue = URL::parseBasicURL($input, $base, $encodingOverride, !is_null($url) ? [
            'url' => URL::parseURL($url),
            'state override' => $stateOverride,
        ] : null);
        
        if ($expectedComponents === false) {
            $this->assertFalse($returnValue, $message);
        } else {
            if (is_null($url)) {
                $this->assertInstanceOf(__NAMESPACE__ . '\\URL', $returnValue);
                $url = $returnValue;
            } else {
                $this->assertNull($returnValue, $message);
            }
            
            foreach ($expectedComponents as $name => $value) {
                $components[$name] = $url->{$name};
            }
            $this->assertEquals($expectedComponents, $components, $message);
        }
    }
    
    public function basicUrlProvider()
    {
        return [
            ["\t\n\f\r http://url.test:80/\t\n\f\r ", null, null, null, null, [
                'scheme' => 'http',
                'nonRelativeFlag' => false, // Deprecated
                'cannotBeABaseURLFlag' => false,
                'host' => 'url.test',
                'port' => null,
                'path' => [''],
            ]],
            ["h\tt\rt\np:/\t/url.test/", null, null, null, null, [
                'scheme' => 'http',
                'host' => 'url.test',
                'path' => [''],
            ]],
            ["ht tp://url.test/", null, null, null, null, false],
            ["ht\ttp://url.test/", URL::parseURL('http://base.test/'), null, null, null, [
                'scheme' => 'http',
                'host' => 'url.test',
                'path' => [''],
            ]],
            ['ht tp://url.test/', URL::parseURL('http://base.test/'), null, null, null, [
                'scheme' => 'http',
                'host' => 'base.test',
                'path' => ['ht%20tp:' , '', 'url.test', ''],
            ]],
            ['//url.test/', URL::parseURL('http://base.test/'), null, null, null, [
                'scheme' => 'http',
                'host' => 'url.test',
            ]],
            ['//url.test/', null, null, null, null, false],
            ['//url.test/', URL::parseURL('tftp://base.test/'), null, null, null, [
                'scheme' => 'tftp',
                'host' => 'url.test',
            ]],
            ['http://url.test/?テスト', null, null, null, null, [
                'query' => '%E3%83%86%E3%82%B9%E3%83%88',
            ]],
            ['http://url.test/?テスト', null, 'shift_jis', null, null, [
                'query' => '%83e%83X%83g',
            ]],
            ['ws://url.test/?テスト', null, 'shift_jis', null, null, [
                'query' => '%E3%83%86%E3%82%B9%E3%83%88',
            ]],
            ['tftp://url.test/?テスト', null, 'shift_jis', null, null, [
                'query' => '%E3%83%86%E3%82%B9%E3%83%88',
            ]],
            ['http://url.test/?%E3%83%86%E3%82%B9%E3%83%88', null, 'shift_jis', null, null, [
                'query' => '%E3%83%86%E3%82%B9%E3%83%88',
            ]],
            ['http://url.test/?🍐 (PEAR)', null, null, null, null, [
                'query' => '%F0%9F%8D%90%20(PEAR)',
            ]],
            ['http://url.test/?🍐 (PEAR)', null, 'shift_jis', null, null, [
                'query' => '%26%23127824%3B%25%20(PEAR)', // &127824; (PEAR)
            ], 'The test is fault because the method depends mbstring or iconv module and it doesn\'t support "HTML" error mode.'],
            ['file:///directory/file', null, null, null, null, [
                'scheme' => 'file',
                'nonRelativeFlag' => false, // Deprecated
                'cannotBeABaseURLFlag' => false,
                'host' => null,
                'port' => null,
                'path' => ['directory', 'file'],
            ]],
            ['file://LOCALHOST/directory/file', null, null, null, null, [
                'scheme' => 'file',
                'nonRelativeFlag' => false, // Deprecated
                'cannotBeABaseURLFlag' => false,
                'host' => null,
                'port' => null,
                'path' => ['directory', 'file'],
            ]],
            ['https://url%2Etest/path%2Eexte%6Esion#%2E.', null, null, null, null, [
                'host' => 'url.test',
                'path' => ['path.exte%6Esion'],
                'fragment' => '%2E.',
            ]],
            ['http://url.test/?query', null, 'utf-16be', null, null, [
                'query' => 'query',
            ]],
            ['https://url.テスト/', null, null, null, null, [
                'host' => 'url.xn--zckzah',
            ]],
            ['example://url.テスト/', null, null, null, null, [
                'host' => 'url.%E3%83%86%E3%82%B9%E3%83%88',
            ]],
        ];
    }
    
    /**
     * @param string $url
     * @param boolean $excludeFragmentFlag
     * @param string $output
     * @dataProvider urlAndExcludeFragmentFlagProvider
     */
    public function testSerializeURL($url, $excludeFragmentFlag, $output)
    {
        $this->assertSame($output, URL::parseURL($url)->serializeURL($excludeFragmentFlag));
    }
    
    public function urlAndExcludeFragmentFlagProvider()
    {
        return [
            ['http://url.test/#fragment'      , false, 'http://url.test/#fragment'    ],
            ['http://url.test/#fragment'      , true , 'http://url.test/'             ],
            ['file:///C:\\directory\\filename', false, 'file:///C:/directory/filename'],
            ['file:/C:\\directory\\filename'  , false, 'file:///C:/directory/filename'],
            
            // invalid arguments
            ['http://url.test/#fragment', null , 'http://url.test/#fragment'],
            ['http://url.test/#fragment', '0'  , 'http://url.test/#fragment'],
        ];
    }
    
    /**
     * @param string $url
     * @param (string|int|null)[]|null $origin
     * @dataProvider originProvider
     */
    public function testGetOrigin($url, $origin)
    {
        $urlObject = URL::parseURL($url);
        $this->assertNotFalse($urlObject);
        if (is_null($origin)) {
            $this->assertRegExp('/^.{23}$/', $urlObject->getOrigin());
            $this->assertNotEquals($urlObject->getOrigin(), $urlObject->getOrigin());
        } else {
            $this->assertEquals($origin, $urlObject->getOrigin());
        }
    }
    
    public function originProvider()
    {
        return [
            ['blob:https://whatwg.org/d0360e2f-caee-469f-9a2f-87d5b0456f6f', ['https' , 'whatwg.org'    ,  443, null]],
            ['blob:d0360e2f-caee-469f-9a2f-87d5b0456f6f'                   , null                                    ],
            ['ftp://username:password@url.test:21/pathname?foobar#hash'    , ['ftp'   , 'url.test'      ,   21, null]],
            ['gopher://username:password@url.test:21/pathname?foobar#hash' , ['gopher', 'url.test'      ,   21, null]],
            ['http://username:password@url.test:8080/pathname?foobar#hash' , ['http'  , 'url.test'      , 8080, null]],
            ['HTTP://URL.テスト/'                                          , ['http'  , 'url.xn--zckzah',   80, null]],
            ['http://url.test:80/'                                         , ['http'  , 'url.test'      ,   80, null]],
            ['https://username:password@url.test:8080/pathname?foobar#hash', ['https' , 'url.test'      , 8080, null]],
            ['ws://username:password@url.test:8080/pathname?foobar#hash'   , ['ws'    , 'url.test'      , 8080, null]],
            ['wss://username:password@url.test:8080/pathname?foobar#hash'  , ['wss'   , 'url.test'      , 8080, null]],
            ['ftp://username:password@url.test:8080/pathname?foobar#hash'  , ['ftp'   , 'url.test'      , 8080, null]],
            ['ftp://username:password@url.test:8080/pathname?foobar#hash'  , ['ftp'   , 'url.test'      , 8080, null]],
            ['file://directory/filename'                                   , null                                    ],
            ['file:///C:/directory/filename'                               , null                                    ],
            ['mailto:postmaster@url.test'                                  , null                                    ],
        ];
    }
    
    /**
     * @param string $username
     * @param string $result
     * @dataProvider usernameProvider
     */
    public function testSetUsername($username, $result)
    {
        $url = URL::parseURL('http://url.test/');
        $url->setUsername($username);
        $this->assertSame($result, $url->username);
    }
    
    public function usernameProvider()
    {
        return [
            ['username', 'username'],
            ['ユーザー名', '%E3%83%A6%E3%83%BC%E3%82%B6%E3%83%BC%E5%90%8D'],
            ['', ''],
            [' !"#$%&\'()*+,-./:;<=>?@[\\]^_`{|}~', '%20!%22%23$%&\'()*+,-.%2F%3A%3B%3C%3D%3E%3F%40%5B%5C%5D%5E_%60%7B%7C%7D~'],
        ];
    }
    
    /**
     * @param string $password
     * @param string $result
     * @dataProvider passwordProvider
     */
    public function testSetpassword($password, $result)
    {
        $url = URL::parseURL('http://url.test/');
        $url->setPassword($password);
        $this->assertSame($result, $url->password);
    }
    
    public function passwordProvider()
    {
        return [
            ['password', 'password'],
            ['パスワード', '%E3%83%91%E3%82%B9%E3%83%AF%E3%83%BC%E3%83%89'],
            ['', null],
            [' !"#$%&\'()*+,-./:;<=>?@[\\]^_`{|}~', '%20!%22%23$%&\'()*+,-.%2F%3A%3B%3C%3D%3E%3F%40%5B%5C%5D%5E_%60%7B%7C%7D~'],
        ];
    }
}
