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

namespace OrangeHRM\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="ohrm_login")
 * @ORM\Entity
 */
class LoginLog
{
    /**
     * @var int
     *
     * @ORM\Column(name="id",type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @var int
     *
     * @ORM\Column(name="user_id",type="integer")
     */
    private int $userId;

    /**
     * @var string
     *
     * @ORM\Column(name="user_name", type="string", length=255, nullable=true)
     */
    private string $userName;

    /**
     * @var string
     *
     * @ORM\Column(name="user_role_name", type="string", length=100)
     */
    private string $userRoleName;

    /**
     * @var int
     *
     * @ORM\Column(name="user_role_predefined",type="integer")
     */
    private int $userRolePredefined;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="login_time",type="time")
     */
    private DateTime $loginTime;

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * @param int $userId
     */
    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

    /**
     * @return string
     */
    public function getUserName(): string
    {
        return $this->userName;
    }

    /**
     * @param string $userName
     */
    public function setUserName(string $userName): void
    {
        $this->userName = $userName;
    }

    /**
     * @return string
     */
    public function getUserRoleName(): string
    {
        return $this->userRoleName;
    }

    /**
     * @param string $userRoleName
     */
    public function setUserRoleName(string $userRoleName): void
    {
        $this->userRoleName = $userRoleName;
    }

    /**
     * @return int
     */
    public function getUserRolePredefined(): int
    {
        return $this->userRolePredefined;
    }

    /**
     * @param int $userRolePredefined
     */
    public function setUserRolePredefined(int $userRolePredefined): void
    {
        $this->userRolePredefined = $userRolePredefined;
    }

    /**
     * @return DateTime
     */
    public function getLoginTime(): DateTime
    {
        return $this->loginTime;
    }

    /**
     * @param DateTime $loginTime
     */
    public function setLoginTime(DateTime $loginTime): void
    {
        $this->loginTime = $loginTime;
    }
}
