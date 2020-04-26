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
 * Description of viewPostPhotoAction
 *
 */
class viewPostPhotoAction extends BaseBuzzAction {
    const SECONDS_IN_YEAR = 31536000;
    
    public function execute($request) {
        $id = $request->getParameter('id');
        $photo = $this->getBuzzService()->getPhoto($id);
        $response = $this->getResponse();

        if (!empty($photo)) {
            $contents = $photo->photo;
            $contentType = $photo->file_type;
            $fileSize = $empPicture->size;
            $fileName = $empPicture->filename;
        } else {
            $response->setStatusCode('404');
            return sfView::NONE;
        }

        $checksum = md5($contents);

        // Allow client side cache image unless image checksum changes.
        $eTag = $request->getHttpHeader('If-None-Match');

        

        if ($eTag == $checksum) {
            $response->setStatusCode('304');
        } else {
            $response->setContentType($contentType);
            $response->setContent($contents);
        }

        $response->setHttpHeader('Pragma', 'Public');
        $response->setHttpHeader('ETag', $checksum);
        
        $date = new DateTime();
        $date->modify('+1 Year');
        
        $response->setHttpHeader('Expires', gmdate('D, d M Y H:i:s', $date->getTimestamp()) . ' GMT');
        
        $response->addCacheControlHttpHeader('public, max-age=' . self::SECONDS_IN_YEAR . ', must-revalidate');

        $response->send();

        return sfView::NONE;
    }

}
