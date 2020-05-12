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
 * Boston, MA 02110-1301, USA
 */

class viewNotificationComponent extends sfComponent
{
    /**
     * @var BuzzService|null
     */
    protected $buzzService = null;
    /**
     * @var BuzzNotificationService|null
     */
    protected $buzzNotificationService = null;

    /**
     * @var BuzzConfigService|null
     */
    protected $buzzConfigService = null;

    /**
     * @return BuzzNotificationService
     */
    public function getBuzzNotificationService(): BuzzNotificationService
    {
        if (!($this->buzzNotificationService instanceof BuzzNotificationService)) {
            $this->buzzNotificationService = new BuzzNotificationService();
        }
        return $this->buzzNotificationService;
    }

    /**
     * @param BuzzNotificationService $buzzNotificationService
     */
    public function setBuzzNotificationService(BuzzNotificationService $buzzNotificationService)
    {
        $this->buzzNotificationService = $buzzNotificationService;
    }

    /**
     * @return BuzzService
     */
    protected function getBuzzService(): BuzzService
    {
        if (!$this->buzzService instanceof BuzzService) {
            $this->buzzService = new BuzzService();
        }
        return $this->buzzService;
    }

    /**
     * @return BuzzConfigService
     */
    protected function getBuzzConfigService()
    {
        if (!$this->buzzConfigService instanceof BuzzConfigService) {
            $this->buzzConfigService = new BuzzConfigService();
        }
        return $this->buzzConfigService;
    }

    public function execute($request)
    {
        $empNumber = $this->getUser()->getEmployeeNumber();
        $buzzNotificationMetadata = $this->getBuzzNotificationService()->getBuzzNotificationMetadata($empNumber);
        $since = new DateTime($this->getBuzzConfigService()->getMaxNotificationPeriod());
        $since = $since instanceof DateTime ? $since : null;
        if ($buzzNotificationMetadata instanceof BuzzNotificationMetadata) {
            if (!is_null($buzzNotificationMetadata->getLastClearNotifications())) {
                $since = new DateTime($buzzNotificationMetadata->getLastClearNotifications());
            }
        }

        $newShares = $this->getBuzzNotificationService()->getSharesExceptEmployeeNumberSince($empNumber, $since);
        $newCommentsOnEmployeePosts = $this->getBuzzNotificationService()->getCommentsOnEmployeePostsSince($empNumber, $since);
        $newLikesOnEmployeePosts = $this->getBuzzNotificationService()->getLikesOnEmployeePostsSince($empNumber, $since);
        $newLikesOnEmployeeComments = $this->getBuzzNotificationService()->getLikesOnEmployeeCommentsSince($empNumber, $since);
        $newSharesOfEmployeePosts = $this->getBuzzNotificationService()->getSharesOfEmployeePostsSince($empNumber, $since);

        $this->notifications = [];

        $this->prepareNotifications(
            $empNumber,
            $newShares,
            $newCommentsOnEmployeePosts,
            $newLikesOnEmployeePosts,
            $newLikesOnEmployeeComments,
            $newSharesOfEmployeePosts
        );

        $sortNotifications = array_column($this->notifications, 'time');
        array_multisort($sortNotifications, SORT_DESC, $this->notifications);

        $this->batchHide = false;
        $this->empty = false;
        $this->lastNotificationViewTime = null;
        if (!empty($this->notifications)) {
            $buzzNotificationMetadata = $this->getBuzzNotificationService()->getBuzzNotificationMetadata($empNumber);
            if ($buzzNotificationMetadata instanceof BuzzNotificationMetadata) {
                $this->lastNotificationViewTime = $buzzNotificationMetadata->getUserLastNotificationViewTime();
                if (!is_null($this->lastNotificationViewTime) && ((new DateTime($sortNotifications[0])) < (new DateTime($this->lastNotificationViewTime)))) {
                    $this->batchHide = true;
                }
            }
        } else {
            $this->batchHide = true;
            $this->empty = true;
        }

        $this->deleteOrEditShareForm = new DeleteOrEditShareForm();
        $this->actionValidateForm = new ActionValidatingForm();
        $this->likedOrSharedEmployeeForm = new LikedOrSharedEmployeeForm();
    }

