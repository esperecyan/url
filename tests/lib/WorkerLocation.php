<?php
namespace esperecyan\url\lib;

/**
 * @link https://html.spec.whatwg.org/multipage/workers.html#workerlocation HTML Standard
 */
class WorkerLocation
{
    use URLUtilsReadOnly;
    
    /**
     * @param string $value
     */
    protected function getBase()
    {
        return null;
    }
    
    /**
     * @param string $absoluteURL
     */
    public function __construct($absoluteURL)
    {
        $this->setInput($absoluteURL);
    }
}
