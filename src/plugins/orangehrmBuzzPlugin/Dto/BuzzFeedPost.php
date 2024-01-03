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

namespace OrangeHRM\Buzz\Dto;

use DateTime;
use OrangeHRM\Buzz\Traits\Service\BuzzServiceTrait;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Entity\BuzzShare;

class BuzzFeedPost
{
    use DateTimeHelperTrait;
    use BuzzServiceTrait;

    private array $employee;
    private array $postOwner;
    private int $type;
    private array $share;
    private array $post;
    private int $photoCount;
    private bool $liked;
    private ?string $videoLink;

    public function __construct(
        int $empNumber,
        string $lastName,
        string $firstName,
        string $middleName,
        ?string $employeeId,
        ?int $terminationId,
        int $shareId,
        int $shareType,
        DateTime $shareCreatedAt,
        int $numOfLikes,
        int $numOfComments,
        int $numOfShares,
        int $liked,
        ?string $shareText,
        int $postId,
        ?string $postText,
        DateTime $postCreatedAt,
        int $postOwnerEmpNumber,
        string $postOwnerLastName,
        string $postOwnerFirstName,
        string $postOwnerMiddleName,
        ?string $postOwnerEmployeeId,
        ?int $postOwnerTerminationId,
        int $photoCount,
        ?string $videoLink
    ) {
        $this->employee = [
            'empNumber' => $empNumber,
            'lastName' => $lastName,
            'firstName' => $firstName,
            'middleName' => $middleName,
            'employeeId' => $employeeId,
            'terminationId' => $terminationId,
        ];
        $this->type = $shareType;
        $this->share = [
            'shareId' => $shareId,
            'createdAt' => $shareCreatedAt,
            'text' => $shareText,
            'numOfLikes' => $numOfLikes,
            'numOfComments' => $numOfComments,
            'numOfShares' => $numOfShares,
        ];
        $this->liked = $liked > 0;
        $this->post = [
            'postId' => $postId,
            'text' => $postText,
            'createdAt' => $postCreatedAt,
        ];
        $this->postOwner = [
            'empNumber' => $postOwnerEmpNumber,
            'lastName' => $postOwnerLastName,
            'firstName' => $postOwnerFirstName,
            'middleName' => $postOwnerMiddleName,
            'employeeId' => $postOwnerEmployeeId,
            'terminationId' => $postOwnerTerminationId,
        ];
        $this->photoCount = $photoCount;
        $this->videoLink = $videoLink;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->share['shareId'];
    }

    /**
     * @return int
     */
    public function getPostId(): int
    {
        return $this->post['postId'];
    }

    /**
     * @return string|null
     */
    public function getText(): ?string
    {
        if ($this->type === BuzzShare::TYPE_POST) {
            return $this->post['text'];
        }
        return $this->share['text'];
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        if ($this->hasVideo()) {
            return BuzzShare::POST_TYPE_VIDEO;
        } elseif ($this->hasPhotos()) {
            return BuzzShare::POST_TYPE_PHOTO;
        }
        return BuzzShare::POST_TYPE_TEXT;
    }

    /**
     * @return array<string, int>
     */
    public function getStats(): array
    {
        return [
            'numOfLikes' => $this->share['numOfLikes'],
            'numOfComments' => $this->share['numOfComments'],
            'numOfShares' => $this->type === BuzzShare::TYPE_POST ? $this->share['numOfShares'] : null,
        ];
    }

    /**
     * @return string|null in Y-m-d format
     */
    public function getCreatedDate(): ?string
    {
        if ($this->type === BuzzShare::TYPE_POST) {
            return $this->getDateTimeHelper()->formatDate($this->post['createdAt']);
        }
        return $this->getDateTimeHelper()->formatDate($this->share['createdAt']);
    }

    /**
     * @return string|null in H:i format
     */
    public function getCreatedTime(): ?string
    {
        if ($this->type === BuzzShare::TYPE_POST) {
            return $this->getDateTimeHelper()->formatDateTimeToTimeString($this->post['createdAt']);
        }
        return $this->getDateTimeHelper()->formatDateTimeToTimeString($this->share['createdAt']);
    }

    /**
     * @return array<string, string>|null
     */
    public function getOriginalPost(): ?array
    {
        if ($this->type === BuzzShare::TYPE_POST) {
            return null;
        }
        return [
            'text' => $this->post['text'],
            'employee' => $this->postOwner,
            'createdDate' => $this->getDateTimeHelper()->formatDate($this->post['createdAt']),
            'createdTime' => $this->getDateTimeHelper()->formatDateTimeToTimeString($this->post['createdAt']),
        ];
    }

    /**
     * @return array<string, string>
     */
    public function getEmployee(): array
    {
        return $this->employee;
    }

    /**
     * @return bool
     */
    public function isLiked(): bool
    {
        return $this->liked;
    }

    /**
     * @return string|null
     */
    public function getVideoLink(): ?string
    {
        return $this->videoLink;
    }

    /**
     * @return int[]|null
     */
    public function getPhotoIds(): ?array
    {
        if ($this->hasPhotos()) {
            return $this->getBuzzService()
                ->getBuzzDao()
                ->getBuzzPhotoIdsByPostId($this->post['postId']);
        }
        return null;
    }

    /**
     * @return bool
     */
    public function hasPhotos(): bool
    {
        return $this->photoCount > 0;
    }

    /**
     * @return bool
     */
    public function hasVideo(): bool
    {
        return $this->videoLink !== null && !empty(trim($this->videoLink));
    }

    /**
     * @return array
     */
    public function getPermission(): array
    {
        return [
            'canUpdate' => $this->getBuzzService()->canUpdateBuzzFeedPost($this->employee['empNumber']),
            'canDelete' => $this->getBuzzService()->canDeleteBuzzFeedPost($this->employee['empNumber']),
        ];
    }
}
