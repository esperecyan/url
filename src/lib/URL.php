<?php
namespace esperecyan\url\lib;

/**
 * A universal identifier.
 * A URL consists of components,
 * namely a scheme, scheme data, username, password, host, port, path, query, and fragment.
 * @link https://url.spec.whatwg.org/#urls URL Standard
 */
class URL
{
    /**
     * @var string A URL’s scheme is a string that identifies the type of URL
     *      and can be used to dispatch a URL for further processing after parsing.
     * @link https://url.spec.whatwg.org/#concept-url-scheme URL Standard
     */
    public $scheme = '';
    
    /**
     * @var string A URL’s username is a string identifying a user.
     * @link https://url.spec.whatwg.org/#concept-url-username URL Standard
     */
    public $username = '';
    
    /**
     * @var string|null A URL’s password is either null or a string identifying a user’s credentials.
     * @link https://url.spec.whatwg.org/#concept-url-password URL Standard
     */
    public $password = null;
    
    /**
     * @var string|integer|float|integer[]|null A URL’s host is either null or a host.
     * @link https://url.spec.whatwg.org/#concept-url-host URL Standard
     */
    public $host = null;
    
    /**
     * @var string A URL’s port is a string that identifies a networking port.
     * @link https://url.spec.whatwg.org/#concept-url-port URL Standard
     */
    public $port = '';
    
    /**
     * @var string[] A URL’s path is a list of zero or more strings holding data,
     *      usually identifying a location in hierarchical form.
     * @link https://url.spec.whatwg.org/#concept-url-path URL Standard
     */
    public $path = [];
    
    /**
     * @var string|null A URL’s query is either null or a string holding data.
     * @link https://url.spec.whatwg.org/#concept-url-query URL Standard
     */
    public $query = null;
    
    /**
     * @var string A URL’s fragment is either null or a string holding data
     *      that can be used for further processing on the resource the URL’s other components identify.
     * @link https://url.spec.whatwg.org/#concept-url-fragment URL Standard
     */
    public $fragment = null;
    
    /**
     * @var A URL also has an associated non-relative flag.
     * @link https://url.spec.whatwg.org/#non_relative-flag URL Standard
     */
    public $nonRelativeFlag = false;
    
    /**
     * @var object|null A URL also has an associated object that is either null or a Blob object.
     * @link https://url.spec.whatwg.org/#concept-url-object URL Standard
     */
    public $object = null;
    
    /**
     * @var (string|null)[] A special scheme is a scheme in the key of this array.
     *      A default port is a special scheme’s optional corresponding port and is in the value on the key.
     * @link https://url.spec.whatwg.org/#special-scheme URL Standard
     */
    public static $specialSchemes = [
        'ftp'    =>  '21',
        'file'   =>  null,
        'gopher' =>  '70',
        'http'   =>  '80',
        'https'  => '443',
        'ws'     =>  '80',
        'wss'    => '443',
    ];
    
    /**
     * A URL is special if its scheme is a special scheme.
     * @link https://url.spec.whatwg.org/#is-special URL Standard
     * @return boolean Return true if a URL is special.
     */
    public function isSpecial()
    {
        return array_key_exists($this->scheme, self::$specialSchemes);
    }
    
    /**
     * @var string[] A local scheme is a scheme that is one of "about", "blob", "data", and "filesystem".
     * @link https://url.spec.whatwg.org/#local-scheme URL Standard
     */
    public static $localSchemes = ['about', 'blob', 'data', 'filesystem'];
    
    /**
     * A URL is local if its scheme is a local scheme.
     * @link https://url.spec.whatwg.org/#is-local URL Standard
     * @return boolean Return true if a URL is local.
     */
    public function isLocal()
    {
        return in_array($this->scheme, self::$localSchemes);
    }
    
