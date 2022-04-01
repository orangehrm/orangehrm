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


use DateTime;
use OrangeHRM\Admin\Dto\UserSearchFilterParams;
use OrangeHRM\Admin\Service\UserService;
use OrangeHRM\Config\Config;
use OrangeHRM\Core\Exception\ServiceException;
use OrangeHRM\Core\Service\EmailService;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\ResetPassword;
use OrangeHRM\Entity\User;

class ResetPasswordService
{
    use DateTimeHelperTrait;

    protected  ?EmailService $emailService=null;
    protected  ?userService $userService=null;

    public function getEmailService():?EmailService{
        if(!$this->emailService instanceof  EmailService){
            $this->emailService=new EmailService();
        }
        return $this->emailService;
    }

    public function getUserService(): ?UserService {
        if (!($this->userService instanceof UserService)) {
            $this->userService = new UserService();
        }
        return $this->userService;
    }

    protected function generateEmailBody($templateFile, array $placeholders, array $replacements) {
        $body = file_get_contents(Config::get('sf_plugins_dir') .
            DIRECTORY_SEPARATOR.'orangehrmSecurityAuthenticationPlugin'.DIRECTORY_SEPARATOR.
            'config'.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR . $templateFile);

        foreach ($placeholders as $key => $value) {
            $placeholders[$key] = "/\{{$value}\}/";
        }

        $body = preg_replace($placeholders, $replacements, $body);
        return $body;
    }

    public function searchForUserRecord($username) {
        if (empty($username)){
            throw new ServiceException(__('Could not find a user with given details'));
        }
         $userfilterParams=new UserSearchFilterParams();
        $userfilterParams->setUsername($username);
        $userService = $this->getUserService();
        $users = $userService->searchSystemUsers($userfilterParams);
        if (count($users)>0) {
            $user = $users[0];
            $associatedEmployee = $user->getEmployee();
            if (!($associatedEmployee instanceof Employee)) {
                throw new ServiceException('User account is not associated with an employee');
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


    protected function generatePasswordResetEmailBody(Employee $receiver, $resetCode) {
        $resetLink ='index.php/auth/resetPassword';

        $placeholders = array('firstName', 'lastName', 'passwordResetLink', 'code', 'passwordResetCodeLink');
        $replacements = array(
            $receiver->getFirstName(),
            $receiver->getLastName(),
            $resetLink,
            $resetCode,
        );

        return $this->generateEmailBody('password-reset-request.txt', $placeholders, $replacements);
    }


    public function sendPasswordResetCodeEmail(Employee $receiver, $resetCode) {
        $this->getEmailService()->setMessageTo([$receiver->getWorkEmail()]);
        $this->getEmailService()->setMessageFrom(
            [$this->getEmailService()->getEmailConfig()->getSentAs() => 'OrangeHRM System']);
        $this->getEmailService()->setMessageSubject('OrangeHRM Password Reset');
        $this->getEmailService()->setMessageBody($this->generatePasswordResetEmailBody($receiver, $resetCode));
        return $this->getEmailService()->sendEmail();
    }

    /**
     * @throws ServiceException
     */
    public function logPasswordResetRequest(User $user)
    {
        $identifier = $user->getUserName();
//        $resetCode = $this->generatePasswordResetCode($identifier);
        $resetCode = "reset code";

        $resetPassword = new ResetPassword();
        $resetPassword->setResetEmail($user->getEmployee()->getWorkEmail());
        $date = $this->getDateTimeHelper()->getNow();
        $date->format('Y-m-d H:i:s');
        $resetPassword->setResetRequestDate($date);
        $resetPassword->setResetCode($resetCode);
        $emailSent = $this->sendPasswordResetCodeEmail($user->getEmployee(), $resetCode);
        if (!$emailSent) {
            throw new ServiceException('Password reset email could not be sent.');
        }
    }

}
