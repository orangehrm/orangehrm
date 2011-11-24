<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of deleteProjectAction
 *
 * @author orangehrm
 */
class deleteSystemUsersAction extends sfAction {

	private $systemUserService ;
        
        public function getSystemUserService() {
            $this->systemUserService = new SystemUserService();
            return $this->systemUserService;
        }

        
	/**
	 *
	 * @param <type> $request
	 */
	public function execute($request) {

		$toBeDeletedUserIds = $request->getParameter('chkSelectRow');
                
                print_r( $toBeDeletedUserIds );

		if (!empty($toBeDeletedUserIds)) {
			$delete = true;
			/*foreach ($toBeDeletedProjectIds as $toBeDeletedProjectId) {
				$deletable = $this->getProjectService()->hasProjectGotTimesheetItems($toBeDeletedProjectId);
				if ($deletable) {
					$delete = false;
					break;
				}
			}
			if ($delete) {
				foreach ($toBeDeletedProjectIds as $toBeDeletedProjectId) {

					$customer = $this->getProjectService()->deleteProject($toBeDeletedProjectId);
				}
				$this->getUser()->setFlash('templateMessage', array('success', __('Selected Project(s) Deleted Successfully')));
			} else {
				$this->getUser()->setFlash('templateMessage', array('failure', __('Not Allowed to Delete Project(s) Which Have Time Logged Against')));
			}
                         */
		}

		$this->redirect('admin/viewSystemUsers');
	}

}

?>
