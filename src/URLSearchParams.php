<?php
namespace esperecyan\url;

use esperecyan\webidl\TypeHinter;

/**
 * The URLSearchParams class defines utility methods to work with the query string of a URL.
 * @link https://url.spec.whatwg.org/#interface-urlsearchparams URL Standard
 * @link https://developer.mozilla.org/docs/Web/API/URLSearchParams URLSearchParams - Web API Interfaces | MDN
 */
class URLSearchParams implements \IteratorAggregate
{
    /**
     * @var string[][] List of name-value pairs.
     * @link https://url.spec.whatwg.org/#concept-urlsearchparams-list URL Standard
     */
    private $list;
    
    /**
     * @var URL|null
     * @link https://url.spec.whatwg.org/#concept-urlsearchparams-url-object URL Standard
     */
    private $urlObject = null;
    
    /**
     * @link https://url.spec.whatwg.org/#concept-urlsearchparams-new URL Standard
     * @param string|URLSearchParams $init A USVString or URLSearchParams.
     */
    public function __construct($init = '')
    {
        $initValue = TypeHinter::to('(USVString or esperecyan\\url\\URLSearchParams)', $init);
        $this->list = is_string($initValue)
            ? lib\URLencoding::parseURLencodedString(preg_replace('/^\\?/u', '', $initValue))
            : $initValue->list;
    }
    
    /**
     * A URLSearchParams object’s update steps.
     * @link https://url.spec.whatwg.org/#concept-urlsearchparams-update URL Standard
     */
    private function update()
    {
        if ($this->urlObject) {
            \Closure::bind(function ($urlObject, $query) {
                $urlObject->url->query = $query;
            }, null, $this->urlObject)->__invoke($this->urlObject, lib\URLencoding::serializeURLencoded($this->list));
        }
    }
    
    /**
     * Append a new name-value pair whose name is name and value is value, to the list of name-value pairs.
     * @link https://url.spec.whatwg.org/#dom-urlsearchparams-appendname-value URL Standard
     * @param string $name A USVString.
     * @param string $value A USVString.
     */
    public function append($name, $value)
    {
        $this->list[] = [TypeHinter::to('USVString', $name, 0), TypeHinter::to('USVString', $value, 1)];
        $this->update();
    }
    
    /**
     * Remove all name-value pairs whose name is name.
     * @link https://url.spec.whatwg.org/#dom-urlsearchparams-deletename URL Standard
     * @param string $name A USVString.
     */
    public function delete($name)
    {
        $nameString = TypeHinter::to('USVString', $name);
        array_splice($this->list, 0, count($this->list), array_filter($this->list, function ($pair) use ($nameString) {
            return $pair[0] !== $nameString;
        }));
        $this->update();
    }
    
    /**
     * Return the value of the first name-value pair whose name is name, and null if there is no such pair.
     * @link https://url.spec.whatwg.org/#dom-urlsearchparams-getname URL Standard
     * @param string $name
     * @return string|null A USVString or null.
     */
    public function get($name)
    {
        $nameString = TypeHinter::to('USVString', $name);
        $value = null;
        foreach ($this->list as $pair) {
            if ($pair[0] === $nameString) {
                $value = $pair[1];
                break;
            }
        }
        return $value;
    }
    
    /**
     * Return the values of all name-value pairs whose name is name, in list order, and the empty sequence otherwise.
     * @link https://url.spec.whatwg.org/#dom-urlsearchparams-getallname URL Standard
     * @param string $name A USVString.
     * @return string[] An array of a USVString.
     */
    public function getAll($name)
    {
        $nameString = TypeHinter::to('USVString', $name);
        $values = [];
        foreach ($this->list as $pair) {
            if ($pair[0] === $nameString) {
                $values[] = $pair[1];
            }
        }
        return $values;
    }
    
    /**
     * Return true if there is a name-value pair whose name is name, and false otherwise.
     * @link https://url.spec.whatwg.org/#dom-urlsearchparams-hasname URL Standard
     * @param string $name A USVString.
     * @return boolean
     */
    public function has($name)
    {
        return !is_null($this->get(TypeHinter::to('USVString', $name)));
    }
    
    /**
     * If there are any name-value pairs whose name is name, set the value of the first such name-value pair to value and remove the others.
     * Otherwise, append a new name-value pair whose name is name and value is value, to the list of name-value pairs.
     * @link https://url.spec.whatwg.org/#dom-urlsearchparams-setname-value URL Standard
     * @param string $name A USVString.
     * @param string $value A USVString.
     */
    public function set($name, $value)
    {
        $nameString = TypeHinter::to('USVString', $name, 0);
        $valueString = TypeHinter::to('USVString', $value, 1);
        $already = false;
        foreach ($this->list as $key => &$pair) {
            if ($pair[0] === $nameString) {
                if ($already) {
                    unset($this->list[$key]);
                } else {
                    $pair[1] = $valueString;
                    $already = true;
                }
            }
        }
        unset($pair);
        if ($already) {
            $this->list = array_values($this->list);
            array_splice($this->list, 0, count($this->list), $this->list);
        } else {
            $this->list[] = [$nameString, $valueString];
        }
        $this->update();
    }
    
    /**
     * @uses lib\URLSearchParamsIterator
     */
    public function getIterator()
    {
        return new lib\URLSearchParamsIterator($this->list, $this);
    }
    
    /**
     * Return the serialization of the URLSearchParams object's associated list of name-value pairs.
     * @link https://url.spec.whatwg.org/#stringification-behavior URL Standard
     * @return string A USVString.
     */
    public function __toString()
    {
        return lib\URLencoding::serializeURLencoded($this->list);
    }
}
