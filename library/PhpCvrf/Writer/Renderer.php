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

        /**
         * Header elements
         */
        $this->_setLanguage($this->dom, $root);
        $this->_setDocumentTitle($this->dom, $root);
        $this->_setDocumentType($this->dom, $root);
        $this->_setDocumentPublisher($this->dom, $root);

        /**
         * Document Tracking elements
         */
        $tracking = $this->dom->createElement('DocumentTracking');
        $root->appendChild($tracking);
        $this->_setIdentification($this->dom, $tracking);
        $this->_setStatus($this->dom, $tracking);
        $this->_setRevisionHistory($this->dom, $tracking);
        $this->_setInitialReleaseDate($this->dom, $tracking);
        $this->_setCurrentReleaseDate($this->dom, $tracking);

        /**
         * Document Notes
         */
        $this->_setDocumentNotes($this->dom, $root);

        /**
         * Product List
         */
        $this->_setProductList($this->dom, $root);

        /**
         * Vulnerabilities
         */
        $this->_setVulnerabilities($this->dom, $root);

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
            $exception = new Exception\InvalidArgumentException($message);
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
            $exception = new Exception\InvalidArgumentException($message);
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

    /**
     * Set document publisher field
     *
     * @param  DOMDocument $dom
     * @param  DOMElement $root
     * @return void
     * @throws Writer\Exception\InvalidArgumentException
     */
    protected function _setDocumentPublisher(DOMDocument $dom, DOMElement $root)
    {
        if (!$this->getDataContainer()->getDocumentPublisher()) {
            $message = 'CVRF 1.1 documents MUST contain a DocumentPublisher, set'
            . ' to a value of Vendor, Discoverer, Coordinator, User or Other';
            $exception = new Exception\InvalidArgumentException($message);
            if (!$this->ignoreExceptions) {
                throw $exception;
            } else {
                $this->exceptions[] = $exception;
                return;
            }
        }

        $pub = $dom->createElement('DocumentPublisher');
        $root->appendChild($pub);
        $pub->setAttribute('Type', ucfirst(strtolower(
            $this->getDataContainer()->getDocumentPublisher()
        )));
    }

    protected function _setIdentification(DOMDocument $dom, DOMElement $root)
    {
        if (!$this->getDataContainer()->getIdentification()) {
            $message = 'CVRF 1.1 documents MUST contain an Identification ID, e.g.'
            . ' "SA-2013-06"';
            $exception = new Exception\InvalidArgumentException($message);
            if (!$this->ignoreExceptions) {
                throw $exception;
            } else {
                $this->exceptions[] = $exception;
                return;
            }
        }

        $i = $dom->createElement('Identification');
        $root->appendChild($i);
        $id = $dom->createElement('ID');
        $i->appendChild($id);
        $text = $dom->createTextNode(
            $this->getDataContainer()->getIdentification()
        );
        $id->appendChild($text);
    }

    protected function _setStatus(DOMDocument $dom, DOMElement $root)
    {
        if (!$this->getDataContainer()->getStatus()) {
            $message = 'CVRF 1.1 documents MUST contain a Status, e.g.'
            . ' "Final"';
            $exception = new Exception\InvalidArgumentException($message);
            if (!$this->ignoreExceptions) {
                throw $exception;
            } else {
                $this->exceptions[] = $exception;
                return;
            }
        }

        $type = $dom->createElement('Status');
        $root->appendChild($type);
        $text = $dom->createTextNode(ucfirst(strtolower(
            $this->getDataContainer()->getStatus()
        )));
        $type->appendChild($text);
    }

    protected function _setRevisionHistory(DOMDocument $dom, DOMElement $root)
    {
        if (!$this->getDataContainer()->getRevisionHistory()) {
            $message = 'CVRF 1.1 documents MUST contain a RevisionHistory';
            $exception = new Exception\InvalidArgumentException($message);
            if (!$this->ignoreExceptions) {
                throw $exception;
            } else {
                $this->exceptions[] = $exception;
                return;
            }
        }

        $revisions = $this->getDataContainer()->getRevisionHistory();

        /**
         * Determine current version and set
         */
        $version = null;
        foreach ($revisions as $key => $rev) {
            if (is_null($version)) {
                $version = $rev['version'];
            } elseif (version_compare($rev['version'], $version, '>')) {
                $version = $rev['version'];
            }
        }

        $v = $dom->createElement('Version');
        $root->appendChild($v);
        $text = $dom->createTextNode($version);
        $v->appendChild($text);

        /**
         * Append Revision History
         */
        $rh = $dom->createElement('RevisionHistory');
        $root->appendChild($rh);
        foreach ($revisions as $key => $rev) {
            $r = $dom->createElement('Revision');
            $rh->appendChild($r);
            $ver = $dom->createElement('Number');
            $date = $dom->createElement('Date');
            $desc = $dom->createElement('Description');
            $r->appendChild($ver);
            $r->appendChild($date);
            $r->appendChild($desc);
            $text = $dom->createTextNode($rev['version']);
            $ver->appendChild($text);
            $text = $dom->createTextNode($rev['date']);
            $date->appendChild($text);
            $text = $dom->createTextNode($rev['description']);
            $desc->appendChild($text);
        }

    }

    protected function _setInitialReleaseDate(DOMDocument $dom, DOMElement $root)
    {
        if (!$this->getDataContainer()->getInitialReleaseDate()) {
            $message = 'CVRF 1.1 documents MUST contain a InitialReleaseDate, e.g.'
            . ' "2013-05-25T00:00:00+00:00"';
            $exception = new Writer\Exception\InvalidArgumentException($message);
            if (!$this->ignoreExceptions) {
                throw $exception;
            } else {
                $this->exceptions[] = $exception;
                return;
            }
        }

        $type = $dom->createElement('InitialReleaseDate');
        $root->appendChild($type);
        $text = $dom->createTextNode(
            $this->getDataContainer()->getInitialReleaseDate()
        );
        $type->appendChild($text);
    }

    protected function _setCurrentReleaseDate(DOMDocument $dom, DOMElement $root)
    {
        if (!$this->getDataContainer()->getCurrentReleaseDate()) {
            $message = 'CVRF 1.1 documents MUST contain a CurrentReleaseDate, e.g.'
            . ' "2013-05-25T00:00:00+00:00"';
            $exception = new Writer\Exception\InvalidArgumentException($message);
            if (!$this->ignoreExceptions) {
                throw $exception;
            } else {
                $this->exceptions[] = $exception;
                return;
            }
        }

        $type = $dom->createElement('CurrentReleaseDate');
        $root->appendChild($type);
        $text = $dom->createTextNode(
            $this->getDataContainer()->getInitialReleaseDate()
        );
        $type->appendChild($text);
    }

    protected function _setDocumentNotes(DOMDocument $dom, DOMElement $root)
    {
        if (!$this->getDataContainer()->getDocumentNotes()) {
            return;
        }

        $notes = $this->getDataContainer()->getDocumentNotes();
        $ordinal = 1;
        $dn = $dom->createElement('DocumentNotes');
        $root->appendChild($dn);
        foreach ($notes as $key => $note) {
            $n = $dom->createElement('Note');
            $dn->appendChild($n);
            $n->setAttribute('Title', $note['title']);
            $n->setAttribute('Audience', $note['audience']);
            $n->setAttribute('Type', $note['type']);
            $n->setAttribute('Ordinal', str_pad($ordinal, 3, '0', STR_PAD_LEFT));
            $ordinal++;
            $text = $dom->createTextNode($note['note']);
            $n->appendChild($text);
        }
    }

    protected function _setProductList(DOMDocument $dom, DOMElement $root)
    {
        if (!$this->getDataContainer()->getProducts()) {
            $message = 'CVRF 1.1 documents MUST contain a Product List';
            $exception = new Exception\InvalidArgumentException($message);
            if (!$this->ignoreExceptions) {
                throw $exception;
            } else {
                $this->exceptions[] = $exception;
                return;
            }
        }

        $pl = $dom->createElementNS(
            'http://www.icasi.org/CVRF/schema/prod/1.1',
            'ProductList');
        $root->appendChild($pl);
        $products = $this->getDataContainer()->getProducts();
        $pid = '1';
        foreach ($products as $product) {
            $name = $product['name'];
            $id = null;
            if (isset($product['id']) && !is_null($product['id'])) {
                $id = $product['id'];
            }
            $branches = $product['branches'];
            $firstBranch = false;
            foreach ($branches as $branch) {
                $b = $dom->createElement('Branch');
                if(false === $firstBranch) {
                    $pl->appendChild($b);
                    $firstBranch = true;
                } else {
                    $lastBranch->appendChild($b);
                }
                $lastBranch = $b;
                $b->setAttribute('Type', $branch['type']);
                $b->setAttribute('Name', $branch['name']);
            }
            $p = $dom->createElement('FullProductName');
            $lastBranch->appendChild($p);
            $text = $dom->createTextNode($name);
            $p->appendChild($text);
            if (is_null($id)) {
                $id = 'CVRFPID-' . str_pad($pid, 4, '0', STR_PAD_LEFT);
                $pid++;
            }
            $p->setAttribute('ProductID', $id);
        }
    }

    protected function _setVulnerabilities(DOMDocument $dom, DOMElement $root)
    {
        if (!$this->getDataContainer()->getVulnerabilities()) {
            return;
        }

        $vulns = $this->getDataContainer()->getVulnerabilities();
        $ordinal = 1;
        foreach($vulns as $vuln) {
            /** Header */
            $v = $dom->createElementNS(
                'http://www.icasi.org/CVRF/schema/vuln/1.1',
                'Vulnerability'
            );
            $v->setAttribute('Ordinal', $ordinal);
            $ordinal++;
            $root->appendChild($v);
            /** Title */
            $title = $dom->createElement('Title');
            $v->appendChild($title);
            $text = $dom->createTextNode($vuln->getTitle());
            $title->appendChild($text);
            /** Notes */
            if (!is_null($vuln->getNotes())) {
                $notes = $vuln->getNotes();
                $n_ordinal = 1;
                foreach ($notes as $note) {
                    $n = $dom->createElement('Note');
                    $v->appendChild($n);
                    $n->setAttribute('Title', $note['title']);
                    $n->setAttribute('Audience', $note['audience']);
                    $n->setAttribute('Type', $note['type']);
                    $n->setAttribute('Ordinal', $n_ordinal);
                    $n_ordinal++;
                    $text = $dom->createTextNode($note['note']);
                    $n->appendChild($text);
                }
            }
            /** */
        }
    }

}
