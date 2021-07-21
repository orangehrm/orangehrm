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

namespace OrangeHRM\Pim\Dao;

use Exception;
use InvalidArgumentException;
use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Entity\CustomField;
use OrangeHRM\ORM\Paginator;
use OrangeHRM\Pim\Dto\CustomFieldSearchFilterParams;

class CustomFieldDao extends BaseDao
{
    /**
     * @param CustomField $customField
     * @return CustomField
     * @throws DaoException
     */
    public function saveCustomField(CustomField $customField): CustomField
    {
        // increment seqNo if not set explicitly
        if ($customField->getFieldNum() === 0) {
            $q = $this->createQueryBuilder(CustomField::class, 'cf');
            $q->select($q->expr()->max('cf.fieldNum'));
            $maxSeqNo = $q->getQuery()->getSingleScalarResult();
            $seqNo = 1;
            if (!is_null($maxSeqNo)) {
                $seqNo += intval($maxSeqNo);
            }
            $customField->setFieldNum($seqNo);
        }
        $seqNo = intval($customField->getFieldNum());
        if (!(strlen((string)$seqNo) <= 10 && $seqNo > 0)) {
            throw new InvalidArgumentException('Invalid `seqNo`');
        }

        $this->persist($customField);
        return $customField;
    }

    /**
     * @param int $id
     * @return CustomField|null
     * @throws DaoException
     */
    public function getCustomFieldById(int $id): ?CustomField
    {
        try {
            $customField = $this->getRepository(CustomField::class)->findOneBy(
                [
                    'fieldNum' => $id,
                ]
            );
            if ($customField instanceof CustomField) {
                return $customField;
            }
            return null;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param array $toDeleteIds
     * @return int
     * @throws DaoException
     */
    public function deleteCustomFields(array $toDeleteIds): int
    {
        try {
            $q = $this->createQueryBuilder(CustomField::class, 'cf');
            $q->delete()
                ->andWhere($q->expr()->in('cf.fieldNum', ':ids'))
                ->setParameter('ids', $toDeleteIds);
            return $q->getQuery()->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * Search CustomField
     *
     * @param CustomFieldSearchFilterParams $customFieldSearchParams
     * @return CustomField[]
     * @throws DaoException
     */
    public function searchCustomField(CustomFieldSearchFilterParams $customFieldSearchParams): array
    {
        try {
            $paginator = $this->getSearchCustomFieldPaginator($customFieldSearchParams);
            return $paginator->getQuery()->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param CustomFieldSearchFilterParams $customFieldSearchParams
     * @return Paginator
     */
    private function getSearchCustomFieldPaginator(
        CustomFieldSearchFilterParams $customFieldSearchParams
    ): Paginator {
        $q = $this->createQueryBuilder(CustomField::class, 'cf');
        $this->setSortingAndPaginationParams($q, $customFieldSearchParams);

        if (!empty($customFieldSearchParams->getScreen())) {
            $q->andWhere('cf.screen = :screen')
                ->setParameter('screen', $customFieldSearchParams->getScreen());
        }

        if (!empty($customFieldSearchParams->getFieldNumbers())) {
            $q->andWhere($q->expr()->in('cf.fieldNum', ':fieldNumbers'))
                ->setParameter('fieldNumbers', $customFieldSearchParams->getFieldNumbers());
        }

        return $this->getPaginator($q);
    }

    /**
     * Get Count of Search Query
     *
     * @param CustomFieldSearchFilterParams $customFieldSearchParams
     * @return int
     * @throws DaoException
     */
    public function getSearchCustomFieldsCount(CustomFieldSearchFilterParams $customFieldSearchParams
    ): int {
        try {
            $paginator = $this->getSearchCustomFieldPaginator($customFieldSearchParams);
            return $paginator->count();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
