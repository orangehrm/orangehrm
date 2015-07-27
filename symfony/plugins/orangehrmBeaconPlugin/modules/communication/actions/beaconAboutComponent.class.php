<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of beaconAboutComponent
 *
 * @author chathura
 */
class beaconAboutComponent extends sfComponent {

    public function setForm($form) {
        if (is_null($this->form)) {
            $this->form = $form;
        }
    }
    
    public function execute($request) {
        
        $this->isAdmin = $this->getUser()->getAttribute('auth.isAdmin') == 'Yes' ? true : false;

        $this->aboutEnabled = true;
        $organizationService = new OrganizationService();

        $companyInfo = $organizationService->getOrganizationGeneralInformation();
        $employeeService = new EmployeeService;

        $configurationService = new BeaconConfigurationService();
        $this->beaconAcceptance = $configurationService->getBeaconActivationAcceptanceStatus();
        if ($companyInfo) {
            $this->companyName = $companyInfo->getName();
        }
        $this->version = 'Orangehrm OS 3.3.2';
        $totalEmployeeCount = $employeeService->getEmployeeCount(true);

        $this->activeEmployeeCount = $employeeService->getEmployeeCount(false);
        $this->terminatedEmployeeCount = $totalEmployeeCount - $this->activeEmployeeCount;

        $this->setForm(new BeaconRegistrationForm());
        
        if(!$this->getUser()->hasAttribute(BeaconCommunicationsService::BEACON_ACTIVATION_SET) && !$this->getUser()->getAttribute(BeaconCommunicationsService::BEACON_ACTIVATION_SET)) {
            $beaconCommunicationService = new BeaconCommunicationsService();
            $beaconCommunicationService->setBeaconActivation();
        }
        
            $this->beaconRequired = ($this->getUser()->hasAttribute(BeaconCommunicationsService::BEACON_ACTIVATION_REQUIRED) 
                        && $this->getUser()->getAttribute(BeaconCommunicationsService::BEACON_ACTIVATION_REQUIRED))||
                        ($this->getUser()->hasAttribute(BeaconCommunicationsService::BEACON_FLASH_REQUIRED) &&
                        $this->getUser()->getAttribute(BeaconCommunicationsService::BEACON_FLASH_REQUIRED));
    }

}
