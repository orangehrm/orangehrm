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
class PasswordResetService extends BaseService {

    const RESET_PASSWORD_TOKEN_RANDOM_BYTES_LENGTH = 16;

    private $passwordResetDao = null;
    private $securityAuthenticationConfigService = null;
    private $authenticationService = null;
    private $emailService = null;
    private $systemUserService = null;
    private $employeeService = null;

    /**
     *
     * @return SecurityAuthenticationConfigService
     */
    public function getSecurityAuthenticationConfigService() {
        if (empty($this->securityAuthenticationConfigService)) {
            $this->securityAuthenticationConfigService = new SecurityAuthenticationConfigService();
        }
        return $this->securityAuthenticationConfigService;
    }

    /**
     *
     * @param SecurityAuthenticationConfigService $securityAuthConfigService
     */
    public function setSecurityAuthenticationConfigService($securityAuthConfigService) {
        $this->securityAuthenticationConfigService = $securityAuthConfigService;
    }

    /**
     *
     * @return AuthenticationService
     */
    public function getAuthenticationService() {
        if (empty($this->authenticationService)) {
            $this->authenticationService = new AuthenticationService();
        }
        return $this->authenticationService;
    }

    /**
     *
     * @param AuthenticationService $authService
     */
    public function setAuthenticationService($authService) {
        $this->authenticationService = $authService;
    }

    /**
     * @param $username
     * @return array
     * @throws ServiceException
     */
    public function searchForUserRecord($username) {
        if (empty($username)){
            throw new ServiceException(__('Could not find a user with given details'));
        }
        $userService = $this->getSystemUserService();
        $users = $userService->searchSystemUsers(array('userName' => $username));
        if ($users->count() > 0) {
            $user = $users->get(0);
            $associatedEmployee = $user->getEmployee();
            if (!($associatedEmployee instanceof Employee)) {
                throw new ServiceException(__('User account is not associated with an employee'));
            } else {
                $emailConfiguration=new EmailConfigurationService();
                $companyEmail=$emailConfiguration->getEmailConfiguration();
                if(!empty($companyEmail['sentAs'])) {
                    if (empty($associatedEmployee['termination_id'])) {
                        $workEmail = trim($associatedEmployee->getEmpWorkEmail());
                        if (!empty($workEmail)) {
                            $passResetCode=$this->hasPasswordResetRequest($workEmail);
                            if (!$passResetCode) {
                                return $user;
                            } else {
                                throw new ServiceException(__('There is a password reset request already in the system.'));
                            }

                        } else {
                            throw new ServiceException(__('Work email is not set. Please contact HR admin in order to reset the password'));
                        }
                    } else {
                        throw new ServiceException(__('Please contact HR admin in order to reset the password'));
                    }
                } else {
                    throw new ServiceException(__('Password reset email could not be sent'));
                }
            }

        } else {
            throw new ServiceException(__('Please contact HR admin in order to reset the password'));
        }
        return null;
    }

    public function getEmployeeService() {
        if (is_null($this->employeeService)) {
            $this->employeeService = new EmployeeService();
        }
        return $this->employeeService;
    }

    /**
     * @param $employeeService
     */
    public function setEmployeeService($employeeService) {
        $this->employeeService = $employeeService;
    }

    /**
     * @param $email
     * @return bool
     */
    public function hasPasswordResetRequest($email) {

        $resetPassword = $this->getResetPasswordLogByEmail($email);
        if ($resetPassword instanceof ResetPassword) {
            return $this->hasPasswordResetRequestNotExpired($resetPassword);
        }

        return false;
    }

    /**
     * @param $email
     * @return mixed
     * @throws ServiceException
     */
    public function getResetPasswordLogByEmail($email) {

        try {
            return $this->getPasswordResetDao()->getResetPasswordLogByEmail($email);
        } catch (Exception $e) {
            throw new ServiceException($e->getMessage());
        }
    }

    /**
     * @return PasswordResetDao
     */
    public function getPasswordResetDao() {
        return $this->passwordResetDao = new PasswordResetDao();
    }

    /**
     * @param $passwordResetDao
     */
    public function setPasswordResetDao($passwordResetDao) {
        $this->passwordResetDao = $passwordResetDao;
    }

    /**
     * @param ResetPassword $resetPassword
     * @return bool
     */
    public function hasPasswordResetRequestNotExpired(ResetPassword $resetPassword) {
        $strExpireTime = strtotime("+1 days " . $resetPassword->getResetRequestDate());
        $strCurrentTime = strtotime(date("Y-m-d H:i:s"));
        return ($strExpireTime > $strCurrentTime);
    }

    /**
     * @param $user
     * @return bool
     * @throws ServiceException
     */
    public function logPasswordResetRequest($user) {
        $identifier = $user->getUserName();
        $resetCode = $this->generatePasswordResetCode($identifier);

        $resetPassword = new ResetPassword();
        $resetPassword->setResetEmail($user->getEmployee()->getEmpWorkEmail());
        $resetPassword->setResetRequestDate(date('Y-m-d H:i:s'));
        $resetPassword->setResetCode($resetCode);

        $emailSent = $this->sendPasswordResetCodeEmail($user->getEmployee(), $resetCode);
        if (!$emailSent) {
            throw new ServiceException(__('Password reset email could not be sent.'));
        }
        $this->saveResetPasswordLog($resetPassword);

        return true;
    }

