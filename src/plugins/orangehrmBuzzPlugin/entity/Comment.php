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

use Doctrine\ORM\Mapping as ORM;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use DateTime;

/**
 * @ORM\Table(name="ohrm_buzz_comment")
 * @ORM\Entity
 */
class Comment
{
    use DateTimeHelperTrait;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @var Share
     *
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\Share")
     * @ORM\JoinColumn(name="share_id", referencedColumnName="id")
     */
    private Share $share;

    /**
     * @var Employee|null
     *
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\Employee", inversedBy="comment", cascade={"persist"})
     * @ORM\JoinColumn(name="employee_number", referencedColumnName="emp_number")
     */
    private ?Employee $employee = null;

    /**
     * @var int|null
     *
     * @ORM\Column(name="number_of_likes", type="int", length=6, nullable=true)
     */
    private ?int $numOfLikes = null;

    /**
     * @var string|null
     *
     * @ORM\Column(name="comment_text",  type="string", nullable=true)
     */
    private ?string $text = null;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="comment_time", type="datetime")
     */
    private DateTime $commentTime;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime")
     */
    private DateTime $updatedAt;

    public function __construct()
    {
        $this->setUpdatedAt($this->getDateTimeHelper()->getNow());
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
     * @return Share
     */
    public function getShare(): Share
    {
        return $this->share;
    }

    /**
     * @param Share $share
     */
    public function setShare(Share $share): void
    {
        $this->share = $share;
    }

    /**
     * @return int|null
     */
    public function getNumOfLikes(): ?int
    {
        return $this->numOfLikes;
    }

    /**
     * @param int|null $numOfLikes
     */
    public function setNumOfLikes(?int $numOfLikes): void
    {
        $this->numOfLikes = $numOfLikes;
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
     * @return string|null
     */
    public function getText(): ?string
    {
        return $this->text;
    }

    /**
     * @param string|null $text
     */
    public function setText(?string $text): void
    {
        $this->text = $text;
    }

    /**
     * @return DateTime
     */
    public function getCommentTime(): DateTime
    {
        return $this->commentTime;
    }

    /**
     * @param DateTime $commentTime
     */
    public function setCommentTime(DateTime $commentTime): void
    {
        $this->commentTime = $commentTime;
    }

    /**
     * @return DateTime
     */
    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @param DateTime $updatedAt
     */
    public function setUpdatedAt(DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }
}
