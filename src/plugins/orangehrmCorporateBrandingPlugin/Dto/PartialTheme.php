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

namespace OrangeHRM\CorporateBranding\Dto;

use OrangeHRM\Entity\Theme;

class PartialTheme
{
    private int $id;

    private string $name;

    private array $variables;

    private bool $showSocialMediaIcons;

    private ?string $clientLogoFilename;

    private ?string $clientBannerFilename;

    private ?string $loginBannerFilename;

    /**
     * @param int $id
     * @param string $name
     * @param array $variables
     * @param bool $showSocialMediaIcons
     * @param string|null $clientLogoFilename
     * @param string|null $clientBannerFilename
     * @param string|null $loginBannerFilename
     */
    public function __construct(
        int $id,
        string $name,
        array $variables,
        bool $showSocialMediaIcons,
        ?string $clientLogoFilename,
        ?string $clientBannerFilename,
        ?string $loginBannerFilename
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->variables = $variables;
        $this->showSocialMediaIcons = $showSocialMediaIcons;
        $this->clientLogoFilename = $clientLogoFilename;
        $this->clientBannerFilename = $clientBannerFilename;
        $this->loginBannerFilename = $loginBannerFilename;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getVariables(): array
    {
        return $this->variables;
    }

    /**
     * @return bool
     */
    public function showSocialMediaIcons(): bool
    {
        return $this->showSocialMediaIcons;
    }

    /**
     * @return string|null
     */
    public function getClientLogoFilename(): ?string
    {
        return $this->clientLogoFilename;
    }

    /**
     * @return string|null
     */
    public function getClientBannerFilename(): ?string
    {
        return $this->clientBannerFilename;
    }

    /**
     * @return string|null
     */
    public function getLoginBannerFilename(): ?string
    {
        return $this->loginBannerFilename;
    }

    /**
     * @param Theme $theme
     * @return self
     */
    public static function createFromTheme(Theme $theme): self
    {
        return new self(
            $theme->getId(),
            $theme->getName(),
            $theme->getVariables(),
            $theme->showSocialMediaIcons(),
            $theme->getClientLogoFilename(),
            $theme->getClientBannerFilename(),
            $theme->getLoginBannerFilename()
        );
    }
}
