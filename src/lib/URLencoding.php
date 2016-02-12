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
     * @param string|null $encodingOverride A valid name of an encoding.
     * @param boolean $useCharsetFlag A use _charset_ flag.
     * @param boolean $isindexFlag An isindex flag.
     * @return string[][]|false A list of name-value tuples.
     *      Return false if $encodingOverride is not utf-8 and $input contains bytes whose value is greater than 0x7F.
     */
    public static function parseURLencoded($input, $encodingOverride = 'utf-8', $useCharsetFlag = false, $isindexFlag = false)
    {
        $encoding = (string)$encodingOverride ?: 'utf-8';
        $useCharset = (boolean)$useCharsetFlag;
        if ($encoding !== 'utf-8' && preg_match('/[\\x7F-\xFF]/', $input) !== 0) {
            $output = false;
        } else {
            $sequences = explode('&', $input);
            if ($isindexFlag && strpos($sequences[0], '=') === false) {
                $sequences[0] = '=' . $sequences[0];
            }
            
            $tuples = [];
            foreach ($sequences as $bytes) {
                if ($bytes === '') {
                    continue;
                }
                $pair = strpos($bytes, '=') !== false ? explode('=', $bytes, 2) : [$bytes, ''];
                if ($useCharset && $pair[0] === '_charset_') {
                    $result = self::getEncoding($pair[1]);
                    if ($result !== false) {
                        $useCharset = false;
                        $encoding = $result;
                    }
                }

                $tuples[] = $pair;
            }
            
            $output = [];
            foreach ($tuples as $tuple) {
                foreach ($tuple as &$nameOrValue) {
                    $nameOrValue = self::runEncoding(urldecode($nameOrValue), $encoding);
                }
                $output[] = $tuple;
            }
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
     *      The name, value, and filename must be a utf-8 string.
     * @param string|null $encodingOverride A valid name of an encoding.
     * @return string
     */
    public static function serializeURLencoded($tuples, $encodingOverride = 'utf-8')
    {
        $encoding = (string)$encodingOverride ?: 'utf-8';
        foreach ($tuples as $i => &$tuple) {
            $outputPair = [];
            $outputPair[0] = self::serializeURLencodedByte(self::encode($tuple[0], $encoding));
            if (isset($tuple[2]) && $outputPair[0] === '_charset_' && $tuple[2] === 'hidden') {
                $outputPair[1] = $encodingOverride;
            } elseif (isset($tuple[2]) && $tuple[2] === 'file') {
                $outputPair[1] = $tuple[1]['name'];
            } else {
                $outputPair[1] = $tuple[1];
            }
            $outputPair[1] = self::serializeURLencodedByte(self::encode($outputPair[1], $encoding));
            if (isset($tuple[2]) && $tuple[2] === 'text' && $outputPair[0] === 'isindex' && $i === 0) {
                array_shift($outputPair);
            }
            $tuple = implode('=', $outputPair);
        }
        
        return implode('&', $tuples);
    }
    
    /**
     * The application/x-www-form-urlencoded string parser.
     * @link https://url.spec.whatwg.org/#concept-urlencoded-string-parser URL Standard
     * @param string $input A utf-8 string.
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
     * Get an encoding from a string.
     * @link https://encoding.spec.whatwg.org/#concept-encoding-get Encoding Standard
     * @param string $label A utf-8 string.
     * @return string|false
     */
    private static function getEncoding($label)
    {
        switch (strtolower(trim($label, self::ASCII_WHITESPACE))) {
            case 'unicode-1-1-utf-8':
            case 'utf-8':
            case 'utf8':
                $encoding = 'utf-8';
                break;
            case 'iso-8859-14':
            case 'iso8859-14':
            case 'iso885914':
                $encoding = 'iso-8859-14';
                break;
            case 'csisolatin9':
            case 'iso-8859-15':
            case 'iso8859-15':
            case 'iso885915':
            case 'iso_8859-15':
            case 'l9':
                $encoding = 'iso-8859-15';
                break;
            case 'iso-8859-16':
                $encoding = 'iso-8859-16';
                break;
            case 'cskoi8r':
            case 'koi':
            case 'koi8':
            case 'koi8-r':
            case 'koi8_r':
                $encoding = 'koi8-r';
                break;
            case 'koi8-u':
                $encoding = 'koi8-u';
                break;
            case 'csmacintosh':
            case 'mac':
            case 'macintosh':
            case 'x-mac-roman':
                $encoding = 'macintosh';
                break;
            case 'dos-874':
            case 'iso-8859-11':
            case 'iso8859-11':
            case 'iso885911':
            case 'tis-620':
            case 'windows-874':
                $encoding = 'windows-874';
                break;
            case 'cp1250':
            case 'windows-1250':
            case 'x-cp1250':
                $encoding = 'windows-1250';
                break;
            case 'cp1251':
            case 'windows-1251':
            case 'x-cp1251':
                $encoding = 'windows-1251';
                break;
            case 'ansi_x3.4-1968':
            case 'ascii':
            case 'cp1252':
            case 'cp819':
            case 'csisolatin1':
            case 'ibm819':
            case 'iso-8859-1':
            case 'iso-ir-100':
            case 'iso8859-1':
            case 'iso88591':
            case 'iso_8859-1':
            case 'iso_8859-1:1987':
            case 'l1':
            case 'latin1':
            case 'us-ascii':
            case 'windows-1252':
            case 'x-cp1252':
                $encoding = 'windows-1252';
                break;
            case 'cp1253':
            case 'windows-1253':
            case 'x-cp1253':
                $encoding = 'windows-1253';
                break;
            case 'cp1254':
            case 'csisolatin5':
            case 'iso-8859-9':
            case 'iso-ir-148':
            case 'iso8859-9':
            case 'iso88599':
            case 'iso_8859-9':
            case 'iso_8859-9:1989':
            case 'l5':
            case 'latin5':
            case 'windows-1254':
            case 'x-cp1254':
                $encoding = 'windows-1254';
                break;
            case 'cp1255':
            case 'windows-1255':
            case 'x-cp1255':
                $encoding = 'windows-1255';
                break;
            case 'cp1256':
            case 'windows-1256':
            case 'x-cp1256':
                $encoding = 'windows-1256';
                break;
            case 'cp1257':
            case 'windows-1257':
            case 'x-cp1257':
                $encoding = 'windows-1257';
                break;
            case 'cp1258':
            case 'windows-1258':
            case 'x-cp1258':
                $encoding = 'windows-1258';
                break;
            case 'x-mac-cyrillic':
            case 'x-mac-ukrainian':
                $encoding = 'x-mac-cyrillic';
                break;
            case 'chinese':
            case 'csgb2312':
            case 'csiso58gb231280':
            case 'gb2312':
            case 'gb_2312':
            case 'gb_2312-80':
            case 'gbk':
            case 'iso-ir-58':
            case 'x-gbk':
                $encoding = 'gbk';
                break;
            case 'gb18030':
                $encoding = 'gb18030';
                break;
            case 'big5':
            case 'big5-hkscs':
            case 'cn-big5':
            case 'csbig5':
            case 'x-x-big5':
                $encoding = 'big5';
                break;
            case 'cseucpkdfmtjapanese':
            case 'euc-jp':
            case 'x-euc-jp':
                $encoding = 'euc-jp';
                break;
            case 'csiso2022jp':
            case 'iso-2022-jp':
                $encoding = 'iso-2022-jp';
                break;
            case 'csshiftjis':
            case 'ms_kanji':
            case 'shift-jis':
            case 'shift_jis':
            case 'sjis':
            case 'windows-31j':
            case 'x-sjis':
                $encoding = 'shift_jis';
                break;
            case 'cseuckr':
            case 'csksc56011987':
            case 'euc-kr':
            case 'iso-ir-149':
            case 'korean':
            case 'ks_c_5601-1987':
            case 'ks_c_5601-1989':
            case 'ksc5601':
            case 'ksc_5601':
            case 'windows-949':
                $encoding = 'euc-kr';
                break;
            case 'csiso2022kr':
            case 'hz-gb-2312':
            case 'iso-2022-cn':
            case 'iso-2022-cn-ext':
            case 'iso-2022-kr':
                $encoding = 'replacement';
                break;
            case 'utf-16be':
                $encoding = 'utf-16be';
                break;
            case 'utf-16':
            case 'utf-16le':
                $encoding = 'utf-16le';
                break;
            case 'x-user-defined':
                $encoding = 'x-user-defined';
                break;
            default:
                $encoding = false;
        }
        return $encoding;
    }
    
    /**
     * Convert the encoding of $input to utf-8 from $encoding.
     * @internal
     * @link https://encoding.spec.whatwg.org/#concept-encoding-run Encoding Standard
     * @param string $input A string encoded by $encoding.
     * @param string $encoding A valid name of an encoding.
     * @throws \DomainException If $encoding is invalid.
     * @return string A utf-8 string.
     */
    public static function runEncoding($input, $encoding)
    {
        switch ($encoding) {
            case 'replacement':
                $output = $input === '' ? '' : '�'; // REPLACEMENT CHARACTER (U+FFFD)
                break;
                
            case 'x-user-defined':
                $output = preg_replace_callback('/[^\\x00-\\x7F]/', function ($matches) {
                    return self::getUTF8Character(0xF780 + \ord($matches[0]) - 0x80);
                }, $input);
                break;
            
            default:
                $output = self::convertEncoding($input, $encoding, true);
        }
        
        return $output;
    }
    
    /**
     * Convert the encoding of $input to $encoding from utf-8.
     * @internal
     * @link https://encoding.spec.whatwg.org/#decode Encoding Standard
     * @param string $input A utf-8 string.
     * @param string $encoding A valid name of an encoding.
     * @return string
     */
    public static function encode($input, $encoding)
    {
        switch ($encoding) {
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
     * Invoke mb_convert_encoding() or iconv().
     * @param string $input A string encoded by $encoding if $decoding is true, a utf-8 string otherwise.
     * @param string $encoding A valid name of an encoding.
     * @param boolean $decoding Convert the encoding to utf-8 from $encoding if true, to $encoding from utf-8 otherwise.
     * @throws \DomainException If $encoding is invalid.
     * @return string A utf-8 string if $decoding is true, a string encoded by $encoding otherwise.
     */
    private static function convertEncoding($input, $encoding, $decoding = false)
    {
        switch ($encoding) {
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
                $characterEncoding = $encoding == 'iso-8859-8-i' ? 'iso-8859-8' : $encoding;
                if ($decoding) {
                    $peviousSubstituteCharacter = mb_substitute_character();
                    mb_substitute_character(0xFFFD);
                }
                $output = mb_convert_encoding(
                    $input,
                    $decoding ? 'utf-8' : $characterEncoding,
                    $decoding ? $encoding : 'utf-8'
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
                    $decoding ? $characterEncoding : 'utf-8',
                    ($decoding ? 'utf-8' : $characterEncoding) . '//TRANSLIT',
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
     * @param string $char Exactly one utf-8 character.
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
     * Get the utf-8 character from a code point $cp.
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
