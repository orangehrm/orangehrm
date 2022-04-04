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

use OrangeHRM\Admin\Dao\UserDao;
use OrangeHRM\Admin\Dto\UserSearchFilterParams;
use OrangeHRM\Admin\Service\UserService;
use OrangeHRM\Authentication\Dao\ResetPasswordDao;
use OrangeHRM\Config\Config;
use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Core\Exception\ServiceException;
use OrangeHRM\Core\Service\EmailService;
use OrangeHRM\Core\Traits\LoggerTrait;
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
    use LoggerTrait;

    public const RESET_PASSWORD_TOKEN_RANDOM_BYTES_LENGTH = 16;
    protected ?EmailService $emailService = null;
    protected ?UserService $userService = null;
    protected ?ResetPasswordDao $resetPasswordDao = null;
    protected ?UserDao $userDao=null;

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
     * @return UserDao
     */
    public function getUserDao(): UserDao
    {
        if (!$this->userDao instanceof UserDao) {
            $this->userDao = new UserDao();
        }
        return $this->userDao;
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
     * @param ResetPassword $resetPassword
     * @return float
     */
    public function hasPasswordResetRequestNotExpired(ResetPassword $resetPassword): float
    {
        $strExpireTime = strtotime($resetPassword->getResetRequestDate()->format('Y-m-d H:i:s'));
        $strCurrentTime = strtotime(date("Y-m-d H:i:s"));
        return floor(($strCurrentTime-$strExpireTime)/(60*60*24));
    }

    /**
     *
     * @param string $resetCode
     * @return array
     */
    public function extractPasswordResetMetaData(string $resetCode): array
    {
        $code = Base64Url::decode($resetCode);

        $metaData = explode('#SEPARATOR#', $code);

        array_pop($metaData);

        return $metaData;
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
                $this->getLogger()->error('User account is not associated with an employee');
            } else {
                if (empty($associatedEmployee->getEmployeeTerminationRecord())) {
                    $companyEmail = $this->getRepository(EmailConfiguration::class)->findOneBy(['id' => '1']);
                    if ($companyEmail instanceof EmailConfiguration) {
                        if (!empty($companyEmail->getSentAs())) {
                            $workEmail = trim($associatedEmployee->getWorkEmail());
                            if (!empty($workEmail)) {
                                return $user;
                            } else {
                                $this->getLogger()->error('Work email is not set. Please contact HR admin in order to reset the password');
                            }
                        } else {
                            $this->getLogger()->error('Password reset email could not be sent');
                        }
                    } else {
                        $this->getLogger()->error('Company email is not set yet');
                    }
                } else {
                    $this->getLogger()->error('Please contact HR admin in order to reset the password');
                }
            }
        } else {
            $this->getLogger()->error('Please contact HR admin in order to reset the password');
        }
        return null;
    }


    /**
     * @param Employee $receiver
     * @param string $resetCode
     * @param string $userName
     * @return array|string|string[]|null
     */
    protected function generatePasswordResetEmailBody(Employee $receiver, string $resetCode, string $userName)
    {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http';
        $url = $protocol."://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $resetLink=str_replace('userNameVerify', 'resetPassword', $url);
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


    /**
     * @param Employee $receiver
     * @param string $resetCode
     * @return bool
     */
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
     * @param string $resetCode
     * @return User|null
     * @throws DaoException
     */
    public function validateUrl(string $resetCode): ?User
    {
        $userNameMetaData= $this->extractPasswordResetMetaData($resetCode);
        $username=$userNameMetaData[0];
        $resetPassword=$this->getResetPasswordDao()->getResetPasswordLogByResetCode($resetCode);
        $expDay=$this->hasPasswordResetRequestNotExpired($resetPassword);
        if ($expDay > 0) {
            $this->getLogger()->error('not valid URL');
            return null;
        }
        $user=$this->getUserDao()->getUserByUserName($username);
        if ($user instanceof  User) {
            if ($user->getEmployee()->getEmployeeTerminationRecord()) {
                $this->getLogger()->error('employee was terminated');
                return null;
            }
            if (!empty($user->getEmployee()->getWorkEmail())) {
                return $user;
            }
            $this->getLogger()->error('employee work email was not set');
        }
        $this->getLogger()->error('user account was deleted');
        return null;
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
            $this->getLogger()->error('Password reset email could not be sent.');
        }
        $this->saveResetPasswordLog($resetPassword);
        return true;
    }
}
