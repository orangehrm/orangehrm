<?php

class SecurityAuthenticationService extends BaseService implements StateAccessibleByExecutionFilters {

    private $securityAuthenticationDao = null;
    private $securityAuthenticationConfigService = null;
    private $authenticationService = null;
    private $emailService = null;
    private $systemUserService = null;
    private $employeeService = null;
    private static $state = self::EMPTY_STATE;
    const STATE_NOT_USED = 0;
    const STATE_USED = 1;

    /**
     * @return string
     */
    public static function getState()
    {
        return self::$state;
    }

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
     *
     */
    public function getSecurityAuthenticationDao() {
        return $this->securityAuthenticationDao = new SecurityAuthenticationDao();
    }

    /**
     * @param $securityAuthenticationDao
     */
    public function setSecurityAuthenticationDao($securityAuthenticationDao) {
        $this->securityAuthenticationDao = $securityAuthenticationDao;
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


        $result = $this->findUser($username);
        list($user, $matchedByField, $matchedValue) = $result;

        if ($user instanceof SystemUser) {

            $associatedEmployee = $user->getEmployee();
            if (!($associatedEmployee instanceof Employee)) {
                throw new ServiceException(__('User account is not associated with an employee'));
            } else {
                $emailConfiguration=new EmailConfigurationService();
                $companyEmail=$emailConfiguration->getEmailConfiguration();
                if(!is_null($companyEmail['sentAs'])) {
                    if (empty($associatedEmployee['termination_id'])) {
                        $workEmail = trim($associatedEmployee->getEmpWorkEmail());
                        $employeeNumber = $associatedEmployee->getEmpNumber();
                        if (empty($workEmail)) {
                            if (empty($employeeNumber)) {
                                throw new ServiceException(__('Please contact HR admin in order to reset the password'));
                            } else {
                                throw new ServiceException(__('Work Email Is Not Set. Please Contact HR Admin in Order to Reset the Password'));
                            }
                        }
                    } else {
                        throw new ServiceException(__('Please Contact HR Admin in Order to Reset the Password'));
                    }
                }else {
                    throw new ServiceException(__('Password Reset Email Could Not Be Sent'));
                }
            }
            if (!$this->hasPasswordResetRequest($workEmail)) {
                return $result;
            } else {
                throw new ServiceException(__('There is a password reset request already in the system.'));
            }
        } elseif (empty($username)){
            throw new ServiceException(__('Could not find a user with given details'));
        } else {
            throw new ServiceException(__('Please Contact HR Admin in Order to Reset the Password'));
        }
        return $result;
    }

    /**
     * @param $username
     * @return array
     */
    protected function findUser($username) {
        $user = null;
        $matchedByField = null;
        $matchedValue = null;

            $employeeService = $this->getEmployeeService();
            $employee = null;
            $result = $employeeService->searchEmployee('emp_firstname', $username);

            if ($result->count() > 0) {
                $employee = $result->get(0);
                $matchedByField = 'Work Email';
            } else {
                $result = $employeeService->searchEmployee('emp_firstname', $username);
                if ($result->count() > 0) {
                    $employee = $result->get(0);
                    $matchedByField = 'Other Email';
                }
            }

            if ($employee instanceof Employee) {
                list($user) = $employee->getSystemUser();
                $matchedValue = $username;
            }

        return array($user, $matchedByField, $matchedValue);

    }

    /**
     * @param $email
     * @return bool
     */
    public function hasPasswordResetRequest($email) {

        $resetPasswordLog = $this->getResetPasswordLogByEmail($email);
        if ($resetPasswordLog instanceof ResetPasswordLog) {
            return $this->hasPasswordResetRequestNotExpired($resetPasswordLog);
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
            return $this->getSecurityAuthenticationDao()->getResetPasswordLogByEmail($email);
        } catch (Exception $e) {
            throw new ServiceException($e->getMessage());
        }
    }

