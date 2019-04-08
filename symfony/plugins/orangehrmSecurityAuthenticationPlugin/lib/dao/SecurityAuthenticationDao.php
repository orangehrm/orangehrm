<?php

class SecurityAuthenticationDao extends BaseDao {

    /**
     * @param ResetPasswordLog $resetPasswordLog
     * @return bool
     * @throws DaoException
     */
    public function saveResetPasswordLog(ResetPasswordLog $resetPasswordLog) {

        try {
            if ($resetPasswordLog instanceof ResetPasswordLog) {

                $resetPasswordLog->save();
            }
            return true;
        } catch (Exception $e) {

            throw new DaoException($e->getMessage());
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
                ->from("ResetPasswordLog")
                ->where('reset_email = ?', $email)
                ->andWhere('status = ?', 0);

            $resetPasswordLog = $q->fetchOne();
            return $resetPasswordLog;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
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
            throw new DaoException($e->getMessage());
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
                ->delete('ResetPasswordLog')
                ->where('reset_email = ?', $email);
            return $query->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }


}