    /**
     * A URL includes credentials if either its username is not the empty string or its password is non-null.
     * @link https://url.spec.whatwg.org/#include-credentials URL Standard
     * @return boolean Return true if a URL includes credentials.
     */
    public function isIncludingCredentials()
    {
        return $this->username !== '' || !is_null($this->password);
    }
    
    /**
     * If url’s scheme is not "file" or url’s path does not contain a single string that is a Windows drive letter,
     * remove url’s path’s last string, if any.
     * @link https://url.spec.whatwg.org/#pop-a-urls-path URL Standard
     */
    public function popPath()
    {
        if ($this->scheme !== 'file'
            || !(count($this->path) === 1 && preg_match('/^[a-z]:$/i', $this->path[0]) === 1)) {
            array_pop($this->path);
        }
    }
    
    /**
     * @var string The regular expression (PCRE) pattern matching the URL code points.
     * @link https://url.spec.whatwg.org/#url-code-points URL Standard
     */
    const URL_CODE_POINTS = '/[!$&\'()*+,\\-.\\/:;=?@_~\xC2\xA0-퟿-﷏ﷰ-�𐀀-🿽𠀀-𯿽𰀀-𿿽񀀀-񏿽񐀀-񟿽񠀀-񯿽񰀀-񿿽򀀀-򏿽򐀀-򟿽򠀀-򯿽򰀀-򿿽󀀀-󏿽󐀀-󟿽󠀀-󯿽󰀀-󿿽􀀀-􏿽]/u';
    
    private function __construct()
    {
    }
    
    /**
     * The URL parser.
     * @link https://url.spec.whatwg.org/#concept-url-parser URL Standard
     * @param string $input A utf-8 string.
     * @param URL|null $base A base URL.
     * @param string|null $encodingOverride A valid name of an encoding.
     * @return URL|false
     */
    public static function parseURL($input, self $base = null, $encodingOverride = null)
    {
        return self::parseBasicURL($input, $base, $encodingOverride);
    }
    
