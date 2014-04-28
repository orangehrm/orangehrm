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
class PayGradeDao extends BaseDao {

	/**
	 *
	 * @param type $payGradeId
	 * @return type 
	 */
	public function getPayGradeById($payGradeId) {

		try {
			return Doctrine :: getTable('PayGrade')->find($payGradeId);
		} catch (Exception $e) {
			throw new DaoException($e->getMessage());
		}
	}

	/**
	 *
	 * @return type 
	 */
	public function getPayGradeList($sortField='name', $sortOrder='ASC') {

		$sortField = ($sortField == "") ? 'name' : $sortField;
		$sortOrder = ($sortOrder == "DESC") ? 'DESC' : 'ASC';

		try {
			$q = Doctrine_Query :: create()
				->from('PayGrade')
				->orderBy($sortField . ' ' . $sortOrder);
			return $q->execute();
		} catch (Exception $e) {
			throw new DaoException($e->getMessage());
		}
	}

	public function getCurrencyListByPayGradeId($payGradeId) {

		try {
			$q = Doctrine_Query :: create()
				->from('PayGradeCurrency pgc')
                                ->leftJoin('pgc.CurrencyType ct')
				->where('pgc.pay_grade_id = ?', $payGradeId)
                                ->orderBy('ct.currency_name ASC');
			return $q->execute();
		} catch (Exception $e) {
			throw new DaoException($e->getMessage());
		}
	}

	public function getCurrencyByCurrencyIdAndPayGradeId($currencyId, $payGradeId) {

		try {
			$q = Doctrine_Query :: create()
				->from('PayGradeCurrency')
				->where('pay_grade_id = ?', $payGradeId)
				->andWhere('currency_id = ?', $currencyId);
			return $q->fetchOne();
		} catch (Exception $e) {
			throw new DaoException($e->getMessage());
		}
	}

}

?>
