<?php
namespace esperecyan\url\lib;

/**
 * The application/x-www-form-urlencoded format is a simple way to encode name-value pairs
 * in a byte sequence where all bytes are in the 0x00 to 0x7F range.
 * @link https://url.spec.whatwg.org/#application/x-www-form-urlencoded URL Standard
 */
class URLencoding
{
    use Utility;
    
    /**
     * The application/x-www-form-urlencoded parser.
     * @link https://url.spec.whatwg.org/#concept-urlencoded-parser URL Standard
     * @param string $input A byte sequence.
     * @return string[][] A list of name-value tuples.
     */
    public static function parseURLencoded($input)
    {
        $tuples = [];
        foreach (explode('&', $input) as $bytes) {
            if ($bytes === '') {
                continue;
            }
            $tuples[] = strpos($bytes, '=') !== false ? explode('=', $bytes, 2) : [$bytes, ''];
        }

        $output = [];
        foreach ($tuples as $tuple) {
            foreach ($tuple as &$nameOrValue) {
                $nameOrValue = self::utf8DecodeWithoutBOM(urldecode($nameOrValue));
            }
            $output[] = $tuple;
        }
        
        return $output;
    }
    
    /**
     * The application/x-www-form-urlencoded byte serializer.
     * @link https://url.spec.whatwg.org/#concept-urlencoded-byte-serializer URL Standard
     * @param string $input A byte sequence.
     * @return string
     */
    public static function serializeURLencodedByte($input)
    {
        return str_replace('%2A', '*', urlencode($input));
    }
    
    /**
     * The application/x-www-form-urlencoded serializer.
     * @link https://url.spec.whatwg.org/#concept-urlencoded-serializer URL Standard
     * @param (string|string[])[][] $tuples A list of name-value or name-value-type tuples.
     *      The name, value, and filename must be a UTF-8 string.
     * @param string|null $encodingOverride A valid name of an encoding.
     * @return string
     */
    public static function serializeURLencoded($tuples, $encodingOverride = 'UTF-8')
    {
        $encoding = (string)$encodingOverride ? self::getOutputEncoding((string)$encodingOverride) : 'UTF-8';
        foreach ($tuples as $i => &$tuple) {
            $outputPair = [];
            $outputPair[0] = self::serializeURLencodedByte(self::encode($tuple[0], $encoding));
            if (isset($tuple[2]) && $outputPair[0] === '_charset_' && $tuple[2] === 'hidden') {
                $outputPair[1] = $encoding;
            } elseif (isset($tuple[2]) && $tuple[2] === 'file') {
                $outputPair[1] = $tuple[1]['name'];
            } else {
                $outputPair[1] = $tuple[1];
            }
            $outputPair[1] = self::serializeURLencodedByte(self::encode($outputPair[1], $encoding));
            $tuple = implode('=', $outputPair);
        }
        
        return implode('&', $tuples);
    }
    
    /**
     * The application/x-www-form-urlencoded string parser.
     * @link https://url.spec.whatwg.org/#concept-urlencoded-string-parser URL Standard
     * @param string $input A UTF-8 string.
     * @return string[][]  An array of two-element arrays with the first element a name and the second the value.
     */
    public static function parseURLencodedString($input)
    {
        return self::parseURLencoded($input);
    }
    
    /**
     * The ASCII whitespace.
     * @internal
     * @var string
     * @link https://encoding.spec.whatwg.org/#ascii-whitespace Encoding Standard
     */
    const ASCII_WHITESPACE = "\t\n\f\r ";
    
    /**
     * UTF-8 decode without BOM a byte stream $stream.
     * @internal
     * @link https://encoding.spec.whatwg.org/#utf-8-decode-without-bom Encoding Standard
     * @param string $stream A string encoded by UTF-8.
     * @return string A UTF-8 string.
     */
    public static function utf8DecodeWithoutBOM($stream)
    {
        return self::convertEncoding($stream, 'UTF-8', true);
    }
    
    /**
     * Convert the encoding of $input to $encoding from UTF-8.
     * @internal
     * @link https://encoding.spec.whatwg.org/#decode Encoding Standard
     * @param string $input A UTF-8 string.
     * @param string $encoding A valid name of an encoding.
     * @return string
     */
    public static function encode($input, $encoding)
    {
        switch (strtolower($encoding)) {
            case 'utf-8':
            case 'replacement':
                $output = $input;
                break;
            
            case 'x-user-defined':
                $output = preg_replace_callback('/[^\\x00-\\x7F]/u', function ($matches) {
                    $codePoint = self::getCodePoint($matches[0]);
                    return $codePoint <= 0xF7FF ? chr($codePoint - 0xF780 + 0x80) : '&#' . $codePoint . ';';
                }, $input);
                break;
            
            default:
                $output = self::convertEncoding($input, $encoding);
        }
        
        return $output;
    }
    
    /**
     * Get an output encoding from an encoding.
     * @internal
     * @link https://encoding.spec.whatwg.org/#get-an-output-encoding Encoding Standard
     * @param string $encoding A valid name of an encoding.
     * @return string
     */
    public static function getOutputEncoding($encoding)
    {
        return in_array(strtolower($encoding), ['replacement', 'utf-16be', 'utf-16le']) ? 'UTF-8' : $encoding;
    }
    
