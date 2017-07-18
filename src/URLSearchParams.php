<?php
namespace esperecyan\url;

use esperecyan\webidl\TypeHinter;
use esperecyan\webidl\TypeError;

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
    private $list = [];
    
    /**
     * @var URL|null
     * @link https://url.spec.whatwg.org/#concept-urlsearchparams-url-object URL Standard
     */
    private $urlObject = null;
    
    /**
     * @link https://url.spec.whatwg.org/#concept-urlsearchparams-new URL Standard
     * @param string[][]|string[]|string|URLSearchParams $init
     *      An array of two-element arrays with the first element the name and the second the value,
     *      associative array, USVString, or URLSearchParams.
     */
    public function __construct($init = '')
    {
        $initValue = TypeHinter::to(
            // The URL Standard expects an interface having a pair iterator to match “sequence<sequence<V>>”,
            // but it matches “record<V, V>” on the API of esperecyan/webidl.
            // So “ or esperecyan\url\URLSearchParams”is appended here.
            '(sequence<sequence<USVString>> or record<USVString, USVString> or USVString'
                . ' or esperecyan\url\URLSearchParams)',
            $init
        );
        
        static::createNewURLSearchParamsObject(
            $this,
            is_string($initValue) ? preg_replace('/^\\?/u', '', $initValue) : $initValue
        );
    }
    
    /**
     * Creates a new URLSearchParams object.
     * @link https://url.spec.whatwg.org/#concept-urlsearchparams-new URL Standard
     * @param $query self|null
     * @param $init string[][]|string[]|string|URLSearchParams|null
     * @throws TypeError
     * @return self
     */
    private static function createNewURLSearchParamsObject($query, $init)
    {
        if (!$query) {
            $query = new static();
        }
        if ($init instanceof URLSearchParams) {
            $query->list = $init->list;
        } elseif (is_array($init)) {
            foreach ($init as $pair) {
                if (count($pair) !== 2) {
                    throw new TypeError(
                        'URLSearchParams require name/value tuples when being initialized by a sequence.'
                    );
                }
            }
            $query->list = $init;
        } elseif ($init instanceof \esperecyan\webidl\Record) {
            foreach ($init as $name => $value) {
                $query->list[] = [$name, $value];
            }
        } else {
            $query->list = lib\URLencoding::parseURLencodedString($init);
        }
        return $query;
    }
    
    /**
     * A URLSearchParams object’s update steps.
     * @link https://url.spec.whatwg.org/#concept-urlsearchparams-update URL Standard
     */
    private function update()
    {
        if ($this->urlObject) {
            \Closure::bind(function ($urlObject, $query) {
                $urlObject->url->query = $query === '' ? null : $query;
            }, null, $this->urlObject)->__invoke($this->urlObject, lib\URLencoding::serializeURLencoded($this->list));
        }
    }
    
    /**
     * Appends a new name-value pair whose name is name and value is value, to the list of name-value pairs.
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
     * Removes all name-value pairs whose name is name.
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
     * Returns the value of the first name-value pair whose name is name, and null if there is no such pair.
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
     * Returns the values of all name-value pairs whose name is name, in list order, and the empty sequence otherwise.
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
     * Returns true if there is a name-value pair whose name is name, and false otherwise.
     * @link https://url.spec.whatwg.org/#dom-urlsearchparams-hasname URL Standard
     * @param string $name A USVString.
     * @return bool
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
     * Sorts all name-value pair by their names and comparing JavaScript strings (UTF-16).
     * @link https://url.spec.whatwg.org/#dom-urlsearchparams-sort URL Standard
     * @param string $name A USVString.
     * @param string $value A USVString.
     */
    public function sort()
    {
        array_multisort(array_map(function ($pair) {
            return mb_convert_encoding($pair[0], 'UTF-16BE', 'UTF-8');
        }, $this->list), SORT_STRING, range(1, count($this->list)), $this->list);
    }
    
    /**
     * @uses lib\URLSearchParamsIterator
     */
    public function getIterator()
    {
        return new lib\URLSearchParamsIterator($this->list, $this);
    }
    
    /**
     * Returns the serialization of the URLSearchParams object's associated list of name-value pairs.
     * @link https://url.spec.whatwg.org/#stringification-behavior URL Standard
     * @return string A USVString.
     */
    public function __toString()
    {
        return lib\URLencoding::serializeURLencoded($this->list);
    }
}
