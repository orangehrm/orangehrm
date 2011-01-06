<?php
/**
 * Form class for leave list
 */
class LeaveListForm extends sfForm {

    const MODE_SUPERVISOR_DETAILED_LIST	= 'detailed_supervisor_list';
    const MODE_HR_ADMIN_DETAILED_LIST	= 'detailed_hr_admin_list';
    const MODE_MY_LEAVE_LIST			= 'my_leave_list';
    const MODE_MY_LEAVE_DETAILED_LIST	= 'my_leave_detailed_list';
    const MODE_TAKEN_LEAVE_LIST			= 'my_taken_leave_list';
    const MODE_DEFAULT_LIST				= 'default_list';

    private $mode;
    private $leavePeriod = null;
    private $employee = null;
    private $actionButtons = array();
    private $list = null;
    private $requestData = null;
    private $showBackButton = false;
    private $tips = array(
        'markedForApproval' => 'Approve',
        'markedForRejection' => 'Reject',
        'markedForCancellation' => 'Cancel',
    );
    private $loggedUserId;
    private $leaveRequest;
    private $empJson;
    private $leavePeriodService;

    public function __construct($mode = null, $leavePeriod = null, $employee = null, $requestData = null, $loggedUserId = null, $leaveRequest = null) {

        parent::__construct(array(), array());

        if (empty($mode)) {
            $mode = self::MODE_DEFAULT_LIST;
        }

        $this->mode = $mode;
        $this->leavePeriod = $leavePeriod;
        $this->employee = $employee;
        $this->actionButtons = array();
        $this->requestData = $requestData;
        $this->loggedUserId = $loggedUserId;
        $this->leaveRequest = $leaveRequest;

        $this->_init();

    }

    public function configure() {

        $this->setWidgets(array(
            'calFromDate' => new ohrmWidgetDatePicker(array(), array('id' => 'calFromDate')),
            'calToDate' => new ohrmWidgetDatePicker(array(), array('id' => 'calToDate')),
        ));

    }

    /**
     * Formats the title of the leave list according to the mode
     *
     * @return string Title of the leave list
     */
    public function getTitle() {

        if ($this->mode === self::MODE_SUPERVISOR_DETAILED_LIST) {
            $title = 'Approve Leave Request for %s';
            $replacements = array($this->employee->getFullName());
        } elseif ($this->mode === self::MODE_HR_ADMIN_DETAILED_LIST) {
            $str = "";
            if($this->leaveRequest instanceof LeaveRequest) {
                $str .= "(" . $this->getLeaveDateRange($this->leaveRequest->getLeaveRequestId()) . ") of ";
            }
            $str .= $this->employee->getFullName();
            $title = 'Leave Request %s';
            $replacements = array($str);
        } elseif ($this->mode === self::MODE_TAKEN_LEAVE_LIST) {
            $title = 'Leave Taken by %s in %s';
            $replacements = array($this->employee->getFullName(), $this->leavePeriod->getDescription());
        } elseif ($this->mode === self::MODE_MY_LEAVE_LIST) {
            $title = 'My Leave List';
            $replacements = null;
        } elseif($this->mode === self::MODE_MY_LEAVE_DETAILED_LIST) {
            $title = 'My Leave Details';
            $replacements = null;
        } else {
            $title = 'Leave List';
            $replacements = null;
        }

        return vsprintf(__($title), $replacements);

    }

    /**
     * Returns the set of action buttons associated with each mode of the leave list
     *
     * @return array Array of action buttons as instances of ohrmWidegetButton class
     */
    public function getSearchActionButtons() {
        return array(
            'btnSearch' => new ohrmWidgetButton('btnSearch', 'Search', array('class' => 'searchbutton')),
            'btnReset' => new ohrmWidgetButton('btnReset', 'Reset', array('class' => 'clearbutton')),
        );
    }

    public function getLeavePeriodService() {
        if(is_null($this->leavePeriodService)) {
            $leavePeriodService = new LeavePeriodService();
            $leavePeriodService->setLeavePeriodDao(new LeavePeriodDao());
            $this->leavePeriodService = $leavePeriodService;
        }
        return $this->leavePeriodService;
    }

    /**
     * Returns the set of status filters used for searching leave requests
     *
     * @return array Array of checkboxes as instances of ohrmWidgetCheckbox class
     */
    public function getStatusFilters() {

        $filterControls = array();

        $leave = new Leave();
        $statusList = $leave->getStatusTextList();

        $postStatuses = $this->requestData->getParameter('chkSearchFilter');
        $postStatuses = (trim($this->requestData->getParameter('status') != ""))?array($this->requestData->getParameter('status')):$postStatuses;
        $postStatuses = is_array($postStatuses) ? $postStatuses : array();

        foreach ($statusList as $status => $text) {
            $attributes = (in_array($status, $postStatuses) ? array('checked' => 'checked') : array());
            $attributes['class'] = 'checkbox';
            $filterControls[] = new ohrmWidgetCheckbox('chkSearchFilter', $status, array('content' => __($text), 'class' => 'subLabelNew'), $attributes);
        }

        if (count($statusList) > 1) {

            $isAllChecked = $this->requestData->getParameter('allCheck');
            $isAllChecked = (!empty($isAllChecked) && $isAllChecked[0] == 'all');

            $attributes = ($isAllChecked) ? array('checked' => 'checked') : array();
            $attributes['class'] = 'checkbox';
            $attributes['id'] = 'checkAll';
            array_unshift($filterControls, new ohrmWidgetCheckbox('allCheck', 'all', array('content' => __("All"), 'class' => 'subLabelNew'), $attributes));

        }

        return $filterControls;

    }

