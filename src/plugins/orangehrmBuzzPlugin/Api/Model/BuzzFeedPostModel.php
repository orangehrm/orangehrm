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

namespace OrangeHRM\Buzz\Api\Model;

use OrangeHRM\Buzz\Dto\BuzzFeedPost;
use OrangeHRM\Core\Api\V2\Serializer\Normalizable;

/**
 * @OA\Schema(
 *     schema="Buzz-FeedPostModel",
 *     oneOf={
 *         @OA\Schema(ref="#/components/schemas/Buzz-FeedPostModel-Text"),
 *         @OA\Schema(ref="#/components/schemas/Buzz-FeedPostModel-Photo"),
 *         @OA\Schema(ref="#/components/schemas/Buzz-FeedPostModel-Video"),
 *         @OA\Schema(ref="#/components/schemas/Buzz-FeedPostModel-Share"),
 *     },
 *     type="object"
 * )
 *
 * @OA\Schema(
 *     schema="Buzz-FeedPostModel-Text",
 *     type="object",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(
 *         property="post",
 *         type="object",
 *         @OA\Property(property="id", type="integer"),
 *     ),
 *     @OA\Property(property="type", type="string", default="text"),
 *     @OA\Property(property="liked", type="boolean"),
 *     @OA\Property(property="text", type="string"),
 *     @OA\Property(
 *         property="employee",
 *         type="object",
 *         @OA\Property(property="empNumber", type="integer"),
 *         @OA\Property(property="lastName", type="string"),
 *         @OA\Property(property="firstName", type="string"),
 *         @OA\Property(property="middleName", type="string"),
 *         @OA\Property(property="employeeId", type="string"),
 *         @OA\Property(property="terminationId", type="integer")
 *     ),
 *     @OA\Property(
 *         property="stats",
 *         type="object",
 *         @OA\Property(property="numOfLikes", type="integer"),
 *         @OA\Property(property="numOfComments", type="integer"),
 *         @OA\Property(property="numOfShares", type="integer"),
 *     ),
 *     @OA\Property(property="createdDate", type="string", format="date"),
 *     @OA\Property(property="createdTime", type="string"),
 *     @OA\Property(property="originalPost", type="object", nullable=true),
 *     @OA\Property(
 *         property="permission",
 *         type="object",
 *         @OA\Property(property="canUpdate", type="boolean"),
 *         @OA\Property(property="canDelete", type="boolean"),
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="Buzz-FeedPostModel-Photo",
 *     type="object",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(
 *         property="post",
 *         type="object",
 *         @OA\Property(property="id", type="integer"),
 *     ),
 *     @OA\Property(property="type", type="string", default="photo"),
 *     @OA\Property(property="liked", type="boolean"),
 *     @OA\Property(property="text", type="string"),
 *     @OA\Property(
 *         property="employee",
 *         type="object",
 *         @OA\Property(property="empNumber", type="integer"),
 *         @OA\Property(property="lastName", type="string"),
 *         @OA\Property(property="firstName", type="string"),
 *         @OA\Property(property="middleName", type="string"),
 *         @OA\Property(property="employeeId", type="string"),
 *         @OA\Property(property="terminationId", type="integer")
 *     ),
 *     @OA\Property(
 *         property="stats",
 *         type="object",
 *         @OA\Property(property="numOfLikes", type="integer"),
 *         @OA\Property(property="numOfComments", type="integer"),
 *         @OA\Property(property="numOfShares", type="integer"),
 *     ),
 *     @OA\Property(property="createdDate", type="string", format="date"),
 *     @OA\Property(property="createdTime", type="string"),
 *     @OA\Property(property="originalPost", type="object", nullable=true),
 *     @OA\Property(
 *         property="permission",
 *         type="object",
 *         @OA\Property(property="canUpdate", type="boolean"),
 *         @OA\Property(property="canDelete", type="boolean"),
 *     ),
 *     @OA\Property(property="photoIds", type="array", @OA\Items(type="integer"))
 * )
 *
 * @OA\Schema(
 *     schema="Buzz-FeedPostModel-Video",
 *     type="object",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(
 *         property="post",
 *         type="object",
 *         @OA\Property(property="id", type="integer"),
 *     ),
 *     @OA\Property(property="type", type="string", default="video"),
 *     @OA\Property(property="liked", type="boolean"),
 *     @OA\Property(property="text", type="string"),
 *     @OA\Property(
 *         property="employee",
 *         type="object",
 *         @OA\Property(property="empNumber", type="integer"),
 *         @OA\Property(property="lastName", type="string"),
 *         @OA\Property(property="firstName", type="string"),
 *         @OA\Property(property="middleName", type="string"),
 *         @OA\Property(property="employeeId", type="string"),
 *         @OA\Property(property="terminationId", type="integer")
 *     ),
 *     @OA\Property(
 *         property="stats",
 *         type="object",
 *         @OA\Property(property="numOfLikes", type="integer"),
 *         @OA\Property(property="numOfComments", type="integer"),
 *         @OA\Property(property="numOfShares", type="integer"),
 *     ),
 *     @OA\Property(property="createdDate", type="string", format="date"),
 *     @OA\Property(property="createdTime", type="string"),
 *     @OA\Property(property="originalPost", type="object", nullable=true),
 *     @OA\Property(
 *         property="permission",
 *         type="object",
 *         @OA\Property(property="canUpdate", type="boolean"),
 *         @OA\Property(property="canDelete", type="boolean"),
 *     ),
 *     @OA\Property(
 *         property="video",
 *         type="object",
 *         @OA\Property(property="link", type="string")
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="Buzz-FeedPostModel-Share",
 *     type="object",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(
 *         property="post",
 *         type="object",
 *         @OA\Property(property="id", type="integer"),
 *     ),
 *     @OA\Property(property="type", type="string", default="text"),
 *     @OA\Property(property="liked", type="boolean"),
 *     @OA\Property(property="text", type="string"),
 *     @OA\Property(
 *         property="employee",
 *         type="object",
 *         @OA\Property(property="empNumber", type="integer"),
 *         @OA\Property(property="lastName", type="string"),
 *         @OA\Property(property="firstName", type="string"),
 *         @OA\Property(property="middleName", type="string"),
 *         @OA\Property(property="employeeId", type="string"),
 *         @OA\Property(property="terminationId", type="integer")
 *     ),
 *     @OA\Property(
 *         property="stats",
 *         type="object",
 *         @OA\Property(property="numOfLikes", type="integer"),
 *         @OA\Property(property="numOfComments", type="integer"),
 *         @OA\Property(property="numOfShares", type="integer"),
 *     ),
 *     @OA\Property(property="createdDate", type="string", format="date"),
 *     @OA\Property(property="createdTime", type="string"),
 *     @OA\Property(
 *         property="originalPost",
 *         type="object",
 *         @OA\Property(property="text", type="string"),
 *         @OA\Property(
 *             property="employee",
 *             type="object",
 *             @OA\Property(property="empNumber", type="integer"),
 *             @OA\Property(property="lastName", type="string"),
 *             @OA\Property(property="firstName", type="string"),
 *             @OA\Property(property="middleName", type="string"),
 *             @OA\Property(property="employeeId", type="string"),
 *             @OA\Property(property="terminationId", type="integer")
 *         ),
 *         @OA\Property(property="createdDate", type="string", format="date"),
 *         @OA\Property(property="createdTime", type="string"),
 *     ),
 *     @OA\Property(
 *         property="permission",
 *         type="object",
 *         @OA\Property(property="canUpdate", type="boolean"),
 *         @OA\Property(property="canDelete", type="boolean"),
 *     )
 * )
 */
