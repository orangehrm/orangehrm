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

namespace OrangeHRM\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="ohrm_theme")
 * @ORM\Entity
 */
class Theme
{
    public const ALLOWED_IMAGE_TYPES = [
        'image/gif',
        'image/jpeg',
        'image/jpg',
        'image/pjpeg',
        'image/png',
        'image/x-png',
        'image/svg+xml',
    ];
    public const ALLOWED_IMAGE_EXTENSIONS = [
        'gif',
        'jpeg',
        'jpg',
        'png',
        'svg',
        'jfif',
    ];
    public const CLIENT_LOGO_ASPECT_RATIO = 1; // 50 x 50
    public const CLIENT_BANNER_ASPECT_RATIO = 91 / 25; // 182 x 50
    public const LOGIN_BANNER_ASPECT_RATIO = 68 / 13; // 340 x 65
    public const IMAGE_ASPECT_RATIO_TOLERANCE = 0.1;

    /**
     * @var int
     *
     * @ORM\Column(name="theme_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @var string
     *
     * @ORM\Column(name="theme_name", type="string", length=100)
     */
    private string $name;

    /**
     * @var array
     *
     * @ORM\Column(name="variables", type="json", nullable=true)
     */
    private array $variables = [];

    /**
     * @var string|resource|null
     *
     * @ORM\Column(name="client_logo", type="blob", nullable=true)
     */
    private $clientLogo = null;

    /**
     * @var string|null
     *
     * @ORM\Column(name="client_logo_filename", type="string", length=100, nullable=true)
     */
    private ?string $clientLogoFilename = null;

    /**
     * @var string|null
     *
     * @ORM\Column(name="client_logo_file_type", type="string", length=100, nullable=true)
     */
    private ?string $clientLogoFileType = null;

    /**
     * @var int|null
     *
     * @ORM\Column(name="client_logo_file_size", type="integer", nullable=true)
     */
    private ?int $clientLogoFileSize = null;

    /**
     * @var string|resource|null
     *
     * @ORM\Column(name="client_banner", type="blob", nullable=true)
     */
    private $clientBanner = null;

    /**
     * @var string|null
     *
     * @ORM\Column(name="client_banner_filename", type="string", length=100, nullable=true)
     */
    private ?string $clientBannerFilename = null;

    /**
     * @var string|null
     *
     * @ORM\Column(name="client_banner_file_type", type="string", length=100, nullable=true)
     */
    private ?string $clientBannerFileType = null;

    /**
     * @var int|null
     *
     * @ORM\Column(name="client_banner_file_size", type="integer", nullable=true)
     */
    private ?int $clientBannerFileSize = null;

    /**
     * @var string|resource|null
     *
     * @ORM\Column(name="login_banner", type="blob", nullable=true)
     */
    private $loginBanner = null;

    /**
     * @var string|null
     *
     * @ORM\Column(name="login_banner_filename", type="string", length=100, nullable=true)
     */
    private ?string $loginBannerFilename = null;

    /**
     * @var string|null
     *
     * @ORM\Column(name="login_banner_file_type", type="string", length=100, nullable=true)
     */
    private ?string $loginBannerFileType = null;

    /**
     * @var int|null
     *
     * @ORM\Column(name="login_banner_file_size", type="integer", nullable=true)
     */
    private ?int $loginBannerFileSize = null;

    /**
     * @var bool
     *
     * @ORM\Column(name="show_social_media_icons", type="boolean", options={"default" : 1})
     */
    private bool $showSocialMediaIcons = true;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return array
     */
    public function getVariables(): array
    {
        return $this->variables;
    }

    /**
     * @param array $variables
     */
    public function setVariables(array $variables): void
    {
        $this->variables = $variables;
    }

    /**
     * @return resource|string|null
     */
    public function getClientLogo()
    {
        return $this->clientLogo;
    }

    /**
     * @param resource|string|null $clientLogo
     */
    public function setClientLogo($clientLogo): void
    {
        $this->clientLogo = $clientLogo;
    }