    /**
     * The basic URL parser.
     * @link https://url.spec.whatwg.org/#concept-basic-url-parser URL Standard
     * @param string $input A utf-8 string.
     * @param URL|null $base A base URL.
     * @param string|null $encodingOverride A valid name of an encoding.
     * @param (URL|string)[]|null $urlAndStateOverride An URL ("url" key) and a state override ("state override" key).
     * @throws \DomainException If $urlAndStateOverride['state override'] is invalid.
     * @return URL|false|null
     */
    public static function parseBasicURL(
        $input,
        self $base = null,
        $encodingOverride = null,
        array $urlAndStateOverride = null
    ) {
        if ($urlAndStateOverride) {
            $url = $urlAndStateOverride['url'];
            $stateOverride = (string)$urlAndStateOverride['state override'];
            $string = (string)$input;
            $state = $stateOverride;
        } else {
            $url = new self();
            $stateOverride = null;
            $string = trim($input, "\x00.. ");
            $state = 'scheme start state';
        }
        $encoding = $encodingOverride ? (string)$encodingOverride : 'utf-8';
        $buffer = '';
        $atFlag = false;
        $bracketFlag = false;
        
        $codePoints = preg_split('//u', $string, -1, PREG_SPLIT_NO_EMPTY);
        for ($pointer = 0; true; $pointer++) {
            $c = isset($codePoints[$pointer]) ? $codePoints[$pointer] : '';
            
            switch ($state) {
                case 'scheme start state':
                    if (stripos('abcdefghijklmnopqrstuvwxyz', $c) !== false) {
                        $buffer .= strtolower($c);
                        $state = 'scheme state';
                    } elseif (!$stateOverride) {
                        $state = 'no scheme state';
                        $pointer--;
                    } else {
                        return;
                    }
                    break;

                case 'scheme state':
                    if (stripos('0123456789abcdefghijklmnopqrstuvwxyz+-.', $c) !== false) {
                        $buffer .= strtolower($c);
                    } elseif ($c === ':') {
                        if ($stateOverride && array_key_exists($url->scheme, self::$specialSchemes) !== array_key_exists($buffer, self::$specialSchemes)) {
                            return;
                        }
                        $url->scheme = $buffer;
                        $buffer = '';
                        if ($stateOverride) {
                            return;
                        }
                        if ($url->scheme === 'file') {
                            $state = 'relative state';
                        } elseif ($url->isSpecial() && $base && $base->scheme === $url->scheme) {
                            $state = 'special relative or authority state';
                        } elseif ($url->isSpecial()) {
                            $state = 'special authority slashes state';
                        } elseif (isset($codePoints[$pointer + 1]) && $codePoints[$pointer + 1] === '/') {
                            $state = 'path or authority state';
                            $pointer++;
                        } else {
                            $url->nonRelativeFlag = true;
                            $url->path[] = '';
                            $state = 'non-relative path state';
                        }
                    } elseif (!$stateOverride) {
                        $buffer = '';
                        $state = 'no scheme state';
                        $pointer = -1;
                    } else {
                        return;
                    }
                    break;

                case 'no scheme state':
                    if (!$base || $base->nonRelativeFlag && $c !== '#') {
                        return false;
                    } elseif ($base->nonRelativeFlag && $c === '#') {
                        $url->scheme = $base->scheme;
                        $url->path = $base->path;
                        $url->query = $base->query;
                        $url->fragment = '';
                        $url->nonRelativeFlag = true;
                        $state = 'fragment state';
                    } else {
                        $state = 'relative state';
                        $pointer--;
                    }
                    break;

                case 'special relative or authority state':
                    if ($c === '/' && isset($codePoints[$pointer + 1]) && $codePoints[$pointer + 1] === '/') {
                        $state = 'special authority ignore slashes state';
                        $pointer++;
                    } else {
                        $state = 'relative state';
                        $pointer--;
                    }
                    break;

                case 'path or authority state':
                    if ($c === '/') {
                        $state = 'authority state';
                    } else {
                        $state = 'path state';
                        $pointer--;
                    }
                    break;
                    
                case 'relative state':
                    if ($url->scheme !== 'file') {
                        $url->scheme = $base->scheme;
                    }
                    switch ($c) {
                        case '':
                            $url->username = $base->username;
                            $url->password = $base->password;
                            $url->host = $base->host;
                            $url->port = $base->port;
                            $url->path = $base->path;
                            $url->query = $base->query;
                            break;
                        case '/':
                            $state = 'relative slash state';
                            break;
                        case '?':
                            $url->username = $base->username;
                            $url->password = $base->password;
                            $url->host = $base->host;
                            $url->port = $base->port;
                            $url->path = $base->path;
                            $url->query = '';
                            $state = 'query state';
                            break;
                        case '#':
                            $url->username = $base->username;
                            $url->password = $base->password;
                            $url->host = $base->host;
                            $url->port = $base->port;
                            $url->path = $base->path;
                            $url->query = $base->query;
                            $url->fragment = '';
                            $state = 'fragment state';
                            break;
                        default:
                            if ($c === '\\' && $url->isSpecial()) {
                                $state = 'relative slash state';
                            } else {
                                $remaining = array_slice($codePoints, $pointer + 1);
                                if ($url->scheme !== 'file'
                                    || stripos('abcdefghijklmnopqrstuvwxyz', $c) === false
                                    || strpos(':|', $remaining[0]) === false
                                    || count($remaining) === 1
                                    || strpos('/\\?#', $remaining[1]) === false) {
                                    $url->username = $base->username;
                                    $url->password = $base->password;
                                    $url->host = $base->host;
                                    $url->port = $base->port;
                                    $url->path = $base->path;
                                    array_pop($url->path);
                                }
                                $state = 'path state';
                                $pointer--;
                            }
                    }
                    break;

                case 'relative slash state':
                    if ($c === '/' || $c === '\\' && $url->isSpecial()) {
                        if ($url->scheme === 'file') {
                            $state = 'file host state';
                        } else {
                            $state = 'special authority ignore slashes state';
                        }
                    } else {
                        if ($url->scheme !== 'file') {
                            $url->username = $base->username;
                            $url->password = $base->password;
                            $url->host = $base->host;
                            $url->port = $base->port;
                        }
                        $state = 'path state';
                        $pointer--;
                    }
                    break;

                case 'special authority slashes state':
                    if ($c === '/' && isset($codePoints[$pointer + 1]) && $codePoints[$pointer + 1] === '/') {
                        $state = 'special authority ignore slashes state';
                        $pointer++;
                    } else {
                        $state = 'special authority ignore slashes state';
                        $pointer--;
                    }
                    break;

                case 'special authority ignore slashes state':
                    if (!in_array($c, ['/', '\\'])) {
                        $state = 'authority state';
                        $pointer--;
                    }
                    break;

                case 'authority state':
                    if ($c === '@') {
                        if ($atFlag) {
                            $buffer = '%40' . $buffer;
                        }
                        $atFlag = true;
                        if (is_null($url->password)) {
                            $usernameAndPassword = explode(':', str_replace(["\t", "\n", "\r"], '', $buffer), 2);
                            $url->username .= self::percentEncodeCodePoints(
                                PercentEncoding::USERNAME_ENCODE_SET,
                                $usernameAndPassword[0]
                            );
                            if (isset($usernameAndPassword[1])) {
                                $url->password = self::percentEncodeCodePoints(
                                    PercentEncoding::PASSWORD_ENCODE_SET,
                                    $usernameAndPassword[1]
                                );
                            }
                        } else {
                            $url->password .= self::percentEncodeCodePoints(
                                PercentEncoding::PASSWORD_ENCODE_SET,
                                $buffer
                            );
                        }
                        $buffer = '';
                    } elseif (in_array($c, ['', '/', '?', '#']) || $c === '\\' && $url->isSpecial()) {
                        $pointer -= mb_strlen($buffer, 'utf-8') + 1;
                        $buffer = '';
                        $state = 'host state';
                    } else {
                        $buffer .= $c;
                    }
                    break;

                case 'host state':
                case 'hostname state':
                    if ($c === ':' && !$bracketFlag) {
                        if ($buffer === '' && $url->isSpecial()) {
                            return false;
                        }
                        $host = HostProcessing::parseHost($buffer);
                        if ($host === false) {
                            return false;
                        }
                        $url->host = $host;
                        $buffer = '';
                        $state = 'port state';
                        if ($stateOverride === 'hostname state') {
                            return;
                        }
                    } elseif (in_array($c, ['', '/', '?', '#']) || $c === '\\' && $url->isSpecial()) {
                        $pointer--;
                        if ($buffer === '' && $url->isSpecial()) {
                            return false;
                        }
                        $host = HostProcessing::parseHost($buffer);
                        if ($host === false) {
                            return false;
                        }
                        $url->host = $host;
                        $buffer = '';
                        $state = 'path start state';
                        if ($stateOverride) {
                            return;
                        }
                    } elseif (strpos("\t\n\r", $c) !== false) {
                    } else {
                        if ($c === '[') {
                            $bracketFlag = true;
                        }
                        if ($c === ']') {
                            $bracketFlag = false;
                        }
                        $buffer .= $c;
                    }
                    break;

                case 'file host state':
                    if (in_array($c, ['', '/', '\\', '?', '#'])) {
                        $pointer--;
                        if (strlen($buffer) === 2
                            && stripos('abcdefghijklmnopqrstuvwxyz', $buffer[0]) !== false
                            && strpos(':|', $buffer[1]) !== false) {
                            $state = 'path state';
                        } elseif ($buffer === '') {
                            $state = 'path start state';
                        } else {
                            $host = HostProcessing::parseHost($buffer);
                            if ($host === false) {
                                return false;
                            }
                            if ($host !== 'localhost') {
                                $url->host = $host;
                            }
                            $buffer = '';
                            $state = 'path start state';
                        }
                    } elseif (strpos("\t\n\r", $c) !== false) {
                    } else {
                        $buffer .= $c;
                    }
                    break;

                case 'port state':
                    if (ctype_digit($c)) {
                        $buffer .= $c;
                    } elseif (in_array($c, ['', '/', '?', '#']) || $c === '\\' && $url->isSpecial() || $stateOverride) {
                        if ($buffer !== '') {
                            $buffer = (string)(int)$buffer;
                        }
                        if (isset(self::$specialSchemes[$url->scheme])
                            && self::$specialSchemes[$url->scheme] === $buffer) {
                            $buffer = '';
                        }
                        $url->port = $buffer;
                        if ($stateOverride) {
                            return;
                        }
                        $buffer = '';
                        $state = 'path start state';
                        $pointer--;
                    } elseif (strpos("\t\n\r", $c) !== false) {
                    } else {
                        return false;
                    }
                    break;

                case 'path start state':
                    $state = 'path state';
                    if (!($c === '/' || $c === '\\' && $url->isSpecial())) {
                        $pointer--;
                    }
                    break;

                case 'path state':
                    if (in_array($c, ['', '/']) || $c === '\\' && $url->isSpecial()
                        || !$stateOverride && in_array($c, ['?', '#'])) {
                        switch (strtolower($buffer)) {
                            case '%2e':
                                $buffer = '.';
                                break;
                            case '.%2e':
                            case '%2e.':
                            case '%2e%2e':
                                $buffer = '..';
                                break;
                        }
                        if ($buffer === '..') {
                            if ($url->path) {
                                array_pop($url->path);
                            }
                            if (!($c === '/' || $c === '\\' && $url->isSpecial())) {
                                $url->path[] = '';
                            }
                        } elseif ($buffer === '.' && !($c === '/' || $c === '\\' && $url->isSpecial())) {
                            $url->path[] = '';
                        } elseif ($buffer !== '.') {
                            if ($url->scheme === 'file'
                                && !$url->path
                                && strlen($buffer) === 2
                                && stripos('abcdefghijklmnopqrstuvwxyz', $buffer[0]) !== false && $buffer[1] === '|') {
                                $buffer[1] = ':';
                            }
                            $url->path[] = $buffer;
                        }
                        $buffer = '';
                        if ($c === '?') {
                            $url->query = '';
                            $state = 'query state';
                        } elseif ($c === '#') {
                            $url->fragment = '';
                            $state = 'fragment state';
                        }
                    } elseif (strpos("\t\n\r", $c) !== false) {
                    } else {
                        $buffer .= PercentEncoding::utf8PercentEncode(PercentEncoding::DEFAULT_ENCODE_SET, $c);
                    }
                    break;

                case 'non-relative path state':
                    if ($c === '?') {
                        $url->query = '';
                        $state = 'query state';
                    } elseif ($c === '#') {
                        $url->fragment = '';
                        $state = 'fragment state';
                    } else {
                        if ($c !== '' && strpos("\t\n\r", $c) === false) {
                            $url->path[0] .= PercentEncoding::utf8PercentEncode(PercentEncoding::SIMPLE_ENCODE_SET, $c);
                        }
                    }
                    break;

                case 'query state':
                    if ($c === '' || !$stateOverride && $c === '#') {
                        if (!$url->isSpecial() || $url->scheme === 'ws' || $url->scheme === 'wss') {
                            $encoding = 'utf-8';
                        }
                        $buffer = URLencoding::encode($buffer, $encoding);
                        $url->query = self::percentEncodeCodePoints('/[^!$-;=?-~]/', $buffer);
                        $buffer = '';
                        if ($c === '#') {
                            $url->fragment = '';
                            $state = 'fragment state';
                        }
                    } elseif (strpos("\t\n\r", $c) !== false) {
                    } else {
                        $buffer .= $c;
                    }
                    break;

                case 'fragment state':
                    if ($c !== '') {
                        $url->fragment .= str_replace(
                            ["\x00", "\t", "\n", "\r"],
                            '',
                            implode('', array_slice($codePoints, $pointer))
                        );
                    }
                    break 2;

                default:
                    throw new \DomainException(sprintf('"%s" is an unknown state', $state));
            }
            
            if ($pointer >= 0 && !isset($codePoints[$pointer])) {
                break;
            }
        }
        
        return $url;
    }
    
