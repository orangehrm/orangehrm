<?php

class sfPhpunitAmfClient
{
  protected $_endPoint;
  protected $_encoding;
  protected $_httpProxy;
  protected $_credentials;
  protected $_header;
  
  public function __construct($endPoint = null)
  {
    if (is_null($endPoint)) {
      $options = sfConfig::get('sf_phpunit_amf');
      $endPoint = $options['endpoint'];
    }
    if (!$endPoint) {
      throw new Exception('The amf server endpoit should be set as constructor parameter or in config as `sf_phpunit_amf_endpoint`');      
    }
    
    $this->_endPoint = $endPoint;
  }
  
  public function sendRequest($servicePath, $data) 
  {
    return $this->_buildSabreAmfClient()->sendRequest($servicePath, $data);
  }
  
  public function setEncoding($encoding) 
  {
    $this->_encoding = $encoding;
  }
  
  public function setHttpProxy($httpProxy) 
  {
    $this->_httpProxy = $httpProxy;
  }
  
  public function setCredentials($username, $password) 
  {
    $this->_credentials['username'] = $username;
    $this->_credentials['password'] = $password;
  }
  
  public function addHeader($name, $required, $data) 
  {
    if (!is_array($this->header)) $this->header = array();
    
    $this->header[] = array('name' => $name, 'required' => $required, 'data' => $data);
  }
  
  /**
   * @return SabreAMF_Client
   */
  protected function _buildSabreAmfClient() 
  {
    if (!class_exists('SabreAMF_Client', true)) {
      @require_once 'SabreAMF/Client.php'; 
    }
    
    if (!class_exists('SabreAMF_Client', true)) {
      throw new Exception('For using `sfPhpunitAmfClient` you need to have SabreAMF 1.3.x or above. It can be stored in project lib or installed throught PEAR. For more info please visit: http://osflash.org/sabreamf');
    }
    
    $c = new SabreAMF_Client($this->_endPoint);
    if (!is_null($this->_encoding)) {
      $c->setEncoding($this->_encoding);
    }
    if (!is_null($this->_httpProxy)) {
      $c->setHttpProxy($this->_httpProxy);
    }
    if (is_array($this->_credentials)) {
      $c->setCredentials($this->_credentials['username'], $this->_credentials['password']);
    }
    if (is_array($this->_header)) {
      foreach ($this->_header as $data) {
        $c->setHeader($data['name'], $data['required'], $data['data']);
      }
    }
    
    return $c;
  }
}