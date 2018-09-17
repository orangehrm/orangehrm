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
 * Boston, MA 02110-1301, USA
 */
class AuthenticateVerifyService
{

    /**
     * @param $userId
     * @param $password
     * @return bool
     * @throws DaoException
     */
    public function isCurrentPassword($userId, $password)
    {
        $systemUser = $this->getSystemUserDao()->getSystemUser($userId);
        if (!($systemUser instanceof SystemUser)) {
            return false;
        }
        $hash = $systemUser->getUserPassword();
        return $this->checkPasswordHash($password, $hash);;
    }

    /**
     * @return SystemUserDao
     */
    public function getSystemUserDao()
    {
        if (empty($this->systemUserDao)) {
            $this->systemUserDao = new SystemUserDao();
        }
        return $this->systemUserDao;
    }

    /**
     * @param $password
     * @param $hash
     * @return bool
     */
    public function checkPasswordHash($password, $hash)
    {
        return $this->getPasswordHasher()->verify($password, $hash);
    }

    /**
     * @return PasswordHash
     */
    public function getPasswordHasher()
    {
        if (empty($this->passwordHasher)) {
            $this->passwordHasher = new PasswordHash();
        }
        return $this->passwordHasher;
    }

    /**
     * @param $password
     * @param $hash
     * @return bool
     */
    public function checkForOldHash($password, $hash)
    {
        $valid = false;
        if ($hash == md5($password)) {
            $valid = true;
        }
        return $valid;
    }
}
