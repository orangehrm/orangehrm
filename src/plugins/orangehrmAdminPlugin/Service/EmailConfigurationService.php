<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software: you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with OrangeHRM.
 * If not, see <https://www.gnu.org/licenses/>.
 */

namespace OrangeHRM\Admin\Service;

use Exception;
use OrangeHRM\Admin\Dao\EmailConfigurationDao;
use OrangeHRM\Core\Service\EmailService;

class EmailConfigurationService
{
    /**
     * @var EmailConfigurationDao|null
     */
    private ?EmailConfigurationDao $emailConfigurationDao = null;

    /**
     * @var null|EmailService
     */
    protected ?EmailService $emailService = null;

    /**
     * @return EmailConfigurationDao|null
     */
    public function getEmailConfigurationDao(): EmailConfigurationDao
    {
        if (!($this->emailConfigurationDao instanceof EmailConfigurationDao)) {
            $this->emailConfigurationDao = new EmailConfigurationDao();
        }
        return $this->emailConfigurationDao;
    }

    /**
     * @return EmailService
     */
    public function getEmailService(): EmailService
    {
        if (is_null($this->emailService)) {
            $this->emailService = new EmailService();
        }
        return $this->emailService;
    }

    /**
     * @param EmailService $emailService
     */
    public function setEmailService(EmailService $emailService): void
    {
        $this->emailService = $emailService;
    }

    /**
     * @param string $testEmail
     * @return bool
     * @throws Exception
     */
    public function sendTestMail(string $testEmail)
    {
        return $this->getEmailService()->sendTestEmail($testEmail);
    }
}