    /**
     *
     * @param ResetPasswordLog $resetPasswordLog
     * @return bool
     */
    public function hasPasswordResetRequestNotExpired(ResetPasswordLog $resetPasswordLog) {
        $strExpireTime = strtotime("+1 days " . $resetPasswordLog->getResetRequestDate());
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

        $resetPasswordLog = new ResetPasswordLog();
        $resetPasswordLog->setResetEmail($user->getEmployee()->getEmpWorkEmail());
        $resetPasswordLog->setResetRequestDate(date('Y-m-d H:i:s'));
        $resetPasswordLog->setResetCode($resetCode);
        $resetPasswordLog->setStatus(SecurityAuthenticationService::STATE_NOT_USED);

        $emailSent = $this->sendPasswordResetCodeEmail($user->getEmployee(), $resetCode);
        if (!$emailSent) {
            throw new ServiceException(__('Password reset email could not be sent.'));
        }
        $this->saveResetPasswordLog($resetPasswordLog);

        return true;
    }

    /**
     *
     * @return string
     */
    public function generatePasswordResetCode($identfier) {
        return base64_encode(uniqid("{$identfier}#SEPARATOR#"));
    }

    /**
     * @param ResetPasswordLog $resetPasswordLog
     * @return mixed
     * @throws ServiceException
     */
    public function saveResetPasswordLog(ResetPasswordLog $resetPasswordLog) {
        try {
            return $this->getSecurityAuthenticationDao()->saveResetPasswordLog($resetPasswordLog);
        } catch (Exception $e) {
            throw new ServiceException($e->getMessage());
        }
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
        //$this->getEmailService()->sendTestEmail($receiver->getEmpWorkEmail());
        //var_dump($this->generatePasswordResetEmailBody($receiver, $resetCode));die;
        return $this->getEmailService()->sendEmail();
    }

    /**
     * @param Employee $receiver
     * @param $resetCode
     * @return string
     */
    protected function generatePasswordResetEmailBody(Employee $receiver, $resetCode) {
        $resetLink = public_path('index.php/auth/resetPassword', true);
        //$passwordResetCodeLink = public_path('index.php/securityAuthentication/passwordResetCode', true);

        $placeholders = array('firstName', 'lastName', 'passwordResetLink', 'code', 'passwordResetCodeLink');
        $replacements = array(
            $receiver->getFirstName(),
            $receiver->getLastName(),
            $resetLink,
            $resetCode,
            //$passwordResetCodeLink,
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
            '/orangehrmSecurityAuthenticationPlugin/config/data/' . $templateFile);

        foreach ($placeholders as $key => $value) {
            $placeholders[$key] = "/\{{$value}\}/";
        }

        $body = preg_replace($placeholders, $replacements, $body);
        return $body;
    }


    /**
     * @param $passowrdResetData
     * @param $resetCode
     * @return bool
     * @throws ServiceException
     */
    public function saveNewPassword($passowrdResetData, $resetCode) {

        $userNameMetaData= $this->extractPasswordResetMetaData($resetCode);
        $username=$userNameMetaData[0];
        $newPrimaryPassword = $passowrdResetData['newPrimaryPassword'];
        $primaryPasswordConfirmation = $passowrdResetData['primaryPasswordConfirmation'];

        $user = $this->getSystemUserService()->searchSystemUsers(array('userName' => $username, 'limit' => 1))->get(0);
        $email = $user->getEmployee()->getEmpWorkEmail();
        $resetPasswordLogByEmail = $this->getSecurityAuthenticationDao()->getResetPasswordLogByEmail($email);

        $expireDate = $this->hasPasswordResetRequest($email);

        if ($newPrimaryPassword !== $primaryPasswordConfirmation) {
            throw new ServiceException(__('New primary password and the confirmation does not match'));
        } else if(!$expireDate) {
            $this->getSecurityAuthenticationDao()->deletePasswordResetRequestsByEmail($email);
            throw new ServiceException(__('The current link has expired'));
        }elseif($resetPasswordLogByEmail['reset_code']!==$resetCode) {
            throw new ServiceException(__('Key does not match'));
        }else {
            try {
                $success = false;
                $primaryHash = $this->getSystemUserService()->hashPassword($newPrimaryPassword);
                $success = (bool)$this->getSecurityAuthenticationDao()->saveNewPrimaryPassword($username, $primaryHash);
                try {
                    $this->getSecurityAuthenticationDao()->deletePasswordResetRequestsByEmail($email);
                } catch (Exception $e) {
                    throw new Exception($e->getMessage());
                }

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
        $code = base64_decode($resetCode);

        $metaData = explode('#', $code);

        array_pop($metaData);

        return $metaData;
    }

}
