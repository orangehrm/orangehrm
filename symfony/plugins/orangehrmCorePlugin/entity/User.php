<?php

namespace OrangeHRM\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * User
 *
 * @ORM\Table(name="ohrm_user")
 * @ORM\Entity
 */
class User
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
    private int $id;

    /**
     * @var string
     *
     * @ORM\Column(name="user_name", type="string", length=40)
     */
    private string $userName;

    /**
     * @var string
     *
     * @ORM\Column(name="user_password", type="string", length=255)
     */
    private string $userPassword;

    /**
     * @var bool
     *
     * @ORM\Column(name="deleted", type="boolean")
     */
    private bool $deleted;

    /**
     * @var bool
     *
     * @ORM\Column(name="status", type="boolean")
     */
    private bool $status;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(name="date_entered", type="datetime", length=25, nullable=true)
     */
    private ?DateTime $dateEntered;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(name="date_modified", type="datetime", length=25, nullable=true)
     */
    private ?DateTime $dateModified;

    /**
     * @var int|null
     *
     * @ORM\Column(name="modified_user_id", type="integer", nullable=true)
     */
    private ?int $modifiedUserId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="created_by", type="integer", nullable=true)
     */
    private ?int $createdBy;

    /**
     * @var Employee|null
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\Employee", inversedBy="users")
     * @ORM\JoinColumn(name="emp_number", referencedColumnName="emp_number")
     */
    private ?Employee $employee;

    /**
     * @var UserRole
     *
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\UserRole")
     * @ORM\JoinColumn(name="user_role_id", referencedColumnName="id", nullable=false)
     */
    private UserRole $userRole;

    public function __construct()
    {
        $this->status = true;
        $this->deleted = false;
    }

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
     * @return bool
     */
    public function isDeleted(): bool
    {
        return $this->deleted;
    }

    /**
     * @param bool $deleted
     */
    public function setDeleted(bool $deleted): void
    {
        $this->deleted = $deleted;
    }

    /**
     * @return bool
     */
    public function getStatus(): bool
    {
        return $this->status;
    }

    /**
     * @param bool $status
     */
    public function setStatus(bool $status): void
    {
        $this->status = $status;
    }

    /**
     * @return DateTime|null
     */
    public function getDateEntered(): ?DateTime
    {
        return $this->dateEntered;
    }

    /**
     * @param DateTime|null $dateEntered
     */
    public function setDateEntered(?DateTime $dateEntered): void
    {
        $this->dateEntered = $dateEntered;
    }

    /**
     * @return DateTime|null
     */
    public function getDateModified(): ?DateTime
    {
        return $this->dateModified;
    }

    /**
     * @param DateTime|null $dateModified
     */
    public function setDateModified(?DateTime $dateModified): void
    {
        $this->dateModified = $dateModified;
    }

    /**
     * @return int|null
     */
    public function getModifiedUserId(): ?int
    {
        return $this->modifiedUserId;
    }

    /**
     * @param int|null $modifiedUserId
     */
    public function setModifiedUserId(?int $modifiedUserId): void
    {
        $this->modifiedUserId = $modifiedUserId;
    }

    /**
     * @return int|null
     */
    public function getCreatedBy(): ?int
    {
        return $this->createdBy;
    }

    /**
     * @param int|null $createdBy
     */
    public function setCreatedBy(?int $createdBy): void
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
     * @return int|null
     */
    public function getEmpNumber(): ?int
    {
        return $this->getEmployee() ? $this->getEmployee()->getEmpNumber() : null;
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
     * @return string
     * @since 4.x
     */
    public function getTextStatus()
    {
        if ($this->getStatus()) {
            return 'Enabled';
        } else {
            return 'Disabled';
        }
    }

    /**
     * @return string
     * @since 4.x
     */
    public function getIsAdmin()
    {
        if ($this->getUserRole()->getId() == User::ADMIN_USER_ROLE_ID) {
            return 'Yes';
        } else {
            return 'No';
        }
    }

    /**
     * @return string|null
     * @since 4.x
     */
    public function getUsergId()
    {
        if ($this->getUserRole()->getId() == User::ADMIN_USER_ROLE_ID) {
            return 'USG001';
        } else {
            return null;
        }
    }

    /**
     * @return string|null
     * @since 4.x
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
