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


class Sass
{
    /**
     * @var ScssPhp\ScssPhp\Compiler
     */
    private $scss = null;

    /**
     * @var ScssPhp\ScssPhp\Compiler
     */
    private static $instance = null;


    /**
     * @var string
     */
    private $sass_path = '';

    public static function instance() {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->sass_path = sfConfig::get('sf_plugins_dir').'/orangehrmCorporateBrandingPlugin/web/sass';

    }

    /**
     * Init Scss compiler
     * @return ScssPhp\ScssPhp\Compiler
     */
    private function getSCSSCompiler() {
        if($this->scss == null) {
            $this->scss = new ScssPhp\ScssPhp\Compiler();
            $this->scss->setImportPaths($this->sass_path);
        }
        return $this->scss;
    }

    /**
     * @return string
     */
    public function compileSCSS(array $variables) {
        $this->getSCSSCompiler()->setVariables($variables);
        return $this->getSCSSCompiler()->compile(file_get_contents($this->sass_path . '/main.scss'));
    }

    /**
     * @return string
     */
    public function compileLoginSCSS(array $variables) {
        $this->getSCSSCompiler()->setVariables($variables);
        return $this->getSCSSCompiler()->compile(file_get_contents($this->sass_path . '/login.scss'));
    }



}
