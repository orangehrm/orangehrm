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

/**
 * deleteCustomFieldsAction action
 */
class deleteCustomFieldsAction extends sfAction {

    protected $customFieldService;

    /**
     * Get CustomFieldsService
     * @returns CustomFieldsService
     */
    public function getCustomFieldService() {
        if (is_null($this->customFieldService)) {
            $this->customFieldService = new CustomFieldsService();
            $this->customFieldService->setCustomFieldsDao(new CustomFieldsDao());
        }
        return $this->customFieldService;
    }

    /**
     * Set Country Service
     */
    public function setCustomFieldService(CustomFieldsService $customFieldsService) {
        $this->customFieldService = $customFieldsService;
    }

    /**
     * Delete Customer fields
     * @param sfWebRequest $request
     * @return unknown_type
     */
    public function execute($request) {
        
        $admin = $this->getUser()->hasCredential(Auth::ADMIN_ROLE);
        
        if (!$admin) {
            $this->forward("auth", "unauthorized");
        } else {
            $this->form = new CustomFieldDeleteForm(array(), array(), true);
            $this->form->bind($request->getParameter($this->form->getName()));
            if ($this->form->isValid()) {
                if (count($request->getParameter('chkLocID')) > 0) {
                    $customFieldsService = $this->getCustomFieldService();
                    $customFieldsService->deleteCustomField($request->getParameter('chkLocID'));
                    $this->getUser()->setFlash('templateMessage', array('success', __('Custom Field(s) Deleted Successfully')));
                } else {
                    $this->getUser()->setFlash('templateMessage', array('notice', __('Please Select At Least One Custom Field To Delete')));
                }
            }
            $this->redirect('pim/listCustomFields');
        }
    }

}

