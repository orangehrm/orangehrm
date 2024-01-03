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

class EmbeddedURLForDailymotion extends AbstractBuzzVideoURL
{
    private const DAILYMOTION_REGEX = '/^(http|https)?:\/\/(?:www\.|dai)(?:dailymotion\.com\/video\/[A-z0-9]+|\.ly\/[A-z0-9]+|)./';
    private const DAILYMOTION_EMBEDDED_REGEX = '/^(https|http):\/\/(?:www\.)?dailymotion.com\/embed\/video\/[A-z0-9]+/';

    /**
     * @inheritDoc
     */
    public function getEmbeddedURL(): ?string
    {
        if (!($this->getTextHelper()->strContains($this->getURL(), 'dailymotion')
            || $this->getTextHelper()->strContains($this->getURL(), 'dai.ly'))
        ) {
            return null;
        }

        if (preg_match(self::DAILYMOTION_EMBEDDED_REGEX, $this->getURL())) {
            return $this->getURL();
        }

        if (preg_match(self::DAILYMOTION_REGEX, $this->getURL())) {
            $shortUrlRegex = '/dai.ly\/([a-zA-Z0-9_-]+)\??/i';
            $longUrlRegex = '/^.+dailymotion.com\/(?:video|swf\/video|embed\/video|hub|swf)\/([^&?]+)/i';

            $dailymotionId = null;
            if (preg_match($longUrlRegex, $this->getURL(), $matches)) {
                $dailymotionId = end($matches);
            } elseif (preg_match($shortUrlRegex, $this->getURL(), $matches)) {
                $dailymotionId = end($matches);
            }

            if ($dailymotionId != null) {
                return 'https://www.dailymotion.com/embed/video/' . $dailymotionId;
            }
        }
        throw InvalidURLException::invalidDailymotionURLProvided();
    }
}
