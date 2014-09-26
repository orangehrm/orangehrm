<?php

/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 *
 */
class CustomerForm extends BaseForm {

    private $updateMode = false;
    private $customerService;
    private $customerPermissions;

    public function getCustomerService() {
        if (is_null($this->customerService)) {
            $this->customerService = new CustomerService();
            $this->customerService->setCustomerDao(new CustomerDao());
        }
        return $this->customerService;
    }

    public function configure() {
        
        $this->customerId = $this->getOption('customerId');
        if (isset($this->customerId)) {
            $customer = $this->getCustomerService()->getCustomerById($this->customerId);
        }
        
        $this->customerPermissions = $this->getOption('customerPermissions');

        $widgets = array('customerId' => new sfWidgetFormInputHidden());
        $validators = array('customerId' => new sfValidatorNumber(array('required' => false)));

        if ($this->customerPermissions->canRead()) {
            $customerWidgets = $this->getCustomerWidgets();
            $customerValidators = $this->getCustomerValidators();

            if (!($this->customerPermissions->canUpdate() || $this->customerPermissions->canCreate())) {
                foreach ($customerWidgets as $widgetName => $widget) {
                    $widget->setAttribute('disabled', 'disabled');
                }
            }

            $widgets = array_merge($widgets, $customerWidgets);
            $validators = array_merge($validators, $customerValidators);
        }
        
        $this->setWidgets($widgets);
        $this->setValidators($validators);

        $this->widgetSchema->setNameFormat('addCustomer[%s]');

        if (isset($customer) && $customer != null) {

            $this->setDefault('customerName', $customer->getName());
            $this->setDefault('description', $customer->getDescription());
            $this->setDefault('hdnOriginalCustomerName', $customer->getName());
        }
    }

    public function save() {

        $this->resultArray = array();
        $customerId = $this->getValue('customerId');
        if ($customerId > 0) {
            $service = $this->getCustomerService();
            $customer = $service->getCustomerById($customerId);
            $this->resultArray['messageType'] = 'success';
            $this->resultArray['message'] = __(TopLevelMessages::UPDATE_SUCCESS);
        } else {
            $customer = new Customer();
            $this->resultArray['messageType'] = 'success';
            $this->resultArray['message'] = __(TopLevelMessages::SAVE_SUCCESS);
        }

        $customer->setName(trim($this->getValue('customerName')));
        $customer->setDescription($this->getValue('description'));
        $customer->save();
        return $this->resultArray;
    }

    public function setUpdateMode() {
        $this->updateMode = true;
    }

    public function isUpdateMode() {
        return $this->updateMode;
    }

    public function getCustomerListAsJson() {

        $list = array();
        $customerList = $this->getCustomerService()->getAllCustomers();
        foreach ($customerList as $customer) {
            if (!$customer->getIsDeleted()) {
                $list[] = array('id' => $customer->getCustomerId(), 'name' => $customer->getName());
            }
        }
        return json_encode($list);
    }

    public function getDeletedCustomerListAsJson() {

        $list = array();
        $customerList = $this->getCustomerService()->getAllCustomers();
        foreach ($customerList as $customer) {
            if ($customer->getIsDeleted()) {
                $list[] = array('id' => $customer->getCustomerId(), 'name' => $customer->getName());
            }
        }
        return json_encode($list);
    }

    public function getCustomerWidgets() {
        $widgets = array();

        $widgets['customerName'] = new sfWidgetFormInputText();
        $widgets['hdnOriginalCustomerName'] = new sfWidgetFormInputHidden();
        $widgets['description'] = new sfWidgetFormTextArea();

        return $widgets;
    }

    public function getCustomerValidators() {
        $validators = array();

        $validators['customerName'] = new sfValidatorString(array('required' => true, 'max_length' => 52));
        $validators['hdnOriginalCustomerName'] = new sfValidatorString(array('required' => false));
        $validators['description'] = new sfValidatorString(array('required' => false, 'max_length' => 255));

        return $validators;
    }

}