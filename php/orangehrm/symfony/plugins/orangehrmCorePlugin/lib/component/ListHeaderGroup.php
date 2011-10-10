<?php

/**
 * Group of list headers
 *
 * @author ruchira
 */
class ListHeaderGroup {
    
    protected $name;
    protected $headers;
    
    public function __construct(array $headers, $name = null) {
        $this->headers = $headers;
        $this->name = $name;
    }
    
    public function addHeader(ListHeader $header) {
        $this->headers[] = $header;
    }
    public function getHeaders() {
        return $this->headers;
    }
    
    public function getName() {
        return $this->name;
    }
    
    public function showHeader() {
        return !empty($this->name);
    }
    
    public function getHeaderCount() {
        return count($this->headers);
    }
}
