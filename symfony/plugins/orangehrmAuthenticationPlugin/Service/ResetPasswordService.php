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

namespace OrangeHRM\Authentication\Service;

use OrangeHRM\Admin\Dto\UserSearchFilterParams;
use OrangeHRM\Admin\Service\UserService;
use OrangeHRM\Authentication\Dao\ResetPasswordDao;
use OrangeHRM\Config\Config;
use OrangeHRM\Core\Exception\ServiceException;
use OrangeHRM\Core\Service\EmailService;
use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Core\Utility\Base64Url;
use OrangeHRM\Entity\EmailConfiguration;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\ResetPassword;
use OrangeHRM\Entity\User;

class ResetPasswordService
{
    use DateTimeHelperTrait;
    use EntityManagerHelperTrait;

    public const RESET_PASSWORD_TOKEN_RANDOM_BYTES_LENGTH = 16;
    protected ?EmailService $emailService = null;
    protected ?UserService $userService = null;
    protected ?ResetPasswordDao $resetPasswordDao = null;

    /**
     * @return EmailService
     */
    public function getEmailService(): EmailService
    {
        if (!$this->emailService instanceof EmailService) {
            $this->emailService = new EmailService();
        }
        return $this->emailService;
    }

    /**
     * @return ResetPasswordDao
     */
    public function getResetPasswordDao(): ResetPasswordDao
    {
        if (!$this->resetPasswordDao instanceof ResetPasswordDao) {
            $this->resetPasswordDao = new ResetPasswordDao();
        }
        return $this->resetPasswordDao;
    }

    /**
     * @return UserService|null
     */
    public function getUserService(): UserService
    {
        if (!($this->userService instanceof UserService)) {
            $this->userService = new UserService();
        }
        return $this->userService;
    }

    /**
     * @param string $templateFile
     * @param array $placeholders
     * @param array $replacements
     * @return array|string|string[]|null
     */
    protected function generateEmailBody(string $templateFile, array $placeholders, array $replacements)
    {
        $body = file_get_contents(
            Config::get(
                Config::PLUGINS_DIR
            ) . '/orangehrmAuthenticationPlugin/config/data' . '//' . $templateFile
        );

        foreach ($placeholders as $key => $value) {
            $placeholders[$key] = "/\{{$value}\}/";
        }

        $body = preg_replace($placeholders, $replacements, $body);
        return $body;
    }

    /**
     * @param string $username
     * @return User|null
     * @throws ServiceException
     */
    public function searchForUserRecord(string $username): ?User
    {
        $userFilterParams = new UserSearchFilterParams();
        $userFilterParams->setUsername($username);
        $users = $this->getUserService()->searchSystemUsers($userFilterParams);

        if (count($users) > 0) {
            $user = $users[0];
            $associatedEmployee = $user->getEmployee();
            if (!($associatedEmployee instanceof Employee)) {
                throw new ServiceException('User account is not associated with an employee');
            } else {
                if (empty($associatedEmployee->getEmployeeTerminationRecord())) {
                    $companyEmail = $this->getRepository(EmailConfiguration::class)->findOneBy(['id' => '1']);
                    if ($companyEmail instanceof EmailConfiguration) {
                        if (!empty($companyEmail->getSentAs())) {
                            $workEmail = trim($associatedEmployee->getWorkEmail());
                            if (!empty($workEmail)) {
                                return $user;
                            } else {
                                throw new ServiceException(
                                    'Work email is not set. Please contact HR admin in order to reset the password'
                                );
                            }
                        } else {
                            throw new ServiceException('Password reset email could not be sent');
                        }
                    } else {
                        throw new ServiceException('Company email is not set yet');
                    }
                } else {
                    throw new ServiceException('Please contact HR admin in order to reset the password');
                }
            }
        } else {
            throw new ServiceException('Please contact HR admin in order to reset the password');
        }
    }


    protected function generatePasswordResetEmailBody(Employee $receiver, string $resetCode, string $userName)
    {
        //TODO
        $resetLink = 'index.php/auth/resetPassword';
        $placeholders = [
            'firstName',
            'lastName',
            'middleName',
            'workEmail',
            'userName',
            'passwordResetLink',
            'code',
            'passwordResetCodeLink'
        ];
        $replacements = [
            $receiver->getFirstName(),
            $receiver->getLastName(),
            $receiver->getMiddleName(),
            $receiver->getWorkEmail(),
            $userName,
            $resetLink,
            $resetCode,
        ];

        return $this->generateEmailBody('password-reset-request.txt', $placeholders, $replacements);
    }


    public function sendPasswordResetCodeEmail(Employee $receiver, string $resetCode): bool
    {
        $this->getEmailService()->setMessageTo([$receiver->getWorkEmail()]);
        $this->getEmailService()->setMessageFrom(
            [$this->getEmailService()->getEmailConfig()->getSentAs() => 'OrangeHRM System']
        );
        $this->getEmailService()->setMessageSubject('OrangeHRM Password Reset');
        $this->getEmailService()->setMessageBody($this->generatePasswordResetEmailBody($receiver, $resetCode, 'Admin'));
        return $this->getEmailService()->sendEmail();
    }

    /**
     * @param string $identifier
     * @return array|false|string|string[]
     */
    public function generatePasswordResetCode(string $identifier)
    {
        return Base64Url::encode(
            "{$identifier}#SEPARATOR#" .
            random_bytes(static::RESET_PASSWORD_TOKEN_RANDOM_BYTES_LENGTH)
        );
    }

    /**
     * @param ResetPassword $resetPassword
     * @return ResetPassword|null
     */
    public function saveResetPasswordLog(ResetPassword $resetPassword): ?ResetPassword
    {
        return $this->getResetPasswordDao()->saveResetPassword($resetPassword);
    }

    /**
     * @param User $user
     * @return bool
     * @throws ServiceException
     */
    public function logPasswordResetRequest(User $user): bool
    {
        $identifier = $user->getUserName();
        $resetCode = $this->generatePasswordResetCode($identifier);
        $resetPassword = new ResetPassword();
        $resetPassword->setResetEmail($user->getEmployee()->getWorkEmail());
        $date = $this->getDateTimeHelper()->getNow();
        $resetPassword->setResetRequestDate($date);
        $resetPassword->setResetCode($resetCode);
        $emailSent = $this->sendPasswordResetCodeEmail($user->getEmployee(), $resetCode);
        if (!$emailSent) {
            throw new ServiceException('Password reset email could not be sent.');
        }
        $this->saveResetPasswordLog($resetPassword);
        return true;
    }
}
