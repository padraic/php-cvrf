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

    public function setDocumentPublisher($value)
    {
        if (!in_array(strtolower($value),
            array('vendor', 'discoverer', 'coordinator', 'user', 'other')
        )) {
            throw new Exception\InvalidArgumentException(
                'Invalid parameter: parameter must be one of Vendor, Discoverer'
                . ', Coordinator, User or Other'
            );
        }
        $this->data['publisher'] = $value;
        return $this;
    }

    public function setIdentification($value)
    {
        if (empty($value) || !is_string($value)) {
            throw new Exception\InvalidArgumentException(
                'Invalid parameter: parameter must be a non-empty string'
            );
        }
        $this->data['id'] = $value;
        return $this;
    }

    public function setStatus($value)
    {
        if (empty($value) || !is_string($value)) {
            throw new Exception\InvalidArgumentException(
                'Invalid parameter: parameter must be a non-empty string'
            );
        }
        $this->data['status'] = $value;
        return $this;
    }

    public function setInitialReleaseDate($value)
    {
        if (empty($value) || !is_string($value)) {
            throw new Exception\InvalidArgumentException(
                'Invalid parameter: parameter must be a non-empty string'
            );
        }
        $this->data['initialreleasedate'] = $value;
        return $this;
    }

    public function setCurrentReleaseDate($value)
    {
        if (empty($value) || !is_string($value)) {
            throw new Exception\InvalidArgumentException(
                'Invalid parameter: parameter must be a non-empty string'
            );
        }
        $this->data['currentreleasedate'] = $value;
        return $this;
    }

    public function setRevisionHistory($value)
    {
        if (empty($value) || !is_array($value)) {
            throw new Exception\InvalidArgumentException(
                'Invalid parameter: parameter MUST be a non-empty array where '
                . ' each element of the array is an array of "version", "date" and '
                . ' "description" for each revision of this document'
            );
        }
        foreach ($value as $rev) {
            $this->addRevisionHistory($rev);
        }
        return $this;
    }

    public function addRevisionHistory(array $rev)
    {
        if (empty($rev) || !is_array($rev)) {
            throw new Exception\InvalidArgumentException(
                'Invalid parameter: parameter MUST be a non-empty array containing '
                . '"version", "date" and "description" for this revision of the document'
            );
        }
        if (!isset($rev['version'])) {
            throw new Exception\InvalidArgumentException(
                'Each revision MUST have a version (also called a "number" '
                . ' in the CVRF schema'
            );
        }
        if (!isset($rev['date'])) {
            throw new Exception\InvalidArgumentException(
                'Each revision MUST be dated in ISO format'
            );
        }
        if (!isset($rev['description'])) {
            throw new Exception\InvalidArgumentException(
                'Each revision MUST have a short description'
            );
        }
        $this->data['revisionhistory'][] = $rev;
        return $this;
    }

    public function setDocumentNotes($value)
    {
        if (empty($value) || !is_array($value)) {
            throw new Exception\InvalidArgumentException(
                'Invalid parameter: parameter MUST be a non-empty array where '
                . ' each element of the array is an array of "title", "audience" and '
                . ' "type" for each document note being added'
            );
        }
        foreach ($value as $note) {
            $this->addDocumentNote($note);
        }
        return $this;
    }

    public function addDocumentNote(array $note)
    {
        if (empty($note) || !is_array($note)) {
            throw new Exception\InvalidArgumentException(
                'Invalid parameter: parameter MUST be a non-empty array containing '
                . '"title", "audience" and "type" for this note'
            );
        }
        if (!isset($note['title'])) {
            throw new Exception\InvalidArgumentException(
                'Each document note MUST have a title'
            );
        }
        if (!isset($note['audience'])) {
            throw new Exception\InvalidArgumentException(
                'Each document note MUST have an audience'
            );
        }
        if (!isset($note['type'])) {
            throw new Exception\InvalidArgumentException(
                'Each document note MUST have a type'
            );
        }
        if (!isset($note['note'])) {
            throw new Exception\InvalidArgumentException(
                'Each document note MUST have the text of the note'
            );
        }
        $this->data['documentnotes'][] = $note;
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

    public function getDocumentPublisher()
    {
        if (!array_key_exists('publisher', $this->data)) {
            return null;
        }
        return $this->data['publisher'];
    }

    public function getIdentification()
    {
        if (!array_key_exists('id', $this->data)) {
            return null;
        }
        return $this->data['id'];
    }

    public function getStatus()
    {
        if (!array_key_exists('status', $this->data)) {
            return null;
        }
        return $this->data['status'];
    }

    public function getInitialReleaseDate()
    {
        if (!array_key_exists('initialreleasedate', $this->data)) {
            return null;
        }
        return $this->data['initialreleasedate'];
    }

    public function getCurrentReleaseDate()
    {
        if (!array_key_exists('currentreleasedate', $this->data)) {
            return null;
        }
        return $this->data['currentreleasedate'];
    }

    public function getRevisionHistory()
    {
        if (!array_key_exists('revisionhistory', $this->data)) {
            return null;
        }
        return $this->data['revisionhistory'];
    }

    public function getDocumentNotes()
    {
        if (!array_key_exists('documentnotes', $this->data)) {
            return null;
        }
        return $this->data['documentnotes'];
    }

}