<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software: you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with OrangeHRM.
 * If not, see <https://www.gnu.org/licenses/>.
 */

namespace OrangeHRM\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Entity\Decorator\DecoratorTrait;
use OrangeHRM\Entity\Decorator\EmployeeAttachmentDecorator;

/**
 * @method EmployeeAttachmentDecorator getDecorator()
 *
 * @ORM\Table(name="hs_hr_emp_attachment")
 * @ORM\Entity
 */
class EmployeeAttachment
{
    use DecoratorTrait;
    use DateTimeHelperTrait;

    public const SCREEN_PERSONAL_DETAILS = 'personal';
    public const SCREEN_CONTACT_DETAILS = 'contact';
    public const SCREEN_EMERGENCY_CONTACTS = 'emergency';
    public const SCREEN_DEPENDENTS = 'dependents';
    public const SCREEN_IMMIGRATION = 'immigration';
    public const SCREEN_QUALIFICATIONS = 'qualifications';
    public const SCREEN_TAX_EXEMPTIONS = 'tax';
    public const SCREEN_SALARY = 'salary';
    public const SCREEN_JOB = 'job';
    public const SCREEN_JOB_CONTRACT = 'contract';
    public const SCREEN_REPORT_TO = 'report-to';
    public const SCREEN_MEMBERSHIP = 'membership';

    public const SCREENS = [
        self::SCREEN_PERSONAL_DETAILS,
        self::SCREEN_CONTACT_DETAILS,
        self::SCREEN_EMERGENCY_CONTACTS,
        self::SCREEN_DEPENDENTS,
        self::SCREEN_IMMIGRATION,
        self::SCREEN_QUALIFICATIONS,
        self::SCREEN_TAX_EXEMPTIONS,
        self::SCREEN_SALARY,
        self::SCREEN_JOB,
        self::SCREEN_REPORT_TO,
        self::SCREEN_MEMBERSHIP,
    ];

    /**
     * @var Employee
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\Employee", inversedBy="attachments", cascade={"persist"})
     * @ORM\JoinColumn(name="emp_number", referencedColumnName="emp_number")
     */
    private Employee $employee;

    /**
     * @var int
     *
     * @ORM\Column(name="eattach_id", type="integer", options={"default" : 0})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private int $attachId = 0;

    /**
     * @var string|null
     *
     * @ORM\Column(name="eattach_desc", type="string", length=200, nullable=true)
     */
    private ?string $description = null;

    /**
     * @var string|null
     *
     * @ORM\Column(name="eattach_filename", type="string", length=100, nullable=true)
     */
    private ?string $filename = null;

    /**
     * @var int|null
     *
     * @ORM\Column(name="eattach_size", type="integer", nullable=true, options={"default" : 0})
     */
    private ?int $size = 0;

    /**
     * @var string|resource
     *
     * @ORM\Column(name="eattach_attachment", type="blob", nullable=true)
     */
    private $attachment = null;

    /**
     * @var string|null
     *
     * @ORM\Column(name="eattach_type", type="string", length=200, nullable=true)
     */
    private ?string $fileType = null;

    /**
     * @var string
     *
     * @ORM\Column(name="screen", type="string", length=100, nullable=true, options={"default" : ""})
     */
    private string $screen = "";

    /**
     * @var int|null
     *
     * @ORM\Column(name="attached_by", type="integer", nullable=true)
     */
    private ?int $attachedBy = null;

    /**
     * @var string|null
     *
     * @ORM\Column(name="attached_by_name", type="string", length=200, nullable=true)
     */
    private ?string $attachedByName = null;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="attached_time", type="datetime")
     */
    private DateTime $attachedTime;

    public function __construct()
    {
        $this->attachedTime = $this->getDateTimeHelper()->getNow();
    }

    /**
     * @return Employee
     */
    public function getEmployee(): Employee
    {
        return $this->employee;
    }

    /**
     * @param Employee $employee
     */
    public function setEmployee(Employee $employee): void
    {
        $this->employee = $employee;
    }

    /**
     * @return int
     */
    public function getAttachId(): int
    {
        return $this->attachId;
    }

    /**
     * @param int $attachId
     */
    public function setAttachId(int $attachId): void
    {
        $this->attachId = $attachId;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return string|null
     */
    public function getFilename(): ?string
    {
        return $this->filename;
    }

    /**
     * @param string|null $filename
     */
    public function setFilename(?string $filename): void
    {
        $this->filename = $filename;
    }

    /**
     * @return int|null
     */
    public function getSize(): ?int
    {
        return $this->size;
    }

    /**
     * @param int|null $size
     */
    public function setSize(?int $size): void
    {
        $this->size = $size;
    }

    /**
     * @return resource|string
     */
    public function getAttachment()
    {
        return $this->attachment;
    }

    /**
     * @param resource|string $attachment
     */
    public function setAttachment(?string $attachment): void
    {
        $this->attachment = $attachment;
    }

    /**
     * @return string|null
     */
    public function getFileType(): ?string
    {
        return $this->fileType;
    }

    /**
     * @param string|null $fileType
     */
    public function setFileType(?string $fileType): void
    {
        $this->fileType = $fileType;
    }

    /**
     * @return string
     */
    public function getScreen(): string
    {
        return $this->screen;
    }

    /**
     * @param string $screen
     */
    public function setScreen(string $screen): void
    {
        $allowedScreens = array_merge(self::SCREENS, [self::SCREEN_JOB_CONTRACT]);
        if (!in_array($screen, $allowedScreens)) {
            throw new InvalidArgumentException('Invalid `screen`');
        }
        $this->screen = $screen;
    }

    /**
     * @return int|null
     */
    public function getAttachedBy(): ?int
    {
        return $this->attachedBy;
    }

    /**
     * @param int|null $attachedBy
     */
    public function setAttachedBy(?int $attachedBy): void
    {
        $this->attachedBy = $attachedBy;
    }

    /**
     * @return string|null
     */
    public function getAttachedByName(): ?string
    {
        return $this->attachedByName;
    }

    /**
     * @param string|null $attachedByName
     */
    public function setAttachedByName(?string $attachedByName): void
    {
        $this->attachedByName = $attachedByName;
    }

    /**
     * @return DateTime
     */
    public function getAttachedTime(): DateTime
    {
        return $this->attachedTime;
    }

    /**
     * @param DateTime $attachedTime
     */
    public function setAttachedTime(DateTime $attachedTime): void
    {
        $this->attachedTime = $attachedTime;
    }
}
