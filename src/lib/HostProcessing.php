<?php
namespace esperecyan\url\lib;

/**
 * @link https://url.spec.whatwg.org/#hosts-(domains-and-ip-addresses) URL Standard
 */
class HostProcessing
{
    use Utility;
    
    /**
     * Maximum utf-8 length of a fatal error does not occur by idn_to_ascii() or idn_to_utf8().
     * @internal
     * @var integer
     */
    const PHP_IDN_HANDLEABLE_LENGTH = 254;
    
    /**
     * The domain to ASCII given a domain $domain.
     * @link https://url.spec.whatwg.org/#concept-domain-to-ascii URL Standard
     * @param string $domain A utf-8 string.
     * @return string|false
     */
    public static function domainToASCII($domain)
    {
        return mb_strlen($domain, 'utf-8') <= self::PHP_IDN_HANDLEABLE_LENGTH
            ? idn_to_ascii($domain, 0, INTL_IDNA_VARIANT_UTS46)
            : false;
    }
    
    /**
     * The domain to Unicode given a domain $domain.
     * @link https://url.spec.whatwg.org/#concept-domain-to-unicode URL Standard
     * @param string $domain A utf-8 string.
     * @return string
     */
    public static function domainToUnicode($domain)
    {
        return mb_strlen($domain, 'utf-8') <= self::PHP_IDN_HANDLEABLE_LENGTH
            ? idn_to_utf8($domain, 0, INTL_IDNA_VARIANT_UTS46)
            : false;
    }

    /**
     * Return true if a domain is a valid domain.
     * @link https://url.spec.whatwg.org/#valid-domain URL Standard
     * @param string $domain A utf-8 string.
     * @return boolean
     */
    public static function isValidDomain($domain)
    {
        $valid = mb_strlen($domain, 'utf-8') <= self::PHP_IDN_HANDLEABLE_LENGTH;

        if ($valid) {
            $result = idn_to_ascii(
                $domain,
                IDNA_USE_STD3_RULES | IDNA_NONTRANSITIONAL_TO_ASCII,
                INTL_IDNA_VARIANT_UTS46
            );

            if (!is_string($result)) {
                $valid = false;
            }
        }
        
        if ($valid) {
            $domainNameLength = strlen($result);
            if ($domainNameLength < 1 || $domainNameLength > 253) {
                $valid = false;
            }
        }
        
        if ($valid) {
            foreach (explode('.', $result) as $label) {
                $labelLength = strlen($label);
                if ($labelLength < 1 || $labelLength > 63) {
                    $valid = false;
                    break;
                }
            }
        }
        
        if ($valid) {
            $result = idn_to_utf8($result, IDNA_USE_STD3_RULES, INTL_IDNA_VARIANT_UTS46, $idna_info);
            if ($idna_info['errors'] !== 0) {
                $valid = false;
            }
        }
        
        return $valid;
    }
    
    /**
     * The host parser.
     * @link https://url.spec.whatwg.org/#concept-host-parser URL Standard
     * @param string $input A utf-8 string.
     * @param boolean $unicodeFlag If true, can return a domain containing non-ASCII characters.
     * @return string|integer|float|integer[]
     *      If host is IPv4 address, returns a 32-bit unsigned integer (an integer or float).
     *      If host is IPv6 address, returns an array of a 16-bit unsigned integer.
     */
    public static function parseHost($input, $unicodeFlag = false)
    {
        $inputString = (string)$input;
        if ($inputString === '') {
            $result = false;
        } elseif ($inputString[0] === '[') {
            $result = substr($inputString, -1) !== ']' ? false : self::parseIPv6(substr($inputString, 1, -1));
        } else {
            $domain = PercentEncoding::percentDecode($input);
            $asciiDomain = self::domainToASCII($domain);
            if ($asciiDomain === false || strpbrk($asciiDomain, "\x00\t\n\r #%/:?@[\\]") !== false) {
                $result = false;
            } else {
                $ipv4Host = self::parseIPv4($asciiDomain);
                $result = is_string($ipv4Host)
                    ? ($unicodeFlag ? self::domainToUnicode($ipv4Host) : $ipv4Host)
                    : $ipv4Host;
            }
        }
        return $result;
    }
    
