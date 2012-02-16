<?php

class saveLeaveEntitlementsAction extends baseLeaveAction {

    public $form;

    public function execute($request) {

        $form = $this->getForm();
        $saveSuccess = true;

        if ($request->isMethod(sfRequest::POST)) {
            $form->bind($request->getParameter($form->getName()));

            $hdnEmpId = $request->getParameter('hdnEmpId');
            $hdnLeaveTypeId = $request->getParameter('hdnLeaveTypeId');
            $hdnLeavePeriodId = $request->getParameter('hdnLeavePeriodId');
            $txtLeaveEntitled = $request->getParameter('txtLeaveEntitled');
            $count = count($txtLeaveEntitled);

            $leaveEntitlementService = $this->getLeaveEntitlementService();
            $leaveSummaryData = $request->getParameter('leaveSummary');

            for ($i = 0; $i < $count; $i++) {
                $leavePeriodId = empty($hdnLeavePeriodId[$i]) ? $leaveSummaryData['hdnSubjectedLeavePeriod'] : $hdnLeavePeriodId[$i];
                try {
                    $leaveEntitlementService->saveEmployeeLeaveEntitlement($hdnEmpId[$i], $hdnLeaveTypeId[$i], $leavePeriodId, $txtLeaveEntitled[$i], true);
                } catch (Exception $e) {
                    $saveSuccess = false;
                }
            }

            if ($saveSuccess) {
                $this->getUser()->setFlash('templateMessage', array('SUCCESS', __(TopLevelMessages::SAVE_SUCCESS)), false);
            } else {
                $this->getUser()->setFlash('templateMessage', array('FAILURE', __(TopLevelMessages::SAVE_FAILURE)), false);
            }

            $this->forwardToLeaveSummary();
        }
    }
    
    protected function forwardToLeaveSummary() {
        $this->forward('leave', 'viewLeaveSummary');
    }

    /**
     *
     * @return LeaveSummaryForm 
     */
    protected function getForm() {
        if (!($this->form instanceof LeaveSummaryForm)) {
            $formDefaults = array();
            $formOptions = $this->getLoggedInUserDetails();
            $this->form = new LeaveSummaryForm($formDefaults, $formOptions, true);
        }

        return $this->form;
    }

    /**
     *
     * @param LeaveSummaryForm $form 
     */
    protected function setForm(LeaveSummaryForm $form) {
        $this->form = $form;
    }

}