    /**
     * Set the username of the URL given $username.
     * @link https://url.spec.whatwg.org/#set-the-username URL Standard
     * @param string $username A utf-8 string.
     */
    public function setUsername($username)
    {
        $this->username = self::percentEncodeCodePoints(PercentEncoding::USERNAME_ENCODE_SET, $username);
    }
    
    /**
     * Set the password of the URL given $password.
     * @link https://url.spec.whatwg.org/#set-the-password URL Standard
     * @param string $password A utf-8 string.
     */
    public function setPassword($password)
    {
        $this->password = $password === ''
            ? null
            : self::percentEncodeCodePoints(PercentEncoding::PASSWORD_ENCODE_SET, $password);
    }
    
    /**
     * Percent encode utf-8 string, using an encode set.
     * @param string $encodeSet Regular expression (PCRE) pattern matching exactly one utf-8 character.
     * @param string $codePoints A utf-8 string.
     * @return string
     */
    private static function percentEncodeCodePoints($encodeSet, $codePoints)
    {
        return preg_replace_callback($encodeSet, function ($matches) {
            $result = rawurlencode($matches[0]);
            if ($result[0] !== '%') {
                $result = PercentEncoding::percentEncode($matches[0]);
            }
            return $result;
        }, $codePoints);
    }
    