    /**
     * The IPv4 number parser.
     * @link https://url.spec.whatwg.org/#ipv4-number-parser URL Standard
     * @param string $input A utf-8 string.
     * @return integer|float|false
     */
    public static function parseIPv4Number($input)
    {
        if ($input === '') {
            $number = 0;
        } elseif (preg_match('/^(?:(?<R16>0x[0-9A-F]*)|(?<R8>0[0-7]+)|(?<R10>0|[1-9][0-9]*))$/ui', $input, $matches) === 1) {
            if ($matches['R16'] !== '') {
                $number = hexdec($input);
            } elseif ($matches['R8'] !== '') {
                $number = octdec($input);
            } else {
                $number = (float)$input > PHP_INT_MAX ? (float)$input : (int)$input;
            }
        } else {
            $number = false;
        }
        return $number;
    }
    
    /**
     * The IPv4 parser.
     * @link https://url.spec.whatwg.org/#concept-ipv4-parser URL Standard
     * @param string $input A utf-8 string.
     * @return integer|float|string|false
     */
    public static function parseIPv4($input)
    {
        $parts = explode('.', $input);
        if ($parts[count($parts) - 1] === '') {
            array_pop($parts);
        }
        
        if ($parts === []) {
            $ipv4 = '';
        } elseif (count($parts) > 4) {
            $ipv4 = (string)$input;
        } else {
            $numbers = [];
            foreach ($parts as $i => $part) {
                if ($part === '') {
                    $ipv4 = (string)$input;
                    break;
                }
                $n = self::parseIPv4Number($part);
                if ($n === false) {
                    $ipv4 = (string)$input;
                    break;
                }
                if ($n > 255 && $i !== count($parts) - 1) {
                    $ipv4 = false;
                }
                $numbers[] = $n;
            }
            
            if (!isset($ipv4)) {
                $ipv4 = array_pop($numbers);
                if ($ipv4 >= pow(256, 4 - count($numbers))) {
                    $ipv4 = false;
                } else {
                    foreach ($numbers as $counter => $n) {
                        $ipv4 += $n * pow(256, 3 - $counter);
                    }
                }
            }
        }
        
        return $ipv4;
    }
    
    /**
     * The IPv6 parser.
     * @link https://url.spec.whatwg.org/#concept-ipv6-parser URL Standard
     * @param string $input A utf-8 string.
     * @return integer[] An array of a 16-bit unsigned integer.
     */
    public static function parseIPv6($input)
    {
        return filter_var($input, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)
            ? array_values(unpack('n*', inet_pton($input)))
            : false;
    }
    
    /**
     * The host serializer.
     * @link https://url.spec.whatwg.org/#concept-host-serializer URL Standard
     * @param string|integer|float|integer[] $host
     *      A domain, IPv4 address (an integer or float) or IPv6 address (an array of a 16-bit unsigned integer).
     * @return string
     */
    public static function serializeHost($host)
    {
        if (is_int($host) || is_float($host) || $host instanceof \SplInt || $host instanceof \SplFloat) {
            $string = self::serializeIPv4($host);
        } elseif (is_array($host)) {
            $string = '[' . self::serializeIPv6($host) . ']';
        } else {
            $string = (string)$host;
        }
        return $string;
    }
    
    /**
     * The IPv4 serializer.
     * @link https://url.spec.whatwg.org/#concept-ipv4-serializer URL Standard
     * @param integer|float $address An integer or float in the range 0 to 0xFFFFFFFF.
     * @return string
     */
    public static function serializeIPv4($address)
    {
        return long2ip($address);
    }
    
    /**
     * The IPv6 serializer.
     * @link https://url.spec.whatwg.org/#concept-ipv6-serializer URL Standard
     * @param integer[] $address An array of a 16-bit unsigned integer.
     * @return string
     */
    public static function serializeIPv6($address)
    {
        $output = inet_ntop(call_user_func_array('pack', array_merge(['n*'], $address)));
        return strpos($output, '.') !== false
            ? '::ffff:' . strtolower(dechex($address[6]) . ':' . dechex($address[7]))
            : $output;
    }
}
