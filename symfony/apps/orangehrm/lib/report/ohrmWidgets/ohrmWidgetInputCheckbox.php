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
class ohrmWidgetInputCheckbox extends sfWidgetFormInputCheckbox implements ohrmEmbeddableWidget {

    protected $whereClauseCondition;

    public function configure($options = array(), $attributes = array()) {
        parent::configure($options, $attributes);
    }

    /**
     * Embeds this widget into the form.
     * @param sfForm $form
     */
    public function embedWidgetIntoForm(sfForm &$form) {

        $widgetSchema = $form->getWidgetSchema();
        $widgetSchema[$this->attributes['id']] = $this;
        $label = ucwords(str_replace("_", " ", $this->attributes['id']));
        $widgetSchema[$this->attributes['id']]->setLabel($label);
        $form->setValidator($this->attributes['id'], new sfValidatorPass());
    }

    /**
     * Sets whereClauseCondition.
     * @param string $condition
     */
    public function setWhereClauseCondition($condition) {

        $this->whereClauseCondition = $condition;
    }

    /**
     * Gets whereClauseCondition. ( if whereClauseCondition is set returns that, else returns default condition )
     * @return string ( a condition )
     */
    public function getWhereClauseCondition() {

        if (isset($this->whereClauseCondition)) {
            return $this->whereClauseCondition;
        } else {
            return "=";
        }
    }

    /**
     * This method generates the where clause part. ( returns null if checkbox is on, otherwise returns where cluase part )
     * @param string $fieldName
     * @param string $value
     * @return string or null
     */
    public function generateWhereClausePart($fieldName, $value) {

        if ($value == 'on') {
            return null;
        } else {

            return $fieldName . " " . $this->getWhereClauseCondition() . " " . "0";
        }
    }

}