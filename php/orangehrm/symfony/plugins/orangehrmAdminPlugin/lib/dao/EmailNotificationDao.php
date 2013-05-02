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
class EmailNotificationDao extends BaseDao {

	public function getEmailNotificationList() {
		try {
			$q = Doctrine_Query :: create()
				->from('EmailNotification');
			return $q->execute();
		} catch (Exception $e) {
			throw new DaoException($e->getMessage());
		}
	}
        
	public function getEmailNotification($id) {
		try {
			$q = Doctrine_Query :: create()
				->from('EmailNotification')
                                ->where('id = ?', $id);
			return $q->fetchOne();
		} catch (Exception $e) {
			throw new DaoException($e->getMessage());
		}
	}        

	public function updateEmailNotification($toBeEnabledIds) {
		try {
			$this->disableEmailNotification($toBeEnabledIds);				
			if (!empty($toBeEnabledIds)) {
				$this->enableEmailNotification($toBeEnabledIds);
			}
			return true;
		} catch (Exception $e) {
			throw new DaoException($e->getMessage());
		}
	}

	private function disableEmailNotification($toBeEnabledIds) {
		try {
			$q = Doctrine_Query :: create()->update('EmailNotification')
				->set('isEnable', '?', EmailNotification::DISABLED);
			if (!empty($toBeEnabledIds)) {
				$q->whereNotIn('id', $toBeEnabledIds);
			}
			return $q->execute();
		} catch (Exception $e) {
			throw new DaoException($e->getMessage());
		}
	}

	private function enableEmailNotification($toBeEnabledIds) {
		try {
			$q = Doctrine_Query :: create()->update('EmailNotification')
				->set('isEnable', '?', EmailNotification::ENABLED)
				->whereIn('id', $toBeEnabledIds);
			return $q->execute();
		} catch (Exception $e) {
			throw new DaoException($e->getMessage());
		}
	}

	public function getEnabledEmailNotificationIdList() {
		try {
			$q = Doctrine_Query :: create()->select('id')
				->from('EmailNotification')
				->where('isEnable = ?', EmailNotification::ENABLED);
			return $q->execute();
		} catch (Exception $e) {
			throw new DaoException($e->getMessage());
		}
	}

	public function getSubscribersByNotificationId($emailNotificationId) {
		try {
			$q = Doctrine_Query :: create()
				->from('EmailSubscriber')
				->where('notificationId = ?', $emailNotificationId)
				->orderBy('name ASC');
			return $q->execute();
		} catch (Exception $e) {
			throw new DaoException($e->getMessage());
		}
	}

	public function getSubscriberById($subscriberId) {

		try {
			return Doctrine :: getTable('EmailSubscriber')->find($subscriberId);
		} catch (Exception $e) {
			throw new DaoException($e->getMessage());
		}
	}

	public function deleteSubscribers($subscriberIdList) {
		try {
			$q = Doctrine_Query::create()
				->delete('EmailSubscriber')
				->whereIn('id', $subscriberIdList);

			return $q->execute();
		} catch (Exception $e) {
			throw new DaoException($e->getMessage());
		}
	}

}

