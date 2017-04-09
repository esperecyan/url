<?php
namespace esperecyan\url\lib;

/**
 * @link https://url.spec.whatwg.org/#infrastructure URL Standard
 */
class Infrastructure
{
    use Utility;
    
    /**
     * Alias of {@link URL}::WINDOWS_DRIVE_LETTER.
     * @deprecated 5.0.0 The constant has been moved to {@link URL}.
     * @see \esperecyan\url\lib\URL::WINDOWS_DRIVE_LETTER
     * @link https://github.com/whatwg/url/commit/ac6489f9b0cf55b27fd1401ab77bc452c97d1457
     *      Editorial: move Windows drive letter to a more logical location · whatwg/url@ac6489f
     * @var string
     */
    const WINDOWS_DRIVE_LETTER = '/^[a-z][:|]$/ui';
    
    /**
     * Alias of {@link URL}::NORMALIZED_WINDOWS_DRIVE_LETTER.
     * @deprecated 5.0.0 The constant has been moved to {@link URL}.
     * @see \esperecyan\url\lib\URL::NORMALIZED_WINDOWS_DRIVE_LETTER
     * @link https://github.com/whatwg/url/commit/ac6489f9b0cf55b27fd1401ab77bc452c97d1457
     *      Editorial: move Windows drive letter to a more logical location · whatwg/url@ac6489f
     * @var string
     */
    const NORMALIZED_WINDOWS_DRIVE_LETTER = '/^[a-z]:$/ui';
    
    /**
     * Percent encodes a byte into a percent-encoded byte.
     * @link https://url.spec.whatwg.org/#percent-encode URL Standard
     * @param string $byte Exactly one byte.
     * @return string “%”, followed by two ASCII hex digits.
     */
    public static function percentEncode($byte)
    {
        return '%' . strtoupper(bin2hex($byte));
    }
    
    /**
     * Percent decodes a byte sequence $input.
     * @link https://url.spec.whatwg.org/#percent-decode URL Standard
     * @param string $input
     * @return string A UTF-8 string if the $input contains only bytes in the range 0x00 to 0x7F.
     */
    public static function percentDecode($input)
    {
        return rawurldecode($input);
    }
    
    /**
     * Alias of C0_CONTROL_PERCENT_ENCODE_SET.
     * @deprecated 5.0.0 The constant has been renamed to C0_CONTROL_PERCENT_ENCODE_SET.
     * @see \esperecyan\url\lib\Infrastructure::C0_CONTROL_PERCENT_ENCODE_SET
     * @var string
     * @link https://github.com/whatwg/url/commit/1d69a774bab856651e0d792e825a976056aeaf59
     *      Editorial: use specific names for encode sets · whatwg/url@1d69a77
     */
    const SIMPLE_ENCODE_SET = '/[^ -~]/u';
    
    /**
     * The regular expression (PCRE) pattern matching a character in the simple encode set.
     * @var string
     * @link https://url.spec.whatwg.org/#c0-control-percent-encode-set URL Standard
     */
    const C0_CONTROL_PERCENT_ENCODE_SET = '/[^ -~]/u';
    
    /**
     * Alias of PATH_PERCENT_ENCODE_SET.
     * @deprecated 5.0.0 The constant has been renamed to PATH_PERCENT_ENCODE_SET.
     * @see \esperecyan\url\lib\Infrastructure::PATH_PERCENT_ENCODE_SET
     * @var string
     * @link https://github.com/whatwg/url/commit/1d69a774bab856651e0d792e825a976056aeaf59
     *      Editorial: use specific names for encode sets · whatwg/url@1d69a77
     */
    const DEFAULT_ENCODE_SET = '/[^!$-;=@-_a-z|~]/u';
    
    /**
     * The regular expression (PCRE) pattern matching a character in the path percent-encode set.
     * @var string
     * @link https://url.spec.whatwg.org/#path-percent-encode-set URL Standard
     */
    const PATH_PERCENT_ENCODE_SET = '/[^!$-;=@-_a-z|~]/u';
    
    /**
     * Alias of USERINFO_PERCENT_ENCODE_SET.
     * @deprecated 3.3.0 The constant is renamed to USERINFO_PERCENT_ENCODE_SET.
     * @see \esperecyan\url\lib\Infrastructure::USERINFO_PERCENT_ENCODE_SET
     * @var string
     * @link https://github.com/whatwg/url/commit/9ca26e5b0edc131f9cca81d0fef4ab92815bc289
     *      Encode more code points for usernames and passwords. Fixes #30. Â· whatwg/url@9ca26e5
     * @link https://github.com/whatwg/url/commit/1d69a774bab856651e0d792e825a976056aeaf59
     *      Editorial: use specific names for encode sets · whatwg/url@1d69a77
     */
    const PASSWORD_ENCODE_SET = '/[^!$-.0-9A-Z_a-z~]/u';
    
    /**
     * Alias of USERINFO_PERCENT_ENCODE_SET.
     * @deprecated 3.3.0 The constant is renamed to USERINFO_PERCENT_ENCODE_SET.
     * @see \esperecyan\url\lib\Infrastructure::USERINFO_PERCENT_ENCODE_SET
     * @var string
     * @link https://github.com/whatwg/url/commit/9ca26e5b0edc131f9cca81d0fef4ab92815bc289
     *      Encode more code points for usernames and passwords. Fixes #30. Â· whatwg/url@9ca26e5
     * @link https://github.com/whatwg/url/commit/1d69a774bab856651e0d792e825a976056aeaf59
     *      Editorial: use specific names for encode sets · whatwg/url@1d69a77
     */
    const USERNAME_ENCODE_SET = '/[^!$-.0-9A-Z_a-z~]/u';
    
    /**
     * Alias of USERINFO_PERCENT_ENCODE_SET.
     * @deprecated 5.0.0 The constant has been renamed to USERINFO_PERCENT_ENCODE_SET.
     * @see \esperecyan\url\lib\Infrastructure::USERINFO_PERCENT_ENCODE_SET
     * @var string
     * @link https://github.com/whatwg/url/commit/1d69a774bab856651e0d792e825a976056aeaf59
     *      Editorial: use specific names for encode sets · whatwg/url@1d69a77
     */
    const USERINFO_ENCODE_SET = '/[^!$-.0-9A-Z_a-z~]/u';
    
    /**
     * The regular expression (PCRE) pattern matching a character in the userinfo percent-encode set.
     * @var string
     * @link https://url.spec.whatwg.org/#userinfo-percent-encode-set URL Standard
     */
    const USERINFO_PERCENT_ENCODE_SET = '/[^!$-.0-9A-Z_a-z~]/u';
    
    /**
     * UTF-8 percent encodes a code point, using an encode set.
     * @link https://url.spec.whatwg.org/#utf_8-percent-encode URL Standard
     * @param string $encodeSet Regular expression (PCRE) pattern matching exactly one UTF-8 character.
     * @param string $codePoint Exactly one UTF-8 character.
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
    
    /**
     * Percent encodes UTF-8 string, using an encode set.
     * @param string $encodeSet Regular expression (PCRE) pattern matching exactly one UTF-8 character.
     * @param string $codePoints A UTF-8 string.
     * @return string
     */
    public static function percentEncodeCodePoints($encodeSet, $codePoints)
    {
        return preg_replace_callback($encodeSet, function ($matches) {
            $result = rawurlencode($matches[0]);
            if ($result[0] !== '%') {
                $result = Infrastructure::percentEncode($matches[0]);
            }
            return $result;
        }, $codePoints);
    }
}
