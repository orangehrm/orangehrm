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

namespace OrangeHRM\Buzz\Api\Model;

use OrangeHRM\Buzz\Dto\BuzzFeedPost;
use OrangeHRM\Core\Api\V2\Serializer\Normalizable;

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
