<?php
namespace esperecyan\url\lib;

/**
 * @deprecated 3.2.0 The methods and constants are moved to {@link Infrastructure}.
 * @link https://github.com/whatwg/url/commit/e5b57a0dfe77464282f3b70c1e605ae40bec278d?w=
 *      Rename Terminology to Infrastructure · whatwg/url@e5b57a0
 */
class PercentEncoding
{
    use Utility;
    
    /**
     * Alias of {@link Infrastructure}::percentEncode().
     * @param string $byte Exactly one byte.
     * @return string "%", followed by two ASCII hex digits.
     */
    public static function percentEncode($byte)
    {
        return Infrastructure::percentEncode($byte);
    }
    
    /**
     * Alias of {@link Infrastructure}::percentDecode().
     * @param string $input
     * @return string A utf-8 string if the $input contains only bytes in the range 0x00 to 0x7F.
     */
    public static function percentDecode($input)
    {
        return Infrastructure::percentDecode($input);
    }
    
    /**
     * Alias of {@link Infrastructure}\SIMPLE_ENCODE_SET.
     * @var string
     */
    const SIMPLE_ENCODE_SET = '/[^ -~]/u';
    
    /**
     * Alias of {@link Infrastructure}\DEFAULT_ENCODE_SET.
     * @var string
     */
    const DEFAULT_ENCODE_SET = '/[^!$-;=@-_a-z|~]/u';
    
    /**
     * Alias of {@link Infrastructure}\USERINFO_ENCODE_SET.
     * @var string
     * @deprecated 3.0.0 The password encode set and the username encode set are obsolete,
     *      then these are replaced by the userinfo encode set.
     * @link https://github.com/whatwg/url/commit/9ca26e5b0edc131f9cca81d0fef4ab92815bc289
     *      Encode more code points for usernames and passwords. Fixes #30. · whatwg/url@9ca26e5
     */
    const PASSWORD_ENCODE_SET = '/[^!$-.0-9A-Z_a-z~]/u';
    
    /**
     * Alias of {@link Infrastructure}\USERINFO_ENCODE_SET.
     * @var string
     * @deprecated 3.0.0 The password encode set and the username encode set are obsolete,
     *      then these are replaced by the userinfo encode set.
     * @link https://github.com/whatwg/url/commit/9ca26e5b0edc131f9cca81d0fef4ab92815bc289
     *      Encode more code points for usernames and passwords. Fixes #30. · whatwg/url@9ca26e5
     */
    const USERNAME_ENCODE_SET = '/[^!$-.0-9A-Z_a-z~]/u';
    
    /**
     * Alias of {@link Infrastructure}\USERINFO_ENCODE_SET.
     * @var string
     * @link https://url.spec.whatwg.org/#userinfo-encode-set URL Standard
     */
    const USERINFO_ENCODE_SET = '/[^!$-.0-9A-Z_a-z~]/u';
    
    /**
     * Alias of {@link Infrastructure}\utf8PercentEncode().
     * @param string $encodeSet Regular expression (PCRE) pattern matching exactly one utf-8 character.
     * @param string $codePoint Exactly one utf-8 character.
     * @return string
     */
    public static function utf8PercentEncode($encodeSet, $codePoint)
    {
        return Infrastructure::utf8PercentEncode($encodeSet, $codePoint);
    }
}
