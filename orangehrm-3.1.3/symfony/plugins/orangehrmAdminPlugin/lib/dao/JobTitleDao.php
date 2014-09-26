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
class JobTitleDao extends BaseDao {

    public function getJobTitleList($sortField='jobTitleName', $sortOrder='ASC', $activeOnly = true, $limit = null, $offset = null) {

        $sortField = ($sortField == "") ? 'jobTitleName' : $sortField;
        $sortOrder = strcasecmp($sortOrder, 'DESC') === 0 ? 'DESC' : 'ASC';

        try {
            $q = Doctrine_Query :: create()
                            ->from('JobTitle');
            if ($activeOnly == true) {
                $q->addWhere('isDeleted = ?', JobTitle::ACTIVE);
            }
            $q->orderBy($sortField . ' ' . $sortOrder);
            if (!empty($limit)) {
                $q->offset($offset)
                  ->limit($limit);
            }
            return $q->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    public function deleteJobTitle($toBeDeletedJobTitleIds) {

        try {
            $q = Doctrine_Query :: create()
                            ->update('JobTitle')
                            ->set('isDeleted', '?', JobTitle::DELETED)
                            ->whereIn('id', $toBeDeletedJobTitleIds);
            return $q->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    public function getJobTitleById($jobTitleId) {

        try {
            return Doctrine::getTable('JobTitle')->find($jobTitleId);
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    public function getJobSpecAttachmentById($attachId) {

        try {
            return Doctrine::getTable('JobSpecificationAttachment')->find($attachId);
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

}

