<?php
namespace esperecyan\url\lib;

class InfrastructureTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param string $byte Exactly one byte.
     * @param string $percentEncodedByte
     * @dataProvider byteProvider
     */
    public function testPercentEncode($byte, $percentEncodedByte)
    {
        $this->assertSame($percentEncodedByte, Infrastructure::percentEncode($byte));
    }
    
    public function byteProvider()
    {
        return [
            ["\x00", '%00'      ],
            ['0'   , '%30'      ],
            ['A'   , '%41'      ],
            ['a'   , '%61'      ],
            ["\xE3", '%E3'      ],
            
            // invalid arguments
            [''    , '%'        ],
            ['abc' , '%616263'  ],
            ['α'  , '%CEB1'    ],
            ['あ'  , '%E38182'  ],
            ['🍐'  , '%F09F8D90'],
        ];
    }
    
    /**
     * @param string $input
     * @param string $output
     * @dataProvider inputProvider
     */
    public function testPercentDecode($input, $output)
    {
        $this->assertSame($output, Infrastructure::percentDecode($input));
    }
    
    public function inputProvider()
    {
        return [
            ['%E3%83%86%E3%82%B9%E3%83%88', 'テスト'          ],
            ['%e3%83%86%e3%82%b9%e3%83%88', 'テスト'          ],
            ['テスト'                      , 'テスト'          ],
            ['%61%62%63'                  , 'abc'             ],
            ['abc'                        , 'abc'             ],
            ['+'                          , '+'               ],
            ['%!!'                        , '%!!'             ],
            ['%'                          , '%'               ],
            [''                           , ''                ],
            ['%F0%80%80%AF'               , "\xF0\x80\x80\xAF"], // percent encoded redundant UTF-8 sequence "/"
            ["\xF0\x80\x80\xAF"           , "\xF0\x80\x80\xAF"], // redundant UTF-8 sequence "/"
        ];
    }
    /**
     * @param string $encodeSet Regular expression matching exactly one utf-8 character.
     * @param string $codePoint Exactly one utf-8 character.
     * @param string $result
     * @dataProvider codePointProvider
     */
    public function testUtf8PercentEncode($encodeSet, $codePoint, $result)
    {
        $this->assertSame($result, Infrastructure::utf8PercentEncode($encodeSet, $codePoint));
    }
    
    public function codePointProvider()
    {
        return [
            ['/[^ -~]/u'                    , "\x00"  , '%00'                        ],
            ['/[^ -~]/u'                    , '0'     , '0'                          ],
            ['/[^ -~]/u'                    , 'a'     , 'a'                          ],
            ['/[^ -~]/u'                    , ' '     , ' '                          ],
            ['/[^ -~]/u'                    , '+'     , '+'                          ],
            ['/[^ -~]/u'                    , 'あ'    , '%E3%81%82'                  ],
            ['/[^ -~]/u'                    , '🍐'    , '%F0%9F%8D%90'               ],
            ['/[-_.~]/u'                    , '-'     , '%2D'                        ],
            
            // invalid arguments
            ['/[^ -~]/u'                    , 'あいう', '%E3%81%82%E3%81%84%E3%81%86'],
            ['/[^ -~]+/u'                   , 'あいう', '%E3%81%82%E3%81%84%E3%81%86'],
            ['/[^ -~]/u'                    , '%20'   , '%20'                        ],
            ['/[^!"$-+-.0-9;=A-[\\]-_a-~]/u', ':::'   , '%3A%3A%3A'                  ],
            ['/[^ -~]/u'                    , 'abc'   , 'abc'                        ],
            ['/[^ -~]/u'                    , 'abcあ' , '%616263E38182'              ],
            ['/[-_.~]/u'                    , '---'   , '%2D2D2D'                    ],
        ];
    }
}
