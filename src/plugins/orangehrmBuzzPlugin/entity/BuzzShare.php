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
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Entity\Decorator\BuzzShareDecorator;
use OrangeHRM\Entity\Decorator\DecoratorTrait;

/**
 * @method BuzzShareDecorator getDecorator()
 *
 * @ORM\Table(name="ohrm_buzz_share")
 * @ORM\Entity
 */
class BuzzShare
{
    use DecoratorTrait;
    use DateTimeHelperTrait;

    public const TYPE_POST = 0;
    public const TYPE_SHARE = 1;

    public const POST_TYPE_TEXT = 'text';
    public const POST_TYPE_PHOTO = 'photo';
    public const POST_TYPE_VIDEO = 'video';

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="bigint")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @var BuzzPost
     *
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\BuzzPost")
     * @ORM\JoinColumn(name="post_id", referencedColumnName="id")
     */
    private BuzzPost $post;

    /**
     * @var Employee
     *
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\Employee")
     * @ORM\JoinColumn(name="employee_number", referencedColumnName="emp_number")
     */
    private Employee $employee;

    /**
     * @var int
     *
     * @ORM\Column(name="number_of_likes", type="integer", nullable=true)
     */
    private int $numOfLikes = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="number_of_comments", type="integer", nullable=true)
     */
    private int $numOfComments = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="type", type="smallint")
     */
    private int $type = self::TYPE_POST;

    /**
     * @var string|null
     *
     * @ORM\Column(name="text", type="text", nullable=true)
     */
    private ?string $text = null;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="share_utc_time", type="datetime")
     */
    private DateTime $createdAtUtc;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(name="updated_utc_time", type="datetime", nullable=true)
     */
    private ?DateTime $updatedAtUtc = null;

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
     * @return BuzzPost
     */
    public function getPost(): BuzzPost
    {
        return $this->post;
    }

    /**
     * @param BuzzPost $post
     */
    public function setPost(BuzzPost $post): void
    {
        $this->post = $post;
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
    public function getNumOfLikes(): int
    {
        return $this->numOfLikes;
    }

    /**
     * @param int $numOfLikes
     */
    public function setNumOfLikes(int $numOfLikes): void
    {
        $this->numOfLikes = $numOfLikes;
    }

    /**
     * @return int
     */
    public function getNumOfComments(): int
    {
        return $this->numOfComments;
    }

    /**
     * @param int $numOfComments
     */
    public function setNumOfComments(int $numOfComments): void
    {
        $this->numOfComments = $numOfComments;
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @param int $type
     */
    public function setType(int $type): void
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
    public function getCreatedAtUtc(): DateTime
    {
        return $this->createdAtUtc;
    }

    public function setCreatedAtUtc(): void
    {
        $this->createdAtUtc = $this->getDateTimeHelper()->getNowInUTC();
    }

    /**
     * @return DateTime|null
     */
    public function getUpdatedAtUtc(): ?DateTime
    {
        return $this->updatedAtUtc;
    }

    public function setUpdatedAtUtc(): void
    {
        $this->updatedAtUtc = $this->getDateTimeHelper()->getNowInUTC();
    }
}
