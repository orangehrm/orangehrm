<?php

abstract class ohrmListConfigurationFactory implements ListConfigurationFactory {

    protected $headers;
    protected $className = 'stdClass';

    public function getHeaders() {
        if (empty($this->headers)) {
            $this->init();
        }

        return $this->headers;
    }

    public function setHeaders(array $headers) {
        $this->headers = $headers;
    }

    public function setHeader($index, ListHeader $header) {

        if (empty($this->headers)) {
            $this->init();
        }

        $this->headers[$index] = $header;
    }

    public function getHeader($index) {

        if (empty($this->headers)) {
            $this->init();
        }

        if (isset($this->headers[$index])) {
            return $this->headers[$index];
        } else {
            throw new Exception('No headers set at index ' . $index);
        }
    }

    protected function init() {
        $this->headers = array();
    }

}
