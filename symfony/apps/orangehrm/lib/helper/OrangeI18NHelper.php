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
 *
 */

/**
 * Refer symfony/lib/vendor/symfony/lib/helper/I18NHelper.php
 * @param $text
 * @param array $args
 * @param string $catalogue
 * @return string
 * @throws sfConfigurationException
 * @throws sfException
 */
function __($text, $args = array(), $catalogue = 'messages')
{
    if (sfConfig::get('sf_i18n')) {
        return sfContext::getInstance()->getI18N()->__($text, $args, $catalogue);
    } else {
        if (empty($args)) {
            $args = array();
        }

        // replace object with strings
        foreach ($args as $key => $value) {
            if (is_object($value) && method_exists($value, '__toString')) {
                $args[$key] = $value->__toString();
            }
        }

        return strtr($text, $args);
    }
}
