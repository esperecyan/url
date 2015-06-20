<?php
namespace esperecyan\url\lib;

use esperecyan\webidl\TypeHinter;

/**
 * @property string $target DOMString
 * @property string $download DOMString
 * @property string $rel DOMString
 * @property string $hreflang DOMString
 * @property string $type DOMString
 * @property string $text DOMString
 * @link https://html.spec.whatwg.org/multipage/semantics.html#the-a-element HTML Standard
 */
class HTMLAnchorElement extends \DOMElement
{
    use URLUtils, URLUtilsSearchParams {
        URLUtils::__set as URLUtils__set;
        URLUtils::__isset as URLUtils__isset;
        URLUtils::__get as URLUtils__get;
    }
    
    /**
     * @param string $value
     */
    protected function getBase()
    {
        return is_null($this->baseURI) ? null : URL::parseURL($this->baseURI);
    }
    
    /**
     * @param string $value
     */
    protected function updateSteps($value)
    {
        parent::setAttribute('href', $value);
    }
    
    /**
     * @param string $name
     * @param string $value
     * @param string $namespaceURI
     */
    public function __construct()
    {
        parent::__construct('a', null, 'http://www.w3.org/1999/xhtml');
        $this->setInput(null);
    }
    
    /**
     * @param string $name
     * @param string $value
     */
    public function setAttribute($name, $value)
    {
        $nameString = TypeHinter::to('DOMString', $name, 0);
        $valueString = TypeHinter::to('DOMString', $value, 1);
        parent::setAttribute($nameString, $valueString);
        if ($nameString === 'href') {
            $this->setInput($valueString);
        }
    }

    /**
     * @param string $name
     */
    public function removeAttribute($name)
    {
        $nameString = TypeHinter::to('DOMString', $name);
        parent::removeAttribute($nameString);
        if ($nameString === 'href') {
            $this->setInput(null);
        }
    }

    /**
     * @param string $name
     * @param string $value DOMString
     */
    public function __set($name, $value)
    {
        switch ($name) {
            case 'target':
            case 'download':
            case 'rel':
            case 'hreflang':
            case 'type':
                $this->setAttribute($name, TypeHinter::to('DOMString', $value));
                break;
            case 'text':
                $this->textContent = TypeHinter::to('DOMString', $value);
                break;
            default:
                $this->URLUtils__set($name, $value);
        }
    }
    
    /**
     * @param string $name
     * @return boolean
     */
    public function __isset($name)
    {
        return $this->URLUtils__isset($name)
            || in_array($name, ['target', 'download', 'rel', 'hreflang', 'type', 'text']);
    }
    
    /**
     * @param string $name
     * @return string DOMString
     */
    public function __get($name)
    {
        switch ($name) {
            case 'target':
            case 'download':
            case 'rel':
            case 'hreflang':
            case 'type':
                $returnValue = $this->getAttribute($name);
                break;
            case 'text':
                $returnValue = $this->textContent;
                break;
            default:
                $returnValue = $this->URLUtils__get($name);
        }
        return $returnValue;
    }
}
