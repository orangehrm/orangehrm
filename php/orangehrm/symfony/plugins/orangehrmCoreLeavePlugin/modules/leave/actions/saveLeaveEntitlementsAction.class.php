<?php

class saveLeaveEntitlementsAction extends baseLeaveAction {

    public $form;

    public function execute($request) {

        $form = $this->getForm();
        $saveSuccess = true;
        
        $logger = Logger::getLogger('saveLeaveEntitlementsAction'); 

        if ($request->isMethod(sfRequest::POST)) {
            $form->bind($request->getParameter($form->getName()));

            $hdnEmpId = $request->getParameter('hdnEmpId');
            $hdnLeaveTypeId = $request->getParameter('hdnLeaveTypeId');
            $leavePeriodId = $request->getParameter('leavePeriodId');
            $txtLeaveEntitled = $request->getParameter('txtLeaveEntitled');

            $idCount = count($hdnEmpId);
            $leaveTypeCount = count($hdnLeaveTypeId);
            $count = count($txtLeaveEntitled);
            
            /*
             * Validate we have all input values for all rows.
             */
            if (($count != $idCount) || ($count != $leaveTypeCount)) {
                $logger->error("saveLeaveEntitlements: field count does not match: " .
                        " employee ids={$idCount}, leaveTypeIds={$hdnLeaveTypeId}, entitlements={$txtLeaveEntitled}");
                $logger->error($hdnEmpId);
                $logger->error($hdnLeaveTypeId);
                $logger->error($txtLeaveEntitled);

                $saveSuccess = false;
            } else {
                $leaveEntitlementService = $this->getLeaveEntitlementService();
                $leaveSummaryData = $request->getParameter('leaveSummary');
                if ($count > 0) {
                     
                    $employeeLeaveEntitlementList = $leaveEntitlementService->searchEmployeeLeaveEntitlement($hdnEmpId, $hdnLeaveTypeId, $leavePeriodId, $count);
                    $employeeLeaveEntitlementArray = $this->getEmployeeLeaveEntitlementArray($employeeLeaveEntitlementList);
                     
                    $employeeLeaveEntitlements = array();
                    
                    for ($i = 0; $i < $count; $i++) {
                        $arrayKey = $hdnEmpId[$i]."_".$hdnLeaveTypeId[$i];
                        if(array_key_exists($arrayKey, $employeeLeaveEntitlementArray)) {
                            $employeeLeaveEntitlement = $employeeLeaveEntitlementArray[$arrayKey];
                            $employeeLeaveEntitlement->setNoOfDaysAllotted($txtLeaveEntitled[$i]);
                        } else {
                            $employeeLeaveEntitlement = new EmployeeLeaveEntitlement();
                            $employeeLeaveEntitlement->setLeaveTypeId($hdnLeaveTypeId[$i]);
                            $employeeLeaveEntitlement->setEmployeeId($hdnEmpId[$i]);
                            $employeeLeaveEntitlement->setLeavePeriodId($leavePeriodId);
                            $employeeLeaveEntitlement->setNoOfDaysAllotted($txtLeaveEntitled[$i]);
                        }
                        $employeeLeaveEntitlements[] = $employeeLeaveEntitlement;
                    }
                    
                    try {
                        $leaveEntitlementService->saveEmployeeLeaveEntitlementCollection($employeeLeaveEntitlements);
                    } catch (Exception $e) {
                        $logger->error($e);
                        $saveSuccess = false;
                    }
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
    
    /**
     * GetLeaveEntitlementArray
     * Itterate through the $employeeLeaveEntitlementList and retrun an array with emplyeeLeaveEntitlements as values and 
     * employeeId_leaveTypeId as keys 
     * @param EmployeeLeaveEntitlementCollection $employeeLeaveEntitlementList
     * @return Array $employeeLeaveEntitlementArray
     */
    protected function getEmployeeLeaveEntitlementArray($employeeLeaveEntitlementList) {
        $employeeLeaveEntitlementArray = array();
        foreach ($employeeLeaveEntitlementList as $employeeLeaveEntitlement) {
            $id = $employeeLeaveEntitlement->getEmployeeId()."_".$employeeLeaveEntitlement->getLeaveTypeId();
            $employeeLeaveEntitlementArray[$id] = $employeeLeaveEntitlement;
        }
        return $employeeLeaveEntitlementArray;
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

