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

class BuzzEmbeddedURL
{
    private string $url;
    private ?string $embeddedURL = null;
    private bool $generated = false;

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
     * @param string|null $embeddedURL
     * @return string|null
     */
    private function setEmbeddedURL(?string $embeddedURL): ?string
    {
        $this->generated = true;
        return $this->embeddedURL = $embeddedURL;
    }

    /**
     * @return string|null
     * @throws InvalidURLException
     */
    public function getEmbeddedURL(): ?string
    {
        if ($this->generated) { // Handle caching since no setter for $this->url
            return $this->embeddedURL;
        }
        $buzzURLGroups = [];
        $buzzURLGroups[] = new EmbeddedURLForYoutube($this->getURL());
        $buzzURLGroups[] = new EmbeddedURLForVimeo($this->getURL());
        $buzzURLGroups[] = new EmbeddedURLForDailymotion($this->getURL());

        foreach ($buzzURLGroups as $buzzURLGroup) {
            $embeddedURL = $buzzURLGroup->getEmbeddedURL();
            if ($embeddedURL != null) {
                return $this->setEmbeddedURL($embeddedURL);
            }
        }
        return $this->setEmbeddedURL(null);
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