    public function setList($list) {
        $this->list = $list;
    }

    public function getList() {
        return $this->list;
    }

    public function getPageLinks($baseUrl, $currentPage = 1, $recordCount = 0) {

        $limit = 50;

        if ($recordCount < $limit) {
            return '';
        }

        $noOfPages = ceil($recordCount / $limit);
        $baseUrl = url_for($baseUrl) . '/page';
        $prevPage = $currentPage - 1;
        $nextPage = $currentPage + 1;

        $html = ($prevPage <= 0) ? 'Prev' : "<a href=\"{$baseUrl}/{$prevPage}\">Prev</a>";

        for ($i = 1; $i <= $noOfPages; $i++) {
            $html .= ($i == $currentPage) ? "\n{$i}" : "\n<a href=\"{$baseUrl}/{$i}\">{$i}</a>";
        }

        $html .= ($nextPage > $noOfPages) ? 'Next' : "<a href=\"{$baseUrl}/{$nextPage}\">Next</a>";

        return $html;

    }

    public function isPaginated() {
        return ($this->mode == self::MODE_DEFAULT_LIST || $this->mode == self::MODE_MY_LEAVE_LIST);
    }

    public function isDetailed() {
        return !($this->mode == self::MODE_DEFAULT_LIST || $this->mode == self::MODE_MY_LEAVE_LIST);
    }

    public function setShowBackButton($value) {
        $this->showBackButton = $value;
    }

    private function _init() {

        $startDate = $this->requestData->getParameter('calFromDate');
        $endDate = $this->requestData->getParameter('calToDate');
        
        if(empty($startDate) && empty($endDate)) {

            if($this->leavePeriod instanceof LeavePeriod) {
                $startDate = $this->leavePeriod->getStartDate();
                $endDate = $this->leavePeriod->getEndDate();
            }
        }
        
        $this->getWidget('calFromDate')->setDefault($startDate);
        $this->getWidget('calToDate')->setDefault($endDate);

        if ($this->mode != self::MODE_MY_LEAVE_LIST && $this->mode != self::MODE_MY_LEAVE_DETAILED_LIST) {

            $companyService = new CompanyService();

            $subUnitList = array(0 => "All");

            $tree = $companyService->getSubDivisionTree();

            foreach($tree as $node) {

                // Add nodes, indenting correctly. Skip root node
                if ($node->getId() != 1) {
                    $indent = str_repeat('&nbsp;&nbsp;', $node->depth - 1);
                    $subUnitList[$node->getId()] = $indent . $node->getTitle();
                }
            }

            $employeeId = trim($this->requestData->getParameter('txtEmpId'));
            if($employeeId == "" && $this->employee instanceof Employee) {
                $employeeId = $this->employee->getEmpNumber();
            }

            $this->setWidget('cmbSubunit', new sfWidgetFormSelect(array('choices' => $subUnitList, 'default' => $this->requestData->getParameter('cmbSubunit')), array('id' => 'cmbSubunit')));
            $this->setWidget('txtEmpID', new sfWidgetFormInputHidden(array('default' => $employeeId)));

            $employeeName = trim($this->requestData->getParameter('txtEmployee'));
            if($employeeName == "" && $this->employee instanceof Employee) {
                $employeeName = $this->employee->getFirstName() . " " . $this->employee->getLastName();
            }
            $this->setWidget('txtEmployee', new sfWidgetFormInput(array('default' => $employeeName)));
        }

    }

    public function getEmployeeListAsJson() {
        return $this->empJson;
    }

