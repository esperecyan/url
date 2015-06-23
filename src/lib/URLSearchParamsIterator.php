<?php
namespace esperecyan\url\lib;

/** @internal */
class URLSearchParamsIterator implements \Iterator
{
    /** @var string[][] */
    private $list;
    
    /** @var integer */
    private $position = 0;
    
    /**
     * @param string[][] $list
     */
    public function __construct(array &$list)
    {
        $this->list = &$list;
    }
    
    /**
     * @return string|null
     */
    public function current()
    {
        return isset($this->list[$this->position]) ? $this->list[$this->position][1] : null;
    }

    /**
     * @return string|null
     */
    public function key()
    {
        return isset($this->list[$this->position]) ? $this->list[$this->position][0] : null;
    }

    public function next()
    {
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
        return isset($this->list[$this->position]);
    }
}
