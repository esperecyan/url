<?php
namespace esperecyan\url\lib;

use esperecyan\webidl\TypeHinter;
use esperecyan\webidl\TypeError;

/**
 * The URLUtils trait defines utility methods to work with URLs.
 * @link https://url.spec.whatwg.org/#urlutils URL Standard
 * @link https://developer.mozilla.org/docs/Web/API/URLUtils URLUtils - Web API Interfaces | MDN
 * @property string $href Is a USVString containing the whole URL.
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
 * @property \esperecyan\url\URLSearchParams $searchParams
 *      Returns a URLSearchParams object allowing to access the GET query arguments contained in the URL.
 * @property string $hash Is a USVString containing a '#' followed by the fragment identifier of the URL.
 */
trait URLUtils
{
    use URLUtilsReadonly {
        URLUtilsReadonly::__set as URLUtilsReadonly__set;
        URLUtilsReadonly::__isset as URLUtilsReadonly__isset;
        URLUtilsReadonly::__get as URLUtilsReadonly__get;
    }
    
    /**
     * Optional abstract method as the update steps.
     * @link https://url.spec.whatwg.org/#concept-urlutils-update URL Standard
     * @param string $value A utf-8 string.
     */
    protected function updateSteps($value)
    {
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
     * @param string|\esperecyan\url\URLSearchParams $value
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
                if ($name === 'searchParams' && in_array(
                    'esperecyan\\url\\lib\\URLUtilsSearchParams',
                    (new \ReflectionClass(__CLASS__))->getTraitNames()
                )) {
                    $object = TypeHinter::to('esperecyan\\url\\URLSearchParams', $value);
                    \Closure::bind(function ($queryObject, $object) {
                        array_splice($queryObject->urlObjects, array_search($this, $queryObject->urlObjects, true), 1);
                        $object->urlObjects[] = $this;
                    }, $this, $this->queryObject)->__invoke($this->queryObject, $object);
                    $this->queryObject = $object;
                    if ($this->url) {
                        $this->url->query = (string)$this->queryObject;
                        $this->preUpdate();
                    }
                } else {
                    $this->URLUtilsReadonly__set($name, $value);
                }
        }
    }
    
    /**
     * @param string $name
     * @return string|\esperecyan\url\URLSearchParams
     */
    public function __get($name)
    {
        switch ($name) {
            case 'username':
                $value = $this->url ? $this->url->username : '';
                break;
            
            case 'password':
                $value = $this->url && !is_null($this->url->password) ? $this->url->password : '';
                break;
            
            case 'searchParams':
                $value = $this->queryObject;
                break;
            
            default:
                if ($name === 'searchParams' && in_array(
                    'esperecyan\\url\\lib\\URLUtilsSearchParams',
                    (new \ReflectionClass(__CLASS__))->getTraitNames()
                )) {
                    $value = $this->queryObject;
                } else {
                    $value = $this->URLUtilsReadonly__get($name);
                }
                break;
        }
        
        return $value;
    }
    
    /**
     * @param string $name
     * @return boolean
     */
    public function __isset($name)
    {
        return in_array($name, ['username', 'password']) || $name === 'searchParams' && in_array(
            'esperecyan\\url\\lib\\URLUtilsSearchParams',
            (new \ReflectionClass(__CLASS__))->getTraitNames()
        ) || $this->URLUtilsReadonly__isset($name);
    }
}
