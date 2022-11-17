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

use OrangeHRM\Core\Traits\Service\TextHelperTrait;

use function PHPUnit\Framework\isNull;

class BuzzValidURL
{
    use TextHelperTrait;

    private const YOUTUBE_EMBEDDED_REGEX = '/(https|http):\/\/(?:www\.)?youtube.com\/embed\/[A-z0-9]+/';
    private const VIMEO_EMBEDDED_REGEX = '/^(https|http):\/\/(?:www\.)?player.vimeo.com\/video\/[A-z0-9]+/';
    private const DAILYMOTION_EMBEDDED_REGEX = '/^(https|http):\/\/(?:www\.)?dailymotion.com\/embed\/video\/[A-z0-9]+/';

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
     */
    public function getEmbeddedURl(): ?string
    {
        $url = $this->url;

        $buzzURLGroups = [];
        if ($this->getTextHelper()->strContains($url, 'youtube')
            || $this->getTextHelper()->strContains($url, 'youtu.be')
        ) {
            $buzzURLGroups[] = new EmbeddedURLForYoutube($url);
        }
        //need to check for other types
        if ($this->getTextHelper()->strContains($url, 'vimeo')) {
            $buzzURLGroups[] = new EmbeddedURLForVimeo($url);
        }
        //need to check for other types
        if ($this->getTextHelper()->strContains($url, 'dailymotion')) {
            $buzzURLGroups[] = new EmbeddedURLForDailymotion($url);
        }

        $embeddedURL = null;
        foreach ($buzzURLGroups as $buzzURLGroup) {
            $buzzEmbeddedURL = new BuzzEmbeddedURL($buzzURLGroup);
            $embeddedURL = $buzzEmbeddedURL->generateEmbeddedURL();
        }
        return $embeddedURL;
    }

    /**
     * @return string
     */
    public function getValidURL(): string
    {
        if (preg_match(self::YOUTUBE_EMBEDDED_REGEX, $this->url)) {
            return $this->url;
        } elseif (preg_match(self::VIMEO_EMBEDDED_REGEX, $this->url)) {
            return $this->url;
        } elseif (preg_match(self::DAILYMOTION_EMBEDDED_REGEX, $this->url)) {
            return $this->url;
        } else {
            return $this->getEmbeddedURl();
        }
    }
}