    /**
     * @return string|null
     */
    public function getClientLogoFilename(): ?string
    {
        return $this->clientLogoFilename;
    }

    /**
     * @param string|null $clientLogoFilename
     */
    public function setClientLogoFilename(?string $clientLogoFilename): void
    {
        $this->clientLogoFilename = $clientLogoFilename;
    }

    /**
     * @return string|null
     */
    public function getClientLogoFileType(): ?string
    {
        return $this->clientLogoFileType;
    }

    /**
     * @param string|null $clientLogoFileType
     */
    public function setClientLogoFileType(?string $clientLogoFileType): void
    {
        $this->clientLogoFileType = $clientLogoFileType;
    }

    /**
     * @return int|null
     */
    public function getClientLogoFileSize(): ?int
    {
        return $this->clientLogoFileSize;
    }

    /**
     * @param int|null $clientLogoFileSize
     */
    public function setClientLogoFileSize(?int $clientLogoFileSize): void
    {
        $this->clientLogoFileSize = $clientLogoFileSize;
    }

    /**
     * @return resource|string|null
     */
    public function getClientBanner()
    {
        return $this->clientBanner;
    }

    /**
     * @param resource|string|null $clientBanner
     */
    public function setClientBanner($clientBanner): void
    {
        $this->clientBanner = $clientBanner;
    }

    /**
     * @return string|null
     */
    public function getClientBannerFilename(): ?string
    {
        return $this->clientBannerFilename;
    }

    /**
     * @param string|null $clientBannerFilename
     */
    public function setClientBannerFilename(?string $clientBannerFilename): void
    {
        $this->clientBannerFilename = $clientBannerFilename;
    }

    /**
     * @return string|null
     */
    public function getClientBannerFileType(): ?string
    {
        return $this->clientBannerFileType;
    }

    /**
     * @param string|null $clientBannerFileType
     */
    public function setClientBannerFileType(?string $clientBannerFileType): void
    {
        $this->clientBannerFileType = $clientBannerFileType;
    }

    /**
     * @return int|null
     */
    public function getClientBannerFileSize(): ?int
    {
        return $this->clientBannerFileSize;
    }

    /**
     * @param int|null $clientBannerFileSize
     */
    public function setClientBannerFileSize(?int $clientBannerFileSize): void
    {
        $this->clientBannerFileSize = $clientBannerFileSize;
    }

    /**
     * @return resource|string|null
     */
    public function getLoginBanner()
    {
        return $this->loginBanner;
    }

    /**
     * @param resource|string|null $loginBanner
     */
    public function setLoginBanner($loginBanner): void
    {
        $this->loginBanner = $loginBanner;
    }

    /**
     * @return string|null
     */
    public function getLoginBannerFilename(): ?string
    {
        return $this->loginBannerFilename;
    }

    /**
     * @param string|null $loginBannerFilename
     */
    public function setLoginBannerFilename(?string $loginBannerFilename): void
    {
        $this->loginBannerFilename = $loginBannerFilename;
    }

    /**
     * @return string|null
     */
    public function getLoginBannerFileType(): ?string
    {
        return $this->loginBannerFileType;
    }

    /**
     * @param string|null $loginBannerFileType
     */
    public function setLoginBannerFileType(?string $loginBannerFileType): void
    {
        $this->loginBannerFileType = $loginBannerFileType;
    }

    /**
     * @return int|null
     */
    public function getLoginBannerFileSize(): ?int
    {
        return $this->loginBannerFileSize;
    }

    /**
     * @param int|null $loginBannerFileSize
     */
    public function setLoginBannerFileSize(?int $loginBannerFileSize): void
    {
        $this->loginBannerFileSize = $loginBannerFileSize;
    }

    /**
     * @return bool
     */
    public function showSocialMediaIcons(): bool
    {
        return $this->showSocialMediaIcons;
    }

    /**
     * @param bool $showSocialMediaIcons
     */
    public function setShowSocialMediaIcons(bool $showSocialMediaIcons): void
    {
        $this->showSocialMediaIcons = $showSocialMediaIcons;
    }
}
