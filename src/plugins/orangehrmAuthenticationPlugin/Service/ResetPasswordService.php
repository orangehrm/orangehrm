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
use OrangeHRM\Admin\Traits\Service\UserServiceTrait;
use OrangeHRM\Authentication\Dao\ResetPasswordDao;
use OrangeHRM\Authentication\Dto\UserCredential;
use OrangeHRM\Config\Config;
use OrangeHRM\Core\Service\EmailService;
use OrangeHRM\Core\Traits\ControllerTrait;
use OrangeHRM\Core\Traits\LoggerTrait;
use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Core\Utility\Base64Url;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\EmployeeTerminationRecord;
use OrangeHRM\Entity\ResetPasswordRequest;
use OrangeHRM\Entity\User;
use OrangeHRM\Framework\Routing\UrlGenerator;
use OrangeHRM\Framework\Services;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class ResetPasswordService
{
    use DateTimeHelperTrait;
    use EntityManagerHelperTrait;
    use LoggerTrait;
    use ControllerTrait;
    use UserServiceTrait;

    public const RESET_PASSWORD_TOKEN_RANDOM_BYTES_LENGTH = 16;
    protected ?ResetPasswordDao $resetPasswordDao = null;
    protected ?EmailService $emailService = null;

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
     * @param ResetPasswordRequest $resetPassword
     * @return float
     */
    public function hasPasswordResetRequestNotExpired(ResetPasswordRequest $resetPassword): float
    {
        $strResetRequestTime = strtotime($resetPassword->getResetRequestDate()->format('Y-m-d H:i:s'));
        $strCurrentTime = $this->getDateTimeHelper()->getNow()->getTimestamp();
        return floor(($strCurrentTime - $strResetRequestTime) / (60 * 60 * 24));
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
     * @param string $templateFile
     * @param array $placeholders
     * @param array $replacements
     * @return array|string|string[]|null
     */
    public function generateEmailBody(string $templateFile, array $placeholders, array $replacements)
    {
        $body = file_get_contents(
            Config::get(
                Config::PLUGINS_DIR
            ) . '/orangehrmAuthenticationPlugin/config/data/' . $templateFile
        );

        return nl2br(str_replace($placeholders, $replacements, $body));
    }

    /**
     * @param string $username
     * @return User|null
     */
    public function searchForUserRecord(string $username): ?User
    {
        $userFilterParams = new UserSearchFilterParams();
        $userFilterParams->setUsername($username);
        $users = $this->getUserService()->searchSystemUsers($userFilterParams);

        if (empty($users)) {
            $this->getLogger()->error('There are no user account for the current username');
            return null;
        }
        $user = $users[0];
        $associatedEmployee = $user->getEmployee();
        if (!$associatedEmployee instanceof Employee) {
            $this->getLogger()->error('User account is not associated with an employee');
            return null;
        }

        if ($associatedEmployee->getEmployeeTerminationRecord() instanceof EmployeeTerminationRecord) {
            $this->getLogger()->error('Please contact HR admin in order to reset the password');
            return null;
        }

        if (!$user->getStatus()) {
            $this->getLogger()->error('Account disabled');
            return null;
        }

        if (empty($associatedEmployee->getWorkEmail())) {
            $this->getLogger()->error(
                'Work email is not set. Please contact HR admin in order to reset the password'
            );
            return null;
        }
        return $user;
    }

    /**
     * @param Employee $receiver
     * @param string $resetCode
     * @param string $userName
     * @return array|string|string[]|null
     */
    protected function generatePasswordResetEmailBody(Employee $receiver, string $resetCode, string $userName)
    {
        /** @var UrlGenerator $urlGenerator */
        $urlGenerator = $this->getContainer()->get(Services::URL_GENERATOR);
        $resetLink = $urlGenerator->generate(
            'auth_reset_code',
            ['resetCode' => $resetCode],
            UrlGenerator::ABSOLUTE_URL
        );
        $placeholders = [
            'firstName',
            'userName',
            'passwordResetLink',
        ];
        $replacements = [
            $receiver->getFirstName(),
            $userName,
            $resetLink
        ];
        return $this->generateEmailBody('password-reset-request.txt', $placeholders, $replacements);
    }

    /**
     * @param Employee $receiver
     * @param string $resetCode
     * @return bool
     */
    public function sendPasswordResetCodeEmail(Employee $receiver, string $resetCode, string $userName): bool
    {
        try {
            $this->getEmailService()->setMessageTo([$receiver->getWorkEmail()]);
            $this->getEmailService()->setMessageFrom(
                [$this->getEmailService()->getEmailConfig()->getSentAs() => 'OrangeHRM']
            );
            $this->getEmailService()->setMessageSubject('OrangeHRM Password Reset');
            $this->getEmailService()->setMessageBody(
                $this->generatePasswordResetEmailBody($receiver, $resetCode, $userName)
            );
            return $this->getEmailService()->sendEmail();
        } catch (TransportExceptionInterface $e) {
            $this->getLogger()->error('Invalid Email configuration');
            return false;
        }
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
     * @param User $user
     * @return User|null
     */
    public function validateUser(?User $user): ?User
    {
        if ($user instanceof User) {
            if ($user->getEmployee()->getEmployeeTerminationRecord()) {
                $this->getLogger()->error('employee was terminated');
                return null;
            }
            return $user;
        }
        $this->getLogger()->error('user account was deleted');
        return null;
    }

    /**
     * @param string $resetCode
     * @return User|null
     */
    public function validateUrl(string $resetCode): ?User
    {
        $userNameMetaData = $this->extractPasswordResetMetaData($resetCode);
        if (count($userNameMetaData) > 0) {
            $username = $userNameMetaData[0];
            $resetPassword = $this->getResetPasswordDao()->getResetPasswordLogByResetCode($resetCode);
            if ($resetPassword instanceof ResetPasswordRequest) {
                $currentResetCode = $this->getResetPasswordDao()->getResetPasswordLogByEmail(
                    $resetPassword->getResetEmail()
                )->getResetCode();
                if ($currentResetCode !== $resetCode) {
                    $this->getLogger()->error('reset code was old one & not valid');
                    return null;
                }
                $expDay = $this->hasPasswordResetRequestNotExpired($resetPassword);
                if ($expDay > 0) {
                    $this->getLogger()->error('Password reset code expired');
                    return null;
                }
                $user = $this->getUserService()->getSystemUserDao()->getUserByUserName($username);
                return $this->validateUser($user);
            }
            return null;
        }
        $this->getLogger()->error('Invalid reset code');
        return null;
    }

    /**
     * @param User $user
     * @return bool
     */
    public function logPasswordResetRequest(User $user): bool
    {
        $identifier = $user->getUserName();
        $resetCode = $this->generatePasswordResetCode($identifier);
        $resetPassword = new ResetPasswordRequest();
        $resetPassword->setResetEmail($user->getEmployee()->getWorkEmail());
        $date = $this->getDateTimeHelper()->getNow();
        $resetPassword->setResetRequestDate($date);
        $resetPassword->setResetCode($resetCode);
        $emailSent = $this->sendPasswordResetCodeEmail($user->getEmployee(), $resetCode, $user->getUserName());
        if (!$emailSent) {
            $this->getLogger()->error('Password reset email could not be sent.');
            return false;
        }
        $this->getResetPasswordDao()->saveResetPasswordRequest($resetPassword);
        return true;
    }

    /**
     * @param UserCredential $credential
     * @return bool
     */
    public function saveResetPassword(UserCredential $credential): bool
    {
        $user = $this->getUserService()->getSystemUserDao()->getUserByUserName($credential->getUsername());
        if ($this->validateUser($user) instanceof User) {
            $hashPassword = $this->getUserService()->hashPassword($credential->getPassword());
            return $this->getUserService()->getSystemUserDao()->updatePassword($user->getId(), $hashPassword);
        }
        return false;
    }
}
