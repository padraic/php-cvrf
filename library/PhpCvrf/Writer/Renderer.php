<?php

namespace PhpCvrf\Writer;

use DOMDocument;
use DOMElement;
use PhpCvrf\Writer\Exception;

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
    public function __construct(Document $container)
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
            throw new Exception\InvalidArgumentException('Invalid parameter: $bool. Should be TRUE or FALSE (defaults to TRUE if null)');
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

    /**
     * Render Atom feed
     *
     * @return Atom
     */
    public function render()
    {
        if (!$this->container->getEncoding()) {
            $this->container->setEncoding('UTF-8');
        }
        $this->dom = new DOMDocument('1.0', $this->container->getEncoding());
        $this->dom->formatOutput = true;
        $root = $this->dom->createElementNS(
            'http://www.icasi.org/CVRF/schema/cvrf/1.1', 'cvrfdoc'
        );
        $this->setRootElement($root);
        $this->dom->appendChild($root);

        $this->_setLanguage($this->dom, $root);
        $this->_setDocumentTitle($this->dom, $root);
        $this->_setDocumentType($this->dom, $root);

        return $this;
    }

    /**
     * Set feed language
     *
     * @param  DOMDocument $dom
     * @param  DOMElement $root
     * @return void
     */
    protected function _setLanguage(DOMDocument $dom, DOMElement $root)
    {
        if ($this->getDataContainer()->getLanguage()) {
            $root->setAttribute(
                'xml:lang',
                $this->getDataContainer()->getLanguage()
            );
        }
    }

    /**
     * Set document title
     *
     * @param  DOMDocument $dom
     * @param  DOMElement $root
     * @return void
     * @throws Writer\Exception\InvalidArgumentException
     */
    protected function _setDocumentTitle(DOMDocument $dom, DOMElement $root)
    {
        if (!$this->getDataContainer()->getDocumentTitle()) {
            $message = 'CVRF 1.1 documents MUST contain a DocumentTitle, e.g.'
            . ' "Acme Security Advisory: XSS Vulnerabilities in Acme 1.8 - 2.1"';
            $exception = new Writer\Exception\InvalidArgumentException($message);
            if (!$this->ignoreExceptions) {
                throw $exception;
            } else {
                $this->exceptions[] = $exception;
                return;
            }
        }

        $title = $dom->createElement('DocumentTitle');
        $root->appendChild($title);
        $text = $dom->createTextNode($this->getDataContainer()->getDocumentTitle());
        $title->appendChild($text);
    }

    /**
     * Set document title
     *
     * @param  DOMDocument $dom
     * @param  DOMElement $root
     * @return void
     * @throws Writer\Exception\InvalidArgumentException
     */
    protected function _setDocumentType(DOMDocument $dom, DOMElement $root)
    {
        if (!$this->getDataContainer()->getDocumentType()) {
            $message = 'CVRF 1.1 documents MUST contain a DocumentType, e.g.'
            . ' "Security Advisory"';
            $exception = new Writer\Exception\InvalidArgumentException($message);
            if (!$this->ignoreExceptions) {
                throw $exception;
            } else {
                $this->exceptions[] = $exception;
                return;
            }
        }

        $type = $dom->createElement('DocumentType');
        $root->appendChild($type);
        $text = $dom->createTextNode($this->getDataContainer()->getDocumentType());
        $type->appendChild($text);
    }

}
