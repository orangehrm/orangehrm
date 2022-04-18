<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PerformanceReviewDao
 *
 * @author nadeera
 */
class PerformanceReviewDao extends BaseDao {

    /**
     *
     * @param sfDoctrineRecord $review
     * @return PerformanceReview      
     */
    public function saveReview(sfDoctrineRecord $review) {
        try {
            $review->save();
            $review->refresh();
            return $review;
            //@codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }//@codeCoverageIgnoreEnd
    }

    /**
     *
     * @param array $parameters
     * @return Doctrine_Collection
     * @throws DaoException 
     */
    public function searchReview($parameters, $orderby = null) {
        if ($orderby['orderBy'] == null) {
            $sortFeild = 'e.emp_firstname,r.group.piority';
        }

        if ($orderby['orderBy'] == 'employeeId') {
            $sortFeild = "e.emp_firstname";
        }

        if ($orderby['orderBy'] == 'due_date') {
            $sortFeild = "dueDate";
        }

        $sortBy = strcasecmp($orderby['sortOrder'], 'DESC') === 0 ? 'DESC' : 'ASC';

        $offset = ($parameters['page'] > 0) ? (($parameters['page'] - 1) * $parameters['limit']) : 0;

        try {

            $query = Doctrine_Query:: create()->from('PerformanceReview p');
            $query->leftJoin("p.Employee e");
            $query->leftJoin("p.reviewers r");
            $query->leftJoin("r.rating rating");

            if (isset($parameters['reviewerId']) && $parameters['reviewerId'] > 0) {
                $query->andWhere('r.employeeNumber = ?', $parameters['reviewerId']);
                $query->andWhere('r.id = rating.reviewer_id');
            }

            if (!empty($parameters)) {
                if (isset($parameters['id']) && $parameters['id'] > 0) {
                    $query->andWhere('id = ?', $parameters['id']);
                    return $query->fetchOne();
                } else {
                    foreach ($parameters as $key => $parameter) {
                        if (is_array($parameter) || strlen(trim($parameter)) > 0) {
                            switch ($key) {
                                case 'employeeName':
                                    $query->andWhere("CONCAT(e.emp_firstname,IF(LENGTH(e.emp_middle_name)>0,' ',''),e.emp_middle_name,' ',e.emp_lastname) LIKE ?", "%" . $parameter . "%");
                                    break;
                                case 'jobTitleCode':
                                    $query->andWhere('jobTitleCode = ?', $parameter);
                                    break;
                                case 'from':
                                    $query->andWhere('dueDate >= ?', $parameter);
                                    break;
                                case 'to':
                                    $query->andWhere('dueDate <= ?', $parameter);
                                    break;
                                case 'employeeNumber':
                                    $query->andWhere('e.empNumber = ?', $parameter);
                                    break;
                                case 'status':
                                    $query->andWhereIn('p.status_id', $parameter);
                                    break;
                                case 'employeeNotIn':
                                    $query->andWhereNotIn('e.empNumber', $parameter);
                                    break;
                                default:
                                    break;
                            }
                        }
                    }
                }
            }
            $query->orderBy($sortFeild . ' ' . $sortBy);

            $query->offset($offset);

            if ($parameters['limit'] != null) {
                $query->limit($parameters['limit']);
            }
            return $query->execute();
            //@codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }//@codeCoverageIgnoreEnd
    }

    /**
     *
     * @param type $reviwerEmployeeId
     * @return type 
     */
    public function getReviwerEmployeeList($reviwerEmployeeId) {
        try {

            $query = Doctrine_Query:: create()
                    ->from('PerformanceReview p');

            $query->leftJoin("p.Employee e");
            $query->leftJoin("p.reviewers r");


            $query->andWhere('r.employeeNumber = ?', $reviwerEmployeeId);
            $query->andWhere('e.empNumber != ?', $reviwerEmployeeId);


            $query->orderBy('e.emp_firstname');
            return $query->execute();
            //@codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }//@codeCoverageIgnoreEnd
    }

    /**
     *
     * @param integer $ids
     * @return boolean
     * @throws DaoException 
     */
    public function deleteReview($ids) {
        try {
            if (sizeof($ids)) {
                $q = Doctrine_Query::create()
                        ->delete('PerformanceReview')
                        ->whereIn('id', $ids);
                $q->execute();
            }
            return true;
            //@codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }//@codeCoverageIgnoreEnd
    }

    /**
     *
     * @param integer $id
     * @return boolean
     * @throws DaoException 
     */
    public function deleteReviewersByReviewId($id) {
        try {
            $q = Doctrine_Query::create()
                    ->delete('Reviewer')
                    ->whereIn('review_id', $id);
            $q->execute();
            return true;
            //@codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }//@codeCoverageIgnoreEnd
    }

    /**
     *
     * @param integer $id
     * @return boolean
     * @throws DaoException 
     */
    public function searchRating($parameters = null) {


        try {
            $q = Doctrine_Query::create()->from('ReviewerRating');
            if (isset($parameters['id']) && sizeof($parameters) == 1) {
                $q->whereIn('id', $parameters['id']);
                return $q->fetchOne();
            } else {
                if (is_array($parameters)) {
                    foreach ($parameters as $key => $parameter) {
                        if (strlen($parameter) > 0) {
                            switch ($key) {
                                case 'reviewId':
                                    $q->andWhere('review_id =?', $parameter);
                                    break;
                                case 'id':
                                    $q->andWhere('id =?', $parameter);
                                    break;
                                default:
                                    break;
                            }
                        }
                    }
                }
                //@codeCoverageIgnoreStart
                return $q->execute();
            }
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }//@codeCoverageIgnoreEnd
    }

    public function getReviewById($id) {
        try {
            $result = Doctrine :: getTable('PerformanceReview')->find($id);
            return $result;
            //@codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }//@codeCoverageIgnoreEnd
    }

    public function getReviewsByReviewerId($reviwerId) {
        try {
            $query = Doctrine_Query:: create()->from('PerformanceReview p');
            $query->leftJoin("p.reviewers r");
            $query->andWhere('r.employeeNumber = ?', $reviwerId);
            return $query->execute();
            //@codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }//@codeCoverageIgnoreEnd
    }

}
