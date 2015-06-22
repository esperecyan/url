English / [日本語](README.ja.md)

URL Standard
============
Makes the algorithms and APIs defined by [URL Standard] available on PHP.

[URL Standard]: https://url.spec.whatwg.org/ "The URL Standard defines URLs, domains, IP addresses, the application/x-www-form-urlencoded format, and their API."

Description
-----------
URL Standard is the Web standard specification that replaces the previous standards [RFC 3986] and [RFC 3987].

The specification defines [URL interface] and [URLSearchPrams interface] as [API].
This library allows you to use [esperecyan\url\URL class] as [URL interface] and [esperecyan\url\URLSearchParams] class as [URLSearchPrams interface].
The documents on MDN may be easy to understand by way of explanation of the interfaces https://developer.mozilla.org/docs/Web/API/URL https://developer.mozilla.org/docs/Web/API/URLSearchParams .

This library allows you to use the algorithms defined by URL Standard. For details, see [The correspondence table of the algorithms].

[RFC 3986]: https://tools.ietf.org/html/rfc3986 "A Uniform Resource Identifier (URI) is a compact sequence of characters that identifies an abstract or physical resource. This specification defines the generic URI syntax and a process for resolving URI references that might be in relative form, along with guidelines and security considerations for the use of URIs on the Internet."
[RFC 3987]: https://tools.ietf.org/html/rfc3987 "This document defines a new protocol element, the Internationalized Resource Identifier (IRI), as a complement to the Uniform Resource Identifier (URI)."
[API]: https://url.spec.whatwg.org/#api "URL Standard defines URL’s existing JavaScript API in full detail and add enhancements to make it easier to work with."
[URL interface]: https://url.spec.whatwg.org/#url "URL Standard adds a new URL object as well for URL manipulation without usage of HTML elements. (Useful for JavaScript worker environments.)"
[URLSearchPrams interface]: https://url.spec.whatwg.org/#interface-urlsearchparams
[esperecyan\url\URL class]: https://esperecyan.github.io/url/class-esperecyan.url.URL
[esperecyan\url\URLSearchParams class]: https://esperecyan.github.io/url/class-esperecyan.url.URLSearchParams
[The correspondence table of the algorithms]: #the-correspondence-table-of-the-algorithms

Example
-------
```php
<?php
require_once 'vendor/autoload.php';

use esperecyan\url\URL;

$url = new URL('http://url.test/foobar?name=value');
var_dump($url->protocol, $url->pathname, $url->searchParams->get('name'));
```

The above example will output:

```plain
string(5) "http:"
string(7) "/foobar"
string(5) "value"
```

Requirement
-----------
* PHP 5.4.7 or later
* [mbstring extension module]
* [Intl extension module]
* Library dependencies (is resolved automatically when you use Composer)
	* [esperecyan/webidl]

[mbstring extension module]: http://uk3.php.net/manual/book.mbstring.php "mbstring provides multibyte specific string functions that help you deal with multibyte encodings in PHP."
[Intl extension module]: http://uk3.php.net/manual/book.intl.php "Internationalization extension (is referred as Intl) is a wrapper for ICU library, enabling PHP programmers to perform UCA-conformant collation and date/time/number/currency formatting in their scripts. "
[esperecyan/webidl]: https://github.com/esperecyan/webidl

Install
-------
```sh
composer require esperecyan/url
```

For help with installation of Composer, see [Composer documentation].

[Composer documentation]: https://getcomposer.org/doc/00-intro.md "Composer is a tool for dependency management in PHP. It allows you to declare the dependent libraries your project needs and it will install them in your project for you."

