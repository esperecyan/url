<?php
namespace esperecyan\url\lib;

class URLUtilsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param string $href USVString
     * @param string $returnValue USVString
     * @dataProvider hrefProvider
     */
    public function testHref($href, $returnValue)
    {
        $anchor = new HTMLAnchorElement();
        (new \DOMDocument())->appendChild($anchor);
        if (!is_null($href)) {
            $anchor->href = $href;
        }
        $this->assertSame(is_null($href) ? '' : $returnValue, $anchor->getAttribute('href'));
    }
    
    public function hrefProvider()
    {
        return [
            ['http://username:password@url.test:8080/pathname?foobar#hash', 'http://username:password@url.test:8080/pathname?foobar#hash'],
            ['http://URL.テスト/'    , 'http://url.xn--zckzah/'],
            ['http://url.test:80/'   , 'http://url.test/'  ],
            [null                    , ''                  ],
            [''                      , ''                  ],
            ['invalid URL'           , 'invalid URL'       ],
        ];
    }
    
    /**
     * @param string $url USVString
     * @param string $origin USVString
     * @dataProvider originProvider
     */
    public function testOrigin($url, $origin)
    {
        $anchor = new HTMLAnchorElement();
        (new \DOMDocument())->appendChild($anchor);
        if (!is_null($url)) {
            $anchor->setAttribute('href', $url);
        }
        $this->assertSame($origin, $anchor->origin);
    }
    
    public function originProvider()
    {
        return [
            ['blob:https://whatwg.org/d0360e2f-caee-469f-9a2f-87d5b0456f6f', 'https://whatwg.org'    ],
            ['blob:d0360e2f-caee-469f-9a2f-87d5b0456f6f'                   , 'null'                  ],
            ['ftp://username:password@url.test:21/pathname?foobar#hash'    , 'ftp://url.test'        ],
            ['gopher://username:password@url.test:70/pathname?foobar#hash' , 'gopher://url.test'     ],
            ['http://username:password@url.test:8080/pathname?foobar#hash' , 'http://url.test:8080'  ],
            ['HTTP://URL.XN--ZCKZAH/'                                      , 'http://url.テスト'      ],
            ['http://url.test:80/'                                         , 'http://url.test'       ],
            ['https://username:password@url.test:8080/pathname?foobar#hash', 'https://url.test:8080' ],
            ['ws://username:password@url.test:8080/pathname?foobar#hash'   , 'ws://url.test:8080'    ],
            ['wss://username:password@url.test:8080/pathname?foobar#hash'  , 'wss://url.test:8080'   ],
            ['ftp://username:password@url.test:8080/pathname?foobar#hash'  , 'ftp://url.test:8080'   ],
            ['ftp://username:password@url.test:8080/pathname?foobar#hash'  , 'ftp://url.test:8080'   ],
            ['file://directory/filename'                                   , 'null'                  ],
            ['file:///C:/directory/filename'                               , 'null'                  ],
            ['mailto:postmaster@url.test'                                  , 'null'                  ],
            [null                                                          , ''                      ],
            [''                                                            , ''                      ],
            ['invalid URL'                                                 , ''                      ],
        ];
    }
    
    /**
     * @param string $url USVString
     * @param string $protocol USVString
     * @param string $returnValue USVString
     * @dataProvider protocolProvider
     */
    public function testProtocol($url, $protocol, $returnValue)
    {
        $anchor = new HTMLAnchorElement();
        (new \DOMDocument())->appendChild($anchor);
        if (!is_null($url)) {
            $anchor->setAttribute('href', $url);
        }
        $anchor->protocol = $protocol;
        $this->assertSame($returnValue, $anchor->protocol);
    }
    
    public function protocolProvider()
    {
        return [
            ['http://url.test/', 'https'                , 'https:' ],
            ['http://url.test/', 'wss:'                 , 'wss:'   ],
            ['http://url.test/', 'WS'                   , 'ws:'    ],
            ['http://url.test/', ' ftp'                 , 'http:'  ],
            ['http://url.test/', 'gopher'               , 'gopher:'],
            ['http://url.test/', 'wss://foobar.example/', 'wss:'   ],
            ['http://url.test/', 'invalid scheme'       , 'http:'  ],
            [null              , 'http'                 , ':'      ],
            [''                , 'https'                , ':'      ],
            ['invalid URL'     , 'mailto'               , ':'      ],
        ];
    }
    
    /**
     * @param string $url USVString
     * @param string $username USVString
     * @param string $returnValue USVString
     * @dataProvider usernameProvider
     */
    public function testUsername($url, $username, $returnValue, $replacedURL)
    {
        $anchor = new HTMLAnchorElement();
        (new \DOMDocument())->appendChild($anchor);
        if (!is_null($url)) {
            $anchor->setAttribute('href', $url);
        }
        $anchor->username = $username;
        $this->assertSame($returnValue, $anchor->username);
        $this->assertSame($replacedURL, $anchor->href);
        $this->assertSame($replacedURL, $anchor->getAttribute('href'));
        $this->assertSame($replacedURL, (string)$anchor);
    }
    
    public function usernameProvider()
    {
        return [
            ['http://url.test/'             , 'username'   , 'username', 'http://username@url.test/'    ],
            ['http://username@url.test/'    ,         ''   , ''        , 'http://url.test/'             ],
            ['tftp://url.test/'             , 'username'   , 'username', 'tftp://username@url.test/'    ],
            ['tftp://username@url.test/'    ,         ''   , ''        , 'tftp://url.test/'             ],
            ['http://url.test/'             , '/:;=@[\\]^|', '%2F%3A%3B%3D%40%5B%5C%5D%5E%7C', 'http://%2F%3A%3B%3D%40%5B%5C%5D%5E%7C@url.test/'],
            ['file:///C:/directory/filename', 'username'   , ''        , 'file:///C:/directory/filename'],
            [null                           , 'username'   , ''        , ''                             ],
            [''                             , 'username'   , ''        , ''                             ],
            ['invalid URL'                  , 'username'   , ''        , 'invalid URL'                  ],
        ];
    }
    
    /**
     * @param string $url USVString
     * @param string $password USVString
     * @param string $returnValue USVString
     * @dataProvider passwordProvider
     */
    public function testPassword($url, $password, $returnValue, $replacedURL)
    {
        $anchor = new HTMLAnchorElement();
        (new \DOMDocument())->appendChild($anchor);
        if (!is_null($url)) {
            $anchor->setAttribute('href', $url);
        }
        $anchor->password = $password;
        $this->assertSame($returnValue, $anchor->password);
        $this->assertSame($replacedURL, $anchor->href);
        $this->assertSame($replacedURL, $anchor->getAttribute('href'));
        $this->assertSame($replacedURL, (string)$anchor);
    }
    
    public function passwordProvider()
    {
        return [
            ['http://url.test/'                  , 'password'   , 'password'  , 'http://:password@url.test/'        ],
            ['http://username@url.test/'         , 'password'   , 'password'  , 'http://username:password@url.test/'],
            ['http://username:password@url.test/', ''           , ''          , 'http://username@url.test/'         ],
            ['tftp://username@url.test/'         , 'password'   , 'password'  , 'tftp://username:password@url.test/'],
            ['tftp://username:password@url.test/', ''           , ''          , 'tftp://username@url.test/'         ],
            ['http://url.test/'                  , '/:;=@[\\]^|', '%2F%3A%3B%3D%40%5B%5C%5D%5E%7C', 'http://:%2F%3A%3B%3D%40%5B%5C%5D%5E%7C@url.test/'      ],
            ['file:///C:/directory/filename'     , 'password'   , ''          , 'file:///C:/directory/filename'     ],
            [null                                , 'password'   , ''          , ''                                  ],
            [''                                  , 'password'   , ''          , ''                                  ],
            ['invalid URL'                       , 'password'   , ''          , 'invalid URL'                       ],
        ];
    }
    
    /**
     * @param string $url USVString
     * @param string $host USVString
     * @param string $returnValue USVString
     * @dataProvider hostProvider
     */
    public function testHost($url, $host, $returnValue, $replacedURL)
    {
        $anchor = new HTMLAnchorElement();
        (new \DOMDocument())->appendChild($anchor);
        if (!is_null($url)) {
            $anchor->setAttribute('href', $url);
        }
        $anchor->host = $host;
        $this->assertSame($returnValue, $anchor->host);
        $this->assertSame($replacedURL, $anchor->href);
        $this->assertSame($replacedURL, $anchor->getAttribute('href'));
        $this->assertSame($replacedURL, (string)$anchor);
    }
    
    public function hostProvider()
    {
        return [
            ['http://url.test/'     , 'url.テスト'              , 'url.xn--zckzah', 'http://url.xn--zckzah/'          ],
            ['http://url.test:8080/', 'url.テスト/pathname'     , 'url.xn--zckzah:8080', 'http://url.xn--zckzah:8080/'],
            ['http://url.test/'     , 'URL.XN--ZCKZAH:008080'   , 'url.xn--zckzah:8080', 'http://url.xn--zckzah:8080/'],
            ['http://url.test/'     , 'xn--u-ccb.invalid'       , 'url.test'      , 'http://url.test/'                ],
            ['http://url.test/'     , 'url.%e3%83%86%E3%82%B9ト', 'url.xn--zckzah', 'http://url.xn--zckzah/'          ],
            ['http://url.test/'     , '%83e%83X%83g.invalid'    , 'url.test'      , 'http://url.test/'                ],
            ['tftp://url.test/'     , 'standard.test'           , 'standard.test' , 'tftp://standard.test/'           ],
            ['file://directory/filename', 'URL.XN--ZCKZAH'      , 'url.xn--zckzah', 'file://url.xn--zckzah/filename'  ],
            ['file://invalid:8080/filename', 'url.test'         , ''              , 'file://invalid:8080/filename'    ],
            ['file:///C:/directory/filename', 'url.test'        , ''              , 'file:///C:/directory/filename'   ],
            [null                   , 'url.test'                , ''              , ''                                ],
            [''                     , 'url.test'                , ''              , ''                                ],
            ['invalid URL'          , 'url.test'                , ''              , 'invalid URL'                     ],
        ];
    }
    
    /**
     * @param string $url USVString
     * @param string $hostname USVString
     * @param string $returnValue USVString
     * @dataProvider hostnameProvider
     */
    public function testHostname($url, $hostname, $returnValue, $replacedURL)
    {
        $anchor = new HTMLAnchorElement();
        (new \DOMDocument())->appendChild($anchor);
        if (!is_null($url)) {
            $anchor->setAttribute('href', $url);
        }
        $anchor->hostname = $hostname;
        $this->assertSame($returnValue, $anchor->hostname);
        $this->assertSame($replacedURL, $anchor->href);
        $this->assertSame($replacedURL, $anchor->getAttribute('href'));
        $this->assertSame($replacedURL, (string)$anchor);
    }
    
    public function hostnameProvider()
    {
        return [
            ['http://url.test/'            , 'url.テスト'           , 'url.xn--zckzah', 'http://url.xn--zckzah/'      ],
            ['http://url.test:8080/'       , 'url.テスト/pathname'  , 'url.xn--zckzah', 'http://url.xn--zckzah:8080/' ],
            ['http://url.test/'            , 'URL.XN--ZCKZAH:8080'  , 'url.xn--zckzah', 'http://url.xn--zckzah/'      ],
            ['http://url.test/'            , 'xn--u-ccb.invalid'    , 'url.test'      , 'http://url.test/'            ],
            ['http://url.test/'            , 'url.%e3%83%86%E3%82%B9ト', 'url.xn--zckzah', 'http://url.xn--zckzah/'   ],
            ['http://url.test/'            , '%83e%83X%83g.invalid' , 'url.test'      , 'http://url.test/'            ],
            ['tftp://url.test/'            , 'standard.test'        , 'standard.test' , 'tftp://standard.test/'       ],
            ['file://directory/filename' , 'URL.XN--ZCKZAH:008080', 'url.xn--zckzah', 'file://url.xn--zckzah/filename'],
            ['file://invalid:8080/filename', 'url.test'             , ''              , 'file://invalid:8080/filename'],
            [null                          , 'url.test'             , ''              , ''                            ],
            [''                            , 'url.test'             , ''              , ''                            ],
            ['invalid URL'                 , 'url.test'             , ''              , 'invalid URL'                 ],
        ];
    }
    
    /**
     * @param string $url USVString
     * @param string $port USVString
     * @param string $returnValue USVString
     * @dataProvider portProvider
     */
    public function testPort($url, $port, $returnValue, $replacedURL)
    {
        $anchor = new HTMLAnchorElement();
        (new \DOMDocument())->appendChild($anchor);
        if (!is_null($url)) {
            $anchor->setAttribute('href', $url);
        }
        $anchor->port = $port;
        $this->assertSame($returnValue, $anchor->port);
        $this->assertSame($replacedURL, $anchor->href);
        $this->assertSame($replacedURL, $anchor->getAttribute('href'));
        $this->assertSame($replacedURL, (string)$anchor);
    }
    
    public function portProvider()
    {
        return [
            ['http://url.test/'            , '8080'         , '8080', 'http://url.test:8080/'       ],
            ['http://url.test:8080/'       , ''             , '8080', 'http://url.test:8080/'       ],
            ['http://url.test:8080/'       , '80'           , ''    , 'http://url.test/'            ],
            ['http://url.test/'            , '008080'       , '8080', 'http://url.test:8080/'       ],
            ['http://url.test/'            , '8080/pathname', '8080', 'http://url.test:8080/'       ],
            ['http://url.test/'            , '8080テスト'   , '8080', 'http://url.test:8080/'       ],
            ['tftp://url.test/'            , 'standard.test', ''    , 'tftp://url.test/'            ],
            ['file://directory/filename'   , '8080'         , ''    , 'file://directory/filename'   ],
            ['file://invalid:8080/filename', '80'           , ''    , 'file://invalid:8080/filename'],
            ['http://url.test:8080/'       , 'invalid port' , '8080', 'http://url.test:8080/'       ],
            [null                          , '8080'         , ''    , ''                            ],
            [''                            , '8080'         , ''    , ''                            ],
            ['invalid URL'                 , '8080'         , ''    , 'invalid URL'                 ],
        ];
    }
    
    /**
     * @param string $url USVString
     * @param string $pathname USVString
     * @param string $returnValue USVString
     * @dataProvider pathnameProvider
     */
    public function testPathname($url, $pathname, $returnValue, $replacedURL)
    {
        $anchor = new HTMLAnchorElement();
        (new \DOMDocument())->appendChild($anchor);
        if (!is_null($url)) {
            $anchor->setAttribute('href', $url);
        }
        $anchor->pathname = $pathname;
        $this->assertSame($returnValue, $anchor->pathname);
        $this->assertSame($replacedURL, $anchor->href);
        $this->assertSame($replacedURL, $anchor->getAttribute('href'));
        $this->assertSame($replacedURL, (string)$anchor);
    }
    
    public function pathnameProvider()
    {
        return [
            ['http://url.test/'            , '/pathname'      , '/pathname'  , 'http://url.test/pathname'    ],
            ['http://url.test/path/name'   , 'foobar'         , '/foobar'    , 'http://url.test/foobar'      ],
            ['http://url.test/'            , '/path/../name'  , '/name'      , 'http://url.test/name'        ],
            ['http://url.test/'            , '/path/%2E./name', '/name'      , 'http://url.test/name'        ],
            ['http://url.test/'            , '/path/./name'   , '/path/name' , 'http://url.test/path/name'   ],
            ['http://url.test/'            , '/path/name/'    , '/path/name/', 'http://url.test/path/name/'  ],
            ['http://url.test/'            , '/path/name/..'  , '/path/'     , 'http://url.test/path/'       ],
            ['http://url.test/'            , '/path/name/.'   , '/path/name/', 'http://url.test/path/name/'  ],
            ['http://url.test/', '/pathname?query#hash', '/pathname%3Fquery%23hash', 'http://url.test/pathname%3Fquery%23hash'],
            ['http://url.test/'            , '/path\\name'    , '/path/name' , 'http://url.test/path/name'   ],
            ['http://url.test/', '/ !"#$%&\'()*+,-.:;<=>?@[]^_`{|}~', '/%20!%22%23$%&\'()*+,-.:;%3C=%3E%3F@[]^_%60%7B|%7D~', 'http://url.test/%20!%22%23$%&\'()*+,-.:;%3C=%3E%3F@[]^_%60%7B|%7D~'],
            ['http://url.test/'            , '/p%61thname'    , '/p%61thname', 'http://url.test/p%61thname'  ],
            ['tftp://url.test/'            , '/pathname'      , '/pathname'  , 'tftp://url.test/pathname'    ],
            ['file://directory/filename'   , '/pathname'      , '/pathname'  , 'file://directory/pathname'   ],
            ['file://invalid:8080/filename', '/pathname'      , ''           , 'file://invalid:8080/filename'],
            [null                          , '8080'           , ''           , ''                            ],
            [''                            , '8080'           , ''           , ''                            ],
            ['invalid URL'                 , '8080'           , ''           , 'invalid URL'                 ],
        ];
    }
    
    /**
     * @param string $url USVString
     * @param string $search USVString
     * @param string $returnValue USVString
     * @dataProvider searchProvider
     */
    public function testSearch($url, $search, $returnValue, $replacedURL)
    {
        $anchor = new HTMLAnchorElement();
        (new \DOMDocument())->appendChild($anchor);
        if (!is_null($url)) {
            $anchor->setAttribute('href', $url);
        }
        if (!is_null($search)) {
            $anchor->search = $search;
        }
        $this->assertSame($returnValue, $anchor->search);
        $this->assertSame($replacedURL, $anchor->href);
        $this->assertSame(is_null($search) ? $url : $replacedURL, $anchor->getAttribute('href'));
        $this->assertSame($replacedURL, (string)$anchor);
    }
    
    public function searchProvider()
    {
        return [
            ['http://url.test/'       , '?search'  , '?search='   , 'http://url.test/?search='   ],
            ['http://url.test/'       , 'search'   , '?search='   , 'http://url.test/?search='   ],
            ['http://url.test/'       , '??search' , '?%3Fsearch=', 'http://url.test/?%3Fsearch='],
            ['http://url.test/?search', ''         , ''           , 'http://url.test/?'       ], // bug in URL Standard?
            ['http://url.test/?search', '?'        , ''           , 'http://url.test/?'          ],
            ['http://url.test/', '?テスト', '?%E3%83%86%E3%82%B9%E3%83%88=', 'http://url.test/?%E3%83%86%E3%82%B9%E3%83%88='],
            ["http://url.test/?\x01\t\n\r\x1F !\"$%'()*+,-.:;<>?@[]^_`{|}~\x7F", null, '?%01%1F%20!%22$%\'()*+,-.:;%3C%3E?@[]^_`{|}~%7F', 'http://url.test/?%01%1F%20!%22$%\'()*+,-.:;%3C%3E?@[]^_`{|}~%7F'],
            ['http://url.test/', "?\x00\t\n\r\x1F !\"#$%'()*+,-.:;<>?@[]^_`{|}~\x7F", '?%00%09%0A%0D%1F+%21%22%23%24%25%27%28%29*+%2C-.%3A%3B%3C%3E%3F%40%5B%5D%5E_%60%7B%7C%7D%7E%7F=', 'http://url.test/?%00%09%0A%0D%1F+%21%22%23%24%25%27%28%29*+%2C-.%3A%3B%3C%3E%3F%40%5B%5D%5E_%60%7B%7C%7D%7E%7F='],
            ['tftp://url.test/'       , '?search'  , '?search='   , 'tftp://url.test/?search='   ],
            [null                     , '?search'  , ''           , ''                           ],
            [''                       , '?search'  , ''           , ''                           ],
            ['invalid URL'            , '?search'  , ''           , 'invalid URL'                ],
        ];
    }
    
    /**
     * @param string $url USVString
     * @param string $hash USVString
     * @param string $returnValue USVString
     * @dataProvider hashProvider
     */
    public function testHash($url, $hash, $returnValue, $replacedURL)
    {
        $anchor = new HTMLAnchorElement();
        (new \DOMDocument())->appendChild($anchor);
        if (!is_null($url)) {
            $anchor->setAttribute('href', $url);
        }
        $anchor->hash = $hash;
        $this->assertSame($returnValue, $anchor->hash);
        $this->assertSame($replacedURL, $anchor->href);
        $this->assertSame($replacedURL, $anchor->getAttribute('href'));
        $this->assertSame($replacedURL, (string)$anchor);
    }
    
    public function hashProvider()
    {
        return [
            ['http://url.test/'         , '#hash'  , '#hash' , 'http://url.test/#hash'         ],
            ['http://url.test/'         , 'hash'   , '#hash' , 'http://url.test/#hash'         ],
            ['http://url.test/'         , '##hash' , '##hash', 'http://url.test/##hash'        ],
            ['http://url.test/#hash'    , ''       , ''      , 'http://url.test/'              ],
            ['http://url.test/'         , '#'      , ''      , 'http://url.test/#'             ],
            ['http://url.test/'         , '#テスト', '#テスト', 'http://url.test/#テスト'       ],
            ['http://url.test/', '#%E3%83%86%E3%82%B9%E3%83%88', '#%E3%83%86%E3%82%B9%E3%83%88', 'http://url.test/#%E3%83%86%E3%82%B9%E3%83%88'],
            ['http://url.test/', '# !"#$%&\'()*+,-./:;<=>?@[\\]^_`{|}~', '# !"#$%&\'()*+,-./:;<=>?@[\\]^_`{|}~', 'http://url.test/# !"#$%&\'()*+,-./:;<=>?@[\\]^_`{|}~'],
            ['tftp://url.test/'         , '#hash'  , '#hash' , 'tftp://url.test/#hash'         ],
            ['file://directory/filename', '#hash'  , '#hash' , 'file://directory/filename#hash'],
            ['tftp://url.test/'         , '#hash'  , '#hash' , 'tftp://url.test/#hash'         ],
            ['javascript:console.log(\'test\');#hash', '#fragment', '#hash', 'javascript:console.log(\'test\');#hash'],
            [null                       , '#hash'  , ''      , ''                              ],
            [''                         , '#hash'  , ''      , ''                              ],
            ['invalid URL'              , '#hash'  , ''      , 'invalid URL'                   ],
        ];
    }
    
    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Cannot write to readonly public property esperecyan\url\lib\HTMLAnchorElement::origin
     */
    public function testReadonly()
    {
        $anchor = new HTMLAnchorElement();
        (new \DOMDocument())->appendChild($anchor);
        $anchor->origin = 'http://url.test';
    }
}
