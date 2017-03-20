[English](README.md) / 日本語

URL Standard
============
[URL Standard]で定義されているアルゴリズム、および API を PHP から利用できるようにします。

[URL Standard]: https://triple-underscore.github.io/URL-ja.html "URL 標準は、 URL, ドメイン, IP アドレス, application/x-www-form-urlencoded 形式, および それらの API を定義する。"

概要
----
URL Standard は、従来の標準である [RFC 3986]、[RFC 3987] を置き換える Web 標準仕様です。

この仕様では[API]として、[URLインターフェース]、および[URLSearchPramsインターフェース]を定義しています。
当ライブラリでは、[URLインターフェース]を[esperecyan\url\URLクラス]として、[URLSearchPramsインターフェース]を[esperecyan\url\URLSearchParamsクラス]として使えるようにしました。
各インターフェースの解説としては、MDNのドキュメントがわかりやすいかもしれません https://developer.mozilla.org/docs/Web/API/URL https://developer.mozilla.org/docs/Web/API/URLSearchParams 。

当ライブラリは、URL Standard で定義されているアルゴリズムも実装しています。詳細は[アルゴリズムなどの対応表]をご覧ください。

[RFC 3986]: http://www.eonet.ne.jp/~h-hash/rfc_ja/rfc3986.ja.html "Uniform Resource Identifier (URI) は、抽象的あるいは物理的なリソースを識別するための簡潔な文字列である。この仕様書は、一般的な URI の構文と相対的形式である URI 参照を解決するための手順を定義し、更にインターネット上での URI の使用のついての指針やセキュリティについての考察を示す。"
[RFC 3987]: https://tools.ietf.org/html/rfc3987 "This document defines a new protocol element, the Internationalized Resource Identifier (IRI), as a complement to the Uniform Resource Identifier (URI)."
[API]: https://triple-underscore.github.io/URL-ja.html#api "URL 標準は、URL の既存の JavaScript API における全部的な詳細を定義し、より扱い易いものになる様に機能を強化する。"
[URLインターフェース]: https://triple-underscore.github.io/URL-ja.html#URL "URL 標準は、HTML 要素を通さずに URL を操作できるようにする（ JavaScript worker 環境でも使えるようにする）ため， URL オブジェクトを新たに追加する。"
[URLSearchPramsインターフェース]: https://triple-underscore.github.io/URL-ja.html#interface-urlsearchparams
[esperecyan\url\URLクラス]: https://esperecyan.github.io/url/class-esperecyan.url.URL
[esperecyan\url\URLSearchParamsクラス]: https://esperecyan.github.io/url/class-esperecyan.url.URLSearchParams
[アルゴリズムなどの対応表]: #アルゴリズムなどの対応表

例
---
```php
<?php
require_once 'vendor/autoload.php';

use esperecyan\url\URL;

$url = new URL('http://url.test/foobar?name=value');
var_dump($url->protocol, $url->pathname, $url->searchParams->get('name'));
```

上の例の出力は以下となります。

```plain
string(5) "http:"
string(7) "/foobar"
string(5) "value"
```

要件
----
* PHP 5.4 以上 **(PHP 5.4、および 5.5 は非推奨)**
	+ SPL Types PECL ライブラリには非対応
* [Intl拡張モジュール]

[Intl拡張モジュール]: http://jp2.php.net/manual/book.intl.php "国際化用拡張モジュール (Intl と略します) は ICU ライブラリのラッパーです。 PHP プログラマが、UCA 準拠の照合順序 (collation) や日付/時刻/数値/通貨のフォーマットを扱えるようにします。"

インストール
-----------
```sh
composer require esperecyan/url
```

Composer のインストール方法については、[Composerドキュメント]をご覧ください。

[Composerドキュメント]: https://kohkimakimoto.github.io/getcomposer.org_doc_jp/doc/00-intro "ComposerはPHPの依存管理ツールです。 Composerはあなたのプロジェクトが必要とする依存ライブラリを定義できるようにして、インストールを行います。"

