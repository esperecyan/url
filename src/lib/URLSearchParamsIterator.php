<?php
namespace esperecyan\url\lib;

/** @internal */
class URLSearchParamsIterator implements \Iterator
{
    /** @var string[][] */
    private $list;

    /** @var \esperecyan\url\URLSearchParams */
    private $searchParams;
    
    /** @var integer */
    private $position = 0;
    
    private function reset()
    {
        \Closure::bind(function ($searchParams) {
            $searchParams->reset();
        }, null, $this->searchParams)->__invoke($this->searchParams);
    }
    
    /**
     * @param string[][] $list
     * @param \esperecyan\url\URLSearchParams $searchParams
     */
    public function __construct(array &$list, \esperecyan\url\URLSearchParams $searchParams)
    {
        $this->list = &$list;
        $this->searchParams = $searchParams;
    }
    
    /**
     * @return string|null
     */
    public function current()
    {
        $this->reset();
        return isset($this->list[$this->position]) ? $this->list[$this->position][1] : null;
    }

    /**
     * @return string|null
     */
    public function key()
    {
        $this->reset();
        return isset($this->list[$this->position]) ? $this->list[$this->position][0] : null;
    }

    public function next()
    {
        $this->reset();
        $this->position++;
    }

    public function rewind()
    {
        $this->position = 0;
    }

    /**
     * @return boolean
     */
    public function valid()
    {
        $this->reset();
        return isset($this->list[$this->position]);
    }
}
