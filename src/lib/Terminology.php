<?php
namespace esperecyan\url\lib;

/**
 * @link https://url.spec.whatwg.org/#terminology URL Standard
 */
class Terminology
{
    /**
     * @var A potential Windows drive letter.
     * @link https://url.spec.whatwg.org/#potential-windows-drive-letter URL Standard
     */
    const POTENTIAL_WINDOWS_DRIVE_LETTER = '/^[a-z][:|]$/i';
    
    /**
     * @var A Windows drive letter.
     * @link https://url.spec.whatwg.org/#windows-drive-letter URL Standard
     */
    const WINDOWS_DRIVE_LETTER = '/^[a-z]:$/i';
}
