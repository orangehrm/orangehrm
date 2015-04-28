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
class AdminWebServiceWrapper implements WebServiceWrapper {
    protected $adminWebServiceHelper;
    
    public function getServiceInstance() {
        if(!$this->adminWebServiceHelper instanceof AdminWebServiceHelper){
            $this->adminWebServiceHelper =  new AdminWebServiceHelper();
        }
        return $this->adminWebServiceHelper;
    }
    
    public function setServiceInstance(AdminWebServiceHelper $adminWebServiceHelper) {
            $this->adminWebServiceHelper = $adminWebServiceHelper;
    }
    /**
     * Get Job title list
     * @param type $options
     * @return type
     */
    public function getJobTitleList($options){
        return $this->getServiceInstance()->getJobTitleList();
    }
    
    /**
     * Get location list
     * @param type $options
     * @return type
     */
    public function getLocationList($options){
        return $this->getServiceInstance()->getLocationList();
    }

}
