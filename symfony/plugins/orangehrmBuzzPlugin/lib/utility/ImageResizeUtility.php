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

/**
 * Description of ImageResizeUtility
 *
 */
class ImageResizeUtility {
    
    /** Exif orientation constants. See EXIF spec for details */
    const EXIF_ORIENTATION_BOTTOM_RIGHT = 3;
    const EXIF_ORIENTATION_RIGHT_TOP = 6;
    const EXIF_ORIENTATION_LEFT_BOTTOM = 8;
    
    private $logger;
    
    /**
     * Converts uploaded image by resizing to be at most $maxWidth/$maxHeight and
     * Fixing JPEG orientation if necessary.
     * 
     * @param string $filename image file
     * @param int $maxWidth
     * @param int $maxHeight
     * @param int $jpegQuality (optional) JPEG quality. 
     * 
     * @return converted image in the the format:
     *         array('image' => <image data>, 'width' => width, 'height' => height);
     */
    public function convertUploadedImage($filename, $maxWidth, $maxHeight, $jpegQuality = 90) {

        if (!extension_loaded('gd') || !function_exists('gd_info')) {
            $this->getLogger()->error('gd extension not available. Please load php gd extension');
            return file_get_contents($filename);
        }
        
        list($width, $height, $imageType) = getimagesize($filename);

        $image = imagecreatefromstring(file_get_contents($filename));
        ImageResizeUtility::reduceImageSize($image, $maxWidth, $maxHeight);
        ImageResizeUtility::fixImageOrientation($image, $filename);                     
        
        $result = array('image' => $this->getImageDataFromResource($image, $imageType, $jpegQuality),
            'width' => imagesx($image),
            'height' => imagesy($image));
        
        imagedestroy($image);         

        return $result;        
    }
            
    
    /**
     * Get image data as a string from an image resource
     * 
     * @param resource $image
     * @param string $imageType
     * @param int $jpegQuality
     * @return Image data as string
     */
    public function getImageDataFromResource(&$image, $imageType = null, $jpegQuality = 90) {
        ob_start();
        switch ($imageType) {
            case IMG_GIF:
                imagegif($image);
                break;
            case IMG_PNG:
                imagepng($image);
                break;
            case IMG_JPG:
                // fallthrough
            default:
                imagejpeg($image, NULL, $jpegQuality);
                break;
        }                
        return ob_get_clean();        
    }
    
    /**
     * Reduce image size to be at most $maxWidth and $maxHeight while keeping aspect ratio.
     * If image is already smaller than the given max values, no change is done.
     * 
     * @param resource $sourceImage Image resource
     * @param int $maxWidth
     * @param int $maxHeight
     * @return boolean true if image converted, false if not
     */
    public function reduceImageSize(&$sourceImage, $maxWidth, $maxHeight, $increaseIfSmall = false) {
        
        if (!extension_loaded('gd') || !function_exists('gd_info')) {
            $this->getLogger()->error('gd extension not available. Please load php gd extension');
            return false;
        }
        
        if ($sourceImage === false) {
            return false;
        }
        
        $sourceWidth = imagesx($sourceImage);
        $sourceHeight = imagesy($sourceImage);
        
        $sourceAspectRatio = $sourceWidth / $sourceHeight;
        $destAspectRatio = $maxWidth / $maxHeight;
        if ($sourceWidth <= $maxWidth && $sourceHeight <= $maxHeight && !$increaseIfSmall) {
            $destImageWidth = $sourceWidth;
            $destImageHeight = $sourceHeight;
            return false;
        } elseif ($destAspectRatio > $sourceAspectRatio) {
            $destImageWidth = (int) ($maxHeight * $sourceAspectRatio);
            $destImageHeight = $maxHeight;
        } else {
            $destImageWidth = $maxWidth;
            $destImageHeight = (int) ($maxWidth / $sourceAspectRatio);
        }
        
        $destImage = imagecreatetruecolor($destImageWidth, $destImageHeight);
        if ($increaseIfSmall && $sourceWidth <= $maxWidth && $sourceHeight <= $maxHeight) {
            $resampled = imagecopyresized($destImage, $sourceImage, 0, 0, 0, 0, $destImageWidth, $destImageHeight, $sourceWidth, $sourceHeight);
        } else {
            $resampled = imagecopyresampled($destImage, $sourceImage, 0, 0, 0, 0, $destImageWidth, $destImageHeight, $sourceWidth, $sourceHeight);
        }
        if ($resampled) {
            imagedestroy($sourceImage);
            $sourceImage = $destImage;
            return true;
        } else {
            imagedestroy($destImage);
        }

        return false;
    }    
    
    /**
     * Fixes image orientation based on EXIF data. Only supported for JPEG.
     * 
     * @param resource $image Image resource
     * @param string $filename
     * 
     * @return true if orientation fixed.
     */
    public function fixImageOrientation(&$image, $filename) {
        
        if (!function_exists('exif_read_data')) {
            $this->getLogger()->error('exif_data_read not available. Please load php exif extension');
            return false;
        }
        
        $orientationFixed = false;
        $exif = exif_read_data($filename);

        if ($exif && isset($exif['Orientation'])) {
            
            switch ($exif['Orientation']) {
                case self::EXIF_ORIENTATION_BOTTOM_RIGHT:
                    $image = imagerotate($image, 180, 0);
                    $orientationFixed = true;
                    break;

                case self::EXIF_ORIENTATION_RIGHT_TOP:
                    $image = imagerotate($image, -90, 0);
                    $orientationFixed = true;
                    break;

                case self::EXIF_ORIENTATION_LEFT_BOTTOM:
                    $image = imagerotate($image, 90, 0);
                    $orientationFixed = true;
                    break;
            }
        }   
        
        return $orientationFixed;
    }
    
    /**
     * Get Logger instance
     * @return Logger
     */
    public function getLogger() {
        if (empty($this->logger)) {
            $this->logger = Logger::getLogger('core.ImageResizeUtility');
        }
        
        return $this->logger;
    }

    
}
