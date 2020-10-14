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

class TextareaCell extends Cell
{
    /**
     * @return string
     */
    public function __toString()
    {
        $readOnly = $this->getPropertyValue('readOnly', false);

        if (($readOnly instanceof sfOutputEscaperArrayDecorator) || is_array($readOnly)) {
            list($method, $params) = $readOnly;
            $readOnly = call_user_func_array([$this->dataObject, $method], $params->getRawValue());
        }

        $placeholderGetters = $this->getPropertyValue('placeholderGetters', []);
        $id = $this->generateAttributeValue($placeholderGetters, $this->getPropertyValue('id'));
        $name = $this->generateAttributeValue($placeholderGetters, $this->getPropertyValue('name'));
        $default = [];
        if (!empty($id)) {
            $default['id'] = $id;
        }
        if (!empty($name)) {
            $default['name'] = $name;
        }
        $props = $this->getPropertyValue('props', []);
        if ($props instanceof sfOutputEscaperArrayDecorator) {
            $props = $props->getRawValue();
        }

        return $readOnly ? $this->getValue() : content_tag(
                'textarea',
                $this->getValue(),
                array_merge(
                    $default,
                    $props
                )
            ) . $this->getHiddenFieldHTML();
    }
}
