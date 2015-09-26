<?php
namespace esperecyan\url\lib;

use esperecyan\webidl\TypeHinter;
use esperecyan\webidl\TypeError;
use esperecyan\url\URLSearchParams;

/**
 * The URLUtils trait defines utility methods to work with URLs.
 * Classes using this trait must use setInput() to set input, url, and query object.
 * @link https://url.spec.whatwg.org/#urlutils URL Standard
 * @link https://developer.mozilla.org/docs/Web/API/URLUtils URLUtils - Web API Interfaces | MDN
 * @property string $href Is a USVString containing the whole URL.
 * @property-read string $origin
 *      Returns a USVString containing the canonical form of the origin of the specific location.
 * @property string $protocol Is a USVString containing the protocol scheme of the URL, including the final ':'.
 * @property string $username Is a USVString containing the username specified before the domain name.
 * @property string $password Is a USVString containing the password specified before the domain name.
 * @property string $host Is a USVString containing the host, that is the hostname,
 *      and then, if the port of the URL is not empty (which can happen because it was not specified
 *          or because it was specified to be the default port of the URL's scheme), a ':', and the port of the URL.
 * @property string $hostname Is a USVString containing the domain of the URL.
 * @property string $port Is a USVString containing the port number of the URL.
 * @property string $pathname Is a USVString containing an initial '/' followed by the path of the URL.
 * @property string $search Is a USVString containing a '?' followed by the parameters of the URL.
 * @property string $hash Is a USVString containing a '#' followed by the fragment identifier of the URL.
 */
trait URLUtils
{
    /**
     * Return the appropriate base URL for the object.
     * @link https://url.spec.whatwg.org/#concept-urlutils-get-the-base URL Standard
     * @return URL|null
     */
    abstract protected function getBase();
    
