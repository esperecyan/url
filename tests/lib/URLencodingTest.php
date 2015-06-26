<?php
namespace esperecyan\url\lib;

class URLencodingTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param string $input
     * @param string|null $encodingOverride
     * @param boolean $useCharsetFlag
     * @param boolean $isindexFlag
     * @param string[][]|false $output
     * @dataProvider byteSequenceProvider
     */
    public function testParseURLencoded($input, $encodingOverride, $useCharsetFlag, $isindexFlag, $output)
    {
        $this->assertSame(
            $output,
            URLencoding::parseURLencoded($input, $encodingOverride, $useCharsetFlag, $isindexFlag)
        );
    }
    
    public function byteSequenceProvider()
    {
        return [
            ['name=value1&name=value2', null, false, false, [
                ['name', 'value1'],
                ['name', 'value2'],
            ]],
            ['value1&&=&value2', null, false, false, [
                ['value1', ''],
                ['', ''],
                ['value2', ''],
            ]],
            ['value1&&=&value2', null, false, true, [
                ['', 'value1'],
                ['', ''],
                ['value2', ''],
            ]],
            ['値', null, false, false, [
                ['値', ''],
            ]],
            ['値', 'utf-8', false, false, [
                ['値', ''],
            ]],
            [mb_convert_encoding('値', 'SJIS', 'utf-8'), null, false, false, [
                ['�l', ''],
            ]],
            [mb_convert_encoding('値', 'SJIS', 'utf-8'), 'shift_jis', false, false, false],
            [mb_convert_encoding('値', 'SJIS', 'utf-8'), 'utf-8', false, false, [
                ['�l', ''],
            ]],
            ['%92l', null, false, false, [
                ['�l', ''],
            ]],
            ['%92l&_charset_=shift_jis', null, false, false, [
                ['�l', ''],
                ['_charset_', 'shift_jis'],
            ]],
            ['%92l&_charset_=shift_jis', null, true, false, [
                ['値', ''],
                ['_charset_', 'shift_jis'],
            ]],
            ['%92l&_charset_=ms_kanji', null, true, false, [
                ['値', ''],
                ['_charset_', 'ms_kanji'],
            ]],
            ['%92l&_charset_=utf-8&_charset_=ms_kanji', null, true, false, [
                ['�l', ''],
                ['_charset_', 'utf-8'],
                ['_charset_', 'ms_kanji'],
            ]],
            ['%92l&_charset_=invalid&_charset_=ms_kanji', null, true, false, [
                ['値', ''],
                ['_charset_', 'invalid'],
                ['_charset_', 'ms_kanji'],
            ]],
            ['%92l&_charset_=ms_kanji&_charset_=utf-8', null, true, false, [
                ['値', ''],
                ['_charset_', 'ms_kanji'],
                ['_charset_', 'utf-8'],
            ]],
            ['%E5%90%8D%E5%89%8D=%E5%80%A41&%E5%90%8D%E5%89%8D=%E5%80%A42', null, false, false, [
                ['名前', '値1'],
                ['名前', '値2'],
            ]],
            ['名前=値1&名前=値2', null, false, false, [
                ['名前', '値1'],
                ['名前', '値2'],
            ]],
            ['space=+%20%2B', null, false, false, [
                ['space', '  +'],
            ]],
            ['%26%2320516%3B&_charset_=HTML-ENTITIES', null, true, false, [
                ['&#20516;', ''],
                ['_charset_', 'HTML-ENTITIES'],
            ]],
        ];
    }
    
    /**
     * @param string $input
     * @param string $output
     * @dataProvider urlencodedByteProvider
     */
    public function testSerializeURLencodedByte($input, $output)
    {
        $this->assertSame($output, URLencoding::serializeURLencodedByte($input));
    }
    
    public function urlencodedByteProvider()
    {
        return [
            [' !"#$%&\'()*+,-.:;<=>?@[]^_`{|}~', '+%21%22%23%24%25%26%27%28%29*%2B%2C-.%3A%3B%3C%3D%3E%3F%40%5B%5D%5E_%60%7B%7C%7D%7E'],
            ['値', '%E5%80%A4'],
            ['%E5%80%A4', '%25E5%2580%25A4'],
            [mb_convert_encoding('値', 'SJIS', 'utf-8'), '%92l'],
            ['', ''],
        ];
    }
    
    /**
     * @param string[][] $pairs
     * @param string|null $encodingOverride
     * @param string $output
     * @param string|null $message
     * @dataProvider urlencodedProvider
     */
    public function testSerializeURLencoded($pairs, $encodingOverride, $output, $message = null)
    {
        if ($message) {
            $this->markTestSkipped($message);
        }
        
        $this->assertSame($output, URLencoding::serializeURLencoded($pairs, $encodingOverride), $message);
    }
    
    public function urlencodedProvider()
    {
        return [
            [[
                ['name', 'value1'],
                ['name', 'value2'],
            ], null, 'name=value1&name=value2'],
            [[
                ['名前', '値1'],
                ['名前', '値2'],
            ], null, '%E5%90%8D%E5%89%8D=%E5%80%A41&%E5%90%8D%E5%89%8D=%E5%80%A42'],
            [[
                ['', ''],
                ['', ''],
            ], null, '=&='],
            [[
            ], null, ''],
            [[
                ['名前', '値1'],
                ['名前', '値2'],
            ], 'shift_jis', '%96%BC%91O=%92l1&%96%BC%91O=%92l2'],
            [[
                ['PEAR', '🍐'],
            ], 'shift_jis', 'PEAR=%26%23127824%3B%25' /* PEAR=&127824; */, 'The test is fault because the method depends mbstring or iconv module and it doesn\'t support "HTML" error mode.'],
        ];
    }
    
    /**
     * @param string $input
     * @param string[][]|false $output
     * @dataProvider stringProvider
     */
    public function testParseURLencodedString($input, $output)
    {
        $this->assertSame($output, URLencoding::parseURLencodedString($input));
    }
    
    public function stringProvider()
    {
        return [
            ['value1&&=&value2', [
                ['value1', ''],
                ['', ''],
                ['value2', ''],
            ]],
            ['%E5%90%8D%E5%89%8D=%E5%80%A41&%E5%90%8D%E5%89%8D=%E5%80%A42', [
                ['名前', '値1'],
                ['名前', '値2'],
            ]],
            ['%92l&_charset_=shift_jis', [
                ['�l', ''],
                ['_charset_', 'shift_jis'],
            ]],
            
            // invalid arguments
            [mb_convert_encoding('値', 'SJIS', 'utf-8'), [
                ['�l', ''],
            ]],
        ];
    }
}
