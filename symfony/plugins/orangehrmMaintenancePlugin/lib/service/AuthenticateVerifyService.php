<?php

/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 6/9/18
 * Time: 12:33 PM
 */
class AuthenticateVerifyService
{

    public function isCurrentPassword($userId, $password)
    {
        $systemUser = $this->getSystemUserDao()->getSystemUser($userId);
        if (!($systemUser instanceof SystemUser)) {
            return false;
        }
        $hash = $systemUser->getUserPassword();

        if ($this->checkPasswordHash($password, $hash)) {
            return true;
        } else if ($this->checkForOldHash($password, $hash)) {
            return true;
        }
        return false;
    }

    public function getSystemUserDao()
    {
        if (empty($this->systemUserDao)) {
            $this->systemUserDao = new SystemUserDao();
        }
        return $this->systemUserDao;
    }

    public function checkPasswordHash($password, $hash)
    {
        return $this->getPasswordHasher()->verify($password, $hash);
    }

    public function getPasswordHasher()
    {
        if (empty($this->passwordHasher)) {
            $this->passwordHasher = new PasswordHash();
        }
        return $this->passwordHasher;
    }

    public function checkForOldHash($password, $hash)
    {
        $valid = false;
        if ($hash == md5($password)) {
            $valid = true;
        }
        return $valid;
    }
}