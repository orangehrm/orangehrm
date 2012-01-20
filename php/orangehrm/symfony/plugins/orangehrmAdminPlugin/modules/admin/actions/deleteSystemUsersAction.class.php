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
                
                

		if (!empty($toBeDeletedUserIds)) {
			$delete = true;
                        $this->getSystemUserService()->deleteSystemUsers($toBeDeletedUserIds);
                        $this->getUser()->setFlash('templateMessage', array('success', __('System User(s) Deleted Successfully')));
			
		}else{
                    $this->getUser()->setFlash('templateMessage', array('warning', __('Please Select at Least One System User to Delete')));
                }

		$this->redirect('admin/viewSystemUsers');
	}

}

?>
