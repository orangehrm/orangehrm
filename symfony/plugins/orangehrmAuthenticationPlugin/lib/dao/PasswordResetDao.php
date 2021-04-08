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
class PasswordResetDao extends BaseDao {

    /**
     * @param ResetPassword $resetPassword
     * @return bool
     * @throws DaoException
     */
    public function saveResetPasswordLog(ResetPassword $resetPassword) {
        try {
            $resetPassword->save();
            return true;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param $email
     * @return array|Doctrine_Record
     * @throws DaoException
     */
    public function getResetPasswordLogByEmail($email) {
        try {
            $q = Doctrine_Query::create()
                ->from("ResetPassword")
                ->where('reset_email = ?', $email)
                ->orderBy('reset_request_date DESC');

            $resetPassword = $q->fetchOne();
            return $resetPassword;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param $username
     * @param $newPassword
     * @return Doctrine_Collection
     * @throws DaoException
     */
    public function saveNewPrimaryPassword($username, $newPassword) {
        try {
            $query = Doctrine_Query::create()
                ->update('SystemUser')
                ->set('user_password', '?', $newPassword)
                ->where('user_name = ?', $username);

            return $query->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param $email
     * @return Doctrine_Collection
     * @throws DaoException
     */
    public function deletePasswordResetRequestsByEmail($email) {
        try {
            $query = Doctrine_Query::create()
                ->delete('ResetPassword')
                ->where('reset_email = ?', $email);
            return $query->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }
}

