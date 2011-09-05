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
class AttendanceDao {

    /**
     * save punchRecord
     * @param AttendanceRecord $attendanceRecord
     * @return AttendanceRecord
     */
    public function savePunchRecord(AttendanceRecord $attendanceRecord) {

        try {

            if ($attendanceRecord->getId() == '') {
                $idGenService = new IDGeneratorService();
                $idGenService->setEntity($attendanceRecord);
                $attendanceRecord->setId($idGenService->getNextID());
            }
            $attendanceRecord->save();

            return $attendanceRecord;
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage());
        }
    }

    /**
     * getLastPunchRecord
     * @param $employeeId, $actionableStatesList
     * @return AttendanceRecord
     */
    public function getLastPunchRecord($employeeId, $actionableStatesList) {


        try {
            $query = Doctrine_Query::create()
                    ->from("attendanceRecord")
                    ->where("employeeId = ?", $employeeId)
                    ->andWhereIn('state', $actionableStatesList);
            $lastReocord = $query->execute();
            if (is_null($lastReocord[0]->getId())) {
                return null;
            } else {

                return $lastReocord[0];
            }
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage());
        }
    }

    /**
     * checkForPunchOutOverLappingRecords
     * @param $punchInTime,$punchOutTime,$employeeId
     * @return string 1,0
     */
    public function checkForPunchOutOverLappingRecords($punchInTime, $punchOutTime, $employeeId) {

        $isValid = "1";

        try {

            $query1 = Doctrine_Query::create()
                    ->from("AttendanceRecord")
                    ->where("employeeId = ?", $employeeId)
                    ->andWhere("punchInUtcTime > ?", $punchInTime)
                    ->andWhere("punchInUtcTime < ?", $punchOutTime);
            $records1 = $query1->execute();

            if ((count($records1) > 1)) {

                $isValid = "0";
            }

            $query2 = Doctrine_Query::create()
                    ->from("attendanceRecord")
                    ->where("employeeId = ?", $employeeId)
                    ->andWhere("punchOutUtcTime > ?", $punchInTime)
                    ->andWhere("punchOutUtcTime < ?", $punchOutTime);
            $records2 = $query2->execute();

            if ((count($records2) > 0)) {


                $isValid = "0";
            }


            $query3 = Doctrine_Query::create()
                    ->from("attendanceRecord")
                    ->where("employeeId = ?", $employeeId)
                    ->andWhere("punchInUtcTime < ?", $punchInTime)
                    ->andWhere("punchOutUtcTime > ?", $punchOutTime);
            $records3 = $query3->execute();

            if ((count($records3) > 0)) {


                $isValid = "0";
            }

            $query4 = Doctrine_Query::create()
                    ->from("attendanceRecord")
                    ->where("employeeId = ?", $employeeId)
                    ->andWhere('punchInUtcTime > ?', $punchInTime)
                    ->andWhere('punchOutUtcTime < ?', $punchOutTime);
            $records4 = $query4->execute();


            if ((count($records4) > 0)) {


                $isValid = "0";
            }
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage());
        }
        return $isValid;
    }

    /**
     * check For Punch In OverLapping Records
     * @param $punchInTime, $employeeId
     * @return string 1,0
     */
    public function checkForPunchInOverLappingRecords($punchInTime, $employeeId) {


        $isValid = "1";


        try {

            $query1 = Doctrine_Query::create()
                    ->from("AttendanceRecord")
                    ->where("employeeId = ?", $employeeId)
                    ->andWhere("punchInUtcTime < ?", $punchInTime)
                    ->andWhere("punchOutUtcTime > ?", $punchInTime);
            $records1 = $query1->execute();



            if ((count($records1) > 0)) {

                $isValid = "0";
            }
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage());
        }
        return $isValid;
    }

    /**
     * get Saved Configuration
     * @param $workflow, $state, $role, $action, $resultingState
     * @return boolean
     */
    public function getSavedConfiguration($workflow, $state, $role, $action, $resultingState) {


        try {

            $query = Doctrine_Query::create()
                    ->from("WorkflowStateMachine")
                    ->where("workflow = ?", $workflow)
                    ->andWhere("state = ?", $state)
                    ->andWhere("role = ?", $role)
                    ->andWhere("action = ?", $action)
                    ->andWhere("resultingState = ?", $resultingState);
            $results = $query->execute();

            if ($results[0]->getId() == null) {

                return false;
            } else {
                return true;
            }
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage());
        }
    }

    /**
     * Get Attendance Record
     * @param $$employeeId,$date
     * @return attendance records
     */
    public function getAttendanceRecord($employeeId, $date) {

        $from = $date . " " . "00:" . "00:" . "00";
        $end = $date . " " . "23:" . "59:" . "59";

        try {

            $query = Doctrine_Query::create()
                    ->from("attendanceRecord")
                    ->where("employeeId = ?", $employeeId)
                    ->andWhere("punchInUserTime >= ?", $from)
                    ->andWhere("punchInUserTime <= ?", $end);
            $records = $query->execute();
            if (is_null($records[0]->getId())) {

                return null;
            } else {

                return $records;
            }
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage());
        }
    }

    /**
     * get delete attendance records
     * @param $attendanceRecordId
     * @return boolean
     */
    public function deleteAttendanceRecords($attendanceRecordId) {

        try {
            $q = Doctrine_Query:: create()
                    ->delete('AttendanceRecord')
                    ->where("id = ?", $attendanceRecordId);


            $result = $q->execute();

            if (count($result) > 0) {
                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * Get Attendance Record By Id
     * @param $attendanceRecordId
     * @return attendanceRecord
     */
    public function getAttendanceRecordById($attendanceRecordId) {

        try {
            $q = Doctrine_Query:: create()
                    ->from('AttendanceRecord')
                    ->where("id = ?", $attendanceRecordId);

            $result = $q->execute();

            return $result[0];
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    public function checkForPunchInOverLappingRecordsWhenEditing($punchInTime, $employeeId, $recordId, $punchOutTime) {


        $isValid = "1";


        try {

            $query1 = Doctrine_Query::create()
                    ->from("AttendanceRecord")
                    ->where("employeeId = ?", $employeeId)
                    ->andWhere("punchInUtcTime < ?", $punchInTime)
                    ->andWhere("punchOutUtcTime > ?", $punchInTime);
            $records1 = $query1->execute();



            if ((count($records1) == 1)) {
                if (($records1[0]->getId() == $recordId)) {
                    $isValid = "1";
                } else {

                    $isValid = "0";
                }
            } elseif ((count($records1) > 0)) {

                $isValid = "0";
            }
            
            
              $query2 = Doctrine_Query::create()
                    ->from("AttendanceRecord")
                    ->where("employeeId = ?", $employeeId)
                    ->andWhere("punchInUtcTime > ?", $punchInTime)
                    ->andWhere("punchOutUtcTime < ?", $punchOutTime);
            $records2 = $query2->execute();



            if ((count($records2) == 1)) {
                if (($records2[0]->getId() == $recordId)) {
                    $isValid = "1";
                } else {

                    $isValid = "0";
                }
            } elseif ((count($records2) > 0)) {

                $isValid = "0";
            }
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage());
        }
        return  $isValid;
    }

    /**
     * checkForPunchOutOverLappingRecordsWhenEditing
     * @param $punchInTime,$punchOutTime,$employeeId
     * @return string 1,0
     */
    public function checkForPunchOutOverLappingRecordsWhenEditing($punchInTime, $punchOutTime, $employeeId, $recordId) {

        $isValid = "1";


        try {

            $query1 = Doctrine_Query::create()
                    ->from("AttendanceRecord")
                    ->where("employeeId = ?", $employeeId)
                    ->andWhere("punchInUtcTime > ?", $punchInTime)
                    ->andWhere("punchInUtcTime < ?", $punchOutTime);
            $records1 = $query1->execute();

            if ((count($records1) == 1)) {
                if (($records1[0]->getId() == $recordId)) {
                    $isValid = "1";
                } else {

                    $isValid = "0";
                }
            } elseif ((count($records1) > 0)) {

                $isValid = "0";
            }
//            $query2 = Doctrine_Query::create()
//                    ->from("attendanceRecord")
//                    ->where("employeeId = ?", $employeeId)
//                    ->andWhere("punchOutUtcTime > ?", $punchInTime)
//                    ->andWhere("punchOutUtcTime < ?", $punchOutTime);
//            $records2 = $query2->execute();
//
//            if ((count($records2) == 1)) {
//                if (($records2[0]->getId() == $recordId)) {
//                    $isValid = "11";
//                } else {
//
//                    $isValid = "0";
//                }
//            } elseif ((count($records2) > 0)) {
//
//                $isValid = "0";
//            }


            $query3 = Doctrine_Query::create()
                    ->from("attendanceRecord")
                    ->where("employeeId = ?", $employeeId)
                    ->andWhere("punchInUtcTime < ?", $punchInTime)
                    ->andWhere("punchOutUtcTime > ?", $punchOutTime);
            $records3 = $query3->execute();

            if ((count($records3) == 1)) {
                if (($records3[0]->getId() == $recordId)) {
                    $isValid = "1";
                } else {

                    $isValid = "0";
                }
            } elseif ((count($records3) > 0)) {

                $isValid = "0";
            }

            $query4 = Doctrine_Query::create()
                    ->from("attendanceRecord")
                    ->where("employeeId = ?", $employeeId)
                    ->andWhere('punchInUtcTime > ?', $punchInTime)
                    ->andWhere('punchOutUtcTime < ?', $punchOutTime);
            $records4 = $query4->execute();


            if ((count($records4) == 1)) {
                if (($records4[0]->getId() == $recordId)) {
                    $isValid = "1";
                } else {

                    $isValid = "0";
                }
            } elseif ((count($records4) > 0)) {

                $isValid = "0";
            }
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage());
        }
        return $isValid;
    }

}

