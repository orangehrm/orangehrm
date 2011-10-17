<?php

class AuthenticationDao extends BaseDao {

    /**
     *
     * @param string $username
     * @param string $password
     * @return Users 
     */
    public function getCredentials($username, $password) {
        $query = Doctrine_Query::create()
                ->from('Users')
                ->where('user_name = ?', $username)
                ->andWhere('user_password = ?', $password);

        return $query->fetchOne();
    }

}