Contribution
------------
1. Fork it ( https://github.com/esperecyan/url )
2. Create your feature branch `git checkout -b my-new-feature`
3. Commit your changes `git commit -am 'Add some feature'`
4. Push to the branch `git push origin my-new-feature`
5. Create new Pull Request

Or

Create new Issue

If you find any mistakes of English in the README or Doc comments or any flaws in tests, please report by such as above means.
I also welcome translations of README too.

Acknowledgement
---------------
I use the code from [コードポイントから UTF-8 の文字を生成する - Qiita] and [UTF-8 の文字からコードポイントを求める - Qiita] in implementing [URLencoding class].

I use [URL Standard (Japanese translation)] as reference in creating this library.

HADAA helped me translate README to English.

[URLencoding class]: src/lib/URLencoding.php
[コードポイントから UTF-8 の文字を生成する - Qiita]: http://qiita.com/masakielastic/items/68f81e1b7d153ee5cc81 "バリデーションの際に想定外の文字が通っていないか調べるには Unicode で定義されるすべての文字を試すことが必要です。UTF-8 の場合、コードポイントの範囲は U+0000 から U+7FFF、U+E000 から U+10FFFF までです。"
[UTF-8 の文字からコードポイントを求める - Qiita]: http://qiita.com/masakielastic/items/5696cf90738c1438f10d "文字の Unicode プロパティやエンコーディングに関する情報を検索で調べる際にコードポイントが必要になることがあります。PHP 5.5 で intl 拡張モジュールに IntlCodePointBreakIterator が追加され、コードポイントを求めやすくなりました。"
[URL Standard (Japanese translation)]: http://www.hcn.zaq.ne.jp/___/WEB/URL-ja.html "このページ は、 WHATWG による，副題の日付 時点の URL Standard を日本語に翻訳したものです。 この翻訳の正確性は保証されません。 この仕様の公式な文書は英語版であり、この日本語訳は公式のものではありません。"

Licence
-------
This library is licensed under the [Mozilla Public License Version 2.0] \(MPL-2.0).

[Mozilla Public License Version 2.0]: https://www.mozilla.org/MPL/2.0/

The correspondence table of the algorithms
------------------------------------------
| [2. Percent-encoded bytes] |                                                           |
|----------------------------|-----------------------------------------------------------|
| [percent encode]           | [esperecyan\url\lib\PercentEncoding::percentEncode()]     |
| [percent decode]           | [esperecyan\url\lib\PercentEncoding::percentDecode()]     |
| [simple encode set]        | [esperecyan\url\lib\PercentEncoding::SIMPLE_ENCODE_SET]   |
| [default encode set]       | [esperecyan\url\lib\PercentEncoding::DEFAULT_ENCODE_SET]  |
| [password encode set]      | [esperecyan\url\lib\PercentEncoding::PASSWORD_ENCODE_SET] |
| [username encode set]      | [esperecyan\url\lib\PercentEncoding::USERNAME_ENCODE_SET] |
| [utf-8 percent encode]     | [esperecyan\url\lib\PercentEncoding::utf8PercentEncode()] |

| [3. Hosts (domains and IP addresses)] |                                                                 |
|---------------------------------------|-----------------------------------------------------------------|
| [domain]                              | A valid utf-8 string                                            |
| [IPv6 address]                        | An array with 8 elements of an integer in the range 0 to 0xFFFF |
| [domain to ASCII]                     | [esperecyan\url\lib\HostProcessing::domainToASCII()]            |
| [domain to Unicode]                   | [esperecyan\url\lib\HostProcessing::domainToUnicode()]          |
| [valid domain]                        | [esperecyan\url\lib\HostProcessing::isValidDomain()]            |
| [host parser]                         | [esperecyan\url\lib\HostProcessing::parseHost()]                |
| [IPv6 parser]                         | [esperecyan\url\lib\HostProcessing::parseIPv6()]                |
| [host serializer]                     | [esperecyan\url\lib\HostProcessing::serializeHost()]            |
| [IPv6 serializer]                     | [esperecyan\url\lib\HostProcessing::serializeIPv6()]            |

| [4. URLs]              |                                                    |
|------------------------|----------------------------------------------------|
| [URL]                  | An instance of [esperecyan\url\lib\URL class]      |
| [scheme]               | [esperecyan\url\lib\URL->scheme]                   |
| [username]             | [esperecyan\url\lib\URL->username]                 |
| [password]             | [esperecyan\url\lib\URL->password]                 |
| [host]                 | [esperecyan\url\lib\URL->host]                     |
| [port]                 | [esperecyan\url\lib\URL->port]                     |
| [path]                 | [esperecyan\url\lib\URL->path]                     |
| [query]                | [esperecyan\url\lib\URL->query]                    |
| [fragment]             | [esperecyan\url\lib\URL->fragment]                 |
| [non relative flag]    | [esperecyan\url\lib\URL->nonRelativeFlag]          |
| [object]               | [esperecyan\url\lib\URL->object]                   |
| [special scheme]       | [esperecyan\url\lib\URL::$specialSchemes]          |
| [is special]           | [esperecyan\url\lib\URL->isSpecial()]              |
| [local scheme]         | [esperecyan\url\lib\URL::$localSchemes]            |
| [is local]             | [esperecyan\url\lib\URL->isLocal()]                |
| [includes credentials] | [esperecyan\url\lib\URL->isIncludingCredentials()] |
| [URL code points]      | [esperecyan\url\lib\URL::URL_CODE_POINTS]          |
| [URL parser]           | [esperecyan\url\lib\URL::parseURL()]               |
| [basic URL parser]     | [esperecyan\url\lib\URL::parseBasicURL()]          |
| [set the username]     | [esperecyan\url\lib\URL->setUsername()]            |
| [set the password]     | [esperecyan\url\lib\URL->setPassword()]            |
| [URL serializer]       | [esperecyan\url\lib\URL->serializeURL()]           |
| [origin]               | [esperecyan\url\lib\URL->getOrigin()]              |

| [5. application/x-www-form-urlencoded]              |                                                            |
|-----------------------------------------------------|------------------------------------------------------------|
| [application/x-www-form-urlencoded parser]          | [esperecyan\url\lib\URLencoding::parseURLencoded()]        |
| [application/x-www-form-urlencoded byte serializer] | [esperecyan\url\lib\URLencoding::serializeURLencodedByte()]|
| [application/x-www-form-urlencoded serializer]      | [esperecyan\url\lib\URLencoding::serializeURLencoded()]    |
| [application/x-www-form-urlencoded string parser]   | [esperecyan\url\lib\URLencoding::parseURLencodedString()]  |

| [6. API]                         |                                                   |
|----------------------------------|---------------------------------------------------|
| [URLUtils interface]             | [esperecyan\url\lib\URLUtils trait]               |
| [URLUtilsSearchParams interface] | [esperecyan\url\lib\URLUtilsSearchParams trait]   |
| [URLUtilsReadOnly interface]     | [esperecyan\url\lib\URLUtilsReadOnly trait]       |
| A [get the base] algorithm       | [esperecyan\url\lib\URLUtilsReadOnly->setInput()] |
| [update steps]                   | [esperecyan\url\lib\URLUtils->updateSteps()]      |

[2. Percent-encoded bytes]: https://url.spec.whatwg.org/#percent-encoded-bytes
[percent encode]: https://url.spec.whatwg.org/#percent-encode
[percent decode]: https://url.spec.whatwg.org/#percent-decode
[simple encode set]: https://url.spec.whatwg.org/#simple-encode-set
[default encode set]: https://url.spec.whatwg.org/#default-encode-set
[password encode set]: https://url.spec.whatwg.org/#password-encode-set
[username encode set]: https://url.spec.whatwg.org/#username-encode-set
[utf-8 percent encode]: https://url.spec.whatwg.org/#utf_8-percent-encode

[3. Hosts (domains and IP addresses)]: https://url.spec.whatwg.org/hosts-(domains-and-ip-addresses)
[domain]: https://url.spec.whatwg.org/#concept-domain
[IPv6 address]: https://url.spec.whatwg.org/#concept-ipv6
[domain to ASCII]: https://url.spec.whatwg.org/#concept-domain-to-ascii
[domain to Unicode]: https://url.spec.whatwg.org/#concept-domain-to-unicode
[valid domain]: https://url.spec.whatwg.org/#valid-domain
[host parser]: https://url.spec.whatwg.org/#concept-host-parser
[IPv6 parser]: https://url.spec.whatwg.org/#concept-ipv6-parser
[host serializer]: https://url.spec.whatwg.org/#concept-host-serializer
[IPv6 serializer]: https://url.spec.whatwg.org/#concept-ipv6-serializer

[4. URLs]: https://url.spec.whatwg.org/#urls
[URL]: https://url.spec.whatwg.org/#concept-url
[scheme]: https://url.spec.whatwg.org/#concept-url-scheme
[username]: https://url.spec.whatwg.org/#concept-url-username
[password]: https://url.spec.whatwg.org/#concept-url-password
[host]: https://url.spec.whatwg.org/#concept-url-host
[port]: https://url.spec.whatwg.org/#concept-url-port
[path]: https://url.spec.whatwg.org/#concept-url-path
[query]: https://url.spec.whatwg.org/#concept-url-query
[fragment]: https://url.spec.whatwg.org/#concept-url-fragment
[non relative flag]: https://url.spec.whatwg.org/#non_relative-flag
[object]: https://url.spec.whatwg.org/#concept-url-object
[special scheme]: https://url.spec.whatwg.org/#special-scheme
[is special]: https://url.spec.whatwg.org/#is-special
[local scheme]: https://url.spec.whatwg.org/#local-scheme
[is local]: https://url.spec.whatwg.org/#is-local
[includes credentials]: https://url.spec.whatwg.org/#include-credentials
[URL code points]: https://url.spec.whatwg.org/#url-code-points
[URL parser]: https://url.spec.whatwg.org/#concept-url-parser
[basic URL parser]: https://url.spec.whatwg.org/#concept-basic-url-parser
[set the username]: https://url.spec.whatwg.org/#set-the-username
[set the password]: https://url.spec.whatwg.org/#set-the-password
[URL serializer]: https://url.spec.whatwg.org/#concept-url-serializer
[origin]: https://url.spec.whatwg.org/#concept-url-origin

[5. application/x-www-form-urlencoded]: https://url.spec.whatwg.org/#application/x-www-form-urlencoded
[application/x-www-form-urlencoded parser]: https://url.spec.whatwg.org/#concept-urlencoded-parser
[application/x-www-form-urlencoded byte serializer]: https://url.spec.whatwg.org/#concept-urlencoded-byte-serializer
[application/x-www-form-urlencoded serializer]: https://url.spec.whatwg.org/#concept-urlencoded-serializer
[application/x-www-form-urlencoded string parser]: https://url.spec.whatwg.org/#concept-urlencoded-string-parser

[6. API]: https://url.spec.whatwg.org/#api
[URLUtils interface]: https://url.spec.whatwg.org/#urlutils
[URLUtilsSearchParams interface]: https://url.spec.whatwg.org/#urlutilssearchparams
[URLUtilsReadOnly interface]: https://url.spec.whatwg.org/#urlutilsreadonly
[get the base]: https://url.spec.whatwg.org/#concept-urlutils-get-the-base
[update steps]: https://url.spec.whatwg.org/#concept-urlutils-update

[esperecyan\url\lib\PercentEncoding::percentEncode()]: https://esperecyan.github.io/url/class-esperecyan.url.lib.PercentEncoding#_percentEncode
[esperecyan\url\lib\PercentEncoding::percentDecode()]: https://esperecyan.github.io/url/class-esperecyan.url.lib.PercentEncoding#_percentDecode
[esperecyan\url\lib\PercentEncoding::SIMPLE_ENCODE_SET]: https://esperecyan.github.io/url/class-esperecyan.url.lib.PercentEncoding#SIMPLE_ENCODE_SET
[esperecyan\url\lib\PercentEncoding::DEFAULT_ENCODE_SET]: https://esperecyan.github.io/url/class-esperecyan.url.lib.PercentEncoding#DEFAULT_ENCODE_SET
[esperecyan\url\lib\PercentEncoding::PASSWORD_ENCODE_SET]: https://esperecyan.github.io/url/class-esperecyan.url.lib.PercentEncoding#PASSWORD_ENCODE_SET
[esperecyan\url\lib\PercentEncoding::USERNAME_ENCODE_SET]: https://esperecyan.github.io/url/class-esperecyan.url.lib.PercentEncoding#USERNAME_ENCODE_SET
[esperecyan\url\lib\PercentEncoding::utf8PercentEncode()]: https://esperecyan.github.io/url/class-esperecyan.url.lib.PercentEncoding#_utf8PercentEncode
[esperecyan\url\lib\HostProcessing::domainToASCII()]: https://esperecyan.github.io/url/class-esperecyan.url.lib.HostProcessing#_domainToASCII
[esperecyan\url\lib\HostProcessing::domainToUnicode()]: https://esperecyan.github.io/url/class-esperecyan.url.lib.HostProcessing#_domainToUnicode
[esperecyan\url\lib\HostProcessing::isValidDomain()]: https://esperecyan.github.io/url/class-esperecyan.url.lib.HostProcessing#_isValidDomain
[esperecyan\url\lib\HostProcessing::parseHost()]: https://esperecyan.github.io/url/class-esperecyan.url.lib.HostProcessing#_parseHost
[esperecyan\url\lib\HostProcessing::parseIPv6()]: https://esperecyan.github.io/url/class-esperecyan.url.lib.HostProcessing#_parseIPv6
[esperecyan\url\lib\HostProcessing::serializeHost()]: https://esperecyan.github.io/url/class-esperecyan.url.lib.HostProcessing#_serializeHost
[esperecyan\url\lib\HostProcessing::serializeIPv6()]: https://esperecyan.github.io/url/class-esperecyan.url.lib.HostProcessing#_serializeIPv6
[esperecyan\url\lib\URL class]: https://esperecyan.github.io/url/class-esperecyan.url.lib.URL
[esperecyan\url\lib\URL->scheme]: https://esperecyan.github.io/url/class-esperecyan.url.lib.URL#$scheme
[esperecyan\url\lib\URL->schemeData]: https://esperecyan.github.io/url/class-esperecyan.url.lib.URL#$schemeData
[esperecyan\url\lib\URL->username]: https://esperecyan.github.io/url/class-esperecyan.url.lib.URL#$username
[esperecyan\url\lib\URL->password]: https://esperecyan.github.io/url/class-esperecyan.url.lib.URL#$password
[esperecyan\url\lib\URL->host]: https://esperecyan.github.io/url/class-esperecyan.url.lib.URL#$host
[esperecyan\url\lib\URL->port]: https://esperecyan.github.io/url/class-esperecyan.url.lib.URL#$port
[esperecyan\url\lib\URL->path]: https://esperecyan.github.io/url/class-esperecyan.url.lib.URL#$path
[esperecyan\url\lib\URL->query]: https://esperecyan.github.io/url/class-esperecyan.url.lib.URL#$query
[esperecyan\url\lib\URL->fragment]: https://esperecyan.github.io/url/class-esperecyan.url.lib.URL#$fragment
[esperecyan\url\lib\URL->nonRelativeFlag]: https://esperecyan.github.io/url/class-esperecyan.url.lib.URL#$nonRelativeFlag
[esperecyan\url\lib\URL->object]: https://esperecyan.github.io/url/class-esperecyan.url.lib.URL#$object
[esperecyan\url\lib\URL::$specialSchemes]: https://esperecyan.github.io/url/class-esperecyan.url.lib.URL#$specialSchemes
[esperecyan\url\lib\URL->isSpecial()]: https://esperecyan.github.io/url/class-esperecyan.url.lib.URL#_isSpecial
[esperecyan\url\lib\URL::$localSchemes]: https://esperecyan.github.io/url/class-esperecyan.url.lib.URL#$localSchemes
[esperecyan\url\lib\URL->isLocal()]: https://esperecyan.github.io/url/class-esperecyan.url.lib.URL#_isLocal
[esperecyan\url\lib\URL->isIncludingCredentials()]: https://esperecyan.github.io/url/class-esperecyan.url.lib.URL#_isIncludingCredentials
[esperecyan\url\lib\URL::URL_CODE_POINTS]: https://esperecyan.github.io/url/class-esperecyan.url.lib.URL#URL_CODE_POINTS
[esperecyan\url\lib\URL::parseURL()]: https://esperecyan.github.io/url/class-esperecyan.url.lib.URL#_parseURL
[esperecyan\url\lib\URL::parseBasicURL()]: https://esperecyan.github.io/url/class-esperecyan.url.lib.URL#_parseBasicURL
[esperecyan\url\lib\URL->setUsername()]: https://esperecyan.github.io/url/class-esperecyan.url.lib.URL#_setUsername
[esperecyan\url\lib\URL->setPassword()]: https://esperecyan.github.io/url/class-esperecyan.url.lib.URL#_setPassword
[esperecyan\url\lib\URL->serializeURL()]: https://esperecyan.github.io/url/class-esperecyan.url.lib.URL#_serializeURL
[esperecyan\url\lib\URL->getOrigin()]: https://esperecyan.github.io/url/class-esperecyan.url.lib.URL#_getOrigin
[esperecyan\url\lib\URLencoding::parseURLencoded()]: https://esperecyan.github.io/url/class-esperecyan.url.lib.URLencoding#_parseURLencoded
[esperecyan\url\lib\URLencoding::serializeURLencodedByte()]: https://esperecyan.github.io/url/class-esperecyan.url.lib.URLencoding#_serializeURLencodedByte
[esperecyan\url\lib\URLencoding::serializeURLencoded()]: https://esperecyan.github.io/url/class-esperecyan.url.lib.URLencoding#_serializeURLencoded
[esperecyan\url\lib\URLencoding::parseURLencodedString()]: https://esperecyan.github.io/url/class-esperecyan.url.lib.URLencoding#_parseURLencodedString
[esperecyan\url\lib\URLUtils trait]: https://esperecyan.github.io/url/class-esperecyan.url.lib.URLUtils
[esperecyan\url\lib\URLUtilsSearchParams trait]: https://esperecyan.github.io/url/class-esperecyan.url.lib.URLUtilsSearchParams
[esperecyan\url\lib\URLUtilsReadOnly trait]: https://esperecyan.github.io/url/class-esperecyan.url.lib.URLUtilsReadOnly
[esperecyan\url\lib\URLUtilsReadOnly->setInput()]: https://esperecyan.github.io/url/class-esperecyan.url.lib.URLUtilsReadOnly#_setInput
[esperecyan\url\lib\URLUtils->updateSteps()]: https://esperecyan.github.io/url/class-esperecyan.url.lib.URLUtils#_updateSteps
