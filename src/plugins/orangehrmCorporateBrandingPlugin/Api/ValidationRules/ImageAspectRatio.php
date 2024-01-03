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

namespace OrangeHRM\CorporateBranding\Api\ValidationRules;

use OrangeHRM\Core\Api\V2\Validator\Rules\AbstractRule;
use OrangeHRM\Entity\Theme;

class ImageAspectRatio extends AbstractRule
{
    private float $aspectRatio;

    /**
     * @param float $aspectRatio
     */
    public function __construct(float $aspectRatio)
    {
        $this->aspectRatio = $aspectRatio;
    }

    /**
     * @inheritDoc
     */
    public function validate($input): bool
    {
        if (!(isset($input['name']) && isset($input['type']) && isset($input['base64']))) {
            return false;
        }

        $fileExtension = pathinfo($input['name'])['extension'] ?? null;
        if ($input['type'] === 'image/svg+xml' && $fileExtension === 'svg') {
            // not checking aspect ratio for SVG
            return true;
        }

        $content = base64_decode($input['base64']);
        list($imageWidth, $imageHeight) = getimagesizefromstring($content);

        $calcRatio = $imageWidth / $imageHeight;
        if (abs($calcRatio - $this->aspectRatio) > Theme::IMAGE_ASPECT_RATIO_TOLERANCE) {
            return false;
        }

        return true;
    }
}