    /**
     * Invoke mb_convert_encoding() or iconv().
     * @param string $input A string encoded by $encoding if $decoding is true, a UTF-8 string otherwise.
     * @param string $encoding A valid name of an encoding.
     * @param boolean $decoding Convert the encoding to UTF-8 from $encoding if true, to $encoding from UTF-8 otherwise.
     * @throws \DomainException If $encoding is invalid.
     * @return string A UTF-8 string if $decoding is true, a string encoded by $encoding otherwise.
     */
    private static function convertEncoding($input, $encoding, $decoding = false)
    {
        switch (strtolower($encoding)) {
            case 'utf-8':
            case 'ibm866':
            case 'iso-8859-2':
            case 'iso-8859-3':
            case 'iso-8859-4':
            case 'iso-8859-5':
            case 'iso-8859-6':
            case 'iso-8859-7':
            case 'iso-8859-8':
            case 'iso-8859-8-i':
            case 'iso-8859-10':
            case 'iso-8859-13':
            case 'iso-8859-14':
            case 'iso-8859-15':
            case 'iso-8859-16':
            case 'koi8-r':
            case 'koi8-u':
            case 'windows-1251':
            case 'windows-1252':
            case 'windows-1254':
            case 'gbk':
            case 'gb18030':
            case 'big5':
            case 'euc-jp':
            case 'iso-2022-jp':
            case 'shift_jis':
            case 'euc-kr':
            case 'utf-16be':
            case 'utf-16le':
                $characterEncoding = strtoupper($encoding) == 'ISO-8859-8-I' ? 'ISO-8859-8' : $encoding;
                if ($decoding) {
                    $peviousSubstituteCharacter = mb_substitute_character();
                    mb_substitute_character($decoding ? 0xFFFD : 'entity');
                }
                $output = mb_convert_encoding(
                    $input,
                    $decoding ? 'UTF-8' : $characterEncoding,
                    $decoding ? $encoding : 'UTF-8'
                );
                if ($decoding) {
                    mb_substitute_character($peviousSubstituteCharacter);
                }
                break;
            
            case 'macintosh':
            case 'windows-874':
            case 'windows-1250':
            case 'windows-1253':
            case 'windows-1255':
            case 'windows-1256':
            case 'windows-1257':
            case 'windows-1258':
            case 'x-mac-cyrillic':
                $characterEncoding = $encoding == 'x-mac-cyrillic' ? 'MacCyrillic' : $encoding;
                $output = iconv(
                    $decoding ? $characterEncoding : 'UTF-8',
                    ($decoding ? 'UTF-8' : $characterEncoding) . '//TRANSLIT//IGNORE',
                    $input
                );
                break;
            
            default:
                throw new \DomainException(
                    sprintf('"%s" is a name of encoding which is not defined by Eoncding Standard', $encoding)
                );
        }
        
        return $output;
    }
    
    /**
     * Get the code point of $char.
     * @link http://qiita.com/masakielastic/items/5696cf90738c1438f10d PHP - UTF-8 の文字からコードポイントを求める - Qiita
     * @param string $char Exactly one UTF-8 character.
     * @return integer
     */
    private static function getCodePoint($char)
    {
        if ($char !== htmlspecialchars_decode(htmlspecialchars($char, ENT_COMPAT, 'UTF-8'))) {
            return 0xFFFD;
        }

        $x = ord($char[0]);

        if ($x < 0x80) {
            return $x;
        } elseif ($x < 0xE0) {
            $y = ord($char[1]);

            return (($x & 0x1F) << 6) | ($y & 0x3F);
        } elseif ($x < 0xF0) {
            $y = ord($char[1]);
            $z = ord($char[2]);

            return (($x & 0xF) << 12) | (($y & 0x3F) << 6) | ($z & 0x3F);
        }

        $y = ord($char[1]);
        $z = ord($char[2]);
        $w = ord($char[3]);

        return (($x & 0x7) << 18) | (($y & 0x3F) << 12) | (($z & 0x3F) << 6) | ($w & 0x3F);
    }
    
    /**
     * Get the UTF-8 character from a code point $cp.
     * @link http://qiita.com/masakielastic/items/68f81e1b7d153ee5cc81 PHP - コードポイントから UTF-8 の文字を生成する - Qiita
     * @param integer $cp A valid code point.
     * @return string
     */
    private static function getUTF8Character($cp)
    {
        if (!is_int($cp)) {
            exit("$cp is not integer\n");
        }

        if ($cp < 0 || (0xD7FF < $cp && $cp < 0xE000) || 0x10FFFF < $cp) {
            exit("$cp is out of range\n");
        }

        if ($cp < 0x80) {
            return chr($cp);
        } elseif ($cp < 0xA0) {
            return chr(0xC0 | $cp >> 6).chr(0x80 | $cp & 0x3F);
        }

        return html_entity_decode('&#'.$cp.';');
    }
}
