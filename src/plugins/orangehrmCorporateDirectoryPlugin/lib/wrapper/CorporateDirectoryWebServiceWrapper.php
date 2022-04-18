<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AdminWebServiceWrapper
 *
 * @author nirmal
 */
class CorporateDirectoryWebServiceWrapper implements WebServiceWrapper {
    protected $corporateDirectoryWebServiceHelper;
    
    public function getServiceInstance() {
        if(!$this->corporateDirectoryWebServiceHelper instanceof CorporateDirectoryWebServiceHelper){
            $this->corporateDirectoryWebServiceHelper =  new CorporateDirectoryWebServiceHelper();
        }
        return $this->corporateDirectoryWebServiceHelper;
    }
    
    /**
     * 
     * @param type $options
     * @return Array
     */
    public function getCorporateDirectoryEmployeeDetails($options){
        $includeTerminated = false;
        if($options['includeTerminate']){
            $includeTerminated = true;
        }
        return $this->getServiceInstance()->getCorporateDirectoryEmployeeDetailsAsArray($includeTerminated);
    }
    
}
