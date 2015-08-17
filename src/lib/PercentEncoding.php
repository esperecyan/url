<?php
namespace esperecyan\url\lib;

/**
 * @link https://url.spec.whatwg.org/#percent_encoded-bytes URL Standard
 */
class PercentEncoding
{
    use Utility;
    
    /**
     * Percent encode a byte into a percent-encoded byte.
     * @link https://url.spec.whatwg.org/#percent-encode URL Standard
     * @param string $byte Exactly one byte.
     * @return string "%", followed by two ASCII hex digits.
     */
    public static function percentEncode($byte)
    {
        return '%' . strtoupper(bin2hex($byte));
    }
    
    /**
     * Percent decode a byte sequence $input.
     * @link https://url.spec.whatwg.org/#percent-decode URL Standard
     * @param string $input
     * @return string A utf-8 string if the $input contains only bytes in the range 0x00 to 0x7F.
     */
    public static function percentDecode($input)
    {
        return rawurldecode($input);
    }
    
    /**
     * @var string The simple encode set.
     * @link https://url.spec.whatwg.org/#simple-encode-set URL Standard
     */
    const SIMPLE_ENCODE_SET = '/[^ -~]/u';
    
    /**
     * @var string The default encode set.
     * @link https://url.spec.whatwg.org/#default-encode-set URL Standard
     */
    const DEFAULT_ENCODE_SET = '/[^!$-;=@-_a-z|~]/u';
    
    /**
     * @var string The password encode set.
     * @deprecated 3.0.0 The password encode set and the username encode set are obsolete,
     *      then these are replaced by the userinfo encode set.
     * @link https://github.com/whatwg/url/commit/9ca26e5b0edc131f9cca81d0fef4ab92815bc289 Encode more code points for usernames and passwords. Fixes #30. · whatwg/url@9ca26e5
     */
    const PASSWORD_ENCODE_SET = '/[^!$-.0-;=A-[\\]-_a-z|~]/u';
    
    /**
     * @var string The username encode set.
     * @deprecated 3.0.0 The password encode set and the username encode set are obsolete,
     *      then these are replaced by the userinfo encode set.
     * @link https://github.com/whatwg/url/commit/9ca26e5b0edc131f9cca81d0fef4ab92815bc289 Encode more code points for usernames and passwords. Fixes #30. · whatwg/url@9ca26e5
     */
    const USERNAME_ENCODE_SET = '/[^!$-.0-9;=A-[\\]-_a-z|~]/u';
    
    /**
     * @var string The userinfo encode set.
     * @link https://url.spec.whatwg.org/#userinfo-encode-set URL Standard
     */
    const USERINFO_ENCODE_SET = '/[^!$-.0-9A-Z_a-z~]/u';
    
    /**
     * utf-8 percent encode a code point, using an encode set.
     * @link https://url.spec.whatwg.org/#utf_8-percent-encode URL Standard
     * @param string $encodeSet Regular expression (PCRE) pattern matching exactly one utf-8 character.
     * @param string $codePoint Exactly one utf-8 character.
     * @return string
     */
    public static function utf8PercentEncode($encodeSet, $codePoint)
    {
        if (preg_match($encodeSet, $codePoint) === 1) {
            $result = rawurlencode($codePoint);
            if ($result[0] !== '%') {
                $result = self::percentEncode($codePoint);
            }
        } else {
            $result = $codePoint;
        }
        return $result;
    }
}