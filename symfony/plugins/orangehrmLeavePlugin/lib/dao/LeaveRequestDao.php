<?php

/*
 *
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

class LeaveRequestDao extends BaseDao {

    private static $doneMarkingApprovedLeaveAsTaken = false;

    /**
     * Save leave request 
     * 
     * @param LeaveRequest $leaveRequest Leave request object
     * @param Array $leaveList Array of leave objects linked to the leave request
     * @param Array $entitlements Array of entitlements to be modified 
     * @return boolean
     */
    public function saveLeaveRequest(LeaveRequest $leaveRequest, $leaveList, $entitlements) {
        
        $conn = Doctrine_Manager::connection();
        $conn->beginTransaction();        
        
        try {
            $leaveRequest->save();
            
            $current = array();
            if (isset($entitlements['current'])) {
                $current = $entitlements['current'];
            }

            foreach ($leaveList as $leave) {
                $leave->setLeaveRequestId($leaveRequest->getId());
                $leave->setLeaveTypeId($leaveRequest->getLeaveTypeId());
                $leave->setEmpNumber($leaveRequest->getEmpNumber());

                $leave->save();

                $leaveId = $leave->getId();

                if (isset($current[$leave->getDate()])) {
                    $entitlementsForDate = $current[$leave->getDate()];
                    foreach ($entitlementsForDate as $entitlementId => $length) {
                        $le = new LeaveLeaveEntitlement();
                        $le->setLeaveId($leaveId);
                        $le->setEntitlementId($entitlementId);
                        $le->setLengthDays($length);
                        $le->save();

                        Doctrine_Query::create()
                                ->update('LeaveEntitlement e')
                                ->set('e.days_used', 'e.days_used + ?', $length)
                                ->where('e.id = ?', $entitlementId)
                                ->execute();
                    }
                }
            }

            if (isset($entitlements['change'])) {
                
                // TODO: Need to update days_used here
                // Also need to check if we need to delete all entitlements or only have changes
                
                $changes = $entitlements['change'];

                foreach ($changes as $leaveId => $change) {
                    Doctrine_Query::create()
                            ->delete('LeaveLeaveEntitlement l')
                            ->where('l.leave_id = ?', $leaveId)
                            ->execute();

                    foreach ($change as $entitlementId => $length) {
                        $le = new LeaveLeaveEntitlement();
                        $le->setLeaveId($leaveId);
                        $le->setEntitlementId($entitlementId);
                        $le->setLengthDays($length);
                        $le->save();
                    }
                }
            }

            $conn->commit();
            return $leaveRequest;
        } catch (Exception $e) {
            $conn->rollback();
            throw new DaoException($e->getMessage());
        }
    }

    public function saveLeaveRequestComment($leaveRequestId, $comment, $createdBy, $loggedInUserId, $loggedInEmpNumber) {
        try {
            $leaveRequestComment = new LeaveRequestComment();
            $leaveRequestComment->setLeaveRequestId($leaveRequestId);
            $leaveRequestComment->setCreated(date('Y-m-d H:i:s'));
            $leaveRequestComment->setCreatedByName($createdBy);
            $leaveRequestComment->setCreatedById($loggedInUserId);
            $leaveRequestComment->setCreatedByEmpNumber($loggedInEmpNumber);
            $leaveRequestComment->setComments($comment);
            
            $leaveRequestComment->save();
            
            return $leaveRequestComment;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    public function saveLeaveComment($leaveId, $comment, $createdBy, $loggedInUserId, $loggedInEmpNumber) {
        try {
            $leavetComment = new LeaveComment();
            $leavetComment->setLeaveId($leaveId);
            $leavetComment->setCreated(date('Y-m-d H:i:s'));
            $leavetComment->setCreatedByName($createdBy);
            $leavetComment->setCreatedById($loggedInUserId);
            $leavetComment->setCreatedByEmpNumber($loggedInEmpNumber);
            $leavetComment->setComments($comment);
            
            $leavetComment->save();
            return $leavetComment;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }
    
    public function getLeaveRequestComments($leaveRequestId) {
        try {
            $q = Doctrine_Query::create()
                    ->from('LeaveRequestComment c')
                    ->andWhere("c.leave_request_id = ?", $leaveRequestId);


            $comments = $q->execute();

            return $comments;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    public function getLeaveComments($leaveId) {
        try {
            $q = Doctrine_Query::create()
                    ->from('LeaveComment c')
                    ->andWhere("c.leave_id = ?", $leaveId);


            $comments = $q->execute();

            return $comments;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }    
    
    public function saveLeave(Leave $leave) {
        try {
            $leave->save();
            return true;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    public function changeLeaveStatus(Leave $leave, $entitlementChanges, $removeLinkedEntitlements = false) {
        $conn = Doctrine_Manager::connection();
        $conn->beginTransaction();  
        
        try {        
            
            if ($removeLinkedEntitlements) {
                
                $leaveId = $leave->getId();
                $stmt = $conn->prepare("UPDATE ohrm_leave_leave_entitlement le LEFT JOIN ohrm_leave_entitlement e " . 
                        "on e.id = le.entitlement_id " .
                        "SET e.days_used = IF(e.days_used<le.length_days,0,e.days_used - le.length_days) " . 
                        "WHERE le.leave_id = ?");
                
                $stmt->execute(array($leaveId));
                
                Doctrine_Query::create()
                        ->delete()
                        ->from('LeaveLeaveEntitlement l')
                        ->where('l.leave_id = ?', $leaveId)
                        ->execute();
            }
            

            $leave->save();
            
            if (isset($entitlementChanges['change'])) {
                // TODO: Need to update days_used here
                // Also need to check if we need to delete all entitlements or only have changes
                
                $changes = $entitlementChanges['change'];

                foreach ($changes as $leaveId => $change) {

                    $updateSql = '';
                    $idList = '';
                    $separator = '';
                    
                    foreach ($change as $entitlementId => $length) {                                         
                        
                        $idList .= $separator . $entitlementId;
                        $updateSql .= sprintf(' WHEN e.id = %d THEN e.days_used + %f',$entitlementId,$length);
                        $separator = ',';
                        
                        $entitlementAssignment = Doctrine_Query::create()
                                ->from('LeaveLeaveEntitlement l')
                                ->where('l.leave_id = ?', $leaveId)
                                ->andWhere('l.entitlement_id = ?', $entitlementId)
                                ->fetchOne(); 
                        
                        if ($entitlementAssignment === false) {
                            $entitlementAssignment = new LeaveLeaveEntitlement();
                            $entitlementAssignment->setLeaveId($leaveId);
                            $entitlementAssignment->setEntitlementId($entitlementId);
                            $entitlementAssignment->setLengthDays($length);                         
                        } else {
                            $entitlementAssignment->setLengthDays($entitlementAssignment->getLengthDays() + $length); 
                        }
                        $entitlementAssignment->save();
                    }
                    
                    if ($updateSql <> '') {
                        $query = "UPDATE ohrm_leave_entitlement e " . 
                                "SET e.days_used = CASE " . 
                                $updateSql .
                                " END " . 
                                sprintf(" WHERE e.id IN (%s)",$idList); 
                        $conn->execute($query);
                    }
                }                
            }

            
            $conn->commit();
            return true;
        } catch (DaoException $e) {
            $conn->rollback();
            throw new DaoException($e->getMessage(), 0, $e);
        }       
    }

    /**
     * Modify Overlap leave request
     * @param LeaveRequest $leaveRequest
     * @return boolean
     */
    public function xmodifyOverlapLeaveRequest(LeaveRequest $leaveRequest, $leaveList, $leavePeriod = null) {
        try {
            $nextLeavePeriod = false;
            $nextLeaveRequest = false;
            if ($leavePeriod == null)
                $leavePeriod = Doctrine :: getTable('LeavePeriod')->find($leaveRequest->getLeavePeriodId());

            foreach ($leaveList as $leave) {

                if ($leave->getLeaveDate() > $leavePeriod->getEndDate()) {
                    if (!($nextLeavePeriod instanceof LeavePeriod)) {

                        $leavePeriodService = new LeavePeriodService();
                        $leavePeriodService->setLeavePeriodDao(new LeavePeriodDao());

                        $nextLeavePeriod = $leavePeriodService->createNextLeavePeriod($leave->getLeaveDate());

                        $nextLeaveRequest = new LeaveRequest();
                        $idGenService = new IDGeneratorService();
                        $idGenService->setEntity($leaveRequest);
                        $nextLeaveRequest->setLeaveRequestId($idGenService->getNextID());

                        $nextLeaveRequest->setLeaveTypeId($leaveRequest->getLeaveTypeId());
                        $nextLeaveRequest->setDateApplied($leaveRequest->getDateApplied());
                        $nextLeaveRequest->setLeavePeriodId($nextLeavePeriod->getLeavePeriodId());
                        $nextLeaveRequest->setLeaveTypeName($leaveRequest->getLeaveTypeName());
                        $nextLeaveRequest->setEmpNumber($leaveRequest->getEmpNumber());
                        $nextLeaveRequest->setLeaveComments($leaveRequest->getLeaveComments());

                        $nextLeaveRequest->save();
                    }

                    $q = Doctrine_Query::create()
                            ->update('Leave l')
                            ->set('l.leave_request_id=', $nextLeaveRequest->getLeaveRequestId())
                            ->where('l.leave_id = ?', $leave->getLeaveId());


                    $q->execute();
                }
            }
            return true;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * Get Overlapping Leave
     * @param String $leaveStartDate
     * @param String $leaveEndDate
     * @param int $empId
     * @param String $startDayStartTime
     * @param String $startDayEndTime
     * @param String $endDayStartTime
     * @param String $endDayEndTime
     * @return Array of Leave objects
     * @throws DaoException
     */
    public function getOverlappingLeave($leaveStartDate, $leaveEndDate, $empId, 
            $startDayStartTime = null, $startDayEndTime = null, $allDaysPartial = false, $endDayStartTime = null, $endDayEndTime = null) {

        try {
            
            $startDayStartTime = $this->addSeconds($startDayStartTime);
            $startDayEndTime = $this->addSeconds($startDayEndTime);
            $endDayStartTime = $this->addSeconds($endDayStartTime);
            $endDayEndTime = $this->addSeconds($endDayEndTime);
            
            $q = Doctrine_Query::create()
                    ->from('Leave l');

            $q->andWhere('l.emp_number = ?' , $empId);
            $q->andWhereNotIn('l.status', array(Leave::LEAVE_STATUS_LEAVE_CANCELLED, Leave::LEAVE_STATUS_LEAVE_REJECTED, Leave::LEAVE_STATUS_LEAVE_WEEKEND, Leave::LEAVE_STATUS_LEAVE_HOLIDAY));

            if ($leaveStartDate == $leaveEndDate) {

                if (is_null($startDayStartTime)) {
                    $startDayStartTime = '00:00:00';
                }
                
                if (is_null($endDayStartTime)) {
                    $endDayStartTime = '00:00:00';
                }
                
                if (is_null($startDayEndTime)) {
                    $startDayStartTime = '23:59:00';
                }
                
                if (is_null($endDayEndTime)) {
                    $endDayEndTime = '23:59:00';
                }
                
                $startDateAndTime = $leaveStartDate . " " . $startDayStartTime;
                $endDateAndTime = $leaveEndDate . " " . $startDayEndTime;
                
                $orParams = array();
                $or [] = "(? <= CONCAT(`date`,' ',start_time) AND CONCAT(`date`,' ',end_time) <= ?)";
                $orParams[] = $startDateAndTime;
                $orParams[] = $endDateAndTime;
                $or [] = "(CONCAT(`date`,' ',start_time) <= ? AND ? <= CONCAT(`date`,' ',end_time))";
                $orParams[] = $startDateAndTime;
                $orParams[] = $endDateAndTime;
                $or [] = "(? < CONCAT(`date`,' ',start_time) AND CONCAT(`date`,' ',start_time) < ?)";
                $orParams[] = $startDateAndTime;
                $orParams[] = $endDateAndTime;
                $or [] = "(? < CONCAT(`date`,' ',end_time) AND CONCAT(`date`,' ',end_time) < ?)";
                $orParams[] = $startDateAndTime;
                $orParams[] = $endDateAndTime;
                $or [] = "(? = CONCAT(`date`,' ',end_time) AND CONCAT(`date`,' ',end_time) = ?)";
                $orParams[] = $startDateAndTime;
                $orParams[] = $endDateAndTime;
                $or [] = "((`date` = ?) AND ((start_time = '00:00:00' AND end_time='00:00:00') OR (start_time IS NULL AND end_time IS NULL)))";
                $orParams[] = $leaveEndDate;

                $orString = implode(" OR ", $or);
                $orString = "(" . $orString . ")";
                $q->andWhere($orString, $orParams);                
            } else {
                
                // first get all overlapping leave, disregarding time periods          
                $q->andWhere("( `date` <= ? AND `date` >= ?)", array($leaveEndDate, $leaveStartDate));
                                
            
                if ($allDaysPartial) {
                    // will overlap with full days or if time period overlaps
                    $q->andWhere("(start_time = '00:00:00' AND end_time='00:00:00') OR (start_time IS NULL AND end_time IS NULL) " . 
                            "OR  ((? < end_time) AND (? > start_time))",
                            array($startDayStartTime, $startDayEndTime));                 
                    
                } else { 
                    
                    // Start Day condition                    
                    if (!is_null($startDayStartTime) && !is_null($startDayEndTime)) {
                        $q->andWhere("`date` <> ? " . 
                                "OR  (? < end_time AND ? > start_time) " .
                                "OR (start_time = '00:00:00' AND end_time='00:00:00') " .
                                "OR (start_time IS NULL AND end_time IS NULL)",
                                array($leaveStartDate, $startDayStartTime, $startDayEndTime));  
                    }
                    
                    // End Day condition                    
                    if (!is_null($endDayStartTime) && !is_null($endDayEndTime)) {
                        $q->andWhere("(`date` <> ?) " . 
                                "OR  ((? < end_time) AND (? > start_time)) " .
                                "OR (start_time = '00:00:00' AND end_time='00:00:00') " .
                                "OR (start_time IS NULL AND end_time IS NULL)",
                                array($leaveEndDate, $endDayStartTime, $endDayEndTime));   
                    }


                }
            }

            $leaveListArray = $q->execute();
            return $leaveListArray;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }
    
    /**
     *
     * @param type $employeeId
     * @param type $date
     * @return type 
     */
    public function getTotalLeaveDuration($employeeId, $date) {

        $this->_markApprovedLeaveAsTaken();

        $leaveStatusNotConsider = array(Leave::LEAVE_STATUS_LEAVE_CANCELLED, Leave::LEAVE_STATUS_LEAVE_REJECTED, Leave::LEAVE_STATUS_LEAVE_WEEKEND, Leave::LEAVE_STATUS_LEAVE_HOLIDAY);

        $q = Doctrine_Query::create()
                ->select('SUM(length_hours) as total_duration')
                ->from('Leave')
                ->where("emp_number =?", $employeeId)
                ->andWhereNotIn("status ", $leaveStatusNotConsider)
                ->andWhere("date =?", $date);
        $duration = $q->fetchOne();

        return $duration->getTotalDuration();
    }

    /**
     * Count leave records in the Leave table
     * @return integer $count
     */
    public function xgetLeaveRecordCount() {
        try {

            $q = Doctrine_Query::create()
                    ->from('Leave');
            $count = $q->count();
            return $count;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     *
     * @param $empId
     * @param $leaveTypeId
     * @return int
     */
    public function xgetNumOfLeave($empId, $leaveTypeId) {
        try {


            $q = Doctrine_Query::create()
                    ->addSelect('sum(leave_length_days) as daysLength')
                    ->from('Leave l')
                    ->andWhere("l.employee_id = ?", $empId)
                    ->andWhere("l.leave_type_id = ?", $leaveTypeId);


            $record = $q->fetchOne();

            return $record['daysLength'];
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     *
     * @param $empId
     * @param $leaveTypeId
     * @return int
     */
    public function xgetNumOfAvaliableLeave($empId, $leaveTypeId, $leavePeriodId = null) {
        try {


            $q = Doctrine_Query::create()
                    ->addSelect('sum(leave_length_days) as daysLength')
                    ->from('Leave l')
                    ->andWhere("l.employee_id = ?", $empId)
                    ->andWhere("l.leave_type_id = ?", $leaveTypeId)
                    ->andWhereNotIn('l.leave_status', array(Leave::LEAVE_STATUS_LEAVE_CANCELLED, Leave::LEAVE_STATUS_LEAVE_PENDING_APPROVAL, Leave::LEAVE_STATUS_LEAVE_REJECTED));

            if ($leavePeriodId) {
                $q->leftJoin('l.LeaveRequest lr');
                $q->andWhere('lr.leave_period_id = ?', $leavePeriodId);
            }

            $record = $q->fetchOne();

            return $record['daysLength'];
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * 
     * @param LeavePeriod $leavePeriod
     * @return unknown_type
     */
    public function xgetLeavePeriodOverlapLeaves(LeavePeriod $leavePeriod) {
        try {
            $q = Doctrine_Query::create()
                    ->from('Leave l')
                    ->andWhere('l.leave_date > ?', $leavePeriod->getEndDate())
                    ->groupBy('l.leave_request_id');

            $leaveList = $q->execute();
            return $leaveList;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * Search Leave Requests.
     * 
     * Valid Search Parameter values
     *    * 'noOfRecordsPerPage' (int) - Number of records per page. If not available, 
     *                                   sfConfig::get('app_items_per_page') will be used.
     *    * 'dateRange' (DateRange)    -
     *    * 'statuses' (array)
     *    * 'employeeFilter' (array)   - Filter by given employees. If an empty array(), does not match any employees.
     *    * 'leavePeriod'
     *    * 'leaveType'
     *    * 'cmbWithTerminated'
     *    * 'subUnit'                  - Only return leave requests for employees in given subunit 
     *                                   (or subunit below that in the org structure).
     *    * 'locations' (array)        - Only return leave requests for employees in given locations.
     *    * 'employeeName' (string)    - Match employee name (Wildcard match against full name).
     * 
     * @param ParameterObject $searchParameters Search Parameters
     * @param int $page $status Page Number
     * @param bool $isCSVPDFExport If true, returns all results (ignores paging) as an array
     * @param bool $isMyLeaveList If true, ignores setting to skip terminated employees.
     * @param bool $prefetchComments If true, will prefetch leave comments for faster access.
     * 
     * @return array Returns results and record count in the following format:
     *               array('list' => results, 'meta' => array('record_count' => count)
     * 
     *               If $isCSVPDFExport is true, returns just an array of results.
     */
    public function searchLeaveRequests($searchParameters, $page = 1, $isCSVPDFExport = false, $isMyLeaveList = false,
            $prefetchLeave = false, $prefetchComments = false) {
        $this->_markApprovedLeaveAsTaken();

        $limit = !is_null($searchParameters->getParameter('noOfRecordsPerPage')) ? $searchParameters->getParameter('noOfRecordsPerPage') : sfConfig::get('app_items_per_page');
        $offset = ($page > 0) ? (($page - 1) * $limit) : 0;

        $list = array();

        $select = 'lr.*, em.firstName, em.lastName, em.middleName, em.termination_id, lt.*';
        
        if ($prefetchComments) {
            $select .= ', lc.*';
        }
        if ($prefetchLeave) {
            $select .= ', l.*';
        }
        
        $q = Doctrine_Query::create()
                ->select($select)
                ->from('LeaveRequest lr')
                ->leftJoin('lr.Leave l')
                ->leftJoin('lr.Employee em')
                ->leftJoin('lr.LeaveType lt');

        if ($prefetchComments) {
            $q->leftJoin('lr.LeaveRequestComment lc');
        }
        
        $dateRange = $searchParameters->getParameter('dateRange', new DateRange());
        $statuses = $searchParameters->getParameter('statuses');
        $employeeFilter = $searchParameters->getParameter('employeeFilter');
        $leavePeriod = $searchParameters->getParameter('leavePeriod');
        $leaveType = $searchParameters->getParameter('leaveType');
        $leaveTypeId = $searchParameters->getParameter('leaveTypeId');
        $includeTerminatedEmployees = $searchParameters->getParameter('cmbWithTerminated');
        $subUnit = $searchParameters->getParameter('subUnit');
        $locations = $searchParameters->getParameter('locations');
        $employeeName = $searchParameters->getParameter('employeeName');

        $fromDate = $dateRange->getFromDate();
        $toDate = $dateRange->getToDate();

        if (!empty($fromDate)) {
            $q->andWhere("l.date >= ?",$fromDate);
        }

        if (!empty($toDate)) {
            $q->andWhere("l.date <= ?",$toDate);
        }

        if (!empty($statuses)) {
            $q->whereIn("l.status", $statuses);
        }

        if (!empty($employeeFilter)) {
            if (is_numeric($employeeFilter) && $employeeFilter > 0) {
                $q->andWhere('lr.emp_number = ?', (int) $employeeFilter);
            } elseif ($employeeFilter instanceof Employee) {
                $q->andWhere('lr.emp_number = ?', $employeeFilter->getEmpNumber());
            } elseif (is_array($employeeFilter)) {
                $empNumbers = array();
                foreach ($employeeFilter as $employee) {
                    $empNumbers[] = ($employee instanceof Employee) ? $employee->getEmpNumber() : $employee;
                }
                
                // Here, ->whereIn() is very slow when employee number count is very high (around 5000).
                // this seems to be due to the time taken by Doctrine to replace the 5000 question marks in the query.
                // Therefore, replaced with manually built IN clause.
                // Note: $empNumbers is not based on user input and therefore is safe to use in the query.
                $q->andWhere('lr.emp_number IN (' . implode(',', $empNumbers) . ')');
            }
        } else {
            // empty array does not match any results.
            if (is_array($employeeFilter)) {
                $q->andWhere('lr.emp_number = ?', -1);
            }
        }

//        if (trim($fromDate) == "" && trim($toDate) == "" && !empty($leavePeriod)) {
//            $leavePeriodId = ($leavePeriod instanceof LeavePeriod) ? $leavePeriod->getLeavePeriodId() : $leavePeriod;
//            $q->andWhere('lr.leave_period_id = ?', (int) $leavePeriodId);
//        }

        if (!empty($leaveType)) {
            $leaveTypeId = ($leaveType instanceof LeaveType) ? $leaveType->getLeaveTypeId() : $leaveType;
            $q->andWhere('lr.leave_type_id = ?', $leaveTypeId);
        }
        if (!empty($leaveTypeId)) {
            $q->andWhere('lr.leave_type_id = ?', $leaveTypeId);
        }

        if ($isMyLeaveList) {
            $includeTerminatedEmployees = true;
        }

        // Search by employee name
        if (!empty($employeeName)) {
            $employeeName = str_replace(' (' . __('Past Employee') . ')', '', $employeeName);
            // Replace multiple spaces in string with wildcards
            $employeeName = preg_replace('!\s+!', '%', $employeeName);

            // Surround with wildcard character
            $employeeName = '%' . $employeeName . '%';

            $q->andWhere('CONCAT_WS(\' \', em.emp_firstname, em.emp_middle_name, em.emp_lastname) LIKE ?', $employeeName);
        }

        if (!empty($subUnit)) {

            // Get given subunit's descendents as well.
            $subUnitIds = array($subUnit);
            $subUnitObj = Doctrine::getTable('Subunit')->find($subUnit);

            if (!empty($subUnitObj)) {
                $descendents = $subUnitObj->getNode()->getDescendants();
                foreach ($descendents as $descendent) {
                    $subUnitIds[] = $descendent->id;
                }
            }

            $q->andWhereIn('em.work_station', $subUnitIds);
        }

        if (empty($includeTerminatedEmployees)) {
            $q->andWhere("em.termination_id IS NULL");
        }

        if (!empty($locations)) {
            $q->leftJoin('em.locations loc');
            $q->andWhereIn('loc.id', $locations);
        }

        $count = $q->count();

        $q->orderBy('l.date DESC, em.emp_lastname ASC, em.emp_firstname ASC');        

        if ($isCSVPDFExport) {
            $limit = $count;
            $offset = 0;
        }
        $q->offset($offset);
        $q->limit($limit);

        $list = $q->execute();

        return $isCSVPDFExport ? $list : array('list' => $list, 'meta' => array('record_count' => $count));
    }

    /**
     *
     * @param int $leaveRequestId
     * @return array
     */
    public function fetchLeave($leaveRequestId) {

        $q = Doctrine_Query::create()
                ->select('*')
                ->from('Leave l')
                ->where('l.leave_request_id = ?', $leaveRequestId);

        return $q->execute();
    }

    /**
     *
     * @param int $leaveId
     * @return array
     */
    public function readLeave($leaveId) {

        $q = Doctrine_Query::create()
                ->select('*')
                ->from('Leave l')
                ->where('l.id = ?', $leaveId);

        return $q->fetchOne();
    }

    public function fetchLeaveRequest($leaveRequestId) {
        $this->_markApprovedLeaveAsTaken();

        $q = Doctrine_Query::create()
                ->select('*')
                ->from('LeaveRequest lr')
                ->where('id = ?', $leaveRequestId);

        return $q->fetchOne();
    }

    public function getLeaveById($leaveId) {
        $this->_markApprovedLeaveAsTaken();

        $q = Doctrine_Query::create()
                ->select('*')
                ->from('Leave l')
                ->where('id = ?', $leaveId);

        return $q->fetchOne();
    }

    public function getScheduledLeavesSum($employeeId, $leaveTypeId, $leavePeriodId) {
        $this->_markApprovedLeaveAsTaken();

        try {

            $q = Doctrine_Query::create()
                    ->select('SUM(lea.length_days) as scheduledSum')
                    ->from('Leave lea')
                    //->leftJoin('lea.LeaveRequest lr')
                    ->where("lea.emp_number = ?", $employeeId)
                    ->andWhere("lea.leave_type_id = ?", $leaveTypeId)
                    ->andWhere("lea.status = ?", Leave::LEAVE_STATUS_LEAVE_APPROVED)
            //->andWhere("lr.leave_period_id = $leavePeriodId")
            ;

            $record = $q->fetchOne();

            return $record['scheduledSum'];
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    public function getTakenLeaveSum($employeeId, $leaveTypeId, $leavePeriodId) {

        $this->_markApprovedLeaveAsTaken();

        $q = Doctrine_Query::create()
                ->select('SUM(lea.length_days) as scheduledSum')
                ->from('Leave lea')
                ->where("lea.emp_number = ?", $employeeId)
                ->andWhere("lea.leave_type_id = ?", $leaveTypeId)
                ->andWhere("lea.status = ?", Leave::LEAVE_STATUS_LEAVE_TAKEN)

        ;

        $record = $q->fetchOne();

        return $record['scheduledSum'];
    }

    public function markApprovedLeaveAsTaken() {
        $this->_markApprovedLeaveAsTaken();
    }

    private function _markApprovedLeaveAsTaken() {
        if (self::$doneMarkingApprovedLeaveAsTaken) {
            return;
        } else {

            $date = date('Y-m-d');

            $conn = Doctrine_Manager::connection()->getDbh();
            $query = "SELECT l.id from ohrm_leave l WHERE l.`date` < ? AND l.status = ?";
            $statement = $conn->prepare($query);
            $result = $statement->execute(array($date, Leave::LEAVE_STATUS_LEAVE_APPROVED));

            if ($result) {
                $ids = $statement->fetchAll(PDO::FETCH_COLUMN, 0);

                if (count($ids) > 0) {

                    $q = Doctrine_Query::create()
                            ->update('Leave l')
                            ->set('l.status', Leave::LEAVE_STATUS_LEAVE_TAKEN)
                            ->whereIn('l.id', $ids);

                    $q->execute();

                    // TODO: Optimize
                    // No longer needed, since entitlement.used values are updated when leave is applied/assigned
                    /*$query = "SELECT le.entitlement_id, le.length_days FROM ohrm_leave_leave_entitlement le " .
                            "WHERE le.leave_id IN (" . implode(',', $ids) . ")";

                    $statement = $conn->prepare($query);
                    $result = $statement->execute();
                    if ($result) {

                        $updateQuery = "UPDATE ohrm_leave_entitlement e " .
                                "SET e.days_used = e.days_used + ? " .
                                "WHERE e.id = ?";

                        $updateStatement = $conn->prepare($updateQuery);

                        while ($row = $statement->fetch()) {
                            $updateStatement->execute(array($row['length_days'], $row['entitlement_id']));
                        }
                    }*/
                }
            }
            self::$doneMarkingApprovedLeaveAsTaken = true;
        }
    }

    public function getLeaveRequestSearchResultAsArray($searchParameters) {
        $this->_markApprovedLeaveAsTaken();

        $q = $this->getSearchBaseQuery($searchParameters);

        $q->select('lr.date_applied, lt.name, lr.comments, sum(l.length_hours) leave_length_hours_total, sum(l.length_days) as total_leave_length_days,em.firstName, em.middleName, em.lastName' .
                        ', sum(IF(l.status = 2, l.length_days, 0)) as scheduled, ' .
                        ', sum(IF(l.status = 0, l.length_days, 0)) as cancelled, ' .
                        ', sum(IF(l.status = 3, l.length_days, 0)) as taken, ' .
                        ', sum(IF(l.status = -1, l.length_days, 0)) as rejected, ' .
                        ', sum(IF(l.status = 1, l.length_days, 0)) as pending_approval, ' .
                        'concat(l.status)')
                ->groupBy('lr.id');

        return $q->execute(array(), Doctrine::HYDRATE_SCALAR);
    }

    public function getDetailedLeaveRequestSearchResultAsArray($searchParameters) {

        $this->_markApprovedLeaveAsTaken();

        $q = $this->getSearchBaseQuery($searchParameters);

        $q->select('lr.date_applied,l.date, lt.name, l.length_hours, ' .
                'l.status,l.comments, em.firstName, em.middleName, em.lastName ');

        return $q->execute(array(), Doctrine::HYDRATE_SCALAR);
    }

    protected function getSearchBaseQuery($searchParameters) {


        $q = Doctrine_Query::create()
                ->from('LeaveRequest lr')
                ->leftJoin('lr.LeaveType lt')
                ->leftJoin('lr.Leave l')
                ->leftJoin('lr.Employee em');

        $dateRange = $searchParameters->getParameter('dateRange', new DateRange());
        $statuses = $searchParameters->getParameter('statuses');
        $employeeFilter = $searchParameters->getParameter('employeeFilter');
        $leavePeriod = $searchParameters->getParameter('leavePeriod');
        $leaveType = $searchParameters->getParameter('leaveType');
        $leaveTypeId = $searchParameters->getParameter('leaveTypeId');
        $includeTerminatedEmployees = $searchParameters->getParameter('cmbWithTerminated');
        $subUnit = $searchParameters->getParameter('subUnit');
        $locations = $searchParameters->getParameter('locations');
        $employeeName = $searchParameters->getParameter('employeeName');

        $fromDate = $dateRange->getFromDate();
        $toDate = $dateRange->getToDate();

        if (!empty($fromDate)) {
            $q->andWhere("l.date >= ?",$fromDate);
        }

        if (!empty($toDate)) {
            $q->andWhere("l.date <= ?",$toDate);
        }

        if (!empty($statuses)) {
            $q->whereIn("l.status", $statuses);
        }

        if (!empty($employeeFilter)) {
            if (is_numeric($employeeFilter) && $employeeFilter > 0) {
                $q->andWhere('lr.empNumber = ?', (int) $employeeFilter);
            } elseif ($employeeFilter instanceof Employee) {
                $q->andWhere('lr.empNumber = ?', $employeeFilter->getEmpNumber());
            } elseif (is_array($employeeFilter)) {
                $empNumbers = array();
                foreach ($employeeFilter as $employee) {
                    $empNumbers[] = ($employee instanceof Employee) ? $employee->getEmpNumber() : $employee;
                }
                $q->whereIn('lr.empNumber', $empNumbers);
            }
        } else {
            // empty array does not match any results.
            if (is_array($employeeFilter)) {
                $q->andWhere('lr.empNumber = ?', -1);
            }
        }

//        if (trim($fromDate) == "" && trim($toDate) == "" && !empty($leavePeriod)) {
//            $leavePeriodId = ($leavePeriod instanceof LeavePeriod) ? $leavePeriod->getLeavePeriodId() : $leavePeriod;
//            $q->andWhere('lr.leave_period_id = ?', (int) $leavePeriodId);
//        }

        if (!empty($leaveType)) {
            $leaveTypeId = ($leaveType instanceof LeaveType) ? $leaveType->getLeaveTypeId() : $leaveType;
            $q->andWhere('lr.leave_type_id = ?', $leaveTypeId);
        }
        if (!empty($leaveTypeId)) {
            $q->andWhere('lr.leave_type_id = ?', $leaveTypeId);
        }

        // Search by employee name
        if (!empty($employeeName)) {
            $employeeName = str_replace(' (' . __('Past Employee') . ')', '', $employeeName);
            // Replace multiple spaces in string with wildcards
            $employeeName = preg_replace('!\s+!', '%', $employeeName);

            // Surround with wildcard character
            $employeeName = '%' . $employeeName . '%';

            $q->andWhere('CONCAT_WS(\' \', em.emp_firstname, em.emp_middle_name, em.emp_lastname) LIKE ?', $employeeName);
        }

        if (!empty($subUnit)) {

            // Get given subunit's descendents as well.
            $subUnitIds = array($subUnit);
            $subUnitObj = Doctrine::getTable('Subunit')->find($subUnit);

            if (!empty($subUnitObj)) {
                $descendents = $subUnitObj->getNode()->getDescendants();
                foreach ($descendents as $descendent) {
                    $subUnitIds[] = $descendent->id;
                }
            }

            $q->andWhereIn('em.work_station', $subUnitIds);
        }

        if (empty($includeTerminatedEmployees)) {
            $q->andWhere("em.termination_id IS NULL");
        }

        if (!empty($locations)) {
            $q->leftJoin('em.locations loc');
            $q->andWhereIn('loc.id', $locations);
        }

        $q->orderBy('l.date DESC, em.emp_lastname ASC, em.emp_firstname ASC');

        return $q;
    }
    
    protected function addSeconds($timeValue) {
        if (is_string($timeValue) && substr_count($timeValue, ':') == 1) {
            $timeValue .= ':00';
        }
        
        return $timeValue;
    }    

}
