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
class addCustomerAction extends sfAction {

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

        $usrObj = $this->getUser()->getAttribute('user');
        if (!$usrObj->isAdmin()) {
            $this->redirect('pim/viewPersonalDetails');
        }

        $this->customerId = $request->getParameter('customerId');
        $values = array('customerId' => $this->customerId);
        $this->setForm(new CustomerForm(array(), $values));

        if ($this->getUser()->hasFlash('templateMessage')) {
            list($this->messageType, $this->message) = $this->getUser()->getFlash('templateMessage');
        }
        $this->getUser()->setAttribute('addScreen', true);

        if ($request->isMethod('post')) {

            $this->form->bind($request->getParameter($this->form->getName()));
            if ($this->form->isValid()) {
                $result = $this->form->save();
                $this->getUser()->setAttribute('addScreen', false);
                $this->getUser()->setFlash('templateMessage', array($result['messageType'], $result['message']));
                $this->redirect('admin/viewCustomers');
            }
        } else {

            $this->undeleteForm = $this->getUndeleteForm();
            $customerId = $request->getParameter('customerId'); // This comes as a GET request from Customer List page

            if (!empty($customerId)) {
                $this->form->setUpdateMode();
            }
        }
    }

}