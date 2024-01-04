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

class ThemeVariables
{
    private string $primaryColor;
    private string $primaryFontColor;
    private string $secondaryColor;
    private string $secondaryFontColor;
    private string $primaryGradientStartColor;
    private string $primaryGradientEndColor;

    /**
     * @param string $primaryColor
     * @param string $primaryFontColor
     * @param string $secondaryColor
     * @param string $secondaryFontColor
     * @param string $primaryGradientStartColor
     * @param string $primaryGradientEndColor
     */
    public function __construct(
        string $primaryColor,
        string $primaryFontColor,
        string $secondaryColor,
        string $secondaryFontColor,
        string $primaryGradientStartColor,
        string $primaryGradientEndColor
    ) {
        $this->primaryColor = $primaryColor;
        $this->primaryFontColor = $primaryFontColor;
        $this->secondaryColor = $secondaryColor;
        $this->secondaryFontColor = $secondaryFontColor;
        $this->primaryGradientStartColor = $primaryGradientStartColor;
        $this->primaryGradientEndColor = $primaryGradientEndColor;
    }

    /**
     * @return string
     */
    public function getPrimaryColor(): string
    {
        return $this->primaryColor;
    }

    /**
     * @return string
     */
    public function getPrimaryFontColor(): string
    {
        return $this->primaryFontColor;
    }

    /**
     * @return string
     */
    public function getSecondaryColor(): string
    {
        return $this->secondaryColor;
    }

    /**
     * @return string
     */
    public function getSecondaryFontColor(): string
    {
        return $this->secondaryFontColor;
    }

    /**
     * @return string
     */
    public function getPrimaryGradientStartColor(): string
    {
        return $this->primaryGradientStartColor;
    }

    /**
     * @return string
     */
    public function getPrimaryGradientEndColor(): string
    {
        return $this->primaryGradientEndColor;
    }

    /**
     * @param array $array
     * @return self
     */
    public static function createFromArray(array $array): self
    {
        return new self(
            $array['primaryColor'],
            $array['primaryFontColor'],
            $array['secondaryColor'],
            $array['secondaryFontColor'],
            $array['primaryGradientStartColor'],
            $array['primaryGradientEndColor']
        );
    }
}
