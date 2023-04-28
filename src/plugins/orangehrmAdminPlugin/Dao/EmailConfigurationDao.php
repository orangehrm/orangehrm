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
use OrangeHRM\Core\Service\EmailService;
use OrangeHRM\Entity\EmailConfiguration;

class EmailConfigurationDao extends BaseDao
{
    public function getEmailConfiguration(): EmailConfiguration
    {
        return EmailConfiguration::instance()
            ->setMailType('smtp')
            ->setSmtpAuthType(EmailService::SMTP_AUTH_LOGIN)
            ->setSmtpSecurityType($_ENV['MAIL_ENCRYPTION'])
            ->setSmtpHost($_ENV['MAIL_HOST'])
            ->setSmtpPort($_ENV['MAIL_PORT'])
            ->setMailType($_ENV['MAIL_MAILER'])
            ->setSmtpUsername($_ENV['MAIL_USERNAME'])
            ->setSmtpPassword($_ENV['MAIL_PASSWORD'])
            ->setSentAs($_ENV['MAIL_FROM_ADDRESS']);
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
