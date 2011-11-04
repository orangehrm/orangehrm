<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of deleteCustomerAction
 *
 * @author orangehrm
 */
class deleteCustomerAction extends sfAction {

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

		$toBeDeletedCustomerIds = $request->getParameter('chkSelectRow');

		if (!empty($toBeDeletedCustomerIds)) {
			$delete = true;
			foreach ($toBeDeletedCustomerIds as $toBeDeletedCustomerId) {
				$deletable = $this->getCustomerService()->isCustomerHasTimesheetItems($toBeDeletedCustomerId);
				if ($deletable) {
					$delete = false;
					break;
				}
			}
			if ($delete) {
				foreach ($toBeDeletedCustomerIds as $toBeDeletedCustomerId) {

					$customer = $this->getCustomerService()->deleteCustomer($toBeDeletedCustomerId);
				}
				$this->getUser()->setFlash('templateMessage', array('success', __('Selected Customer(s) Deleted Successfully')));
			} else {
				$this->getUser()->setFlash('templateMessage', array('failure', __('Not Allowed to Delete Customer(s) Which Have Time Logged Against')));
			}
		}

		$this->redirect('admin/viewCustomers');
	}

}

?>
