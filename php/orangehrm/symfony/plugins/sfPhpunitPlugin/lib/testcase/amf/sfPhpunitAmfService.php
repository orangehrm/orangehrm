<?php

/**
 * sfBasePhpunitAmfService is the helper class for doing request to the amf endpoint.
 *
 * @package    sfPhpunitPlugin
 * @subpackage testcase
 * @author     Maksim Kotlyar <mkotlar@ukr.net>
 */
class sfPhpunitAmfService
{ 
  protected 
    $_client,
    $_service = false;
 
  public function __construct($service, sfPhpunitAmfClient $client)
  {
    $this->setService($service);
    $this->_client = $client;
  }
  
  public function setService($name)
  {
    if (!(is_string($name) || !empty($name))) {
      throw new Exception('Invalid amf service name. Should be none empty string. You provide: `'.$name.'`');
    }
    
    $this->_service = $name;
  }
  
  public function __call($method, $args)
  {
    $request = "{$this->_service}.{$method}"; 
    return $this->_client->sendRequest($request, $args);
  }
}