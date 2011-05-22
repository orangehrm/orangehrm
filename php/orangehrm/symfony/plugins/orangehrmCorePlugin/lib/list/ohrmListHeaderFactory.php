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

    protected function init() {
        $this->headers = array();
    }

}
