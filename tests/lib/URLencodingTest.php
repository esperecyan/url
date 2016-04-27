<?php
namespace esperecyan\url\lib;

class URLencodingTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param string $input
     * @param string|null $encodingOverride
     * @param boolean $useCharsetFlag
     * @param string[][]|false $output
     * @dataProvider byteSequenceProvider
     */
    public function testParseURLencoded($input, $encodingOverride, $useCharsetFlag, $output)
    {
        $this->assertEquals(
            $output,
            URLencoding::parseURLencoded($input, $encodingOverride, $useCharsetFlag)
        );
    }
    
    public function byteSequenceProvider()
    {
        return [
            ['name=value1&name=value2', null, false, [
                ['name', 'value1'],
                ['name', 'value2'],
            ]],
            ['value1&&=&value2', null, false, [
                ['value1', ''],
                ['', ''],
                ['value2', ''],
            ]],
            ['値', null, false, [
                ['値', ''],
            ]],
            ['値', 'UTF-8', false, [
                ['値', ''],
            ]],
            [mb_convert_encoding('値', 'Shift_JIS', 'UTF-8'), null, false, [
                ['�l', ''],
            ]],
            [mb_convert_encoding('値', 'Shift_JIS', 'UTF-8'), 'Shift_JIS', false, false],
            [mb_convert_encoding('値', 'Shift_JIS', 'UTF-8'), 'UTF-8', false, [
                ['�l', ''],
            ]],
            ['%92l', null, false, [
                ['�l', ''],
            ]],
            ['%92l&_charset_=shift_jis', null, false, [
                ['�l', ''],
                ['_charset_', 'shift_jis'],
            ]],
            ['%92l&_charset_=shift_jis', null, true, [
                ['値', ''],
                ['_charset_', 'shift_jis'],
            ]],
            ['%92l&_charset_=ms_kanji', null, true, [
                ['値', ''],
                ['_charset_', 'ms_kanji'],
            ]],
            ['%92l&_charset_=utf-8&_charset_=ms_kanji', null, true, [
                ['�l', ''],
                ['_charset_', 'utf-8'],
                ['_charset_', 'ms_kanji'],
            ]],
            ['%92l&_charset_=invalid&_charset_=ms_kanji', null, true, [
                ['値', ''],
                ['_charset_', 'invalid'],
                ['_charset_', 'ms_kanji'],
            ]],
            ['%92l&_charset_=ms_kanji&_charset_=utf-8', null, true, [
                ['値', ''],
                ['_charset_', 'ms_kanji'],
                ['_charset_', 'utf-8'],
            ]],
            ['%E5%90%8D%E5%89%8D=%E5%80%A41&%E5%90%8D%E5%89%8D=%E5%80%A42', null, false, [
                ['名前', '値1'],
                ['名前', '値2'],
            ]],
            ['名前=値1&名前=値2', null, false, [
                ['名前', '値1'],
                ['名前', '値2'],
            ]],
            ['space=+%20%2B', null, false, [
                ['space', '  +'],
            ]],
            ['%26%2320516%3B&_charset_=HTML-ENTITIES', null, true, [
                ['&#20516;', ''],
                ['_charset_', 'HTML-ENTITIES'],
            ]],
            ['=', 'replacement', false, [
                ['', ''],
            ]],
            ['name=value', 'replacement', false, [
                ['�', '�'],
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
            [mb_convert_encoding('値', 'Shift_JIS', 'UTF-8'), '%92l'],
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
            ], 'Shift_JIS', '%96%BC%91O=%92l1&%96%BC%91O=%92l2'],
            [[
                ['PEAR', '🍐'],
            ], 'Shift_JIS', 'PEAR=%26%23127824%3B%25' /* PEAR=&127824; */, 'The test is fault because the method depends mbstring or iconv module and it doesn\'t support "HTML" error mode.'],
            [[
                ['PEAR', '🍐'],
            ], 'macintosh', 'PEAR=%26%23127824%3B%25' /* PEAR=&127824; */, 'The test is fault because the method depends mbstring or iconv module and it doesn\'t support "HTML" error mode.'],
            [[
                ['_charset_', 'UTF-8', 'hidden'],
                ['名前', '値', 'text'],
            ], 'Shift_JIS', '_charset_=Shift_JIS&%96%BC%91O=%92l'],
            [[
                ['_charset_', 'UTF-8', 'text'],
                ['名前', '値', 'text'],
            ], 'Shift_JIS', '_charset_=UTF-8&%96%BC%91O=%92l'],
            [[
                ['_charset_', '', 'hidden'],
                ['名前', '値', 'text'],
            ], 'replacement', '_charset_=UTF-8&%E5%90%8D%E5%89%8D=%E5%80%A4'],
            [[
                ['input', ['name' => 'file name', 'type' => 'text/plain', 'body' => 'contents'], 'file'],
            ], null, 'input=file+name'],
            [[
                ['isindex', 'keyword', 'text'],
            ], null, 'isindex=keyword'],
            [[
                ['isindex', 'keyword'],
            ], null, 'isindex=keyword'],
            [[
                ['isindex', 'keyword', 'text'],
                ['token', '123', 'hidden'],
            ], null, 'isindex=keyword&token=123'],
            [[
                ['token', '123', 'hidden'],
                ['isindex', 'keyword', 'text'],
            ], null, 'token=123&isindex=keyword'],
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
            [mb_convert_encoding('値', 'Shift_JIS', 'UTF-8'), [
                ['�l', ''],
            ]],
        ];
    }
}
