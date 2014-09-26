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

class viewLanguagesAction extends sfAction {
    
    private $languageService;
    
    public function getLanguageService() {
        
        if (!($this->languageService instanceof LanguageService)) {
            $this->languageService = new LanguageService();
        }        
        
        return $this->languageService;
    }

    public function setLanguageService($languageService) {
        $this->languageService = $languageService;
    }
    
    public function execute($request) {
        
        $this->_checkAuthentication();
        
        $this->form = new LanguageForm();
        $this->records = $this->getLanguageService()->getLanguageList();
        
		if ($this->getUser()->hasFlash('templateMessage')) {
            $this->templateMessage = $this->getUser()->getFlash('templateMessage');
        }        
        
        if ($request->isMethod('post')) {
            
			$this->form->bind($request->getParameter($this->form->getName()));
            
			if ($this->form->isValid()) {

                $this->_checkDuplicateEntry();
                
				$templateMessage = $this->form->save();
				$this->getUser()->setFlash($templateMessage['messageType'], $templateMessage['message']);                
                $this->redirect('admin/viewLanguages');
                
            }
            
        }
       $this->listForm = new DefaultListForm(); 
    }
    
    protected function _checkAuthentication() {
        
        $user = $this->getUser()->getAttribute('user');
        
		if (!$user->isAdmin()) {
			$this->redirect('pim/viewPersonalDetails');
		}
        
    }

    protected function _checkDuplicateEntry() {

        $id = $this->form->getValue('id');
        $object = $this->getLanguageService()->getLanguageByName($this->form->getValue('name'));
        
        if ($object instanceof Language) {
            
            if (!empty($id) && $id == $object->getId()) {
                return false;
            }
            
            $this->getUser()->setFlash('warning', __('Name Already Exists'));
            $this->redirect('admin/viewLanguages');            
            
        }
        
        return false;

    }    
    
}
