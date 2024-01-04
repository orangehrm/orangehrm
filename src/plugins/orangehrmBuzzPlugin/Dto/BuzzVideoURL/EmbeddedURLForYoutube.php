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

class EmbeddedURLForYoutube extends AbstractBuzzVideoURL
{
    private const YOUTUBE_EMBEDDED_REGEX = '/(https|http):\/\/(?:www\.)?youtube.com\/embed\/[A-z0-9]+/';
    private const YOUTUBE_REGEX = '/(?:http:|https:)*?\/\/(?:www\.|)(?:youtube\.com|m\.youtube\.com|youtu\.|youtube-nocookie\.com).*(?:v=|v%3D|v\/|(?:a|p)\/(?:a|u)\/\d.*\/|watch\?|vi(?:=|\/)|\/embed\/|\/shorts\/|oembed\?|be\/|e\/)([^&?%#\/\n]*)/';

    /**
     * @inheritDoc
     */
    public function getEmbeddedURL(): ?string
    {
        if (!($this->getTextHelper()->strContains($this->getURL(), 'youtube')
            || $this->getTextHelper()->strContains($this->getURL(), 'youtu.be'))
        ) {
            return null;
        }

        if (preg_match(self::YOUTUBE_EMBEDDED_REGEX, $this->getURL())) {
            return $this->getURL();
        }

        if (preg_match(self::YOUTUBE_REGEX, $this->getURL())) {
            $longUrlRegex = '/(?:http:|https:)*?\/\/(?:www\.|)(?:youtube\.com|m\.youtube\.com|youtu\.|youtube-nocookie\.com).*(?:v=|v%3D|v\/|(?:a|p)\/(?:a|u)\/\d.*\/|watch\?|vi(?:=|\/)|\/shorts\/|be\/|e\/)([^&?%#\/\n]*)/i';

            $youtubeId = null;
            if (preg_match($longUrlRegex, $this->getURL(), $matches)) {
                $youtubeId = end($matches);
            }

            if ($youtubeId != null) {
                return 'https://www.youtube.com/embed/' . $youtubeId;
            }
        }
        throw InvalidURLException::invalidYouTubeURLProvided();
    }
}
