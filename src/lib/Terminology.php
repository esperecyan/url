<?php
namespace esperecyan\url\lib;

/**
 * @deprecated 3.2.0 The constants are moved to {@link Infrastructure} or {@link URL}.
 * @link https://github.com/whatwg/url/commit/e5b57a0dfe77464282f3b70c1e605ae40bec278d?w=
 *      Rename Terminology to Infrastructure · whatwg/url@e5b57a0
 */
class Terminology
{
    /**
     * Alias of {@link URL}::WINDOWS_DRIVE_LETTER.
     * @var string
     * @deprecated 3.1.0 A potential Windows drive letter is renamed.
     * @link https://github.com/whatwg/url/commit/0755b4855187c94e1dfca900ba5122fa02a359ec
     *      Define syntax for file URLs. Third part towards fixing #33. · whatwg/url@0755b48
     */
    const POTENTIAL_WINDOWS_DRIVE_LETTER = '/^[a-z][:|]$/ui';
    
    /**
     * Alias of {@link URL}::WINDOWS_DRIVE_LETTER.
     * @var string
     */
    const WINDOWS_DRIVE_LETTER = '/^[a-z][:|]$/ui';
    
    /**
     * Alias of {@link URL}::NORMALIZED_WINDOWS_DRIVE_LETTER.
     * @var string
     */
    const NORMALIZED_WINDOWS_DRIVE_LETTER = '/^[a-z]:$/ui';
}
