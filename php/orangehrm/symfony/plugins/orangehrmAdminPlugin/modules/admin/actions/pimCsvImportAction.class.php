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
class pimCsvImportAction extends baseCsvImportAction {

	/**
	 * @param sfForm $form
	 * @return
	 */
	public function setForm(sfForm $form) {
		if (is_null($this->form)) {
			$this->form = $form;
		}
	}

	public function execute($request) {
        
        $this->_checkAuthentication();

		$this->setForm(new PimCsvImportForm());

		if ($this->getUser()->hasFlash('templateMessage')) {
			list($this->messageType, $this->message) = $this->getUser()->getFlash('templateMessage');
		}

		if ($request->isMethod('post')) {

			$this->form->bind($request->getParameter($this->form->getName()), $request->getFiles($this->form->getName()));
			$file = $request->getFiles($this->form->getName());
			if ($_FILES['pimCsvImport']['size']['csvFile'] > 1024000 || $_FILES == null) {
				$this->getUser()->setFlash('templateMessage', array('warning', __('Failed to Import: File Size Exceeded')));
				$this->redirect('admin/pimCsvImport');
			}
			if ($this->form->isValid()) {
				$result = $this->form->save();

				if (isset($result['messageType'])) {
					$this->messageType = $result['messageType'];
					$this->message = $result['message'];
				} else {
				    if($result != 0) {
					   $this->getUser()->setFlash('templateMessage', array('success', __('Number of Records Imported').": ".$result));
				    } else {
				        $this->getUser()->setFlash('templateMessage', array('warning', __('Failed to Import: No Compatible Records')));
				    }
					$this->redirect('admin/pimCsvImport');
				}
			}
		}
	}
    
    protected function _checkAuthentication() {
        
        $user = $this->getUser()->getAttribute('user');
        
		if (!$user->isAdmin()) {
			$this->redirect('auth/login');
		}
        
    }    

}

?>
