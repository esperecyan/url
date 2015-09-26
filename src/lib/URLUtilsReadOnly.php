<?php
namespace esperecyan\url\lib;

use esperecyan\webidl\TypeHinter;

/**
 * @deprecated 3.4.0 URLUtilsReadOnly is now inlined into the HTML standard directly.
 * @link https://github.com/whatwg/url/commit/c2877946857bc904ecb8a5805abc423c82d9da98
 *      Remove URLUtilsReadOnly · whatwg/url@c287794
 * @link https://github.com/whatwg/html/commit/32a7a2092eeff52aca78a0224816a9b327
 *      Inline URLUtilsReadOnly · whatwg/html@32a7a20
 * @property-read string $href Is a USVString containing the whole URL.
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
    use URLUtils;
    
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
}