    public function setEmployeeListAsJson($str) {
        $this->empJson = $str;
    }
    public function renderItemActions($item) {

        $actionClasses = array();
        $html = '';

        if ($item instanceof LeaveRequest) {

            if ($item->canApprove() && $this->mode != self::MODE_MY_LEAVE_LIST &&
                $item->getEmployeeId() != $this->loggedUserId) {
                $actionClasses[Leave::LEAVE_STATUS_LEAVE_APPROVED] = 'markedForApproval';
                $actionClasses[Leave::LEAVE_STATUS_LEAVE_REJECTED] = 'markedForRejection';

            }

            if ($item->canCancel(Auth::instance()->hasRole(Auth::ADMIN_ROLE))) {
                $actionClasses[Leave::LEAVE_STATUS_LEAVE_CANCELLED] = 'markedForCancellation';
            }

        } elseif ($item instanceof Leave) {

            if ($item->canApprove() && $this->mode != self::MODE_MY_LEAVE_DETAILED_LIST &&
                $item->getEmployeeId() != $this->loggedUserId) {
                $actionClasses[Leave::LEAVE_STATUS_LEAVE_APPROVED] = 'markedForApproval';
                $actionClasses[Leave::LEAVE_STATUS_LEAVE_REJECTED] = 'markedForRejection';
            }

            if ($item->canCancel(Auth::instance()->hasRole(Auth::ADMIN_ROLE))) {
                $actionClasses[Leave::LEAVE_STATUS_LEAVE_CANCELLED] = 'markedForCancellation';
            }

        } else {

            throw new Exception('Invalid type of item passed');

        }

        $returnArray = array(""=>__("Select Action"));

        foreach ($actionClasses as $key => $class) {
            $returnArray[$class] =  __($this->tips[$class]);
        }

        return array('select_options' => $returnArray);

    }

    public function getActionButtons() {

        if ((!empty($this->list)) && ($this->mode !== self::MODE_TAKEN_LEAVE_LIST)) {
            $this->actionButtons['btnSave'] = new ohrmWidgetButton('btnSave', "Save", array('class' => 'savebutton'));
        }

        /*if ($this->mode != self::MODE_DEFAULT_LIST || $this->showBackButton) {
            $this->actionButtons['btnBack'] = new ohrmWidgetButton('btnBack', "Back", array('class' => 'backbutton'));
        }*/

        // showing back button only on details
        if($this->isDetailed()) {
            $this->actionButtons['btnBack'] = new ohrmWidgetButton('btnBack', "Back", array('class' => 'backbutton'));
        }
        
        return $this->actionButtons;

    }

    public function getLeaveDateRange($leaveRequestId) {

        $leaveRequestService = new LeaveRequestService();
        $leaveRequestService->setLeaveRequestDao(new LeaveRequestDao());

        $leaveList = $leaveRequestService->searchLeave($leaveRequestId);
        $count = count($leaveList);

        if ($count == 1) {

            return $leaveList[0]->getLeaveDate();

        } else {

            $range = $leaveList[0]->getLeaveDate();
            $range .= ' to ';
            $range .= $leaveList[$count-1]->getLeaveDate();

            return $range;

        }

    }

    private function _isOverQuotaAllowed($leaveTypeId) {

        $leaveTypeService = new LeaveTypeService();
        $leaveTypeService->setLeaveTypeDao(new LeaveTypeDao());
        $leaveType = $leaveTypeService->readLeaveType($leaveTypeId);

        if (!$leaveType instanceof LeaveType) {
            return false;
        }

        $leaveRequestService = new LeaveRequestService();
        $leaveRequestService->setLeaveRequestDao(new LeaveRequestDao());

        //if ($leaveRequestService->isApplyToMoreThanCurrent($leaveType)) {
            return true;
        //} else {
        //    return false;
        //}

    }

    public function getQuotaClass($leaveTypeId) {

        if ($this->_isOverQuotaAllowed($leaveTypeId)) {
            return '';
        } else {
            return ' quotaSelect';
        }

    }

    public function getQuotaArray($leaveRequestList) {

        $quotaArray = array();

        if ($leaveRequestList[0] instanceof LeaveRequest) {

            foreach ($leaveRequestList as $request) {

                $employeeId = $request->getEmpNumber();
                $leaveTypeId = $request->getLeaveTypeId();
                $leavePeriodId = $request->getLeavePeriodId();

                if (!$this->_isOverQuotaAllowed($leaveTypeId)) {

                    $key = $employeeId.'-';
                    $key .= $leaveTypeId.'-';
                    $key .= $leavePeriodId;

                    $leaveEntitlementService = new LeaveEntitlementService();
                    $leaveEntitlementService->setLeaveEntitlementDao(
                                              new LeaveEntitlementDao());

                    $leaveBalance = $leaveEntitlementService->getLeaveBalance(
                                                              $employeeId,
                                                              $leaveTypeId,
                                                              $leavePeriodId);

                    $quotaArray[$key] = $leaveBalance;

                }

            }

        } elseif ($leaveRequestList[0] instanceof Leave) {

            $employeeId = $leaveRequestList[0]->getEmployeeId();
            $leaveTypeId = $leaveRequestList[0]->getLeaveTypeId();
            $leavePeriodId = $leaveRequestList[0]->getLeaveRequest()
                                                 ->getLeavePeriodId();

            $key = $employeeId.'-';
            $key .= $leaveTypeId.'-';
            $key .= $leavePeriodId;

            $leaveEntitlementService = new LeaveEntitlementService();
            $leaveEntitlementService->setLeaveEntitlementDao(
                                      new LeaveEntitlementDao());

            $leaveBalance = $leaveEntitlementService->getLeaveBalance(
                                                      $employeeId, $leaveTypeId,
                                                      $leavePeriodId);

            $quotaArray[$key] = $leaveBalance;

        }

        return $quotaArray;

    }


}