class BuzzFeedPostModel implements Normalizable
{
    private BuzzFeedPost $buzzFeedPost;

    public function __construct(BuzzFeedPost $buzzFeedPost)
    {
        $this->buzzFeedPost = $buzzFeedPost;
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        $result = [
            'id' => $this->buzzFeedPost->getId(),
            'post' => ['id' => $this->buzzFeedPost->getPostId()],
            'type' => $this->buzzFeedPost->getType(),
            'liked' => $this->buzzFeedPost->isLiked(),
            'text' => $this->buzzFeedPost->getText(),
            'employee' => $this->buzzFeedPost->getEmployee(),
            'stats' => $this->buzzFeedPost->getStats(),
            'createdDate' => $this->buzzFeedPost->getCreatedDate(),
            'createdTime' => $this->buzzFeedPost->getCreatedTime(),
            'originalPost' => $this->buzzFeedPost->getOriginalPost(),
            'permission' => $this->buzzFeedPost->getPermission(),
        ];
        if ($this->buzzFeedPost->hasVideo()) {
            $result['video'] = [
                'link' => $this->buzzFeedPost->getVideoLink(),
            ];
        }
        if ($this->buzzFeedPost->hasPhotos()) {
            $result['photoIds'] = $this->buzzFeedPost->getPhotoIds();
        }
        return $result;
    }
}
