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
        if ($this->getUser()->getAttribute('auth.isAdmin') == 'Yes') {
            $this->aboutEnabled = true;
            $organizationService = new OrganizationService();

            $companyInfo = $organizationService->getOrganizationGeneralInformation();
            $employeeService = new EmployeeService;

            $configurationService = new BeaconConfigurationService();
            $this->beaconAcceptance = $configurationService->getBeaconActivationAcceptanceStatus();
            if ($companyInfo) {
                $this->companyName = $companyInfo->getName();
            }
            $this->version = 'OrangeHRM 3.2.1';
            $totalEmployeeCount = $employeeService->getEmployeeCount(true);

            $this->activeEmployeeCount = $employeeService->getEmployeeCount(false);
            $this->terminatedEmployeeCount = $totalEmployeeCount - $this->activeEmployeeCount;
            $this->beaconRequired = ($this->getUser()->hasAttribute(BeaconCommunicationsService::BEACON_ACTIVATION_REQUIRED) 
                    && $this->getUser()->getAttribute(BeaconCommunicationsService::BEACON_ACTIVATION_REQUIRED))||
                    ($this->getUser()->hasAttribute(BeaconCommunicationsService::BEACON_FLASH_REQUIRED) &&
                    $this->getUser()->getAttribute(BeaconCommunicationsService::BEACON_FLASH_REQUIRED) ||
                   ($this->getUser()->hasAttribute(BeaconCommunicationsService::BEACON_MESSAGES_REQUIRED) &&
                    $this->getUser()->getAttribute(BeaconCommunicationsService::BEACON_MESSAGES_REQUIRED)));
            $this->setForm(new BeaconRegistrationForm());
        } else {
            $this->aboutEnabled = false;
             $this->beaconRequired = ($this->getUser()->hasAttribute(BeaconCommunicationsService::BEACON_ACTIVATION_REQUIRED) 
                    && $this->getUser()->getAttribute(BeaconCommunicationsService::BEACON_ACTIVATION_REQUIRED))||
                    ($this->getUser()->hasAttribute(BeaconCommunicationsService::BEACON_FLASH_REQUIRED) &&
                    $this->getUser()->getAttribute(BeaconCommunicationsService::BEACON_FLASH_REQUIRED) ||
                   ($this->getUser()->hasAttribute(BeaconCommunicationsService::BEACON_MESSAGES_REQUIRED) &&
                    $this->getUser()->getAttribute(BeaconCommunicationsService::BEACON_MESSAGES_REQUIRED)));
        }
    }

}
