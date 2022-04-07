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
 * @ORM\Table(name="ohrm_reset_password")
 * @ORM\Entity
 */
class ResetPasswordRequest
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
     * @var string
     *
     * @ORM\Column(name="reset_email", type="string", length=60)
     */
    private string $resetEmail;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="reset_request_date", type="datetime")
     */
    private DateTime $resetRequestDate;

    /**
     * @var string
     *
     * @ORM\Column(name="reset_code", type="string", length=200)
     */
    private string $resetCode;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getResetEmail(): string
    {
        return $this->resetEmail;
    }

    /**
     * @param string $resetEmail
     */
    public function setResetEmail(string $resetEmail): void
    {
        $this->resetEmail = $resetEmail;
    }

    /**
     * @return DateTime
     */
    public function getResetRequestDate(): DateTime
    {
        return $this->resetRequestDate;
    }

    /**
     * @param DateTime $resetRequestDate
     */
    public function setResetRequestDate(DateTime $resetRequestDate): void
    {
        $this->resetRequestDate = $resetRequestDate;
    }

    /**
     * @return string
     */
    public function getResetCode(): string
    {
        return $this->resetCode;
    }

    /**
     * @param string $resetCode
     */
    public function setResetCode(string $resetCode): void
    {
        $this->resetCode = $resetCode;
    }
}
