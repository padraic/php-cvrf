<?php

namespace PhpCvrf\Writer;

use DOMDocument;
use DOMElement;

class Renderer
{

    /**
     * @var Writer\Document
     */
    protected $container = null;

    /**
     * @var DOMDocument
     */
    protected $dom = null;

    /**
     * @var bool
     */
    protected $ignoreExceptions = false;

    /**
     * @var array
     */
    protected $exceptions = array();

    /**
     * Encoding of all text values
     *
     * @var string
     */
    protected $encoding = 'UTF-8';

    /**
     * @var DOMElement
     */
    protected $rootElement = null;

    /**
     * Constructor
     *
     * @param Writer\Document $container
     */
    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * Save XML to string
     *
     * @return string
     */
    public function saveXml()
    {
        return $this->getDomDocument()->saveXml();
    }

    /**
     * Get DOM document
     *
     * @return DOMDocument
     */
    public function getDomDocument()
    {
        return $this->dom;
    }

    /**
     * Get document element from DOM
     *
     * @return DOMElement
     */
    public function getElement()
    {
        return $this->getDomDocument()->documentElement;
    }

    /**
     * Get data container of items being rendered
     *
     * @return Writer\Document
     */
    public function getDataContainer()
    {
        return $this->container;
    }

    /**
     * Set feed encoding
     *
     * @param  string $enc
     * @return Writer\Renderer
     */
    public function setEncoding($enc)
    {
        $this->encoding = $enc;
        return $this;
    }

    /**
     * Get feed encoding
     *
     * @return string
     */
    public function getEncoding()
    {
        return $this->encoding;
    }

    /**
     * Indicate whether or not to ignore exceptions
     *
     * @param  bool $bool
     * @return AbstractRenderer
     * @throws Writer\Exception\InvalidArgumentException
     */
    public function ignoreExceptions($bool = true)
    {
        if (!is_bool($bool)) {
            throw new Writer\Exception\InvalidArgumentException('Invalid parameter: $bool. Should be TRUE or FALSE (defaults to TRUE if null)');
        }
        $this->ignoreExceptions = $bool;
        return $this;
    }

    /**
     * Get exception list
     *
     * @return array
     */
    public function getExceptions()
    {
        return $this->exceptions;
    }

    /**
     * Sets the absolute root element for the XML feed being generated. This
     * helps simplify the appending of namespace declarations, but also ensures
     * namespaces are added to the root element - not scattered across the entire
     * XML file - may assist namespace unsafe parsers and looks pretty ;).
     *
     * @param DOMElement $root
     */
    public function setRootElement(DOMElement $root)
    {
        $this->rootElement = $root;
    }

    /**
     * Retrieve the absolute root element for the XML feed being generated.
     *
     * @return DOMElement
     */
    public function getRootElement()
    {
        return $this->rootElement;
    }

}
