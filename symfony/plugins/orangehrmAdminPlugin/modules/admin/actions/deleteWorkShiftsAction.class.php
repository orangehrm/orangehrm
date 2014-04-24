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
class deleteWorkShiftsAction extends sfAction {
	
	private $workShiftService;

	public function getWorkShiftService() {
		if (is_null($this->workShiftService)) {
			$this->workShiftService = new WorkShiftService();
			$this->workShiftService->setWorkShiftDao(new WorkShiftDao());
		}
		return $this->workShiftService;
	}
	
	public function execute($request) {
                $form = new DefaultListForm();
                $form->bind($request->getParameter($form->getName())); 
		$toBeDeletedShiftIds = $request->getParameter('chkSelectRow');

		if (!empty($toBeDeletedShiftIds)) {

			foreach ($toBeDeletedShiftIds as $toBeDeletedShiftId) {
                                if ($form->isValid()) {
                                    $shift = $this->getWorkShiftService()->getWorkShiftById($toBeDeletedShiftId);
                                    
                if ($shift instanceof WorkShift) {
                    $shift->delete();
                }
			
			$this->getUser()->setFlash('success', __(TopLevelMessages::DELETE_SUCCESS));
                                }
                        }
		}

		$this->redirect('admin/workShift');
	}
}

?>
