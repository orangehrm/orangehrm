<?php

namespace OrangeHRM\Authentication\Utils;

use OrangeHRM\Entity\UserRole;

class RoleUtils
{
    public const ROLE_ADMIN = 'Admin';
    public const ROLE_ESS = 'ESS';

    public static function admin(): UserRole
    {
        $role = new UserRole();
        $role->setIsAssignable(true);
        $role->setName(self::ROLE_ADMIN);
        $role->setDisplayName(self::ROLE_ADMIN);
        $role->setIsPredefined(true);
        return $role;
    }

    public static function ess(): UserRole
    {
        $role = new UserRole();
        $role->setIsAssignable(true);
        $role->setName(self::ROLE_ESS);
        $role->setDisplayName(self::ROLE_ESS);
        $role->setIsPredefined(true);
        return $role;
    }
}