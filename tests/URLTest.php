<?php
namespace esperecyan\url;

class URLTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param string $domain USVString
     * @param string $ascii USVString
     * @param string|null $message
     * @dataProvider asciiProvider
     */
    public function testDomainToASCII($domain, $ascii, $message = null)
    {
        $this->assertSame($ascii, URL::domainToASCII($domain), $message);
    }
    
    /**
     * @link http://www.unicode.org/reports/tr46/#Table_Example_Processing UTS #46: Unicode IDNA Compatibility Processing
     */
    public function asciiProvider()
    {
        return [
            ['Bloß.de'       , 'xn--blo-7ka.de'   ],
            ['xn--blo-7ka.de', 'xn--blo-7ka.de'   ],
            ['ü.com'        , 'xn--tda.com'      ], // u + COMBINING DIAERESIS (U+0308) + .com
            ['xn--tda.com'   , 'xn--tda.com'      ],
            ['xn--u-ccb.com' , ''                 ],
            ['a⒈com'        , ''                 ], // a + DIGIT ONE FULL STOP (U+2488)
            ['xn--a-ecp.ru'  , ''                 ],
            ['xn--0.pt'      , ''                 ],
            ['日本語。ＪＰ'  , 'xn--wgv71a119e.jp'],
            ['☕.us'         , 'xn--53h.us'       ], // HOT BEVERAGE (U+2615) + .us
            ['[2001:db8::1]' , ''                 ],
            ['[2001:db8:::1]', ''                 ],
            ['u/r/l?#.test'  , ''                 ],
            ['203.0.113.1'   , ''                 ],
            ['203.000.113.01', ''                 ],
            ['203.0.113.256' , ''                 ],
            ['0xCB007101'    , ''                 ],
        ];
    }

    /**
     * @param string $domain USVString
     * @param string $unicode USVString
     * @param string|null $message
     * @dataProvider unicodeProvider
     */
    public function testDomainToUnicode($domain, $unicode, $message = null)
    {
        $this->assertSame($unicode, URL::domainToUnicode($domain), $message);
    }
    
    /**
     * @link http://www.unicode.org/reports/tr46/#Table_Example_Processing UTS #46: Unicode IDNA Compatibility Processing
     */
    public function unicodeProvider()
    {
        return [
            ['Bloß.de'       , 'bloß.de'       ],
            ['xn--blo-7ka.de', 'bloß.de'       ],
            ['ü.com'        , 'ü.com'         ], // u + COMBINING DIAERESIS (U+0308) + .com
            ['xn--tda.com'   , 'ü.com'         ],
            ['xn--u-ccb.com' , ''              ],
            ['a⒈com'        , ''              ], // a + DIGIT ONE FULL STOP (U+2488)
            ['xn--a-ecp.ru'  , ''              ],
            ['xn--0.pt'      , ''              ],
            ['日本語。ＪＰ'  , '日本語.jp'     ],
            ['☕.us'         , '☕.us'         ], // HOT BEVERAGE (U+2615) + .us
            ['[2001:db8::1]' , ''              ],
            ['[2001:db8:::1]', ''              ],
            ['u/r/l?#.test'  , ''              ],
            ['203.0.113.1'   , ''              ],
            ['203.000.113.01', ''              ],
            ['203.0.113.256' , ''              ],
            ['0xCB007101'    , ''              ],
        ];
    }
    
    /**
     * @param string $url
     * @param string|null $base
     * @param string $string
     * @dataProvider urlProvider
     */
    public function testConstruct($url, $base, $string)
    {
        $this->assertSame($string, (string)(is_null($base) ? new URL($url) : new URL($url, $base)));
    }
    
    public function urlProvider()
    {
        return [
            ['http://url.test/'                  , null                       , 'http://url.test/'                  ],
            ['//url.test/'                       , 'http://base.test/'        , 'http://url.test/'                  ],
            ['/path'                             , 'http://base.test/foo/bar' , 'http://base.test/path'             ],
            ['filename'                          , 'http://base.test/foo/bar' , 'http://base.test/foo/filename'     ],
            ['filename'                          , 'http://base.test/foo/bar/', 'http://base.test/foo/bar/filename' ],
            [''                                  , 'http://base.test/foo/bar' , 'http://base.test/foo/bar'          ],
            ['.'                                 , 'http://base.test/foo/bar' , 'http://base.test/foo/'             ],
            // https://url.spec.whatwg.org/#example-url-parsing
            ['https:example.org'                 , null                       , 'https://example.org/'              ],
            ['https://////example.com///'        , null                       , 'https://example.com///'            ],
            ['https://example.com/././foo'       , null                       , 'https://example.com/foo'           ],
            ['hello:world'                       , 'https://example.com/'     , 'hello:world'                       ],
            ['https:example.org'                 , 'https://example.com/'     , 'https://example.com/example.org'   ],
            ['\\example\\..\\demo/.\\'           , 'https://example.com/'     , 'https://example.com/demo/'         ],
            ['example'                           , 'https://example.com/demo' , 'https://example.com/example'       ],
            ['file:///C|/demo'                   , null                       , 'file:///C:/demo'                   ],
            ['..'                                , 'file:///C:/demo'          , 'file:///C:/'                       ],
            ['file://loc%61lhost/'               , null                       , 'file:///'                          ],
            ['https://user:password@example.org/', null                       , 'https://user:password@example.org/'],
            ['https://example.org/foo bar'       , null                       , 'https://example.org/foo%20bar'     ],
            ['https://EXAMPLE.com/../x'          , null                       , 'https://example.com/x'             ],
            // https://github.com/whatwg/url/commit/fe6b251739e225555f04319f19c70c031a5d99eb
            ['C|'                                , 'file://host/dir/file'     , 'file:///C:'                        ],
        ];
    }
    
    public function testMissingArgument()
    {
        if (class_exists('ArgumentCountError')) {
            // PHP 7.1 or later
            $this->expectException('ArgumentCountError');
            $this->expectExceptionMessage('Too few arguments to function esperecyan\url\URL::__construct(), 0 passed');
        } else {
            // PHP 7.0 or earlier
            $this->expectException('PHPUnit_Framework_Error_Warning');
            $this->expectExceptionMessage('Missing argument 1 for esperecyan\url\URL::__construct()');
        }
        
        new URL();
    }
    
    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Argument 1 passed to esperecyan\url\URL::__construct() is not of the expected type
     */
    public function testInvalidType()
    {
        new URL(new \stdClass());
    }
    
    /**
     * @param string $url
     * @param string|null $base
     * @expectedException \esperecyan\webidl\TypeError
     * @expectedExceptionMessage is not a valid URL
     * @dataProvider invalidHrefProvider
     * @dataProvider invalidURLProvider
     */
    public function testInvalidURL($url, $base = null)
    {
        if (is_null($base)) {
            new URL($url);
        } else {
            new URL($url, $base);
        }
    }
    
    public function invalidHrefProvider()
    {
        return [
            ['//url.test/'                 ],
            ['/path'                       ],
            ['filename'                    ],
            [''                            ],
            ['.'                           ],
            ['file://invalid:8080/filename'],
            // https://url.spec.whatwg.org/#example-url-parsing
            ['https://ex ample.org/'       ],
            ['example'                     ],
            ['https://example.com:demo'    ],
            ['http://[www.example.com]/ '  ],
        ];
    }
    
    public function invalidURLProvider()
    {
        return [
            ['http://url.test/', ''            ],
            ['http://url.test/', '//base.test/'],
        ];
    }
    
    /**
     * @param string $href
     * @expectedException \esperecyan\webidl\TypeError
     * @expectedExceptionMessage is not a valid URL
     * @dataProvider invalidHrefProvider
     */
    public function testInvalidHref($href)
    {
        $url = new URL('http://url.test/');
        $url->href = $href;
    }
    
    /**
     * @param string $href USVString
     * @param string $returnValue USVString
     * @dataProvider hrefProvider
     */
    public function testHref($href, $returnValue)
    {
        $url = new URL($href);
        $this->assertSame($returnValue, $url->href);
    }
    
    public function hrefProvider()
    {
        return [
            ['http://username:password@url.test:8080/pathname?foobar#hash', 'http://username:password@url.test:8080/pathname?foobar#hash'],
            ['http://URL.テスト/'         , 'http://url.xn--zckzah/'     ],
            ['http://url.test:80/'        , 'http://url.test/'           ],
            ['https://username:@url.test/', 'https://username@url.test/' ],
            ['https://:password@url.test/', 'https://:password@url.test/'],
            ['https://:@url.test/'        , 'https://url.test/'          ],
        ];
    }
    
    /**
     * @param string $urlString USVString
     * @param string $origin USVString
     * @dataProvider originProvider
     */
    public function testOrigin($urlString, $origin)
    {
        $url = new URL($urlString);
        $this->assertSame($origin, $url->origin);
    }
    
    public function originProvider()
    {
        return [
            ['blob:https://whatwg.org/d0360e2f-caee-469f-9a2f-87d5b0456f6f', 'https://whatwg.org'    ],
            ['blob:d0360e2f-caee-469f-9a2f-87d5b0456f6f'                   , 'null'                  ],
            ['ftp://username:password@url.test:21/pathname?foobar#hash'    , 'ftp://url.test'        ],
            ['gopher://username:password@url.test:70/pathname?foobar#hash' , 'gopher://url.test'     ],
            ['http://username:password@url.test:8080/pathname?foobar#hash' , 'http://url.test:8080'  ],
            ['HTTP://URL.XN--ZCKZAH/'                                      , 'http://url.xn--zckzah' ],
            ['http://URL.テスト/'                                          , 'http://url.xn--zckzah' ],
            ['http://url.test:80/'                                         , 'http://url.test'       ],
            ['https://username:password@url.test:8080/pathname?foobar#hash', 'https://url.test:8080' ],
            ['ws://username:password@url.test:8080/pathname?foobar#hash'   , 'ws://url.test:8080'    ],
            ['wss://username:password@url.test:8080/pathname?foobar#hash'  , 'wss://url.test:8080'   ],
            ['ftp://username:password@url.test:8080/pathname?foobar#hash'  , 'ftp://url.test:8080'   ],
            ['ftp://username:password@url.test:8080/pathname?foobar#hash'  , 'ftp://url.test:8080'   ],
            ['file://directory/filename'                                   , 'null'                  ],
            ['file:///C:/directory/filename'                               , 'null'                  ],
            ['mailto:postmaster@url.test'                                  , 'null'                  ],
        ];
    }
    
    /**
     * @param string $urlString USVString
     * @param string $protocol USVString
     * @param string $returnValue USVString
     * @param string|null $returnPortValue USVString
     * @dataProvider protocolProvider
     */
    public function testProtocol($urlString, $protocol, $returnValue, $returnPortValue = null)
    {
        $url = new URL($urlString);
        $url->protocol = $protocol;
        $this->assertSame($returnValue, $url->protocol);
        if (isset($returnPortValue)) {
            $this->assertSame($returnPortValue, $url->port);
        }
    }
    
    public function protocolProvider()
    {
        return [
            ['http://url.test/'    , 'https'                , 'https:'     ],
            ['https://url.test:80/', 'http:'                , 'http:'  , ''],
            ['http://url.test/'    , 'wss:'                 , 'wss:'       ],
            ['http://url.test/'    , 'WS'                   , 'ws:'        ],
            ['http://url.test/'    , ' ftp'                 , 'http:'      ],
            ['http://url.test/'    , 'gopher'               , 'gopher:'    ],
            ['http://url.test/'    , 'wss://foobar.example/', 'wss:'       ],
            ['http://url.test/'    , 'invalid scheme'       , 'http:'      ],
        ];
    }
    
    /**
     * @param string $urlString USVString
     * @param string $username USVString
     * @param string $returnValue USVString
     * @dataProvider usernameProvider
     */
    public function testUsername($urlString, $username, $returnValue, $replacedURL)
    {
        $url = new URL($urlString);
        $url->username = $username;
        $this->assertSame($returnValue, $url->username);
        $this->assertSame($replacedURL, $url->href);
        $this->assertSame($replacedURL, (string)$url);
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
        ];
    }
    
    /**
     * @param string $urlString USVString
     * @param string $password USVString
     * @param string $returnValue USVString
     * @dataProvider passwordProvider
     */
    public function testPassword($urlString, $password, $returnValue, $replacedURL)
    {
        $url = new URL($urlString);
        $url->password = $password;
        $this->assertSame($returnValue, $url->password);
        $this->assertSame($replacedURL, $url->href);
        $this->assertSame($replacedURL, (string)$url);
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
        ];
    }
    
    /**
     * @param string $urlString USVString
     * @param string $host USVString
     * @param string $returnValue USVString
     * @dataProvider hostProvider
     */
    public function testHost($urlString, $host, $returnValue, $replacedURL)
    {
        $url = new URL($urlString);
        $url->host = $host;
        $this->assertSame($returnValue, $url->host);
        $this->assertSame($replacedURL, $url->href);
        $this->assertSame($replacedURL, (string)$url);
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
            ['file:///C:/directory/filename', 'url.test'        , 'url.test' , 'file://url.test/C:/directory/filename'],
        ];
    }
    
    /**
     * @param string $urlString USVString
     * @param string $hostname USVString
     * @param string $returnValue USVString
     * @dataProvider hostnameProvider
     */
    public function testHostname($urlString, $hostname, $returnValue, $replacedURL)
    {
        $url = new URL($urlString);
        $url->hostname = $hostname;
        $this->assertSame($returnValue, $url->hostname);
        $this->assertSame($replacedURL, $url->href);
        $this->assertSame($replacedURL, (string)$url);
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
            ['file://directory/filename'   , 'url.test:8080'        , 'directory'     , 'file://directory/filename'   ],
            ['file://directory/filename'   , 'url.test'             , 'url.test'      , 'file://url.test/filename'    ],
        ];
    }
    
    /**
     * @param string $urlString USVString
     * @param string $port USVString
     * @param string $returnValue USVString
     * @dataProvider portProvider
     */
    public function testPort($urlString, $port, $returnValue, $replacedURL)
    {
        $url = new URL($urlString);
        $url->port = $port;
        $this->assertSame($returnValue, $url->port);
        $this->assertSame($replacedURL, $url->href);
        $this->assertSame($replacedURL, (string)$url);
    }
    
    public function portProvider()
    {
        return [
            ['http://url.test/'            , '8080'         , '8080', 'http://url.test:8080/'       ],
            ['http://url.test:8080/'       , ''             , ''    , 'http://url.test/'            ],
            ['http://url.test:8080/'       , '80'           , ''    , 'http://url.test/'            ],
            ['http://url.test/'            , '008080'       , '8080', 'http://url.test:8080/'       ],
            ['http://url.test/'            , '8080/pathname', '8080', 'http://url.test:8080/'       ],
            ['http://url.test/'            , '8080テスト'   , '8080', 'http://url.test:8080/'       ],
            ['tftp://url.test/'            , 'standard.test', ''    , 'tftp://url.test/'            ],
            ['file://directory/filename'   , '8080'         , ''    , 'file://directory/filename'   ],
            ['http://url.test:8080/'       , 'invalid port' , '8080', 'http://url.test:8080/'       ],
        ];
    }
    
    /**
     * @param string $urlString USVString
     * @param string $pathname USVString
     * @param string $returnValue USVString
     * @dataProvider pathnameProvider
     */
    public function testPathname($urlString, $pathname, $returnValue, $replacedURL)
    {
        $url = new URL($urlString);
        $url->pathname = $pathname;
        $this->assertSame($returnValue, $url->pathname);
        $this->assertSame($replacedURL, $url->href);
        $this->assertSame($replacedURL, (string)$url);
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
        ];
    }
    
    /**
     * @param string $urlString USVString
     * @param string $search USVString
     * @param string $returnValue USVString
     * @dataProvider searchProvider
     */
    public function testSearch($urlString, $search, $returnValue, $replacedURL)
    {
        $url = new URL($urlString);
        if (!is_null($search)) {
            $url->search = $search;
        }
        $this->assertSame($returnValue, $url->search);
        $this->assertSame($replacedURL, $url->href);
        $this->assertSame($replacedURL, (string)$url);
    }
    
    public function searchProvider()
    {
        return [
            ['http://url.test/'       , '?search'  , '?search' , 'http://url.test/?search'   ],
            ['http://url.test/'       , 'search'   , '?search' , 'http://url.test/?search'   ],
            ['http://url.test/'       , '??search' , '??search', 'http://url.test/??search'],
            ['http://url.test/?search', ''         , ''        , 'http://url.test/'          ],
            ['http://url.test/?search', '?'        , ''        , 'http://url.test/?'         ],
            ['http://url.test/', '?テスト', '?%E3%83%86%E3%82%B9%E3%83%88', 'http://url.test/?%E3%83%86%E3%82%B9%E3%83%88'],
            ["http://url.test/?\x01\t\n\r\x1F !\"$%'()*+,-.:;<>?@[]^_`{|}~\x7F", null, '?%01%1F%20!%22$%\'()*+,-.:;%3C%3E?@[]^_`{|}~%7F', 'http://url.test/?%01%1F%20!%22$%\'()*+,-.:;%3C%3E?@[]^_`{|}~%7F'],
            ['http://url.test/', "?\x00\t\n\r\x1F !\"#$%'()*+,-.:;<>?@[]^_`{|}~\x7F", '?%00%1F%20!%22%23$%\'()*+,-.:;%3C%3E?@[]^_`{|}~%7F', 'http://url.test/?%00%1F%20!%22%23$%\'()*+,-.:;%3C%3E?@[]^_`{|}~%7F'],
            ['tftp://url.test/'       , '?search'  , '?search' , 'tftp://url.test/?search'   ],
        ];
    }
    
    /**
     * @param string $urlString USVString
     * @param string $hash USVString
     * @param string $returnValue USVString
     * @dataProvider hashProvider
     */
    public function testHash($urlString, $hash, $returnValue, $replacedURL)
    {
        $url = new URL($urlString);
        $url->hash = $hash;
        $this->assertSame($returnValue, $url->hash);
        $this->assertSame($replacedURL, $url->href);
        $this->assertSame($replacedURL, (string)$url);
    }
    
    public function hashProvider()
    {
        return [
            ['http://url.test/'         , '#hash'  , '#hash' , 'http://url.test/#hash'         ],
            ['http://url.test/'         , 'hash'   , '#hash' , 'http://url.test/#hash'         ],
            ['http://url.test/'         , '##hash' , '##hash', 'http://url.test/##hash'        ],
            ['http://url.test/#hash'    , ''       , ''      , 'http://url.test/'              ],
            ['http://url.test/'         , '#'      , ''      , 'http://url.test/#'             ],
            ['http://url.test/'         , '#テスト', '#%E3%83%86%E3%82%B9%E3%83%88', 'http://url.test/#%E3%83%86%E3%82%B9%E3%83%88'],
            ['http://url.test/', '#%E3%83%86%E3%82%B9%E3%83%88', '#%E3%83%86%E3%82%B9%E3%83%88', 'http://url.test/#%E3%83%86%E3%82%B9%E3%83%88'],
            ['http://url.test/', '# !"#$%&\'()*+,-./:;<=>?@[\\]^_`{|}~', '# !"#$%&\'()*+,-./:;<=>?@[\\]^_`{|}~', 'http://url.test/# !"#$%&\'()*+,-./:;<=>?@[\\]^_`{|}~'],
            ['tftp://url.test/'         , '#hash'  , '#hash' , 'tftp://url.test/#hash'         ],
            ['file://directory/filename', '#hash'  , '#hash' , 'file://directory/filename#hash'],
            ['tftp://url.test/'         , '#hash'  , '#hash' , 'tftp://url.test/#hash'         ],
            ['javascript:console.log(\'test\');#hash', '#fragment', '#fragment', 'javascript:console.log(\'test\');#fragment'],
        ];
    }
    
    /**
     * @param string $urlString USVString
     * @param string|null $propertyName
     * @param string|null $value USVString
     * @param string[] $pairs USVString
     * @dataProvider searchParamsProvider
     */
    public function testSearchParams($urlString, $propertyName, $value, $pairs)
    {
        $url = new URL($urlString);
        if ($propertyName) {
            $url->{$propertyName} = $value;
        }
        $this->assertEquals($pairs, iterator_to_array($url->searchParams));
    }
    
    public function searchParamsProvider()
    {
        return [
            ['https://url.test/?name=value' , null    , null                          , ['name' => 'value'] ],
            ['https://url.test/??name=value', null    , null                          , ['?name' => 'value']],
            ['https://url.test/'            , 'search',                  '?name=value', ['name' => 'value'] ],
            ['https://url.test/'            , 'href'  , 'https://url.test/?name=value', ['name' => 'value'] ],
        ];
    }
    
    public function testJsonSerialize()
    {
        $this->assertSame('"https://url.test/"', json_encode(new URL('https://url.test/'), JSON_UNESCAPED_SLASHES));
    }
    
    /**
     * @param string $propertyName
     * @expectedException \LogicException
     * @expectedExceptionMessage Cannot write to readonly public property esperecyan\url\URL::
     * @dataProvider readonlyProvider
     */
    public function testReadonly($propertyName)
    {
        $url = new URL('http://url.test/');
        $url->$propertyName = 'http://url.test';
    }
    
    public function readonlyProvider()
    {
        return [
            ['origin'],
            ['searchParams'],
        ];
    }
}
