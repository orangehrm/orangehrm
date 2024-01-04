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

namespace OrangeHRM\Buzz\Service;

use OrangeHRM\Buzz\Dao\BuzzDao;
use OrangeHRM\Buzz\Dao\BuzzLikeDao;
use OrangeHRM\Core\Traits\UserRoleManagerTrait;

class BuzzService
{
    use UserRoleManagerTrait;

    private BuzzDao $buzzDao;
    private BuzzLikeDao $buzzLikeDao;
    private array $buzzFeedPostPermissionCache = [];
    private array $buzzCommentPermissionCache = [];

    /**
     * @return BuzzDao
     */
    public function getBuzzDao(): BuzzDao
    {
        return $this->buzzDao ??= new BuzzDao();
    }

    /**
     * @return BuzzLikeDao
     */
    public function getBuzzLikeDao(): BuzzLikeDao
    {
        return $this->buzzLikeDao ??= new BuzzLikeDao();
    }

    /**
     * @param int $postOwnerEmpNumber
     * @return bool
     */
    public function canUpdateBuzzFeedPost(int $postOwnerEmpNumber): bool
    {
        $self = $this->getUserRoleManagerHelper()->isSelfByEmpNumber($postOwnerEmpNumber);
        if (!isset($this->buzzFeedPostPermissionCache[$self])) {
            $this->buzzFeedPostPermissionCache[$self] = $this->getUserRoleManager()
                ->getDataGroupPermissions('buzz_post', [], [], $self);
        }
        return $this->buzzFeedPostPermissionCache[$self]->canUpdate();
    }

    /**
     * @param int $postOwnerEmpNumber
     * @return bool
     */
    public function canDeleteBuzzFeedPost(int $postOwnerEmpNumber): bool
    {
        $self = $this->getUserRoleManagerHelper()->isSelfByEmpNumber($postOwnerEmpNumber);
        if (!isset($this->buzzFeedPostPermissionCache[$self])) {
            $this->buzzFeedPostPermissionCache[$self] = $this->getUserRoleManager()
                ->getDataGroupPermissions('buzz_post', [], [], $self);
        }
        return $this->buzzFeedPostPermissionCache[$self]->canDelete();
    }

    /**
     * @param int $commentOwnerEmpNumber
     * @return bool
     */
    public function canUpdateBuzzComment(int $commentOwnerEmpNumber): bool
    {
        $self = $this->getUserRoleManagerHelper()->isSelfByEmpNumber($commentOwnerEmpNumber);
        if (!isset($this->buzzCommentPermissionCache[$self])) {
            $this->buzzCommentPermissionCache[$self] = $this->getUserRoleManager()
                ->getDataGroupPermissions('buzz_comment', [], [], $self);
        }
        return $this->buzzCommentPermissionCache[$self]->canUpdate();
    }

    /**
     * @param int $commentOwnerEmpNumber
     * @return bool
     */
    public function canDeleteBuzzComment(int $commentOwnerEmpNumber): bool
    {
        $self = $this->getUserRoleManagerHelper()->isSelfByEmpNumber($commentOwnerEmpNumber);
        if (!isset($this->buzzCommentPermissionCache[$self])) {
            $this->buzzCommentPermissionCache[$self] = $this->getUserRoleManager()
                ->getDataGroupPermissions('buzz_comment', [], [], $self);
        }
        return $this->buzzCommentPermissionCache[$self]->canDelete();
    }
}
