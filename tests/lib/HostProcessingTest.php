<?php
namespace esperecyan\url\lib;

class HostProcessingTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param string $domain
     * @param string|false $ascii
     * @param string|null $message
     * @dataProvider asciiProvider
     */
    public function testDomainToASCII($domain, $ascii, $message = null)
    {
        if ($message) {
            $this->markTestSkipped($message);
        }
        $this->assertSame($ascii, HostProcessing::domainToASCII($domain), $message);
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
            ['xn--u-ccb.com' , false              ],
            ['a⒈com'        , false              ], // a + DIGIT ONE FULL STOP (U+2488)
            ['xn--a-ecp.ru'  , false              ],
            ['xn--0.pt'      , false              ],
            ['日本語。ＪＰ'   , 'xn--wgv71a119e.jp'],
            ['☕.us'         , 'xn--53h.us'       ], // HOT BEVERAGE (U+2615) + .us
            ['[2001:db8::1]' , '[2001:db8::1]'    ],
            ['[2001:db8:::1]', '[2001:db8:::1]'   ],
            ['u/r/l?#.test'  , 'u/r/l?#.test'     ],
            ['203.0.113.1'   , '203.0.113.1'      ],
            ['203.000.113.01', '203.000.113.01'   ],
            [str_repeat('a', 249) . '.test', str_repeat('a', 249) . '.test',
                'The test is fault because the method depends Intl module and it doesn\'t support "false" VerifyDnsLength.'],
            [str_repeat('a', 250) . '.test', str_repeat('a', 250) . '.test',
                'The test is fault because the method depends Intl module and it doesn\'t support "false" VerifyDnsLength.'],
            [str_repeat('a', 63), str_repeat('a', 63)],
            [str_repeat('a', 64), str_repeat('a', 64),
                'The test is fault because the method depends Intl module and it doesn\'t support "false" VerifyDnsLength.'],
        ];
    }

    /**
     * @param string $domain
     * @param string|false $unicode
     * @param string|null $message
     * @dataProvider unicodeProvider
     */
    public function testDomainToUnicode($domain, $unicode, $message = null)
    {
        if ($message) {
            $this->markTestSkipped($message);
        }
        $this->assertSame($unicode, HostProcessing::domainToUnicode($domain), $message);
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
            ['xn--u-ccb.com' , false           ],
            ['a⒈com'        , false           ], // a + DIGIT ONE FULL STOP (U+2488)
            ['xn--a-ecp.ru'  , false           ],
            ['xn--0.pt'      , false           ],
            ['日本語。ＪＰ'  , '日本語.jp'     ],
            ['☕.us'         , '☕.us'         ], // HOT BEVERAGE (U+2615) + .us
            ['[2001:db8::1]' , '[2001:db8::1]' ],
            ['[2001:db8:::1]', '[2001:db8:::1]'],
            ['u/r/l?#.test'  , 'u/r/l?#.test'  ],
            ['203.0.113.1'   , '203.0.113.1'   ],
            ['203.000.113.01', '203.000.113.01'],
            [str_repeat('a', 249) . '.test', str_repeat('a', 249) . '.test',
                'The test is fault because the method depends Intl module and it doesn\'t support "false" VerifyDnsLength.'],
            [str_repeat('a', 250) . '.test', str_repeat('a', 250) . '.test',
                'The test is fault because the method depends Intl module and it doesn\'t support "false" VerifyDnsLength.'],
        ];
    }

    /**
     * @param string $domain
     * @param boolean $returnValue
     * @dataProvider domainProvider
     */
    public function testIsValidDomain($domain, $returnValue)
    {
        $this->assertSame($returnValue, HostProcessing::isValidDomain($domain), $domain);
    }
    
    /**
     * @link http://www.unicode.org/reports/tr46/#Table_Example_Processing UTS #46: Unicode IDNA Compatibility Processing
     */
    public function domainProvider()
    {
        return [
            ['Bloß.de'                            , true ],
            ['xn--blo-7ka.de'                     , true ],
            ['ü.com'                             , true ], // u + COMBINING DIAERESIS (U+0308) + .com
            ['xn--tda.com'                        , true ],
            ['xn--u-ccb.com'                      , false],
            ['a⒈com'                             , false], // a + DIGIT ONE FULL STOP (U+2488)
            ['xn--a-ecp.ru'                       , false],
            ['xn--0.pt'                           , false],
            ['日本語。ＪＰ'                       , true ],
            ['☕.us'                              , true ], // HOT BEVERAGE (U+2615) + .us
            ['[2001:db8::1]'                      , false],
            ['[2001:db8:::1]'                     , false],
            ['u/r/l?#.test'                       , false],
            ['203.0.113.1'                        , true ],
            ['203.000.113.01'                     , true ],
            ['low_line.test'                      , false],
            ['low＿line.test'                     , false],
            ['url-.test'                          , false],
            ['-url.test'                          , false],
            [str_repeat('123456789.', 25) . '123' , true ],
            [str_repeat('123456789.', 25) . '1234', false],
            [str_repeat('a', 63) . '.test'        , true ],
            [str_repeat('a', 64) . '.test'        , false],
            [''                                   , false],
            ['url..test'                          , false],
            ['url.test.'                          , false],
            ['test'                               , true ],
            ['xn--zckzah'                         , true ],
            ['%E3%83%86%E3%82%B9%E3%83%88'        , false],
            ['.'                                  , false],
            ['.test'                              , false],
        ];
    }
    
    /**
     * @param string $input
     * @param boolean $isSpecial
     * @param string|integer[]|false $domain
     * @dataProvider hostProvider
     */
    public function testParseHost($input, $isSpecial, $domain)
    {
        $this->assertSame($domain, HostProcessing::parseHost($input, $isSpecial));
    }
    
    /**
     * @link http://www.unicode.org/reports/tr46/#Table_Example_Processing UTS #46: Unicode IDNA Compatibility Processing
     */
    public function hostProvider()
    {
        return [
            ['special-url.test'                 , true , 'special-url.test'                                           ],
            ['non-special-url.test'             , false, 'non-special-url.test'                                       ],
            ['test'                             , true , 'test'                                                       ],
            ['test'                             , false, 'test'                                                       ],
            ['テスト'                           , true , 'xn--zckzah'                                                 ],
            ['テスト'                           , false, '%E3%83%86%E3%82%B9%E3%83%88'                                ],
            ['xn--zckzah'                       , true , 'xn--zckzah'                                                 ],
            ['xn--zckzah'                       , false, 'xn--zckzah'                                                 ],
            ['%E3%83%86%E3%82%B9%E3%83%88'      , true , 'xn--zckzah'                                                 ],
            ['%E3%83%86%E3%82%B9%E3%83%88'      , false, '%E3%83%86%E3%82%B9%E3%83%88'                                ],
            ['Bloß.de'                          , true , 'xn--blo-7ka.de'                                             ],
            ['xn--blo-7ka.de'                   , true , 'xn--blo-7ka.de'                                             ],
            ['ü.com'                           , true , 'xn--tda.com'                                                ],
            ['xn--tda.com'                      , true , 'xn--tda.com'                                                ],
            ['xn--u-ccb.com'                    , true , false                                                        ],
            ['a⒈com'                           , true , false                                                        ],
            ['xn--a-ecp.ru'                     , true , false                                                        ],
            ['xn--0.pt'                         , true , false                                                        ],
            ['日本語。ＪＰ'                     , true , 'xn--wgv71a119e.jp'                                          ],
            ['☕.us'                            , true , 'xn--53h.us'                                                 ],
            ['[2001:db8::1]'                    , true , [0x2001, 0xDB8, 0, 0, 0, 0, 0, 0x1]                          ],
            ['[2001:db8::1]'                    , false, [0x2001, 0xDB8, 0, 0, 0, 0, 0, 0x1]                          ],
            ['[2001:db8::1'                     , true , false                                                        ],
            ['[2001:db8::1'                     , false, false                                                        ],
            ['[2001:db8::]'                     , true , [0x2001, 0xDB8, 0, 0, 0, 0, 0, 0]                            ],
            ['[2001:DB8::1]'                    , true , [0x2001, 0xDB8, 0, 0, 0, 0, 0, 0x1]                          ],
            ['[2001:dB8::1]'                    , true , [0x2001, 0xDB8, 0, 0, 0, 0, 0, 0x1]                          ],
            ['[2001:0db8::1]'                   , true , [0x2001, 0xDB8, 0, 0, 0, 0, 0, 0x1]                          ],
            ['[2001:00db8::]'                   , true , false                                                        ],
            ['[2001:db8:::1]'                   , true , false                                                        ],
            ['[:2001:db8::1]'                   , true , false                                                        ],
            ['[2001:db8::1::1]'                 , true , false                                                        ],
            ['203.0.113.1'                      , true , 203 * 0x1000000 + 0 * 0x10000 + 113 * 0x100 + 1              ],
            ['203.0.113.1'                      , false, '203.0.113.1'                                                ],
            ['203.000.113.01'                   , true , 203 * 0x1000000 + 0 * 0x10000 + 113 * 0x100 + 1              ],
            ['203%2E0%2E113%2E1'                , true , 203 * 0x1000000 + 0 * 0x10000 + 113 * 0x100 + 1              ],
            ['203.0.113.256'                    , true , false                                                        ],
            ['0xCB007101'                       , true , 0xCB007101                                                   ],
            ['[::ffff:203.0.113.1]'             , true , [0, 0, 0, 0, 0, 0xFFFF, 203 * 0x100 + 0, 113 * 0x100 + 1]    ],
            ['[::203.0.113.1]'                  , true , [0, 0, 0, 0, 0, 0, 203 * 0x100 + 0, 113 * 0x100 + 1]         ],
            ['[0:0:0:0:0:ffff:203.0.113.1]'     , true , [0, 0, 0, 0, 0, 0xFFFF, 203 * 0x100 + 0, 113 * 0x100 + 1]    ],
            ['[2001:db8::203.0.113.1]'          , true , [0x2001, 0xDB8, 0, 0, 0, 0, 203 * 0x100 + 0, 113 * 0x100 + 1]],
            ['[2001:db8:203.0.113.1::]'         , true , false                                                        ],
            ['[2001:db8::203.0.113.1.0]'        , true , false                                                        ],
            ['[0:0:0:0:0:0:ffff:cb00:7101]'     , true , false                                                        ],
            ['[0:0:0:0:0:0:ffff:203.0.113.1]'   , true , false                                                        ],
            ['[::ffff:203.0.113.01]'            , true , false                                                        ],
            ['[::ffff:203.0.113.256]'           , true , false                                                        ],
            ["null \x00character.test"          , true , false                                                        ],
            ["null \x00character.test"          , false, false                                                        ],
            ["character\ttabulation.test"       , true , false                                                        ],
            ["line\nfeed.test"                  , true , false                                                        ],
            ["carriage\rreturn.test"            , true , false                                                        ],
            ['space character.test'             , true , false                                                        ],
            ['number#sign.test'                 , true , false                                                        ],
            ['percent%sign.test'                , true , false                                                        ],
            ['solidu/s.test'                    , true , false                                                        ],
            ['colo:n.test'                      , true , false                                                        ],
            ['question?mark.test'               , true , false                                                        ],
            ['commercial@at.test'               , true , false                                                        ],
            ['square[bracket.test'              , true , false                                                        ],
            ['reverse\\solidus.test'            , true , false                                                        ],
            ['square]bracket.test'              , true , false                                                        ],
            ["\v\f\e!\"\$&'()*+,;<=>^_`{|}~.test",true , "\v\f\e!\"\$&'()*+,;<=>^_`{|}~.test"                         ],
        ];
    }
    
    /**
     * @param string $input
     * @param integer|float|false $number
     * @param string|null $message
     * @dataProvider ipv4NumberProvider
     */
    public function testParseIPv4Number($input, $number)
    {
        $this->assertSame($number, HostProcessing::parseIPv4Number($input));
    }
    
    public function ipv4NumberProvider()
    {
        return [
            [                 '192',                  192],
            [                   '0',                    0],
            [                 '255',                  255],
            [                 '256',                  256],
            ['18446744073709551616', 18446744073709551616],
            ['12345678901234567890', 12345678901234567168],
            [                 '0xF',                  0xF],
            [                 '0XF',                  0xF],
            [                 '0xf',                  0xF],
            [                '0x0F',                  0xF],
            [                '0777',                 0777],
            [               '00777',                 0777],
            [                    '',                    0],
            [                  '0x',                    0],
            [                  'FF',                false],
            [                '0xAZ',                false],
            [                  '08',                false],
            [                 '1.0',                false],
            [                  '-1',                false],
            [                  '-0',                false],
            [                  '+1',                false],
            [           '192.0.2.1',                false],
        ];
    }
    
    /**
     * @param string $input
     * @param integer|float|string|false $address
     * @dataProvider ipv4Provider
     */
    public function testParseIPv4($input, $address)
    {
        $this->assertSame($address, HostProcessing::parseIPv4($input));
    }
    
    public function ipv4Provider()
    {
        return [
            ['192.0.2.1'          ,  192 * 0x1000000 +     0 * 0x10000 +    2 * 0x100 +    1],
            ['192.0.2.1.'         ,  192 * 0x1000000 +     0 * 0x10000 +    2 * 0x100 +    1],
            ['192..2.1'           , '192..2.1'                                              ],
            ['192'                , 192                                                     ],
            ['0xFFFFFFFF'         , 0xFFFFFFFF                                              ],
            ['0x100000000'        , false                                                   ],
            ['198.51.100.1'       ,  198 * 0x1000000 +    51 * 0x10000 +  100 * 0x100 +    1],
            ['198.51.25854'       ,  198 * 0x1000000 +    51 * 0x10000 + 25854              ],
            ['10.1.0xFFFF'        ,   10 * 0x1000000 +     1 * 0x10000 + 0xFFFF             ],
            ['10.1.0x10000'       , false                                                   ],
            ['198.256.0.1'        , false                                                   ],
            ['198.256..1'         , '198.256..1'                                            ],
            ['0306.0x33.25854'    , 0306 * 0x1000000 +  0x33 * 0x10000 + 25854              ],
            ['0xC6.0x33.0x64.0xFE', 0xC6 * 0x1000000 +  0x33 * 0x10000 + 0x64 * 0x100 + 0xFE],
            ['C6.33.64.FE'        , 'C6.33.64.FE'                                           ],
            ['0.0.0.0.0'          , '0.0.0.0.0'                                             ],
            [''                   , ''                                                      ],
            ['invalid'            , 'invalid'                                               ],
        ];
    }
    
    /**
     * @param string $input
     * @param integer[]|false $address
     * @dataProvider ipv6Provider
     */
    public function testParseIPv6($input, $address)
    {
        $this->assertSame($address, HostProcessing::parseIPv6($input));
    }
    
    public function ipv6Provider()
    {
        return [
            ['url.test'                    , false                                                        ],
            ['[2001:db8::1]'               , false                                                        ],
            ['2001:db8::1'                 , [0x2001, 0xDB8, 0, 0, 0, 0, 0, 0x1]                          ],
            ['2001:db8::'                  , [0x2001, 0xDB8, 0, 0, 0, 0, 0, 0]                            ],
            ['2001:DB8::1'                 , [0x2001, 0xDB8, 0, 0, 0, 0, 0, 0x1]                          ],
            ['2001:dB8::1'                 , [0x2001, 0xDB8, 0, 0, 0, 0, 0, 0x1]                          ],
            ['2001:0db8::1'                , [0x2001, 0xDB8, 0, 0, 0, 0, 0, 0x1]                          ],
            ['2001:00db8::'                , false                                                        ],
            ['2001:db8:::1'                , false                                                        ],
            [':2001:db8::1'                , false                                                        ],
            ['2001:db8::1::1'              , false                                                        ],
            ['203.0.113.1'                 , false                                                        ],
            ['::ffff:203.0.113.1'          , [0, 0, 0, 0, 0, 0xFFFF, 203 * 0x100 + 0, 113 * 0x100 + 1]    ],
            ['::203.0.113.1'               , [0, 0, 0, 0, 0, 0, 203 * 0x100 + 0, 113 * 0x100 + 1]         ],
            ['0:0:0:0:0:ffff:203.0.113.1'  , [0, 0, 0, 0, 0, 0xFFFF, 203 * 0x100 + 0, 113 * 0x100 + 1]    ],
            ['2001:db8::203.0.113.1'       , [0x2001, 0xDB8, 0, 0, 0, 0, 203 * 0x100 + 0, 113 * 0x100 + 1]],
            ['2001:db8:203.0.113.1::'      , false                                                        ],
            ['2001:db8::203.0.113.1.0'     , false                                                        ],
            ['0:0:0:0:0:0:ffff:cb00:7101'  , false                                                        ],
            ['0:0:0:0:0:0:ffff:203.0.113.1', false                                                        ],
            ['::ffff:203.0.113.01'         , false                                                        ],
            ['::ffff:203.0.113.256'        , false                                                        ],
            ['2001:db8::1:1:1:1:1'         , [0x2001, 0xDB8, 0, 0x1, 0x1, 0x1, 0x1, 0x1]                  ],
        ];
    }
    
    /**
     * @param string $input
     * @param string|false $opaqueHost
     * @dataProvider opaqueHostProvider
     */
    public function testParseOpaqueHost($input, $opaqueHost)
    {
        $this->assertSame($opaqueHost, HostProcessing::parseOpaqueHost($input));
    }
    
    public function opaqueHostProvider()
    {
        return [
            ['opaque-host.test'                         , 'opaque-host.test'                        ],
            ['test'                                     , 'test'                                    ],
            ['𩸽'                                       , '%F0%A9%B8%BD'                            ],
            ['%F0%A9%B8%BD'                             , '%F0%A9%B8%BD'                            ],
            ['%'                                        , '%'                                       ],
            ["\x00"                                     , false                                     ],
            ["\t"                                       , false                                     ],
            ["\n"                                       , false                                     ],
            ["\r"                                       , false                                     ],
            [' '                                        , false                                     ],
            ['#'                                        , false                                     ],
            ['/'                                        , false                                     ],
            [':'                                        , false                                     ],
            ['?'                                        , false                                     ],
            ['@'                                        , false                                     ],
            ['['                                        , false                                     ],
            ['\\'                                       , false                                     ],
            [']'                                        , false                                     ],
            ["\x01\v\f\e\x1F!\"\$&'()*+,;<=>^_`{|}~\x7F", '%01%0B%0C%1B%1F!"$&\'()*+,;<=>^_`{|}~%7F'],
        ];
    }
    
    /**
     * @param string|integer[]|null $host
     * @param string $returnValue
     * @dataProvider hostProvider2
     */
    public function testSerializeHost($host, $returnValue)
    {
        $this->assertSame($returnValue, HostProcessing::serializeHost($host));
    }
    
    /**
     * @link http://www.unicode.org/reports/tr46/#Table_Example_Processing UTS #46: Unicode IDNA Compatibility Processing
     */
    public function hostProvider2()
    {
        return [
            [null                                              , ''                               ],
            ['url.test'                                        , 'url.test'                       ],
            ['url.テスト'                                      , 'url.テスト'                     ],
            ['url.xn--zckzah'                                  , 'url.xn--zckzah'                 ],
            ['url.%E3%83%86%E3%82%B9%E3%83%88'                 , 'url.%E3%83%86%E3%82%B9%E3%83%88'], // opaque host
            [[0x2001, 0x0DB8, 0, 0, 0, 0, 0, 0x1]              , '[2001:db8::1]'                  ],
            [[0, 0, 0, 0, 0, 0xFFFF, 203 * 0x100 + 0, 113 * 0x100 + 1], '[::ffff:cb00:7101]'    ], //IPv4-mapped address
            [[0x2001, 0x0DB8, 0, 0, 0x1, 0, 0, 0x1]            , '[2001:db8::1:0:0:1]'            ],
            [[0x2001, 0x0DB8, 0, 0x1, 0x1, 0x1, 0x1, 0x1]      , '[2001:db8:0:1:1:1:1:1]'         ],
            [192 * 0x1000000 +  0 * 0x10000 +   2 * 0x100 +   0, '192.0.2.0'                      ],
            [198 * 0x1000000 + 51 * 0x10000 + 100 * 0x100 +   1, '198.51.100.1'                   ],
            [203 * 0x1000000 +  0 * 0x10000 + 113 * 0x100 + 255, '203.0.113.255'                  ],
            
            // invalid arguments
            ['xn--u-ccb.com'                                          , 'xn--u-ccb.com'         ],
            ['a⒈com'                                                 , 'a⒈com'                ],
            ['xn--a-ecp.ru'                                           , 'xn--a-ecp.ru'          ],
            ['xn--0.pt'                                               , 'xn--0.pt'              ],
            ['[2001:db8::1]'                                          , '[2001:db8::1]'         ],
            ['2001:db8::1'                                            , '2001:db8::1'           ],
            ['203.0.113.1'                                            , '203.0.113.1'           ],
            ['203.000.113.01'                                         , '203.000.113.01'        ],
        ];
    }
    
    /**
     * @param integer|float $address
     * @param string $output
     * @dataProvider ipv4Provider2
     */
    public function testSerializeIPv4($address, $output)
    {
        $this->assertSame($output, HostProcessing::serializeIPv4($address));
    }
    public function ipv4Provider2()
    {
        return [
            [192 * 0x1000000 +  0 * 0x10000 +   2 * 0x100 +   0, '192.0.2.0'    ],
            [198 * 0x1000000 + 51 * 0x10000 + 100 * 0x100 +   1, '198.51.100.1' ],
            [203 * 0x1000000 +  0 * 0x10000 + 113 * 0x100 + 255, '203.0.113.255'],
        ];
    }
    
    /**
     * @param integer[] $address
     * @param string $output
     * @dataProvider ipv6Provider2
     */
    public function testSerializeIPv6($address, $output)
    {
        $this->assertSame($output, HostProcessing::serializeIPv6($address));
    }
    
    public function ipv6Provider2()
    {
        return [
            [[0x2001, 0x0DB8, 0, 0, 0, 0, 0, 0x1]                     , '2001:db8::1'         ],
            [[0, 0, 0, 0, 0, 0xFFFF, 203 * 0x100 + 0, 113 * 0x100 + 1], '::ffff:cb00:7101'    ], //IPv4-mapped address
            [[0x2001, 0x0DB8, 0, 0, 0x1, 0, 0, 0x1]                   , '2001:db8::1:0:0:1'   ],
            [[0x2001, 0x0DB8, 0, 0x1, 0x1, 0x1, 0x1, 0x1]             , '2001:db8:0:1:1:1:1:1'],
        ];
    }
}
