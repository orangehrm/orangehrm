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
 * Description of BuzzImageCompressUtility
 *
 */
class BuzzImageCompressUtility {

    private $buzzDao;
    private $buzzConfigService;

    /**
     * 
     * @return BuzzConfigService
     */
    protected function getBuzzConfigService() {
        if (!$this->buzzConfigService instanceof BuzzConfigService) {
            $this->buzzConfigService = new BuzzConfigService();
        }
        return $this->buzzConfigService;
    }
    
    public function getBuzzDao() {
        if (empty($this->buzzDao)) {
            $this->buzzDao = new BuzzDao();
        }
        return $this->buzzDao;
    }

    public function setBuzzDao($buzzDao) {
        $this->buzzDao = $buzzDao;
    }
    
    public function compressImages() {
        $maxDimension = $this->getBuzzConfigService()->getMaxImageDimension();
        $imageUtility = new ImageResizeUtility();
        $buzzDao = $this->getBuzzDao();
        
        $photos = $buzzDao->getAllPhotos();
        
        foreach ($photos as $photo) {
            $image = imagecreatefromstring($photo->getPhoto());
            $reduced = $imageUtility->reduceImageSize($image, $maxDimension, $maxDimension);
            
            if ($reduced) {
                
                $message = $photo->filename . "({$photo->width} x {$photo->height}) size: " . strlen($photo->photo) . " bytes => ";
                $photo->width = imagesx($image);
                $photo->height = imagesy($image);
                $photo->photo = $imageUtility->getImageDataFromResource($image);
                $photo->size = strlen($photo->photo);
                
                $message .= "reduced to: ({$photo->width} x {$photo->height}) size: " . strlen($photo->photo);
                
                imagedestroy($image);
                $buzzDao->savePhoto($photo);
                $photo->free();
                unset($photo);
            }
        }        
    }
}
