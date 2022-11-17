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

namespace OrangeHRM\Buzz\Dto\BuzzVideoURL;

use OrangeHRM\Buzz\Exception\InvalidURLException;

class EmbeddedURLForVimeo implements BuzzVideoURL
{
    private string $url;
    private const VIMEO_REGEX = '/(http|https)?:\/\/(www\.|player\.)?vimeo\.com\/(?:channels\/(?:\w+\/)?|groups\/([^\/]*)\/videos\/|video\/|)(\d+)(?:|\/\?)/';

    /**
     * @param string $url
     */
    public function __construct(string $url)
    {
        $this->url = $url;
    }

    /**
     * @return bool
     * @throws InvalidURLException
     */
    public function getValidation(): bool
    {
        if (preg_match(self::VIMEO_REGEX, $this->url))
        {
            return true;
        } else {
            throw InvalidURLException::invalidVimeoURLProvided();
        }
    }

    /**
     * @inheritDoc
     * @throws InvalidURLException
     */
    public function getEmbeddedURL(): ?string
    {
        //TODO - need to check/change
        if($this->getValidation()) {
            preg_match(
                '///(www.)?vimeo.com/(d+)($|/)/',
                $this->url,
                $matches
            );

            $id = $matches[2];
            return 'https://player.vimeo.com/video/' .$id;
        }
        return null;
    }
}