    /**
     * Optional abstract method as the update steps.
     * @link https://url.spec.whatwg.org/#concept-urlutils-update URL Standard
     * @param string $value A utf-8 string.
     */
    protected function updateSteps($value)
    {
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
     * @var URLSearchParams|null An associated query object.
     * @link https://url.spec.whatwg.org/#concept-urlutils-query-object URL Standard
     */
    private $queryObject = null;
    
    /**
     * @var URL|null An associated URL.
     * @link https://url.spec.whatwg.org/#concept-urlutils-url URL Standard
     */
    private $url = null;
    
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
     * Run the pre-update steps for an instance of classes using this trait.
     * @link https://url.spec.whatwg.org/#pre_update-steps URL Standard
     * @param string|null $value A utf-8 string.
     */
    private function preUpdate($value = null)
    {
        $this->updateSteps(is_null($value) ? $this->url->serializeURL() : $value);
    }
    
    /**
     * @param string $name
     * @param string $value
     * @throws TypeError
     */
    public function __set($name, $value)
    {
        if (in_array(
            $name,
            ['href', 'protocol', 'username', 'password', 'host', 'hostname', 'port', 'pathname', 'search', 'hash']
        )) {
            $input = TypeHinter::to('USVString', $value);
        }
        
        switch ($name) {
            case 'href':
                if ($this instanceof \esperecyan\url\URL) {
                    $parsedURL = URL::parseBasicURL($input, $this->getBase());
                    if ($parsedURL === false) {
                        throw new TypeError(sprintf('<%s> is not a valid URL', $input));
                    }
                    $this->setInput('', $parsedURL);
                } else {
                    $this->setInput($input);
                    $this->preUpdate($input);
                }
                break;
            
            case 'protocol':
                if ($this->url) {
                    URL::parseBasicURL($input . ':', null, null, [
                        'url' => $this->url,
                        'state override' => 'scheme start state'
                    ]);
                    $this->preUpdate();
                }
                break;
            
            case 'username':
                if ($this->url && !is_null($this->url->host) && !$this->url->nonRelativeFlag) {
                    $this->url->setUsername($input);
                    $this->preUpdate();
                }
                break;
            
            case 'password':
                if ($this->url && !is_null($this->url->host) && !$this->url->nonRelativeFlag) {
                    $this->url->setPassword($input);
                    $this->preUpdate();
                }
                break;
            
            case 'host':
                if ($this->url && !$this->url->nonRelativeFlag) {
                    URL::parseBasicURL($input, null, null, [
                        'url' => $this->url,
                        'state override' => 'host state'
                    ]);
                    $this->preUpdate();
                }
                break;
            
            case 'hostname':
                if ($this->url && !$this->url->nonRelativeFlag) {
                    URL::parseBasicURL($input, null, null, [
                        'url' => $this->url,
                        'state override' => 'hostname state'
                    ]);
                    $this->preUpdate();
                }
                break;
            
            case 'port':
                if ($this->url && !is_null($this->url->host) && !$this->url->nonRelativeFlag
                    && $this->url->scheme !== 'file') {
                    URL::parseBasicURL($input, null, null, [
                        'url' => $this->url,
                        'state override' => 'port state'
                    ]);
                    $this->preUpdate();
                }
                break;
            
            case 'pathname':
                if ($this->url && !$this->url->nonRelativeFlag) {
                    $this->url->path = [];
                    URL::parseBasicURL($input, null, null, [
                        'url' => $this->url,
                        'state override' => 'path start state'
                    ]);
                    $this->preUpdate();
                }
                break;
            
            case 'search':
                if ($this->url) {
                    if ($input === '') {
                        $this->url->query = null;
                        $list = [];
                    } else {
                        $query = $input[0] === '?' ? substr($input, 1) : $input;
                        $this->url->query = '';
                        URL::parseBasicURL($query, null, $this->queryEncoding, [
                            'url' => $this->url,
                            'state override' => 'query state'
                        ]);
                        $list = URLencoding::parseURLencodedString($query);
                    }
                    \Closure::bind(function ($list, $queryObject) {
                        array_splice($queryObject->list, 0, count($queryObject->list), $list);
                        $queryObject->update();
                    }, null, $this->queryObject)->__invoke($list, $this->queryObject);
                }
                break;
            
            case 'hash':
                if ($this->url && $this->url->scheme !== 'javascript') {
                    if ($input === '') {
                        $this->url->fragment = null;
                    } else {
                        $fragment = $input[0] === '#' ? substr($input, 1) : $input;
                        $this->url->fragment = '';
                        URL::parseBasicURL($fragment, null, null, [
                            'url' => $this->url,
                            'state override' => 'fragment state'
                        ]);
                    }
                    $this->preUpdate();
                }
                break;
            
            default:
                if ($name === 'origin'
                    || $name === 'searchParams' && in_array(
                        'esperecyan\\url\\lib\\URLUtilsSearchParams',
                        (new \ReflectionClass(__CLASS__))->getTraitNames()
                    )) {
                    TypeHinter::throwReadonlyException();
                } else {
                    TypeHinter::triggerVisibilityErrorOrDefineProperty();
                }
        }
    }
    
    /**
     * @param string $name
     * @return string|URLSearchParams
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
            
            case 'username':
                $this->resetInput();
                $value = $this->url ? $this->url->username : '';
                break;
            
            case 'password':
                $this->resetInput();
                $value = $this->url && !is_null($this->url->password) ? $this->url->password : '';
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
                if ($name === 'searchParams' && in_array(
                    'esperecyan\\url\\lib\\URLUtilsSearchParams',
                    (new \ReflectionClass(__CLASS__))->getTraitNames()
                )) {
                    $value = $this->queryObject;
                } else {
                    TypeHinter::triggerVisibilityErrorOrUndefinedNotice();
                    $value = null;
                }
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
     * The URLUtils::__toString() stringifier method returns a USVString containing the whole URL.
     * It is a read-only version of URLUtils::href.
     * @link https://url.spec.whatwg.org/#URLUtils-stringification-behavior URL Standard
     * @link https://developer.mozilla.org/docs/Web/API/URLUtils/toString
     *      URLUtils.toString() - Web API Interfaces | MDN
     * @return string USVString.
     */
    public function __toString()
    {
        return $this->__get('href');
    }
    
    /**
     * @param string $name
     * @return boolean
     */
    public function __isset($name)
    {
        return in_array($name, ['href', 'origin', 'protocol', 'username', 'password', 'host', 'hostname', 'port', 'pathname', 'search', 'hash'])
            || $name === 'searchParams' && in_array(
                'esperecyan\\url\\lib\\URLUtilsSearchParams',
                (new \ReflectionClass(__CLASS__))->getTraitNames()
            );
    }
}