貢献
----
1. Fork します ( https://github.com/esperecyan/url )
2. branch を作成します `git checkout -b my-new-feature`
3. 変更を commit します `git commit -am 'Add some feature'`
4. branch に push します `git push origin my-new-feature`
5. Pull Request を作成します

もしくは

Issue を作成します

README や Doc コメントの英文の間違い、またテストの不備などを見つけたら、Pull Request や Issue などからご連絡ください。
README の翻訳も歓迎いたします。

謝辞
----
[URLencodingクラス]の実装に当たり、[コードポイントから UTF-8 の文字を生成する - Qiita]、および[UTF-8 の文字からコードポイントを求める - Qiita]のコードを利用させていただきました。

ライブラリの作成に当たり、[URL Standard （日本語訳）]を参考にさせていただきました。

READMEの英訳をハダーさんに協力していただきました。

[URLencodingクラス]: src/lib/URLencoding.php
[コードポイントから UTF-8 の文字を生成する - Qiita]: http://qiita.com/masakielastic/items/68f81e1b7d153ee5cc81 "バリデーションの際に想定外の文字が通っていないか調べるには Unicode で定義されるすべての文字を試すことが必要です。UTF-8 の場合、コードポイントの範囲は U+0000 から U+7FFF、U+E000 から U+10FFFF までです。"
[UTF-8 の文字からコードポイントを求める - Qiita]: http://qiita.com/masakielastic/items/5696cf90738c1438f10d "文字の Unicode プロパティやエンコーディングに関する情報を検索で調べる際にコードポイントが必要になることがあります。PHP 5.5 で intl 拡張モジュールに IntlCodePointBreakIterator が追加され、コードポイントを求めやすくなりました。"
[URL Standard （日本語訳）]: https://triple-underscore.github.io/URL-ja.html "このページ は、 WHATWG による，副題の日付 時点の URL Standard を日本語に翻訳したものです。 この翻訳の正確性は保証されません。 この仕様の公式な文書は英語版であり、この日本語訳は公式のものではありません。"

セマンティック バージョニング
---------------------------
当ライブラリは[セマンティック バージョニング]を採用しています。
パブリックAPIは、[当ライブラリのドキュメント]に記載されているクラス・メソッド・定数・プロパティです。

[セマンティック バージョニング]: http://semver.org/lang/ja/
[当ライブラリのドキュメント]: https://esperecyan.github.io/webidl/

ライセンス
---------
当ライブラリのライセンスは [Mozilla Public License Version 2.0] \(MPL-2.0) です。

[Mozilla Public License Version 2.0]: https://www.mozilla.org/MPL/2.0/

アルゴリズムなどの対応表
---------------------
| [1. 基盤]                           |                                                                      |
|-------------------------------------|----------------------------------------------------------------------|
| [Windows ドライブレター]            | [esperecyan\url\lib\Infrastructure::WINDOWS_DRIVE_LETTER]            |
| [正規化済み Windows ドライブレター] | [esperecyan\url\lib\Infrastructure::NORMALIZED_WINDOWS_DRIVE_LETTER] |
| [パーセント符号化]                  | [esperecyan\url\lib\Infrastructure::percentEncode()]                 |
| [パーセント復号]                    | [esperecyan\url\lib\Infrastructure::percentDecode()]                 |
| [C0 制御文字 %‐符号化集合]         | [esperecyan\url\lib\Infrastructure::C0_CONTROL_PERCENT_ENCODE_SET]   |
| [パス %‐符号化集合]                | [esperecyan\url\lib\Infrastructure::PATH_PERCENT_ENCODE_SET]         |
| [ユーザ情報 %‐符号化集合]          | [esperecyan\url\lib\Infrastructure::USERINFO_PERCENT_ENCODE_SET]     |
| [utf-8 パーセント符号化]            | [esperecyan\url\lib\Infrastructure::utf8PercentEncode()]             |

| [3. ホスト（ドメインと IP アドレス）] |                                                        |
|---------------------------------------|--------------------------------------------------------|
| [ドメイン]                            | 妥当な utf-8 の文字列                                  |
| [IPv4 アドレス]                       | 0〜0xFFFFFFFFの整数、または浮動小数点数                |
| [IPv6 アドレス]                       | 0〜0xFFFFの整数を要素に持つ長さが8の配列               |
| [ドメインから ASCII へ変換]           | [esperecyan\url\lib\HostProcessing::domainToASCII()]   |
| [ドメインから Unicode へ変換]         | [esperecyan\url\lib\HostProcessing::domainToUnicode()] |
| [妥当なドメイン]                      | [esperecyan\url\lib\HostProcessing::isValidDomain()]   |
| [ホスト構文解析器]                    | [esperecyan\url\lib\HostProcessing::parseHost()]       |
| [IPv4 番号構文解析器]                 | [esperecyan\url\lib\HostProcessing::parseIPv4Number()] |
| [IPv4 構文解析器]                     | [esperecyan\url\lib\HostProcessing::parseIPv4()]       |
| [IPv6 構文解析器]                     | [esperecyan\url\lib\HostProcessing::parseIPv6()]       |
| [ホスト直列化器]                      | [esperecyan\url\lib\HostProcessing::serializeHost()]   |
| [IPv4 直列化器]                       | [esperecyan\url\lib\HostProcessing::serializeIPv4()]   |
| [IPv6 直列化器]                       | [esperecyan\url\lib\HostProcessing::serializeIPv6()]   |

| [4. URL]               |                                                    |
|------------------------|----------------------------------------------------|
| [URL]                  | [esperecyan\url\lib\URLクラス]のインスタンス       |
| [スキーム]             | [esperecyan\url\lib\URL->scheme]                   |
| [ユーザ名]             | [esperecyan\url\lib\URL->username]                 |
| [パスワード]           | [esperecyan\url\lib\URL->password]                 |
| [ホスト]               | [esperecyan\url\lib\URL->host]                     |
| [ポート]               | [esperecyan\url\lib\URL->port]                     |
| [パス]                 | [esperecyan\url\lib\URL->path]                     |
| [クエリ]               | [esperecyan\url\lib\URL->query]                    |
| [素片]                 | [esperecyan\url\lib\URL->fragment]                 |
| [基底 URL 不可用 flag] | [esperecyan\url\lib\URL->cannotBeABaseURLFlag]     |
| [オブジェクト]         | [esperecyan\url\lib\URL->object]                   |
| [特別スキーム]         | [esperecyan\url\lib\URL::$specialSchemes]          |
| [特別]                 | [esperecyan\url\lib\URL->isSpecial()]              |
| [局所的スキーム]       | [esperecyan\url\lib\URL::$localSchemes]            |
| [局所的]               | [esperecyan\url\lib\URL->isLocal()]                |
| [資格証明情報を含む]   | [esperecyan\url\lib\URL->isIncludingCredentials()] |
| [パスを短縮する]       | [esperecyan\url\lib\URL->shortenPath()]            |
| [単ドットパス区分]     | [esperecyan\url\lib\URL::SINGLE_DOT_PATH_SEGMENT]  |
| [二重ドットパス区分]   | [esperecyan\url\lib\URL::DOUBLE_DOT_PATH_SEGMENT]  |
| [URL 符号位置]         | [esperecyan\url\lib\URL::URL_CODE_POINTS]          |
| [URL 構文解析器]       | [esperecyan\url\lib\URL::parseURL()]               |
| [基本 URL 構文解析器]  | [esperecyan\url\lib\URL::parseBasicURL()]          |
| [ユーザ名を設定する]   | [esperecyan\url\lib\URL->setUsername()]            |
| [パスワードを設定する] | [esperecyan\url\lib\URL->setPassword()]            |
| [URL 直列化器]         | [esperecyan\url\lib\URL->serializeURL()]           |
| [生成元]               | [esperecyan\url\lib\URL->getOrigin()]              |

| [5. application/x-www-form-urlencoded]               |                                                             |
|------------------------------------------------------|-------------------------------------------------------------|
| [application/x-www-form-urlencoded 構文解析器]       | [esperecyan\url\lib\URLencoding::parseURLencoded()]         |
| [application/x-www-form-urlencoded バイト直列化器]   | [esperecyan\url\lib\URLencoding::serializeURLencodedByte()] |
| [application/x-www-form-urlencoded 直列化器]         | [esperecyan\url\lib\URLencoding::serializeURLencoded()]     |
| [application/x-www-form-urlencoded 文字列構文解析器] | [esperecyan\url\lib\URLencoding::parseURLencodedString()]   |
| ( 名, 値 ) の組, または [( 名, 値, 型 ) の組]        | 1つ目の要素に名、2つ目の要素に値、任意で3つ目の要素に型を持つ配列。値は文字列、または `name` キーからファイル名を取得できる配列 |

[1. 基盤]: https://triple-underscore.github.io/URL-ja.html#terminology
[Windows ドライブレター]: https://triple-underscore.github.io/URL-ja.html#windows-drive-letter
[正規化済み Windows ドライブレター]: https://triple-underscore.github.io/URL-ja.html#normalized-windows-drive-letter
[パーセント符号化]: https://triple-underscore.github.io/URL-ja.html#percent-encode
[パーセント復号]: https://triple-underscore.github.io/URL-ja.html#percent-decode
[C0 制御文字 %‐符号化集合]: https://triple-underscore.github.io/URL-ja.html#c0-control-percent-encode-set
[パス %‐符号化集合]: https://triple-underscore.github.io/URL-ja.html#path-percent-encode-set
[ユーザ情報 %‐符号化集合]: https://triple-underscore.github.io/URL-ja.html#userinfo-percent-encode-set
[utf-8 パーセント符号化]: https://triple-underscore.github.io/URL-ja.html#utf_8-percent-encode

[3. ホスト（ドメインと IP アドレス）]: https://triple-underscore.github.io/URL-ja.html#hosts-(domains-and-ip-addresses)
[ドメイン]: https://triple-underscore.github.io/URL-ja.html#concept-domain
[IPv4 アドレス]: https://triple-underscore.github.io/URL-ja.html#concept-ipv4
[IPv6 アドレス]: https://triple-underscore.github.io/URL-ja.html#concept-ipv6
[ドメインから ASCII へ変換]: https://triple-underscore.github.io/URL-ja.html#concept-domain-to-ascii
[ドメインから Unicode へ変換]: https://triple-underscore.github.io/URL-ja.html#concept-domain-to-unicode
[妥当なドメイン]: https://triple-underscore.github.io/URL-ja.html#valid-domain
[ホスト構文解析器]: https://triple-underscore.github.io/URL-ja.html#concept-host-parser
[IPv4 番号構文解析器]: https://triple-underscore.github.io/URL-ja.html#ipv4-number-parser
[IPv4 構文解析器]: https://triple-underscore.github.io/URL-ja.html#concept-ipv4-parser
[IPv6 構文解析器]: https://triple-underscore.github.io/URL-ja.html#concept-ipv6-parser
[ホスト直列化器]: https://triple-underscore.github.io/URL-ja.html#concept-host-serializer
[IPv4 直列化器]: https://triple-underscore.github.io/URL-ja.html#concept-ipv4-serializer
[IPv6 直列化器]: https://triple-underscore.github.io/URL-ja.html#concept-ipv6-serializer

[4. URL]: https://triple-underscore.github.io/URL-ja.html#urls
[URL]: https://triple-underscore.github.io/URL-ja.html#concept-url
[スキーム]: https://triple-underscore.github.io/URL-ja.html#concept-url-scheme
[ユーザ名]: https://triple-underscore.github.io/URL-ja.html#concept-url-username
[パスワード]: https://triple-underscore.github.io/URL-ja.html#concept-url-password
[ホスト]: https://triple-underscore.github.io/URL-ja.html#concept-url-host
[ポート]: https://triple-underscore.github.io/URL-ja.html#concept-url-port
[パス]: https://triple-underscore.github.io/URL-ja.html#concept-url-path
[クエリ]: https://triple-underscore.github.io/URL-ja.html#concept-url-query
[素片]: https://triple-underscore.github.io/URL-ja.html#concept-url-fragment
[基底 URL 不可用 flag]: https://triple-underscore.github.io/URL-ja.html#url-cannot-be-a-base-url-flag
[オブジェクト]: https://triple-underscore.github.io/URL-ja.html#concept-url-object
[特別スキーム]: https://triple-underscore.github.io/URL-ja.html#special-scheme
[特別]: https://triple-underscore.github.io/URL-ja.html#is-special
[局所的スキーム]: https://triple-underscore.github.io/URL-ja.html#local-scheme
[局所的]: https://triple-underscore.github.io/URL-ja.html#is-local
[資格証明情報を含む]: https://triple-underscore.github.io/URL-ja.html#include-credentials
[パスを短縮する]: https://triple-underscore.github.io/URL-ja.html#shorten-a-urls-path
[単ドットパス区分]: https://triple-underscore.github.io/URL-ja.html#syntax-url-path-segment-dot
[二重ドットパス区分]: https://triple-underscore.github.io/URL-ja.html#syntax-url-path-segment-dotdot
[URL 符号位置]: https://triple-underscore.github.io/URL-ja.html#url-code-points
[URL 構文解析器]: https://triple-underscore.github.io/URL-ja.html#concept-url-parser
[基本 URL 構文解析器]: https://triple-underscore.github.io/URL-ja.html#concept-basic-url-parser
[ユーザ名を設定する]: https://triple-underscore.github.io/URL-ja.html#set-the-username
[パスワードを設定する]: https://triple-underscore.github.io/URL-ja.html#set-the-password
[URL 直列化器]: https://triple-underscore.github.io/URL-ja.html#concept-url-serializer
[生成元]: https://triple-underscore.github.io/URL-ja.html#concept-url-origin

[5. application/x-www-form-urlencoded]: https://triple-underscore.github.io/URL-ja.html#application/x-www-form-urlencoded
[application/x-www-form-urlencoded 構文解析器]: https://triple-underscore.github.io/URL-ja.html#concept-urlencoded-parser
[application/x-www-form-urlencoded バイト直列化器]: https://triple-underscore.github.io/URL-ja.html#concept-urlencoded-byte-serializer
[application/x-www-form-urlencoded 直列化器]: https://triple-underscore.github.io/URL-ja.html#concept-urlencoded-serializer
[application/x-www-form-urlencoded 文字列構文解析器]: https://triple-underscore.github.io/URL-ja.html#concept-urlencoded-string-parser

[esperecyan\url\lib\Infrastructure::WINDOWS_DRIVE_LETTER]: https://esperecyan.github.io/url/class-esperecyan.url.lib.Infrastructure#WINDOWS_DRIVE_LETTER
[esperecyan\url\lib\Infrastructure::NORMALIZED_WINDOWS_DRIVE_LETTER]: https://esperecyan.github.io/url/class-esperecyan.url.lib.Infrastructure#NORMALIZED_WINDOWS_DRIVE_LETTER
[esperecyan\url\lib\Infrastructure::percentEncode()]: https://esperecyan.github.io/url/class-esperecyan.url.lib.Infrastructure#_percentEncode
[esperecyan\url\lib\Infrastructure::percentDecode()]: https://esperecyan.github.io/url/class-esperecyan.url.lib.Infrastructure#_percentDecode
[esperecyan\url\lib\Infrastructure::C0_CONTROL_PERCENT_ENCODE_SET]: https://esperecyan.github.io/url/class-esperecyan.url.lib.Infrastructure#C0_CONTROL_PERCENT_ENCODE_SET
[esperecyan\url\lib\Infrastructure::PATH_PERCENT_ENCODE_SET]: https://esperecyan.github.io/url/class-esperecyan.url.lib.Infrastructure#PATH_PERCENT_ENCODE_SET
[esperecyan\url\lib\Infrastructure::USERINFO_PERCENT_ENCODE_SET]: https://esperecyan.github.io/url/class-esperecyan.url.lib.Infrastructure#USERINFO_PERCENT_ENCODE_SET
[esperecyan\url\lib\Infrastructure::utf8PercentEncode()]: https://esperecyan.github.io/url/class-esperecyan.url.lib.Infrastructure#_utf8PercentEncode
[esperecyan\url\lib\HostProcessing::domainToASCII()]: https://esperecyan.github.io/url/class-esperecyan.url.lib.HostProcessing#_domainToASCII
[esperecyan\url\lib\HostProcessing::domainToUnicode()]: https://esperecyan.github.io/url/class-esperecyan.url.lib.HostProcessing#_domainToUnicode
[esperecyan\url\lib\HostProcessing::isValidDomain()]: https://esperecyan.github.io/url/class-esperecyan.url.lib.HostProcessing#_isValidDomain
[esperecyan\url\lib\HostProcessing::parseHost()]: https://esperecyan.github.io/url/class-esperecyan.url.lib.HostProcessing#_parseHost
[esperecyan\url\lib\HostProcessing::parseIPv4Number()]: https://esperecyan.github.io/url/class-esperecyan.url.lib.HostProcessing#_parseIPv4Number
[esperecyan\url\lib\HostProcessing::parseIPv4()]: https://esperecyan.github.io/url/class-esperecyan.url.lib.HostProcessing#_parseIPv4
[esperecyan\url\lib\HostProcessing::parseIPv6()]: https://esperecyan.github.io/url/class-esperecyan.url.lib.HostProcessing#_parseIPv6
[esperecyan\url\lib\HostProcessing::serializeHost()]: https://esperecyan.github.io/url/class-esperecyan.url.lib.HostProcessing#_serializeHost
[esperecyan\url\lib\HostProcessing::serializeIPv4()]: https://esperecyan.github.io/url/class-esperecyan.url.lib.HostProcessing#_serializeIPv4
[esperecyan\url\lib\HostProcessing::serializeIPv6()]: https://esperecyan.github.io/url/class-esperecyan.url.lib.HostProcessing#_serializeIPv6
[esperecyan\url\lib\URLクラス]: https://esperecyan.github.io/url/class-esperecyan.url.lib.URL
[esperecyan\url\lib\URL->scheme]: https://esperecyan.github.io/url/class-esperecyan.url.lib.URL#$scheme
[esperecyan\url\lib\URL->schemeData]: https://esperecyan.github.io/url/class-esperecyan.url.lib.URL#$schemeData
[esperecyan\url\lib\URL->username]: https://esperecyan.github.io/url/class-esperecyan.url.lib.URL#$username
[esperecyan\url\lib\URL->password]: https://esperecyan.github.io/url/class-esperecyan.url.lib.URL#$password
[esperecyan\url\lib\URL->host]: https://esperecyan.github.io/url/class-esperecyan.url.lib.URL#$host
[esperecyan\url\lib\URL->port]: https://esperecyan.github.io/url/class-esperecyan.url.lib.URL#$port
[esperecyan\url\lib\URL->path]: https://esperecyan.github.io/url/class-esperecyan.url.lib.URL#$path
[esperecyan\url\lib\URL->query]: https://esperecyan.github.io/url/class-esperecyan.url.lib.URL#$query
[esperecyan\url\lib\URL->fragment]: https://esperecyan.github.io/url/class-esperecyan.url.lib.URL#$fragment
[esperecyan\url\lib\URL->cannotBeABaseURLFlag]: https://esperecyan.github.io/url/class-esperecyan.url.lib.URL#$cannotBeABaseURLFlag
[esperecyan\url\lib\URL->object]: https://esperecyan.github.io/url/class-esperecyan.url.lib.URL#$object
[esperecyan\url\lib\URL::$specialSchemes]: https://esperecyan.github.io/url/class-esperecyan.url.lib.URL#$specialSchemes
[esperecyan\url\lib\URL->isSpecial()]: https://esperecyan.github.io/url/class-esperecyan.url.lib.URL#_isSpecial
[esperecyan\url\lib\URL::$localSchemes]: https://esperecyan.github.io/url/class-esperecyan.url.lib.URL#$localSchemes
[esperecyan\url\lib\URL->isLocal()]: https://esperecyan.github.io/url/class-esperecyan.url.lib.URL#_isLocal
[esperecyan\url\lib\URL->isIncludingCredentials()]: https://esperecyan.github.io/url/class-esperecyan.url.lib.URL#_isIncludingCredentials
[esperecyan\url\lib\URL->shortenPath()]: https://esperecyan.github.io/url/class-esperecyan.url.lib.URL#_shortenPath
[esperecyan\url\lib\URL::SINGLE_DOT_PATH_SEGMENT]: https://esperecyan.github.io/url/class-esperecyan.url.lib.URL#SINGLE_DOT_PATH_SEGMENT
[esperecyan\url\lib\URL::DOUBLE_DOT_PATH_SEGMENT]: https://esperecyan.github.io/url/class-esperecyan.url.lib.URL#DOUBLE_DOT_PATH_SEGMENT
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
