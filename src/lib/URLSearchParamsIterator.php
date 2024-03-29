<?php
namespace esperecyan\url\lib;

/** @internal */
class URLSearchParamsIterator implements \Iterator
{
    /** @var string[][] */
    private $list;

    /** @var \esperecyan\url\URLSearchParams */
    private $searchParams;

    /** @var int */
    private $position = 0;

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
    #[\ReturnTypeWillChange]
    public function current()
    {
        return isset($this->list[$this->position]) ? $this->list[$this->position][1] : null;
    }

    /**
     * @return string|null
     */
    #[\ReturnTypeWillChange]
    public function key()
    {
        return isset($this->list[$this->position]) ? $this->list[$this->position][0] : null;
    }

    #[\ReturnTypeWillChange]
    public function next()
    {
        $this->position++;
    }

    #[\ReturnTypeWillChange]
    public function rewind()
    {
        $this->position = 0;
    }

    /**
     * @return bool
     */
    #[\ReturnTypeWillChange]
    public function valid()
    {
        return isset($this->list[$this->position]);
    }
}
