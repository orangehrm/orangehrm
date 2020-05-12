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

/**
 * Description of viewBuzzImageAction
 *
 * @author nirmal
 */
class viewBuzzImageAction extends BaseBuzzAction {

    public function execute($request) {
        $imageId = $request->getParameter('imageId');

        if ($imageId) {
            $buzzImage = $this->getBuzzService()->getPhoto($imageId);
            $contents = $buzzImage->getPhoto();
            $contentType = $buzzImage->getFileType();

            $checksum = md5($contents);

            // Allow client side cache image unless image checksum changes.
            $eTag = $request->getHttpHeader('If-None-Match');

            $response = $this->getResponse();

            if ($eTag == $checksum) {
                $response->setStatusCode('304');
            } else {
                $response->setContentType($contentType);
                $response->setContent($contents);
            }

            $response->setHttpHeader('Pragma', 'Public');
            $response->setHttpHeader('ETag', $checksum);
            $response->addCacheControlHttpHeader('public, max-age=0, must-revalidate');

            $response->send();
        }

        return sfView::NONE;
    }

}
