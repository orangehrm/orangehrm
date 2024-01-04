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

namespace OrangeHRM\CorporateBranding\Service;

use ScssPhp\ScssPhp\Compiler;
use ScssPhp\ScssPhp\Exception\SassException;
use ScssPhp\ScssPhp\OutputStyle;

class ScssHelper
{
    /**
     * @return Compiler
     */
    protected function getNewCompiler(): Compiler
    {
        $compiler = new Compiler();
        $compiler->setOutputStyle(OutputStyle::COMPRESSED);
        return $compiler;
    }

    /**
     * @param string $color
     * @param string $amount
     * @return string
     */
    public function darken(string $color, string $amount): string
    {
        $compiler = $this->getNewCompiler();
        $compiler->addVariables([
            'color' => $color,
            'amount' => $amount,
        ]);
        $css = $compiler->compileString('p{color:darken($color, $amount)}')->getCss();
        return str_replace(['p{color:', '}'], '', $css);
    }

    /**
     * @param string $color
     * @param string $amount
     * @return string
     */
    public function lighten(string $color, string $amount): string
    {
        $compiler = $this->getNewCompiler();
        $compiler->addVariables([
            'color' => $color,
            'amount' => $amount,
        ]);
        $css = $compiler->compileString('p{color:lighten($color, $amount)}')->getCss();
        return str_replace(['p{color:', '}'], '', $css);
    }

    /**
     * @param string $color
     * @param float $alpha
     * @return string
     */
    public function rgba(string $color, float $alpha): string
    {
        $compiler = $this->getNewCompiler();
        $compiler->addVariables([
            'color' => $color,
            'alpha' => $alpha,
        ]);
        $css = $compiler->compileString('p{color:rgba($color, $alpha)}')->getCss();
        return str_replace(['p{color:', '}'], '', $css);
    }

    /**
     * @param string $color
     * @return bool
     */
    public function isValidColor(string $color): bool
    {
        try {
            $this->lighten($color, '50%');
            return true;
        } catch (SassException $e) {
            return false;
        }
    }
}
