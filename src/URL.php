<?php
namespace esperecyan\url;

use esperecyan\webidl\TypeHinter;
use esperecyan\webidl\TypeError;
use esperecyan\url\lib\URLUtils;
use esperecyan\url\lib\URLUtilsSearchParams;

/**
 * The URL class represent an object providing static methods used for creating object URLs.
 * @link https://url.spec.whatwg.org/#URL URL Standard
 * @link https://developer.mozilla.org/docs/Web/API/URL URL - Web API Interfaces | MDN
 */
class URL
{
    use URLUtils, URLUtilsSearchParams;
    
    /**
     * The constructor returns a newly created URL object representing the URL defined by the parameters.
     * @link https://url.spec.whatwg.org/#dom-URL-URL URL Standard
     * @link https://developer.mozilla.org/docs/Web/API/URL/URL URL() - Web API Interfaces | MDN
     * @param string $url Is a DOMString representing an absolute or relative URL.
     *      If $url is a relative URL, $base which is present, will be used as the base URL.
     *      If $url is an absolute URL, $base are ignored.
     * @param string $base Is a DOMString representing the base URL to use in case $url is a relative URL.
     *      If not specified, and no $base is passed in parameters, it default to 'about:blank'.
     * @throws TypeError If the given base URL or the resulting URL are not valid URLs, a TypeError is thrown.
     */
    public function __construct($url, $base = null)
    {
        if (!is_null($base)) {
            $this->baseURL = lib\URL::parseBasicURL(TypeHinter::to('USVString', $base, 1));
            if ($this->baseURL === false) {
                throw new TypeError(sprintf('<%s> is not a valid URL', $base));
            }
        }
        $parsedURL = lib\URL::parseBasicURL(TypeHinter::to('USVString', $url, 0), $this->baseURL);
        if ($parsedURL === false) {
            throw new TypeError(sprintf('<%s> is not a valid URL', $url));
        }
        
        $this->setInput('', $parsedURL);
    }
    
    /** @var lib\URL|null */
    private $baseURL = null;
    
    /**
     * Return the result of running the basic URL parser on $base of __construct($url, $base).
     * @link https://url.spec.whatwg.org/#dom-URL-URL URL Standard
     * @return lib\URL|null
     */
    protected function getBase()
    {
        return $this->baseURL = null;
    }
    
    /**
     * Convert domain name to IDNA ASCII form.
     * @link https://url.spec.whatwg.org/#dom-URL-domainToASCII URL Standard
     * @param string $domain
     * @return string Returns an empty string if $domain is an IPv6 address or an invalid domain.
     */
    public static function domainToASCII($domain)
    {
        $asciiDomain = lib\HostProcessing::parseHost(TypeHinter::to('USVString', $domain));
        return is_string($asciiDomain) ? $asciiDomain : '';
    }
    
    /**
     * Convert domain name from IDNA ASCII to Unicode.
     * @link https://url.spec.whatwg.org/#dom-URL-domainToASCII URL Standard
     * @param string $domain
     * @return string Returns an empty string if $domain is an IPv6 address or an invalid domain.
     */
    public static function domainToUnicode($domain)
    {
        $unicodeDomain = lib\HostProcessing::parseHost(TypeHinter::to('USVString', $domain), true);
        return is_string($unicodeDomain) ? $unicodeDomain : '';
    }
}