    /**
     * The URL serializer.
     * @link https://url.spec.whatwg.org/#concept-url-serializer URL Standard
     * @param boolean $excludeFragmentFlag An exclude fragment flag.
     */
    public function serializeURL($excludeFragmentFlag = false)
    {
        $output = $this->scheme . ':';
        if (!is_null($this->host)) {
            $output .= '//';
            if ($this->username !== '' || !is_null($this->password)) {
                $output .= $this->username;
                if (!is_null($this->password)) {
                    $output .= ':' . $this->password;
                }
                $output .= '@';
            }
            $output .= HostProcessing::serializeHost($this->host);
            if ($this->port !== '') {
                $output .= ':' . $this->port;
            }
        }
        $output .= $this->nonRelativeFlag ? $this->path[0] :  '/' . implode('/', $this->path);
        if (!is_null($this->query)) {
            $output .= '?' . $this->query;
        }
        if (!$excludeFragmentFlag && !is_null($this->fragment)) {
            $output .= '#' . $this->fragment;
        }
        return $output;
    }
    
    /**
     * A URL’s origin is the origin, switching on URL’s scheme.
     * @link https://url.spec.whatwg.org/#origin URL Standard
     * @return string[]|string
     *      An array with the first element the scheme, the second element the host and the third element the port.
     *      Or an unique string of 23 characters.
     */
    public function getOrigin()
    {
        switch ($this->scheme) {
            case 'blob':
                $url = self::parseBasicURL($this->path[0]);
                $origin = $url === false ? uniqid('', true) : $url->getOrigin();
                break;
            
            case 'ftp':
            case 'gopher':
            case 'http':
            case 'https':
            case 'ws':
            case 'wss':
                $origin = [
                    $this->scheme,
                    $this->host,
                    $this->port === '' ? self::$specialSchemes[$this->scheme] : $this->port,
                ];
                break;
            
            default:
                $origin = uniqid('', true);
        }
        return $origin;
    }
}
