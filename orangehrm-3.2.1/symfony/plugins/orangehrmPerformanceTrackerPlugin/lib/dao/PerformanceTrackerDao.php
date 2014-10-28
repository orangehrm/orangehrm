<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PerformanceTrackDao
 *
 * @author indiran
 */
class PerformanceTrackerDao extends BaseDao {

    /**
     * Save PerformanceTrack
     * @param PerformanceTrack $performanceTrack
     * @returns boolean
     * @throws DaoException
     */
    public function savePerformanceTrack(PerformanceTrack $performanceTrack) {
        try {
            return $performanceTrack->save();
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd        
    }

    /**
     * Save PerformanceTrackerLog
     * @param PerformanceTrackerLog $performanceTrackerLog
     * @returns boolean
     * @throws DaoException
     */
    public function savePerformanceTrackerLog(PerformanceTrackerLog $performanceTrackerLog) {
        try {
            return $performanceTrackerLog->save();
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd        
    }

    /**
     * Retrieve PerformanceTrack by performanceTrackId, must make this retrieve domain object
     * @param int $performanceTrackId
     * @returns boolean
     * @throws DaoException
     */
    public function getPerformanceTrack($performanceTrackId) {
        try {
            return Doctrine :: getTable('PerformanceTrack')->find($performanceTrackId);
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * Retrieve PerformanceTrackerLog by performanceTrackLogId, must make this retrieve domain object
     * @param int $performanceTrackLogId
     * @returns boolean
     * @throws DaoException
     */
    public function getPerformanceTrackerLog($performanceTrackLogId) {
        try {
            return Doctrine :: getTable('PerformanceTrackerLog')->find($performanceTrackLogId);
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * 
     * @param type $trackId
     * @return array
     * @throws DaoException
     */
    public function getPerformanceReviewersIdListByTrackId($trackId) {
        try {
            $reviewerIds = array();
            if ($trackId != null) {
                $q = Doctrine_Query :: create()
                        ->select('ptr.reviewer_id')
                        ->from('PerformanceTrackerReviewer ptr')
                        ->where('ptr.performance_track_id =?', $trackId);
                $reviewers = $q->execute();

                foreach ($reviewers as $reviewer) {
                    array_push($reviewerIds, $reviewer->getReviewerId());
                }
            }

            return $reviewerIds;

            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     *
     * @return type 
     */
    public function getPerformanceTrackerLogList() {
        try {
            $q = Doctrine_Query :: create()
                    ->from('PerformanceTrackerLog ptl')
                    ->where('ptl.status=?', PerformanceTrackerLog::STATUS_ACTIVE)
                    ->orderBy('added_date DESC');
            return $q->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     *
     * @return type 
     */
    public function getPerformanceTrackList($parameters) {
        try {

            $offset = ($parameters['page'] > 0) ? (($parameters['page'] - 1) * $parameters['limit']) : 0;

            $q = Doctrine_Query :: create()
                    ->from('PerformanceTrack pt')
                    ->leftJoin('pt.Employee e')
                    ->where('pt.status=?', PerformanceTrack::STATUS_ACTIVE)
                    ->orderBy('e.firstName ASC');
            $q->offset($offset);

            if ($parameters['limit'] != null) {
                $q->limit($parameters['limit']);
            }
            return $q->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * Retrieve PerformanceTrack by reviewerId, must make this retrieve domain object
     * @param int $reviewerId
     * @returns boolean
     * @throws DaoException
     */
    public function getPerformanceTrackListByReviewer($parameters) {
        try {

            $offset = ($parameters['page'] > 0) ? (($parameters['page'] - 1) * $parameters['limit']) : 0;

            $q = Doctrine_Query :: create()
                    ->from('PerformanceTrack p')
                    ->where('p.PerformanceTrackerReviewer.reviewer_id =?', $parameters['reviewerId'])
                    ->andWhere('p.status=?', PerformanceTrack::STATUS_ACTIVE)
                    ->orderBy('added_date ASC');

            $q->offset($offset);

            if ($parameters['limit'] != null) {
                $q->limit($parameters['limit']);
            }

            return $q->execute();
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * Retrieve PerformanceTrackerLog by reviewerId, must make this retrieve domain object
     * @param int $reviewerId
     * @returns boolean
     * @throws DaoException
     */
    public function getPerformanceTrackerLogListByReviewer($reviewerId) {
        try {
            $q = Doctrine_Query :: create()
                    ->from('PerformanceTrackerLog ptl')
                    ->where('ptl.reviewer_id =?', $reviwerId)
                    ->andWhere('ptl.status=?', PerformanceTrackerLog::STATUS_ACTIVE)
                    ->orderBy('added_date ASC');
            return $q->execute();
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }

    public function deleteReviweres($trackId, $reviwerArray) {
        try {
            $q = Doctrine_Query::create()
                    ->delete('PerformanceTrackerReviewer ptr')
                    ->where('ptr.performance_track_id =?', $trackId)
                    ->andWhereIn('ptr.reviewer_id', $reviwerArray);
            return $q->execute();
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }

    public function getPerformanceTrackerLogListByTrack($trackId) {
        try {
            $q = Doctrine_Query :: create()
                    ->from('PerformanceTrackerLog ptl')
                    ->where('ptl.performance_track_id =?', $trackId)
                    ->andWhere('ptl.status=?', PerformanceTrackerLog::STATUS_ACTIVE)
                    ->orderBy('added_date DESC');
            return $q->execute();
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }

    public function getPerformanceTrackerByEmployee($parameters) {
        try {
            $offset = ($parameters['page'] > 0) ? (($parameters['page'] - 1) * $parameters['limit']) : 0;
            $q = Doctrine_Query :: create()
                    ->from('PerformanceTrack pt')
                    ->where('pt.emp_number =?', $parameters['employeeId'])
                    ->andWhere('pt.status =?', PluginPerformanceTrack::STATUS_ACTIVE)
                    ->orderBy('added_date DESC');
            $q->offset($offset);

            if ($parameters['limit'] != null) {
                $q->limit($parameters['limit']);
            }
            return $q->execute();
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }

    public function getPerformanceTrackerLogByEmployeeNumber($empNumber) {
        try {
            $q = Doctrine_Query :: create()
                    ->from('PerformanceTrackerLog ptl')
                    ->where('ptl.PerformanceTrack.emp_number =?', $empNumber)
                    ->andWhere('ptl.status=?', PerformanceTrackerLog::STATUS_ACTIVE)
                    ->orderBy('added_date DESC');
            return $q->execute();
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }

    public function isTrackerExistForEmployee($empNumber) {
        try {
            //TO DO use count or EXIST to chek
            $q = Doctrine_Query :: create()
                    ->from('PerformanceTrack pt')
                    //->where('pt.emp_number =?', $empNumber)
                    ->where('pt.emp_number =?', $empNumber)
                    ->orderBy('added_date DESC');
            return $q->execute();
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }

}

?>
