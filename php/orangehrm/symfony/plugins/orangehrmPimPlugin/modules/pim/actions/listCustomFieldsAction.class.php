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
 * listCustomFields action
 */
class listCustomFieldsAction extends sfAction {

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
     * List Custom fields
     * @param sfWebRequest $request
     * @return void
     */
    public function execute($request) {
        $admin = $this->getUser()->hasCredential(Auth::ADMIN_ROLE);
        
        if (!$admin) {
            $this->redirect($this->getRequest()->getReferer());
        } else {        
            if ($this->getUser()->hasFlash('templateMessage')) {
                list($this->messageType, $this->message) = $this->getUser()->getFlash('templateMessage');
            } else if ($request->hasParameter('message')) {
                $message = $request->getParameter('message');
                
                if ($message == 'UPDATE_SUCCESS') {
                    $this->messageType = 'success';
                    $this->message = __('Custom Field Successfully Updated');
                }
            }
            
            $this->form = new CustomFieldForm(array(), array(), true);
            $this->deleteForm = new CustomFieldDeleteForm(array(), array(), true);
            $customFieldsService = $this->getCustomFieldService();
            $this->sorter = new ListSorter('propoerty.sort', 'admin_module', $this->getUser(), array('field_num', ListSorter::ASCENDING));

            if ($request->getParameter('sort')) {
                $this->sorter->setSort(array($request->getParameter('sort'), $request->getParameter('order')));
                $this->listCustomField = $customFieldsService->getCustomFieldList(null, $request->getParameter('sort'), $request->getParameter('order'));
            } else {

                $this->listCustomField = $customFieldsService->getCustomFieldList();
            }
        }
    }

}