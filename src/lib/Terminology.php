<?php
namespace esperecyan\url\lib;

/**
 * @link https://url.spec.whatwg.org/#terminology URL Standard
 */
class Terminology
{
    /**
     * Alias of WINDOWS_DRIVE_LETTER.
     * @var string
     * @deprecated 3.1.0 A potential Windows drive letter is renamed. Use WINDOWS_DRIVE_LETTER instead.
     * @link https://github.com/whatwg/url/commit/0755b4855187c94e1dfca900ba5122fa02a359ec
     *      Define syntax for file URLs. Third part towards fixing #33. · whatwg/url@0755b48
     */
    const POTENTIAL_WINDOWS_DRIVE_LETTER = '/^[a-z][:|]$/ui';
    
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
}
