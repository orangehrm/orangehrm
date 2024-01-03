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

namespace OrangeHRM\Buzz\Dto\BuzzVideoURL;

use OrangeHRM\Buzz\Exception\InvalidURLException;

class EmbeddedURLForVimeo extends AbstractBuzzVideoURL
{
    private const VIMEO_REGEX = '/(http|https)?:\/\/(www\.|player\.)?vimeo\.com\/(?:channels\/(?:\w+\/)?|groups\/([^\/]*)\/videos\/|video\/|)(\d+)(?:|\/\?)/';
    private const VIMEO_EMBEDDED_REGEX = '/^(https|http):\/\/(?:www\.)?player.vimeo.com\/video\/[A-z0-9]+/';

    /**
     * @inheritDoc
     */
    public function getEmbeddedURL(): ?string
    {
        if (!($this->getTextHelper()->strContains($this->getURL(), 'vimeo'))) {
            return null;
        }

        if (preg_match(self::VIMEO_EMBEDDED_REGEX, $this->getURL())) {
            return $this->getURL();
        }

        if (preg_match(self::VIMEO_REGEX, $this->getURL())) {
            $shortUrlRegex = '/vimeo.com\/([0-9]+)\??/i';
            $longUrlRegex = '/player.vimeo.com\/video\/([0-9]+)\??/i';

            $vimeoId = null;
            if (preg_match($longUrlRegex, $this->getURL(), $matches)) {
                $vimeoId = end($matches);
            } elseif (preg_match($shortUrlRegex, $this->getURL(), $matches)) {
                $vimeoId = end($matches);
            }

            if ($vimeoId != null) {
                return 'https://player.vimeo.com/video/' . $vimeoId;
            }
        }
        throw InvalidURLException::invalidVimeoURLProvided();
    }
}
