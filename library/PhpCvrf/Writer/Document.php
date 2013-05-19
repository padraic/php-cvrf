<?php

namespace PhpCvrf\Writer;

use PhpCvrf\Writer\Exception;

class Document
{

    protected $data = array();

    /**
     * Setters
     */

    public function setEncoding($encoding)
    {
        if (empty($encoding) || !is_string($encoding)) {
            throw new Exception\InvalidArgumentException(
                'Invalid parameter: parameter must be a non-empty string'
            );
        }
        $this->data['encoding'] = $encoding;

        return $this;
    }

    public function setLanguage($language)
    {
        if (empty($language) || !is_string($language)) {
            throw new Exception\InvalidArgumentException(
                'Invalid parameter: parameter must be a non-empty string'
            );
        }
        $this->data['language'] = $language;
        return $this;
    }

    public function setDocumentTitle($value)
    {
        if (empty($value) || !is_string($value)) {
            throw new Exception\InvalidArgumentException(
                'Invalid parameter: parameter must be a non-empty string'
            );
        }
        $this->data['title'] = $value;
        return $this;
    }

    public function setDocumentType($value)
    {
        if (empty($value) || !is_string($value)) {
            throw new Exception\InvalidArgumentException(
                'Invalid parameter: parameter must be a non-empty string'
            );
        }
        $this->data['type'] = $value;
        return $this;
    }

    /**
     * Getters
     */

    public function getEncoding()
    {
        if (!array_key_exists('encoding', $this->data)) {
            return 'UTF-8';
        }
        return $this->data['encoding'];
    }

    public function getLanguage()
    {
        if (!array_key_exists('language', $this->data)) {
            return null;
        }
        return $this->data['language'];
    }

    public function getDocumentTitle()
    {
        if (!array_key_exists('title', $this->data)) {
            return null;
        }
        return $this->data['title'];
    }

    public function getDocumentType()
    {
        if (!array_key_exists('type', $this->data)) {
            return null;
        }
        return $this->data['type'];
    }

}