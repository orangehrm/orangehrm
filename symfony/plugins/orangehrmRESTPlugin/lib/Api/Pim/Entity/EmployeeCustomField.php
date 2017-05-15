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

namespace Orangehrm\Rest\Api\Pim\Entity;

use Orangehrm\Rest\Api\Entity\Serializable;

class EmployeeCustomField implements Serializable
{
    /**
     * @var
     */
    private $id = '';

    private $name = '';

    private $type = '';

    private $screen = '';

    private $value = '';

    Const DROP_DOWN = 'Drop Down';

    Const TEXT_NUMBER = 'Text or Number';


    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getScreen()
    {
        return $this->screen;
    }

    /**
     * @param string $screen
     */
    public function setScreen($screen)
    {
        $this->screen = $screen;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    public function toArray()
    {
        return array(
            'id' => $this->getId(),
            'name' => $this->getName(),
            'type' => $this->getType(),
            'screen' => $this->getScreen(),
            'value' => $this->getValue()
        );
    }

    public function build(\CustomField $field, \Employee $employee)
    {
        $this->setId($field->getId());
        $this->setName($field->getName());
        $this->setScreen($field->getScreen());

        if ($field->getType() == 0) {
            $this->setType(self::TEXT_NUMBER);
        } elseif ($field->getType() == 1) {
            $this->setType(self::DROP_DOWN);
        }
        $this->setValue($this->getEmployeeCustomFieldValue($employee, $field->getId()));
    }

    protected function getEmployeeCustomFieldValue(\Employee $employee, $functionId)
    {
        $function = "getCustom" . $functionId;
        return call_user_func(array($employee, $function));
    }
}