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

class BuzzEmbeddedURL
{
    protected string $url;

    /**
     * @param string $url
     */
    public function __construct(string $url)
    {
        $this->url = $url;
    }

    /**
     * @return string|null
     */
    public function getURL(): ?string
    {
        return $this->url;
    }

    /**
     * @return string|null
     * @throws InvalidURLException
     */
    public function getEmbeddedURL(): ?string
    {
        $buzzURLGroups = [];
        $buzzURLGroups[] = new EmbeddedURLForYoutube($this->getURL());
        $buzzURLGroups[] = new EmbeddedURLForVimeo($this->getURL());
        $buzzURLGroups[] = new EmbeddedURLForDailymotion($this->getURL());

        foreach ($buzzURLGroups as $buzzURLGroup) {
            $embeddedURL = $buzzURLGroup->getEmbeddedURL();
            if ($embeddedURL != null) {
                return $embeddedURL;
            }
        }
        return null;
    }

    /**
     * @return bool
     */
    public function isValidURL(): bool
    {
        try {
            return $this->getEmbeddedURL() != null;
        } catch (InvalidURLException $e) {
            return false;
        }
    }
}
