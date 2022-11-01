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
 * @ORM\Table(name="ohrm_buzz_share")
 * @ORM\Entity
 */
class BuzzShare
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
     * @var Post
     *
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\Post")
     * @ORM\JoinColumn(name="post_id", referencedColumnName="id")
     */
    private Post $post;

    /**
     * @var Employee|null
     *
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\Employee")
     * @ORM\JoinColumn(name="employee_number", referencedColumnName="emp_number", nullable=true)
     */
    private ?Employee $employee = null;

    /**
     * @var int|null
     *
     * @ORM\Column(name="number_of_likes", type="int", length=6, nullable=true)
     */
    private ?int $numOfLikes = null;

    /**
     * @var int|null
     *
     * @ORM\Column(name="number_of_comments", type="int", length=6, nullable=true)
     */
    private ?int $numOfComments = null;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="share_time", type="datetime")
     */
    private DateTime $shareTime;

    /**
     * @var int|null
     *
     * @ORM\Column(name="type", type="int", length=1, nullable=true)
     */
    private ?int $type = null;

    /**
     * @var string|null
     *
     * @ORM\Column(name="text", type="string", nullable=true)
     */
    private ?string $text = null;

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
     * @return Post
     */
    public function getPost(): Post
    {
        return $this->post;
    }

    /**
     * @param Post $post
     */
    public function setPost(Post $post): void
    {
        $this->post = $post;
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
     * @return int|null
     */
    public function getNumOfComments(): ?int
    {
        return $this->numOfComments;
    }

    /**
     * @param int|null $numOfComments
     */
    public function setNumOfComments(?int $numOfComments): void
    {
        $this->numOfComments = $numOfComments;
    }

    /**
     * @return DateTime
     */
    public function getShareTime(): DateTime
    {
        return $this->shareTime;
    }

    /**
     * @param DateTime $shareTime
     */
    public function setShareTime(DateTime $shareTime): void
    {
        $this->shareTime = $shareTime;
    }

    /**
     * @return int|null
     */
    public function getType(): ?int
    {
        return $this->type;
    }

    /**
     * @param int|null $type
     */
    public function setType(?int $type): void
    {
        $this->type = $type;
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
