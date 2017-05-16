<?php
namespace esperecyan\url\lib;

/**
 * A universal identifier (a URL record).
 * A URL consists of components,
 * namely a scheme, scheme data, username, password, host, port, path, query, and fragment.
 * @link https://url.spec.whatwg.org/#urls URL Standard
 * @property bool $nonRelativeFlag [Deprecated] Alias of $cannotBeABaseURLFlag.
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
     * @var string A URL’s password is either null or a string identifying a user’s credentials.
     * @link https://url.spec.whatwg.org/#concept-url-password URL Standard
     */
    public $password = '';
    
    /**
     * @var string|int|float|int[]|null A URL’s host is either null or a host.
     * @link https://url.spec.whatwg.org/#concept-url-host URL Standard
     */
    public $host = null;
    
    /**
     * @var int|null A URL’s port is either null or a 16-bit integer that identifies a networking port.
     * @link https://url.spec.whatwg.org/#concept-url-port URL Standard
     */
    public $port = null;
    
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
     * @var A URL also has an associated cannot-be-a-base-URL flag.
     * @link https://url.spec.whatwg.org/#url-cannot-be-a-base-url-flag URL Standard
     */
    public $cannotBeABaseURLFlag = false;
    
    /**
     * @param string $name
     * @return string
     */
    public function __get($name)
    {
        if ($name === 'nonRelativeFlag') {
            return $this->cannotBeABaseURLFlag;
        }
        TypeHinter::triggerVisibilityErrorOrUndefinedNotice();
    }
    
    /**
     * @param string $name
     * @param bool $value
     */
    public function __set($name, $value)
    {
        if ($name === 'nonRelativeFlag') {
            $this->cannotBeABaseURLFlag = $value;
        }
        TypeHinter::triggerVisibilityErrorOrDefineProperty();
    }
    
    /**
     * @var object|null A URL also has an associated object that is either null or a Blob object.
     * @link https://url.spec.whatwg.org/#concept-url-object URL Standard
     */
    public $object = null;
    
    /**
     * @var (int|null)[] A special scheme is a scheme in the key of this array.
     *      A default port is a special scheme’s optional corresponding port and is in the value on the key.
     * @link https://url.spec.whatwg.org/#special-scheme URL Standard
     */
    public static $specialSchemes = [
        'ftp'    =>   21,
        'file'   => null,
        'gopher' =>   70,
        'http'   =>   80,
        'https'  =>  443,
        'ws'     =>   80,
        'wss'    =>  443,
    ];
    
    /**
     * A URL is special if its scheme is a special scheme.
     * @link https://url.spec.whatwg.org/#is-special URL Standard
     * @return bool Return true if a URL is special.
     */
    public function isSpecial()
    {
        return array_key_exists($this->scheme, self::$specialSchemes);
    }
    
    /**
     * @var string[] A local scheme is a scheme that is one of “about”, “blob”, “data”, and “filesystem”.
     * @deprecated 5.0.0 The term “local scheme” has been moved
     *      from the URL Standard specification to the Fetch Standard specification.
     * @link https://github.com/whatwg/url/commit/8fb8684a19b449db4c8920aee6cd3efb41bcdcfd
     *      Editorial: move some terminology to the Fetch Standard · whatwg/url@8fb8684
     * @link https://fetch.spec.whatwg.org/#local-scheme URL Standard
     */
    public static $localSchemes = ['about', 'blob', 'data', 'filesystem'];
    
    /**
     * A URL is local if its scheme is a local scheme.
     * @deprecated 5.0.0 The term “URL is local” has been moved
     *      from the URL Standard specification to the Fetch Standard specification.
     * @link https://github.com/whatwg/url/commit/8fb8684a19b449db4c8920aee6cd3efb41bcdcfd
     *      Editorial: move some terminology to the Fetch Standard · whatwg/url@8fb8684
     * @link https://fetch.spec.whatwg.org/#is-local URL Standard
     * @return bool Return true if a URL is local.
     */
    public function isLocal()
    {
        return in_array($this->scheme, self::$localSchemes);
    }
    
    /**
     * A URL includes credentials if either its username is not the empty string or its password is non-null.
     * @link https://url.spec.whatwg.org/#include-credentials URL Standard
     * @return bool Return true if a URL includes credentials.
     */
    public function isIncludingCredentials()
    {
        return $this->username !== '' || $this->password !== '';
    }
    
    /**
     * A URL cannot have a username/password/port
     *      if its host is null or the empty string, its cannot-be-a-base-URL flag is set, or its scheme is “file”.
     * @link https://url.spec.whatwg.org/#cannot-have-a-username-password-port URL Standard
     * @return bool Return true if a URL cannot have a username/password/port.
     */
    public function cannotHaveUsernamePasswordPort()
    {
        return in_array($this->host, [null, ''], true) || $this->cannotBeABaseURLFlag || $this->scheme === 'file';
    }
    
    /**
     * The regular expression (PCRE) pattern matching a Windows drive letter.
     * @var string
     * @link https://url.spec.whatwg.org/#windows-drive-letter URL Standard
     */
    const WINDOWS_DRIVE_LETTER = '/^[a-z][:|]$/ui';
    
    /**
     * The regular expression (PCRE) pattern matching a normalized Windows drive letter.
     * @var string
     * @link https://url.spec.whatwg.org/#normalized-windows-drive-letter URL Standard
     */
    const NORMALIZED_WINDOWS_DRIVE_LETTER = '/^[a-z]:$/ui';
    
    /**
     * Shortens a path.
     * @link https://url.spec.whatwg.org/#shorten-a-urls-path URL Standard
     */
    public function shortenPath()
    {
        if ($this->scheme !== 'file'
            || !(count($this->path) === 1 && preg_match(static::NORMALIZED_WINDOWS_DRIVE_LETTER, $this->path[0]) === 1)) {
            array_pop($this->path);
        }
    }
    
    /**
     * Alias of shortenPath().
     * @deprecated 5.0.0 The method has been renamed to shortenPath.
     * @see \esperecyan\url\lib\URL::shortenPath()
     * @link https://github.com/whatwg/url/commit/c94f6f2220e9b988f079d1bf903417c1f7695d89
     *      Editorial: pop → shorten · whatwg/url@c94f6f2
     */
    public function popPath()
    {
        $this->shortenPath();
    }
    
    /**
     * The regular expression (PCRE) pattern matching a single-dot path segment.
     * @var string
     * @link https://url.spec.whatwg.org/#syntax-url-path-segment-dot URL Standard
     */
    const SINGLE_DOT_PATH_SEGMENT = '/^(?:\\.|%2e)$/ui';
    
    /**
     * The regular expression (PCRE) pattern matching a double-dot path segment.
     * @var string
     * @link https://url.spec.whatwg.org/#syntax-url-path-segment-dotdot URL Standard
     */
    const DOUBLE_DOT_PATH_SEGMENT = '/^(?:\\.|%2e){2}$/ui';
    
    /**
     * The regular expression (PCRE) pattern matching the URL code points.
     * @var string
     * @link https://url.spec.whatwg.org/#url-code-points URL Standard
     */
    const URL_CODE_POINTS = '/[!$&\'()*+,\\-.\\/:;=?@_~\xC2\xA0-퟿-﷏ﷰ-�𐀀-🿽𠀀-𯿽𰀀-𿿽񀀀-񏿽񐀀-񟿽񠀀-񯿽񰀀-񿿽򀀀-򏿽򐀀-򟿽򠀀-򯿽򰀀-򿿽󀀀-󏿽󐀀-󟿽󠀀-󯿽󰀀-󿿽􀀀-􏿽]/u';
    
    private function __construct()
    {
    }
    
    /**
     * The URL parser.
     * @link https://url.spec.whatwg.org/#concept-url-parser URL Standard
     * @param string $input A UTF-8 string.
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
     * @param string $input A UTF-8 string.
     * @param URL|null $base A base URL.
     * @param string|null $encodingOverride A valid name of an encoding.
     * @param (URL|string)[]|null $urlAndStateOverride An URL (“url” key) and a state override (“state override” key).
     * @throws \DomainException If $urlAndStateOverride['state override'] is invalid.
     * @return URL|false|void
     */
    public static function parseBasicURL(
        $input,
        self $base = null,
        $encodingOverride = null,
        array $urlAndStateOverride = null
    ) {
        $input = str_replace(["\t", "\n", "\r"], '', $input);
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
        $encoding = $encodingOverride ? URLencoding::getOutputEncoding((string)$encodingOverride) : 'UTF-8';
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
                        return false;
                    }
                    break;

                case 'scheme state':
                    if (stripos('0123456789abcdefghijklmnopqrstuvwxyz+-.', $c) !== false) {
                        $buffer .= strtolower($c);
                    } elseif ($c === ':') {
                        if ($stateOverride && (
                            array_key_exists($url->scheme, self::$specialSchemes) !== array_key_exists($buffer, self::$specialSchemes)
                            || ($url->isIncludingCredentials() || !is_null($url->port)) && $buffer === 'file'
                            || $url->scheme === 'file' && in_array($url->host, ['', null], true)
                        )) {
                            return;
                        }
                        $url->scheme = $buffer;
                        $buffer = '';
                        if ($stateOverride) {
                            return;
                        }
                        if ($url->scheme === 'file') {
                            $state = 'file state';
                        } elseif ($url->isSpecial() && $base && $base->scheme === $url->scheme) {
                            $state = 'special relative or authority state';
                        } elseif ($url->isSpecial()) {
                            $state = 'special authority slashes state';
                        } elseif (isset($codePoints[$pointer + 1]) && $codePoints[$pointer + 1] === '/') {
                            $state = 'path or authority state';
                            $pointer++;
                        } else {
                            $url->cannotBeABaseURLFlag = true;
                            $url->path[] = '';
                            $state = 'non-relative path state';
                        }
                    } elseif (!$stateOverride) {
                        $buffer = '';
                        $state = 'no scheme state';
                        $pointer = -1;
                    } else {
                        return false;
                    }
                    break;

                case 'no scheme state':
                    if (!$base || $base->cannotBeABaseURLFlag && $c !== '#') {
                        return false;
                    } elseif ($base->cannotBeABaseURLFlag && $c === '#') {
                        $url->scheme = $base->scheme;
                        $url->path = $base->path;
                        $url->query = $base->query;
                        $url->fragment = '';
                        $url->cannotBeABaseURLFlag = true;
                        $state = 'fragment state';
                    } elseif ($base->scheme !== 'file') {
                        $state = 'relative state';
                        $pointer--;
                    } else {
                        $state = 'file state';
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
                    $url->scheme = $base->scheme;
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
                                $url->username = $base->username;
                                $url->password = $base->password;
                                $url->host = $base->host;
                                $url->port = $base->port;
                                $url->path = $base->path;
                                array_pop($url->path);
                                $state = 'path state';
                                $pointer--;
                            }
                    }
                    break;

                case 'relative slash state':
                    if ($url->isSpecial() && in_array($c, ['/', '\\'])) {
                        $state = 'special authority ignore slashes state';
                    } elseif ($c === '/') {
                        $state = 'authority state';
                    } else {
                        $url->username = $base->username;
                        $url->password = $base->password;
                        $url->host = $base->host;
                        $url->port = $base->port;
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
                        $usernameAndPassword = explode(':', $buffer, 2);
                        $url->username .= Infrastructure::percentEncodeCodePoints(
                            Infrastructure::USERINFO_PERCENT_ENCODE_SET,
                            $usernameAndPassword[0]
                        );
                        if (isset($usernameAndPassword[1])) {
                            $url->password .= Infrastructure::percentEncodeCodePoints(
                                Infrastructure::USERINFO_PERCENT_ENCODE_SET,
                                $usernameAndPassword[1]
                            );
                        }
                        $buffer = '';
                    } elseif (in_array($c, ['', '/', '?', '#']) || $c === '\\' && $url->isSpecial()) {
                        $pointer -= mb_strlen($buffer, 'UTF-8') + 1;
                        $buffer = '';
                        $state = 'host state';
                    } else {
                        $buffer .= $c;
                    }
                    break;

                case 'host state':
                case 'hostname state':
                    if ($stateOverride && $url->scheme === 'file') {
                        $pointer--;
                        $state = 'file host state';
                    } elseif ($c === ':' && !$bracketFlag) {
                        if ($buffer === '' && $url->isSpecial()) {
                            return false;
                        }
                        $host = HostProcessing::parseHost($buffer, $url->isSpecial());
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
                        } elseif ($stateOverride && $buffer === ''
                            && ($url->isIncludingCredentials() || !is_null($url->port))) {
                            return false;
                        }
                        $host = HostProcessing::parseHost($buffer, $url->isSpecial());
                        if ($host === false) {
                            return false;
                        }
                        $url->host = $host;
                        $buffer = '';
                        $state = 'path start state';
                        if ($stateOverride) {
                            return;
                        }
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

                case 'port state':
                    if (ctype_digit($c)) {
                        $buffer .= $c;
                    } elseif (in_array($c, ['', '/', '?', '#']) || $c === '\\' && $url->isSpecial() || $stateOverride) {
                        if ($buffer !== '') {
                            $port = (int)$buffer;
                            if ($port > pow(2, 16) - 1) {
                                return false;
                            }
                            $url->port = isset(self::$specialSchemes[$url->scheme]) && self::$specialSchemes[$url->scheme] === $port
                                ? null
                                : $port;
                            $buffer = '';
                        }
                        if ($stateOverride) {
                            return;
                        }
                        $state = 'path start state';
                        $pointer--;
                    } else {
                        return false;
                    }
                    break;

                case 'file state':
                    $url->scheme = 'file';
                    if (in_array($c, ['/', '\\'])) {
                        $state = 'file slash state';
                    } elseif ($base && $base->scheme === 'file') {
                        switch ($c) {
                            case '':
                                $url->host = $base->host;
                                $url->path = $base->path;
                                $url->query = $base->query;
                                break;
                            case '?':
                                if ($base && $base->scheme === 'file') {
                                    $url->host = $base->host;
                                    $url->path = $base->path;
                                    $url->query = '';
                                    $state = 'query state';
                                }
                                break;
                            case '#':
                                if ($base && $base->scheme === 'file') {
                                    $url->host = $base->host;
                                    $url->path = $base->path;
                                    $url->query = $base->query;
                                    $url->fragment = '';
                                    $state = 'fragment state';
                                }
                                break;
                            default:
                                $remaining = array_slice($codePoints, $pointer + 1);
                                if (count($remaining) === 0
                                    || preg_match(static::WINDOWS_DRIVE_LETTER, $c . $remaining[0]) === 0
                                    || count($remaining) === 2 && strpos('/\\?#', $remaining[1]) === false) {
                                    $url->host = $base->host;
                                    $url->path = $base->path;
                                    $url->shortenPath();
                                }
                                $state = 'path state';
                                $pointer--;
                        }
                    } else {
                        $state = 'path state';
                        $pointer--;
                    }
                    break;

                case 'file slash state':
                    if ($c === '/' || $c === '\\') {
                        $state = 'file host state';
                    } else {
                        if ($base && $base->scheme === 'file') {
                            if (isset($base->path[0])
                                && preg_match(static::NORMALIZED_WINDOWS_DRIVE_LETTER, $base->path[0]) === 1) {
                                $url->path[] = $base->path[0];
                            } else {
                                $url->host = $base->host;
                            }
                        }
                        $state = 'path state';
                        $pointer--;
                    }
                    break;

                case 'file host state':
                    if (in_array($c, ['', '/', '\\', '?', '#'])) {
                        $pointer--;
                        if (!$stateOverride && preg_match(static::WINDOWS_DRIVE_LETTER, $buffer) === 1) {
                            $state = 'path state';
                        } elseif ($buffer === '') {
                            $url->host = '';
                            if ($stateOverride) {
                                return;
                            }
                            $state = 'path start state';
                        } else {
                            $host = HostProcessing::parseHost($buffer, $url->isSpecial());
                            if ($host === false) {
                                return false;
                            }
                            $url->host = $host === 'localhost' ? '' : $host;
                            if ($stateOverride) {
                                return;
                            }
                            $buffer = '';
                            $state = 'path start state';
                        }
                    } else {
                        $buffer .= $c;
                    }
                    break;

                case 'path start state':
                    if ($url->isSpecial()) {
                        $state = 'path state';
                        if (!in_array($c, ['/', '\\'])) {
                            $pointer--;
                        }
                    } elseif (!$stateOverride && $c === '?') {
                        $url->query = '';
                        $state = 'query state';
                    } elseif (!$stateOverride && $c === '#') {
                        $url->fragment = '';
                        $state = 'fragment state';
                    } elseif ($c !== '') {
                        if ($c !== '/') {
                            $pointer--;
                        }
                        $state = 'path state';
                    }
                    break;

                case 'path state':
                    if (in_array($c, ['', '/']) || $c === '\\' && $url->isSpecial()
                        || !$stateOverride && in_array($c, ['?', '#'])) {
                        if (preg_match(self::DOUBLE_DOT_PATH_SEGMENT, $buffer) === 1) {
                            $url->shortenPath();
                            if (!($c === '/' || $c === '\\' && $url->isSpecial())) {
                                $url->path[] = '';
                            }
                        } elseif (preg_match(self::SINGLE_DOT_PATH_SEGMENT, $buffer) === 1
                            && !($c === '/' || $c === '\\' && $url->isSpecial())) {
                            $url->path[] = '';
                        } elseif (preg_match(self::SINGLE_DOT_PATH_SEGMENT, $buffer) !== 1) {
                            if ($url->scheme === 'file'
                                && !$url->path
                                && preg_match(Infrastructure::WINDOWS_DRIVE_LETTER, $buffer) === 1) {
                                if (!in_array($url->host, ['', null], true)) {
                                    $url->host = '';
                                }
                                $buffer[1] = ':';
                            }
                            $url->path[] = $buffer;
                        }
                        $buffer = '';
                        if ($url->scheme === 'file' && in_array($c, ['', '?', '#'], true)) {
                            while (isset($url->path[0]) && $url->path[0] === '') {
                                array_shift($url->path);
                            }
                        }
                        if ($c === '?') {
                            $url->query = '';
                            $state = 'query state';
                        } elseif ($c === '#') {
                            $url->fragment = '';
                            $state = 'fragment state';
                        }
                    } else {
                        $buffer .= Infrastructure::utf8PercentEncode(Infrastructure::PATH_PERCENT_ENCODE_SET, $c);
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
                        if ($c !== '') {
                            $url->path[0]
                                .= Infrastructure::utf8PercentEncode(Infrastructure::C0_CONTROL_PERCENT_ENCODE_SET, $c);
                        }
                    }
                    break;

                case 'query state':
                    if ($c === '' || !$stateOverride && $c === '#') {
                        if (!$url->isSpecial() || $url->scheme === 'ws' || $url->scheme === 'wss') {
                            $encoding = 'UTF-8';
                        }
                        $buffer = URLencoding::encode($buffer, $encoding);
                        $url->query = Infrastructure::percentEncodeCodePoints('/[^!$-;=?-~]/', $buffer);
                        $buffer = '';
                        if ($c === '#') {
                            $url->fragment = '';
                            $state = 'fragment state';
                        }
                    } else {
                        $buffer .= $c;
                    }
                    break;

                case 'fragment state':
                    if ($c !== '') {
                        $url->fragment .= Infrastructure::utf8PercentEncode(
                            Infrastructure::C0_CONTROL_PERCENT_ENCODE_SET,
                            str_replace("\x00", '', implode('', array_slice($codePoints, $pointer)))
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
     * Sets the username of the URL given $username.
     * @link https://url.spec.whatwg.org/#set-the-username URL Standard
     * @param string $username A UTF-8 string.
     */
    public function setUsername($username)
    {
        $this->username
            = Infrastructure::percentEncodeCodePoints(Infrastructure::USERINFO_PERCENT_ENCODE_SET, $username);
    }
    
    /**
     * Sets the password of the URL given $password.
     * @link https://url.spec.whatwg.org/#set-the-password URL Standard
     * @param string $password A UTF-8 string.
     */
    public function setPassword($password)
    {
        $this->password
            = Infrastructure::percentEncodeCodePoints(Infrastructure::USERINFO_PERCENT_ENCODE_SET, $password);
    }
    
    /**
     * The URL serializer.
     * @link https://url.spec.whatwg.org/#concept-url-serializer URL Standard
     * @param bool $excludeFragmentFlag An exclude fragment flag.
     */
    public function serializeURL($excludeFragmentFlag = false)
    {
        $output = $this->scheme . ':';
        if (!is_null($this->host)) {
            $output .= '//';
            if ($this->isIncludingCredentials()) {
                $output .= $this->username;
                if ($this->password !== '') {
                    $output .= ':' . $this->password;
                }
                $output .= '@';
            }
            $output .= HostProcessing::serializeHost($this->host);
            if (!is_null($this->port)) {
                $output .= ':' . $this->port;
            }
        } elseif (is_null($this->host) && $this->scheme === 'file') {
            $output .= '//';
        }
        $output .= $this->cannotBeABaseURLFlag ? $this->path[0] :  '/' . implode('/', $this->path);
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
     * @return (string|int|null)[]|string An array with the first element the scheme, the second element the host,
     *      the third element the port, and the four element the domain. Or an unique string of 23 characters.
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
                    is_null($this->port) ? self::$specialSchemes[$this->scheme] : $this->port,
                    null,
                ];
                break;
            
            default:
                $origin = uniqid('', true);
        }
        return $origin;
    }
}
