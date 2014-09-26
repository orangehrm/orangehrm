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
 */
class addCustomerAction extends baseAdminAction {

    /**
     * @param sfForm $form
     * @return
     */
    public function setForm(sfForm $form) {
        if (is_null($this->form)) {
            $this->form = $form;
        }
    }

    protected function getUndeleteForm() {
        return new UndeleteCustomerForm(array(), array('fromAction' => 'addCustomer', 'projectId' => ''), true);
    }

    public function execute($request) {

        /* For highlighting corresponding menu item */
        $request->setParameter('initialActionName', 'viewCustomers');

        $this->customerPermissions = $this->getDataGroupPermissions('time_customers');

        $this->customerId = $request->getParameter('customerId');
                
        $values = array('customerId' => $this->customerId, 'customerPermissions' => $this->customerPermissions);
        
        $this->setForm(new CustomerForm(array(), $values));

        $this->getUser()->setAttribute('addScreen', true);

        if ($request->isMethod('post')) {
            if ($this->customerPermissions->canCreate() || $this->customerPermissions->canUpdate()) {

                $this->form->bind($request->getParameter($this->form->getName()));
                if ($this->form->isValid()) {
                    $customerId = $this->form->getValue('customerId');

                    if (($this->customerPermissions->canCreate() && empty($customerId)) || 
                            ($this->customerPermissions->canUpdate() && $customerId > 0)) {
                        $result = $this->form->save();
                        $this->getUser()->setAttribute('addScreen', false);
                        $this->getUser()->setFlash($result['messageType'], $result['message']);
                        $this->redirect('admin/viewCustomers');
                    } else {
                        $this->forward(sfConfig::get('sf_secure_module'), sfConfig::get('sf_secure_action'));
                    }
                }
            }
        } else {
            if (!(($this->customerPermissions->canCreate() && empty($this->customerId)) || 
                    ($this->customerPermissions->canUpdate() && $this->customerId > 0))) {
                $this->forward(sfConfig::get('sf_secure_module'), sfConfig::get('sf_secure_action'));                
            }
            $this->undeleteForm = $this->getUndeleteForm();

            if (!empty($this->customerId)) {
                $this->form->setUpdateMode();
            }
        }
    }

}