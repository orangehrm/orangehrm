<?php

namespace OrangeHRM\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * SystemUser
 *
 * @ORM\Table(name="ohrm_user")
 * @ORM\Entity
 */
class SystemUser
{
    public const NO_OF_RECORDS_PER_PAGE = 50;
    public const ADMIN_USER_ROLE_ID = 1;
    public const ENABLED = 1;
    public const DISABLED = 0;
    public const DELETED = 1;
    public const UNDELETED = 0;
    public const USER_TYPE_ADMIN = "Admin"; // TODO: Check the needness when new user roles are implemented
    public const USER_TYPE_EMPLOYEE = "Employee"; // TODO: Check the needness when new user roles are implemented
    public const USER_TYPE_SUPERVISOR = "Supervisor"; // TODO: Check the needness when new user roles are implemented

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="user_name", type="string", length=40)
     */
    private $userName;

    /**
     * @var string
     *
     * @ORM\Column(name="user_password", type="string", length=255)
     */
    private $userPassword;

    /**
     * @var int
     *
     * @ORM\Column(name="deleted", type="integer", length=1)
     */
    private $deleted;

    /**
     * @var int
     *
     * @ORM\Column(name="status", type="integer", length=1)
     */
    private $status;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="date_entered", type="datetime", length=25)
     */
    private $dateEntered;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="date_modified", type="datetime", length=25)
     */
    private $dateModified;

    /**
     * @var int
     *
     * @ORM\Column(name="modified_user_id", type="integer")
     */
    private $modifiedUserId;

    /**
     * @var int
     *
     * @ORM\Column(name="created_by", type="integer")
     */
    private $createdBy;

    /**
     * @var Employee|null
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\Employee", inversedBy="systemUsers")
     * @ORM\JoinColumn(name="emp_number", referencedColumnName="emp_number")
     */
    private $employee;

    /**
     * @var UserRole
     *
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\UserRole")
     * @ORM\JoinColumn(name="user_role_id", referencedColumnName="id", nullable=false)
     */
    private $userRole;

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
    public function getUserPassword(): string
    {
        return $this->userPassword;
    }

    /**
     * @param string $userPassword
     */
    public function setUserPassword(string $userPassword): void
    {
        $this->userPassword = $userPassword;
    }

    /**
     * @return int
     */
    public function getDeleted(): int
    {
        return $this->deleted;
    }

    /**
     * @param int $deleted
     */
    public function setDeleted(int $deleted): void
    {
        $this->deleted = $deleted;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @param int $status
     */
    public function setStatus(int $status): void
    {
        $this->status = $status;
    }

    /**
     * @return DateTime
     */
    public function getDateEntered(): DateTime
    {
        return $this->dateEntered;
    }

    /**
     * @param DateTime $dateEntered
     */
    public function setDateEntered(DateTime $dateEntered): void
    {
        $this->dateEntered = $dateEntered;
    }

    /**
     * @return DateTime
     */
    public function getDateModified(): DateTime
    {
        return $this->dateModified;
    }

    /**
     * @param DateTime $dateModified
     */
    public function setDateModified(DateTime $dateModified): void
    {
        $this->dateModified = $dateModified;
    }

    /**
     * @return int
     */
    public function getModifiedUserId(): int
    {
        return $this->modifiedUserId;
    }

    /**
     * @param int $modifiedUserId
     */
    public function setModifiedUserId(int $modifiedUserId): void
    {
        $this->modifiedUserId = $modifiedUserId;
    }

    /**
     * @return int
     */
    public function getCreatedBy(): int
    {
        return $this->createdBy;
    }

    /**
     * @param int $createdBy
     */
    public function setCreatedBy(int $createdBy): void
    {
        $this->createdBy = $createdBy;
    }

    /**
     * @return Employee|null
     */
    public function getEmployee(): ?Employee
    {
        return $this->employee;
    }

    /**
     * @param Employee|null $employee
     */
    public function setEmployee(?Employee $employee): void
    {
        $this->employee = $employee;
    }

    /**
     * @return UserRole
     */
    public function getUserRole(): UserRole
    {
        return $this->userRole;
    }

    /**
     * @param UserRole $userRole
     */
    public function setUserRole(UserRole $userRole): void
    {
        $this->userRole = $userRole;
    }

    /**
     * @since 4.x
     * @return string
     */
    public function getTextStatus()
    {
        if ($this->getStatus() == '1') {
            return 'Enabled';
        } else {
            return 'Disabled';
        }
    }

    /**
     * @since 4.x
     * @return string
     */
    public function getIsAdmin()
    {
        if ($this->getUserRole()->getId() == SystemUser::ADMIN_USER_ROLE_ID) {
            return 'Yes';
        } else {
            return 'No';
        }
    }

    /**
     * @since 4.x
     * @return string|null
     */
    public function getUsergId()
    {
        if ($this->getUserRole()->getId() == SystemUser::ADMIN_USER_ROLE_ID) {
            return 'USG001';
        } else {
            return null;
        }
    }

    /**
     * @since 4.x
     * @return string|null
     */
    public function getName()
    {
        if ($this->getEmployee()->getFirstName() != '') {
            return $this->getEmployee()->getFirstName();
        } else {
            return $this->getUserRole()->getName();
        }
    }
}
