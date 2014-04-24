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
class deleteCustomerAction extends baseAdminAction {

    public function getCustomerService() {
        if (is_null($this->customerService)) {
            $this->customerService = new CustomerService();
            $this->customerService->setCustomerDao(new CustomerDao());
        }
        return $this->customerService;
    }

    /**
     *
     * @param <type> $request
     */
    public function execute($request) {

        $customerPermissions = $this->getDataGroupPermissions('time_customers');

        if ($customerPermissions->canDelete()) {

            $form = new DefaultListForm();
            $form->bind($request->getParameter($form->getName()));
            
            if ($form->isValid()) {
                $toBeDeletedCustomerIds = $request->getParameter('chkSelectRow');            
                if (!empty($toBeDeletedCustomerIds)) {
                    $delete = true;
                    foreach ($toBeDeletedCustomerIds as $toBeDeletedCustomerId) {
                        $deletable = $this->getCustomerService()->hasCustomerGotTimesheetItems($toBeDeletedCustomerId);
                        if ($deletable) {
                            $delete = false;
                            break;
                        }
                    }
                    if ($delete) {
                        foreach ($toBeDeletedCustomerIds as $toBeDeletedCustomerId) {

                            $customer = $this->getCustomerService()->deleteCustomer($toBeDeletedCustomerId);
                        }
                        $this->getUser()->setFlash('success', __(TopLevelMessages::DELETE_SUCCESS));
                    } else {
                        $this->getUser()->setFlash('error', __('Not Allowed to Delete Customer(s) Which Have Time Logged Against'));
                    }
                }
            }
            $this->redirect('admin/viewCustomers');
        }
    }

}

?>
