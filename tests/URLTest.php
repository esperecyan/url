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
            ['Bloß.de'       , 'bloss.de'         ],
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
            ['Bloß.de'       , 'bloss.de'      ],
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
            ['http://url.test/', null                       , 'http://url.test/'                 ],
            ['//url.test/'     , 'http://base.test/'        , 'http://url.test/'                 ],
            ['/path'           , 'http://base.test/foo/bar' , 'http://base.test/path'            ],
            ['filename'        , 'http://base.test/foo/bar' , 'http://base.test/foo/filename'    ],
            ['filename'        , 'http://base.test/foo/bar/', 'http://base.test/foo/bar/filename'],
            [''                , 'http://base.test/foo/bar' , 'http://base.test/foo/bar'         ],
            ['.'               , 'http://base.test/foo/bar' , 'http://base.test/foo/'            ],
        ];
    }
    
    /**
     * @expectedException \PHPUnit_Framework_Error_Warning
     * @expectedExceptionMessage Missing argument 1 for esperecyan\url\URL::__construct()
     */
    public function testMissingArgument()
    {
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
            ['//url.test/'     ],
            ['/path'           ],
            ['filename'        ],
            [''                ],
            ['.'               ],
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
}