    protected function prepareNotifications(
        $empNumber,
        $newShares,
        $newCommentsOnEmployeePosts,
        $newLikesOnEmployeePosts,
        $newLikesOnEmployeeComments,
        $newSharesOfEmployeePosts
    )
    {
        foreach ($newShares as $activity) {
            if ($activity instanceof Share) {
                $employee = $activity->getEmployeePostShared();
                if ($employee instanceof Employee && is_null($employee->getPurgedAt())) {
                    array_push($this->notifications, [
                        "message" => $employee->getFirstAndLastNames() . ' ' . __("shared a post."),
                        "empNumber" => $activity->getEmployeeNumber(),
                        "postOwnerEmpNumber" => $activity->getEmployeeNumber(),
                        "elapsedTime" => $this->getBuzzNotificationService()->timeElapsedString(new DateTime($activity->getShareTime())),
                        "shareId" => $activity->getId(),
                        "time" => $activity->getShareTime(),
                        "type" => "share_new",
                    ]);
                }
            }
        }

        foreach ($newCommentsOnEmployeePosts as $activity) {
            if ($activity instanceof Comment) {
                $employee = $activity->getEmployeeComment();
                if ($employee instanceof Employee && is_null($employee->getPurgedAt())) {
                    array_push($this->notifications, [
                        "message" => $employee->getFirstAndLastNames() . ' ' . __("commented on your post."),
                        "empNumber" => $activity->getEmployeeNumber(),
                        "postOwnerEmpNumber" => $empNumber,
                        "elapsedTime" => $this->getBuzzNotificationService()->timeElapsedString(new DateTime($activity->getCommentTime())),
                        "shareId" => $activity->getShareId(),
                        "time" => $activity->getCommentTime(),
                        "type" => "comment_on_post",
                    ]);
                }
            }
        }

        foreach ($newLikesOnEmployeePosts as $activity) {
            if ($activity instanceof LikeOnShare) {
                $employee = $activity->getEmployeeLike();
                if ($employee instanceof Employee && is_null($employee->getPurgedAt())) {
                    $likeTime = $this->getBuzzNotificationService()->getUserDateTime($activity->getLikeTime());
                    array_push($this->notifications, [
                        "message" => $employee->getFirstAndLastNames() . ' ' . __("liked to a post you shared."),
                        "empNumber" => $activity->getEmployeeNumber(),
                        "postOwnerEmpNumber" => $empNumber,
                        "elapsedTime" => $this->getBuzzNotificationService()->timeElapsedString(new DateTime($likeTime)),
                        "shareId" => $activity->getShareId(),
                        "time" => $likeTime,
                        "type" => "like_post",
                    ]);
                }
            }
        }

        foreach ($newLikesOnEmployeeComments as $activity) {
            if ($activity instanceof LikeOnComment) {
                $employee = $activity->getEmployeeLike()->getFirst();
                if ($employee instanceof Employee && is_null($employee->getPurgedAt())) {
                    $likeTime = $this->getBuzzNotificationService()->getUserDateTime($activity->getLikeTime());
                    array_push($this->notifications, [
                        "message" => $employee->getFirstAndLastNames() . ' ' . __("liked to your comment."),
                        "empNumber" => $activity->getEmployeeNumber(),
                        "postOwnerEmpNumber" => $empNumber,
                        "elapsedTime" => $this->getBuzzNotificationService()->timeElapsedString(new DateTime($likeTime)),
                        "shareId" => $activity->getCommentLike()->getShareId(),
                        "time" => $likeTime,
                        "type" => "like_comment",
                    ]);
                }
            }
        }

        foreach ($newSharesOfEmployeePosts as $activity) {
            if ($activity instanceof Share) {
                $employee = $activity->getEmployeePostShared();
                if ($employee instanceof Employee && is_null($employee->getPurgedAt())) {
                    array_push($this->notifications, [
                        "message" => $employee->getFirstAndLastNames() . ' ' . __("shared your post."),
                        "empNumber" => $activity->getEmployeeNumber(),
                        "postOwnerEmpNumber" => $activity->getEmployeeNumber(),
                        "elapsedTime" => $this->getBuzzNotificationService()->timeElapsedString(new DateTime($activity->getShareTime())),
                        "shareId" => $activity->getId(),
                        "time" => $activity->getShareTime(),
                        "type" => "share_post",
                    ]);
                }
            }
        }
    }
}
