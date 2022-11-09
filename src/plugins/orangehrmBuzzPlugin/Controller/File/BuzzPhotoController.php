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

namespace OrangeHRM\Buzz\Controller\File;

use OrangeHRM\Buzz\Traits\Service\BuzzServiceTrait;
use OrangeHRM\Core\Controller\AbstractFileController;
use OrangeHRM\Core\Traits\ETagHelperTrait;
use OrangeHRM\Entity\BuzzPhoto;
use OrangeHRM\Framework\Http\Request;
use OrangeHRM\Framework\Http\Response;

class BuzzPhotoController extends AbstractFileController
{
    use ETagHelperTrait;
    use BuzzServiceTrait;

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

        $photo = $this->getBuzzService()->getBuzzDao()->getBuzzPhotoById($photoId);
        if (!$photo instanceof BuzzPhoto) {
            $this->handleBadRequest();
            return $this->getResponse();
        }

        $response = $this->getResponse();
        // TODO:: get Etag from cache
        $response->setEtag($this->generateEtag($photo->getDecorator()->getPhoto()));

        if (!$response->isNotModified($request)) {
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
