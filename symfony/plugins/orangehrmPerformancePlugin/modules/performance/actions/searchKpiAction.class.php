<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of searchKpiAction
 *
 * @author nadeera
 */

class searchKpiAction extends basePeformanceAction {
    
    public $kpiSearchForm;
    
    public function preExecute() {
       $this->_checkAuthentication();
    }
    
    /**
     *
     * @return KpiSearchForm
     */
    public function getKpiSearchForm() {
        if($this->kpiSearchForm == null ){
            return new KpiSearchForm();
        } else {
            return $this->kpiSearchForm;
        }
    }

    /**
     *
     * @param KpiSearchForm $kpiSearchForm 
     */
    public function setKpiSearchForm($kpiSearchForm) {
        $this->kpiSearchForm = $kpiSearchForm;
    }
        
    public function execute($request) {
              
        $form = $this->getKpiSearchForm();
        
        if ($request->isMethod('post')) {
            $form->bind($request->getParameter($form->getName()));
            if ($form->isValid()) {
                try {
                  
                } catch (LeaveAllocationServiceException $e) {
                    $this->templateMessage = array('WARNING', __($e->getMessage()));
                }
            }
        }
        
        $message = $this->getUser()->getFlash('templateMessage');        
        $this->messageType = (isset($message[0]))?strtolower($message[0]):"";
        $this->message = (isset($message[1]))?$message[1]:"";
        

        if ($this->getUser()->hasFlash('templateMessage')) {
            $this->templateMessage = $this->getUser()->getFlash('templateMessage');
            $this->getUser()->setFlash('templateMessage', array());
        }
        
        $kpiList = $form->searchKpi();
        $this->setListComponent($kpiList);
        $this->form = $form;        
    }

    /**
     *
     * @param Doctrine_Collection $kpiList 
     */
    protected function setListComponent($kpiList) {

        $configurationFactory = $this->getListConfigurationFactory();
        
        ohrmListComponent::setActivePlugin('orangehrmPerformancePlugin');
        ohrmListComponent::setConfigurationFactory($configurationFactory);
        ohrmListComponent::setListData($kpiList);
        ohrmListComponent::setPageNumber(0);
        $numRecords = count($kpiList);
        ohrmListComponent::setItemsPerPage($numRecords);
        ohrmListComponent::setNumberOfRecords($numRecords);
    }
    
    /**
     *
     * @return \KpiListConfigurationFactory 
     */
    protected function getListConfigurationFactory() {
        return new KpiListConfigurationFactory();
    }   
}