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

namespace OrangeHRM\Admin\Dao;

use Exception;
use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Entity\EmailConfiguration;

class EmailConfigurationDao extends BaseDao
{
    /**
     * @return EmailConfiguration|null
     * @throws DaoException
     */
    public function getEmailConfiguration(): ?EmailConfiguration
    {
        try {
            $q = $this->createQueryBuilder(EmailConfiguration::class, 'e');
            $emailConfig = $this->fetchOne($q);
            if ($emailConfig instanceof EmailConfiguration) {
                return $emailConfig;
            }
            return null;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param EmailConfiguration $emailConfiguration
     * @return EmailConfiguration
     * @throws DaoException
     */
    public function saveEmailConfiguration(EmailConfiguration $emailConfiguration): EmailConfiguration
    {
        try {
            $this->persist($emailConfiguration);
            return $emailConfiguration;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }
}