    /**
     *
     * @return string
     */
    public function generatePasswordResetCode($identfier) {
        return Base64Url::encode("{$identfier}#SEPARATOR#" .
            random_bytes(static::RESET_PASSWORD_TOKEN_RANDOM_BYTES_LENGTH));
    }

    /**
     * @param Employee $receiver
     * @param $resetCode
     * @return bool
     */
    public function sendPasswordResetCodeEmail(Employee $receiver, $resetCode) {
        $this->getEmailService()->setMessageTo(array($receiver->getEmpWorkEmail()));
        $this->getEmailService()->setMessageFrom(
            array($this->getEmailService()->getEmailConfig()->getSentAs() => 'OrangeHRM System'));
        $this->getEmailService()->setMessageSubject('OrangeHRM Password Reset');
        $this->getEmailService()->setMessageBody($this->generatePasswordResetEmailBody($receiver, $resetCode));
        return $this->getEmailService()->sendEmail();
    }

    /**
     *
     * @return EmailService
     */
    public function getEmailService() {
        if (!($this->emailService instanceof EmailService)) {
            $this->emailService = new EmailService();
        }
        return $this->emailService;
    }

    /**
     *
     * @param EmailService $emailService
     */
    public function setEmailService(EmailService $emailService) {
        $this->emailService = $emailService;
    }

    /**
     * @param Employee $receiver
     * @param $resetCode
     * @return string
     */
    protected function generatePasswordResetEmailBody(Employee $receiver, $resetCode) {
        $resetLink = public_path('index.php/auth/resetPassword', true);

        $placeholders = array('firstName', 'lastName', 'passwordResetLink', 'code', 'passwordResetCodeLink');
        $replacements = array(
            $receiver->getFirstName(),
            $receiver->getLastName(),
            $resetLink,
            $resetCode,
        );

        return $this->generateEmailBody('password-reset-request.txt', $placeholders, $replacements);
    }

    /**
     *
     * @param string $templateFile
     * @param array $placeholders
     * @param array $replacements
     * @return string
     */
    protected function generateEmailBody($templateFile, array $placeholders, array $replacements) {
        $body = file_get_contents(sfConfig::get('sf_plugins_dir') .
            DIRECTORY_SEPARATOR.'orangehrmSecurityAuthenticationPlugin'.DIRECTORY_SEPARATOR.
            'config'.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR . $templateFile);

        foreach ($placeholders as $key => $value) {
            $placeholders[$key] = "/\{{$value}\}/";
        }

        $body = preg_replace($placeholders, $replacements, $body);
        return $body;
    }

    /**
     * @param ResetPassword $resetPassword
     * @return mixed
     * @throws ServiceException
     */
    public function saveResetPasswordLog(ResetPassword $resetPassword) {
        try {
            return $this->getPasswordResetDao()->saveResetPasswordLog($resetPassword);
        } catch (Exception $e) {
            throw new ServiceException($e->getMessage());
        }
    }

    /**
     * @param $passwordResetData
     * @param $resetCode
     * @return bool
     * @throws ServiceException
     */
    public function saveNewPassword($passwordResetData, $resetCode) {

        $userNameMetaData= $this->extractPasswordResetMetaData($resetCode);
        $username=$userNameMetaData[0];
        $newPrimaryPassword = $passwordResetData['newPrimaryPassword'];
        $primaryPasswordConfirmation = $passwordResetData['primaryPasswordConfirmation'];

        $user = $this->getSystemUserService()->searchSystemUsers(array('userName' => $username, 'limit' => 1))->get(0);
        $email = $user->getEmployee()->getEmpWorkEmail();
        $resetPasswordLogByEmail = $this->getPasswordResetDao()->getResetPasswordLogByEmail($email);

        $expireDate = $this->hasPasswordResetRequest($email);

        if ($newPrimaryPassword !== $primaryPasswordConfirmation) {
            throw new ServiceException(__('New primary password and the confirmation does not match'));
        } else if(!$expireDate) {
            $this->getPasswordResetDao()->deletePasswordResetRequestsByEmail($email);
            throw new ServiceException(__('This link is expired, Please request again'));
        }elseif($resetPasswordLogByEmail['reset_code']!==$resetCode) {
            throw new ServiceException(__('Key does not match'));
        }else {
            try {
                $primaryHash = $this->getSystemUserService()->hashPassword($newPrimaryPassword);
                $success = (bool)$this->getPasswordResetDao()->saveNewPrimaryPassword($username, $primaryHash);
                $this->getPasswordResetDao()->deletePasswordResetRequestsByEmail($email);
                return $success;
            } catch (Exception $e) {
                throw new ServiceException($e->getMessage());
            }
        }
    }

    /**
     *
     * @param string $resetCode
     * @return array
     */
    public function extractPasswordResetMetaData($resetCode) {
        $code = Base64Url::decode($resetCode);

        $metaData = explode('#SEPARATOR#', $code);

        array_pop($metaData);

        return $metaData;
    }

    /**
     *
     * @return SystemUserService
     */
    public function getSystemUserService() {
        if (!($this->systemUserService instanceof SystemUserService)) {
            $this->systemUserService = new SystemUserService();
        }
        return $this->systemUserService;
    }

    /**
     *
     * @param SystemUserService $systemUserService
     */
    public function setSystemUserService($systemUserService) {
        $this->systemUserService = $systemUserService;
    }

}
