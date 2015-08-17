<?php
namespace esperecyan\url\lib;

use esperecyan\webidl\TypeHinter;
use esperecyan\url\URLSearchParams;

/**
 * The URLUtilsReadOnly trait defines utility methods to work with URLs.
 * It defines only non-modifying methods intended to be used on data that cannot be changed.
 * Classes using this trait must use setInput() to set input, url, and query object.
 * @link https://url.spec.whatwg.org/#urlutilsreadonly URL Standard
 * @link https://developer.mozilla.org/docs/Web/API/URLUtilsReadOnly URLUtilsReadOnly - Web API Interfaces | MDN
 * @property-read string $href Is a USVString containing the whole URL.
 * @property-read string $origin Returns a USVString containing the canonical form of the origin of the specific location.
 * @property-read string $protocol Is a USVString containing the protocol scheme of the URL, including the final ':'.
 * @property-read string $host Is a USVString containing the host, that is the hostname, a ':', and the port of the URL.
 * @property-read string $hostname Is a USVString containing the domain of the URL.
 * @property-read string $port Is a USVString containing the port number of the URL.
 * @property-read string $pathname Is a USVString containing an initial '/' followed by the path of the URL.
 * @property-read string $search Is a USVString containing a '?' followed by the parameters of the URL.
 * @property-read string $hash Is a USVString containing a '#' followed by the fragment identifier of the URL.
 */
trait URLUtilsReadOnly
{
    /**
     * Return the appropriate base URL for the object.
     * @link https://url.spec.whatwg.org/#concept-urlutils-get-the-base URL Standard
     * @return URL|null
     */
    abstract protected function getBase();
    
    /**
     * Set the input.
     * @link https://url.spec.whatwg.org/#concept-urlutils-set-the-input URL Standard
     * @param string|null $input A utf-8 string or null.
     * @param URL|null $url
     */
    protected function setInput($input, URL $url = null)
    {
        $this->input = is_null($input) ? null : (string)$input;
        $this->url = $url;
        if (!is_null($input)) {
            $url = URL::parseURL($this->input, $this->getBase(), $this->queryEncoding);
            if ($url !== false) {
                $this->url = $url;
            }
        }
        
        $query = $url && !is_null($url->query) ? $url->query : '';
        
        if (!$this->queryObject) {
            $this->queryObject = new URLSearchParams($query);
            \Closure::bind(function ($queryObject) {
                $queryObject->urlObject = $this;
            }, $this, $this->queryObject)->__invoke($this->queryObject);
        } else {
            \Closure::bind(function ($list, $queryObject) {
                array_splice($queryObject->list, 0, count($queryObject->list), $list);
            }, null, $this->queryObject)->__invoke(
                URLencoding::parseURLencodedString($query),
                $this->queryObject
            );
        }
    }
    
    /**
     * Reset the input.
     * @link https://url.spec.whatwg.org/#reset-the-input URL Standard
     */
    private function resetInput()
    {
        if (!($this instanceof \esperecyan\url\URL) && !is_null($this->input)) {
            $this->setInput($this->input, $this->url);
        }
    }
    
    /**
     * @var string|null An associated input.
     * @link https://url.spec.whatwg.org/#concept-urlutils-input URL Standard
     */
    private $input;
    
    /**
     * @var string An associated query encoding (A valid name of an encoding).
     * @link https://url.spec.whatwg.org/#concept-urlutils-query-encoding URL Standard
     */
    private $queryEncoding = 'utf-8';
    
    /**
     * @var \esperecyan\url\URLSearchParams|null An associated query object.
     * @link https://url.spec.whatwg.org/#concept-urlutils-query-object URL Standard
     */
    private $queryObject = null;
    
    /**
     * @var URL|null An associated URL.
     * @link https://url.spec.whatwg.org/#concept-urlutils-url URL Standard
     */
    private $url = null;
    
