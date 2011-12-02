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
                ->from('SystemUser')
                ->where('user_name = ?', $username)
                ->andWhere('user_password = ?', $password)
                ->andWhere('deleted = 0');
        
      
        return $query->fetchOne();
    }

}

