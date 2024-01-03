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

namespace OrangeHRM\Buzz\Controller\File;

use InvalidArgumentException;
use OrangeHRM\Buzz\Traits\Service\BuzzServiceTrait;
use OrangeHRM\Core\Controller\AbstractFileController;
use OrangeHRM\Core\Traits\CacheTrait;
use OrangeHRM\Core\Traits\ETagHelperTrait;
use OrangeHRM\Entity\BuzzPhoto;
use OrangeHRM\Framework\Http\Request;
use OrangeHRM\Framework\Http\Response;

class BuzzPhotoController extends AbstractFileController
{
    use ETagHelperTrait;
    use BuzzServiceTrait;
    use CacheTrait;

    public const BUZZ_PHOTO_CACHE_KEY_PREFIX = 'buzz.photo';
    public const ETAG_CACHE_KEY_SUFFIX = 'etag';

    /**
     * @param int $id
     * @return string
     */
    private function generateBuzzPhotoETagCacheKey(int $id): string
    {
        return self::BUZZ_PHOTO_CACHE_KEY_PREFIX . ".$id." . self::ETAG_CACHE_KEY_SUFFIX;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function handle(Request $request): Response
    {
        if (!$request->attributes->has('id')) {
            $this->handleBadRequest();
            return $this->getResponse();
        }

        $photoId = $request->attributes->getInt('id');

        $photo = null;
        $generateCache = false;
        $response = $this->getResponse();
        try {
            $etag = $this->getCache()->get(
                $this->generateBuzzPhotoETagCacheKey($photoId),
                function () use ($photoId, &$photo, &$generateCache): string {
                    $photo = $this->getBuzzService()->getBuzzDao()->getBuzzPhotoById($photoId);
                    if (!$photo instanceof BuzzPhoto) {
                        throw new InvalidArgumentException('Invalid photo id');
                    }
                    $generateCache = true;
                    return $this->generateEtag($photo->getDecorator()->getPhoto());
                }
            );
        } catch (InvalidArgumentException $e) {
            $this->handleBadRequest();
            return $this->getResponse();
        }

        $response->setEtag($etag);
        if (!$response->isNotModified($request)) {
            if (!$generateCache) {
                $photo = $this->getBuzzService()->getBuzzDao()->getBuzzPhotoById($photoId);
            }
            if (!$photo instanceof BuzzPhoto) {
                $this->handleBadRequest();
                return $this->getResponse();
            }
            $response->setContent($photo->getDecorator()->getPhoto());
            $this->setCommonHeaders($response, $photo->getFileType());
        }

        return $response;
    }

    /**
     * @param $response
     * @param string $contentType
     */
    private function setCommonHeaders($response, string $contentType): void
    {
        $response->headers->set('Content-Type', $contentType);
        $response->setPublic();
        $response->setMaxAge(0);
        $response->headers->addCacheControlDirective('must-revalidate', true);
        $response->headers->set('Pragma', 'Public');
    }
}
