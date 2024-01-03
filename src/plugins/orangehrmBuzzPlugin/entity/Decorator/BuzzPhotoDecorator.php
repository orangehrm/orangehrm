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

namespace OrangeHRM\Entity\Decorator;

use OrangeHRM\Entity\BuzzPhoto;

class BuzzPhotoDecorator
{
    /**
     * @var BuzzPhoto
     */
    protected BuzzPhoto $buzzPhoto;

    /**
     * This property to read `photo` resource in `BuzzPhoto`
     * @var string|null
     */
    private ?string $photoString = null;

    /**
     * @param BuzzPhoto $buzzPhoto
     */
    public function __construct(BuzzPhoto $buzzPhoto)
    {
        $this->buzzPhoto = $buzzPhoto;
    }

    /**
     * @return BuzzPhoto
     */
    protected function getBuzzPhoto(): BuzzPhoto
    {
        return $this->buzzPhoto;
    }

    /**
     * @return string
     */
    public function getPhoto(): string
    {
        $photo = $this->getBuzzPhoto()->getPhoto();
        if (is_string($photo)) {
            return $photo;
        }
        if (is_null($this->photoString) && is_resource($photo)) {
            $this->photoString = stream_get_contents($photo);
        }
        return $this->photoString;
    }
}