    /**
     * @param string $name
     * @param string $value
     */
    public function __set($name, $value)
    {
        if ($this->__isset($name)) {
            TypeHinter::throwReadonlyException();
        } else {
            TypeHinter::triggerVisibilityErrorOrDefineProperty();
        }
    }
    
    /**
     * @param string $name
     * @return string $value
     */
    public function __get($name)
    {
        switch ($name) {
            case 'href':
                $this->resetInput();
                if (is_null($this->input)) {
                    $value = '';
                } elseif (!$this->url) {
                    $value = $this->input;
                } else {
                    $value = $this->url->serializeURL();
                }
                break;
            
            case 'origin':
                $this->resetInput();
                $value = $this->url ? self::unicodeSerialiseOrigin($this->url->getOrigin()) : '';
                break;
            
            case 'protocol':
                $this->resetInput();
                $value = ($this->url ? $this->url->scheme : '') . ':';
                break;
            
            case 'host':
                $this->resetInput();
                $value = $this->url && $this->url->host
                    ? HostProcessing::serializeHost($this->url->host)
                        . (is_null($this->url->port) ? '' : ':' . $this->url->port)
                    : '';
                break;
            
            case 'hostname':
                $this->resetInput();
                $value = $this->url && $this->url->host ? HostProcessing::serializeHost($this->url->host) : '';
                break;
            
            case 'port':
                $this->resetInput();
                $value = is_null($this->url) || is_null($this->url->port) ? '' : (string)$this->url->port;
                break;
            
            case 'pathname':
                $this->resetInput();
                if (!$this->url) {
                    $value = '';
                } elseif ($this->url->nonRelativeFlag) {
                    $value = $this->url->path[0];
                } else {
                    $value = '/' . implode('/', $this->url->path);
                }
                break;
            
            case 'search':
                $this->resetInput();
                $value = $this->url && !is_null($this->url->query) && $this->url->query !== ''
                    ? '?' . $this->url->query
                    : '';
                break;
            
            case 'hash':
                $value = $this->url && !is_null($this->url->fragment) && $this->url->fragment !== ''
                    ? '#' . $this->url->fragment
                    : '';
                break;
            
            default:
                TypeHinter::triggerVisibilityErrorOrUndefinedNotice();
                $value = null;
        }
        
        return $value;
    }
    
    /**
     * The Unicode serialisation of an origin.
     * @link https://html.spec.whatwg.org/multipage/browsers.html#unicode-serialisation-of-an-origin HTML Standard
     * @param string[]|string $origin
     * @return string
     */
    private static function unicodeSerialiseOrigin($origin)
    {
        if (!is_array($origin)) {
            $result = 'null';
        } else {
            $result = $origin[0] . '://' . HostProcessing::domainToUnicode($origin[1]);
            if (isset(URL::$specialSchemes[$origin[0]]) && $origin[2] !== URL::$specialSchemes[$origin[0]]) {
                $result .= ':' . $origin[2];
            }
        }
        return $result;
    }
    
    /**
     * @param string $name
     * @return boolean
     */
    public function __isset($name)
    {
        return in_array(
            $name,
            ['href', 'origin', 'protocol', 'host', 'hostname', 'port', 'pathname', 'search', 'hash']
        );
    }
    
    /**
     * The URLUtils::__toString() stringifier method returns a USVString containing the whole URL.
     * It is a read-only version of URLUtils::href.
     * @link https://url.spec.whatwg.org/#URLUtils-stringification-behavior URL Standard
     * @link https://url.spec.whatwg.org/#URLUtilsReadOnly-stringification-behavior URL Standard
     * @link https://developer.mozilla.org/docs/Web/API/URLUtils/toString URLUtils.toString() - Web API Interfaces | MDN
     * @link https://developer.mozilla.org/docs/Web/API/URLUtilsReadOnly/toString URLUtilsReadOnly.toString() - Web API Interfaces | MDN
     * @return string USVString.
     */
    public function __toString()
    {
        return $this->__get('href');
    }
}
