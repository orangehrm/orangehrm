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
class ohrmWidgetDateRange extends sfWidgetForm implements ohrmEmbeddableWidget {

    private $whereClauseCondition;
    private $id;

    public function configure($options = array(), $attributes = array()) {

        $this->id = $attributes['id'];
        $this->addOption($this->id . '_' . 'from_date', new ohrmWidgetDatePickerNew(array(), array('id' => $this->id . '_' . 'from_date')));
        $this->addOption($this->id . '_' . 'to_date', new ohrmWidgetDatePickerNew(array(), array('id' => $this->id . '_' . 'to_date')));


        $this->addOption('template', 'From &nbsp %from_date% &nbsp&nbsp&nbsp&nbsp&nbsp To &nbsp %to_date%');
    }

    public function render($name, $value = null, $attributes = array(), $errors = array()) {
        $values = array_merge(array('from' => '', 'to' => '', 'is_empty' => ''), is_array($value) ? $value : array());

        return strtr($this->translate($this->getOption('template')), array(
            '%from_date%' => $this->getOption($this->attributes['id'] . '_' . 'from_date')->render($name . '[from]', null, array('id' => $this->attributes['id'] . '_' . 'from_date')),
            '%to_date%' => $this->getOption($this->attributes['id'] . '_' . 'to_date')->render($name . '[to]', null, array('id' => $this->attributes['id'] . '_' . 'to_date')),
        ));
    }

    /**
     * Embeds this widget into the form. Sets label and validator for this widget.
     * @param sfForm $form
     */
    public function embedWidgetIntoForm(sfForm &$form) {


        $widgetSchema = $form->getWidgetSchema();
        $validatorSchema = $form->getValidatorSchema();

        $widgetSchema[$this->attributes['id']] = $this;
        $widgetSchema[$this->attributes['id']]->setLabel(ucwords(str_replace("_", " ", $this->attributes['id'])));
        //$form->setValidator('date_period', new sfValidatorString());

        $validatorSchema[$this->attributes['id']] = new ohrmValidatorDateRange(array(), array("invalid" => "Insert a correct date"));
//        $validatorSchema[$this->attributes['id']] = new sfValidatorPass();
//        $validatorSchema->setPostValidator(new ohrmValidatorSchemaDateRange($this->attributes['id'], ohrmValidatorSchemaDateRange::LESS_THAN_EQUAL, $this->attributes['id'],
//                        array('throw_global_error' => true),
//                        array('invalid' => 'The from date ("%left_field%") must be before the to date ("%right_field%")')
//        ));
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
            $setCondition = $this->whereClauseCondition;
            return $setCondition;
        } else {
            $defaultCondition = "BETWEEN";
            return $defaultCondition;
        }
    }

    /**
     * This method generates the where clause part.
     * @param string $fieldName
     * @param string $value
     * @return string
     */
    public function generateWhereClausePart($fieldName, $dateRanges) {

        $fromDate = "1970-01-01";
        $toDate = date("Y-m-d");

        if (($dateRanges["from"] != "YYYY-MM-DD") && ($dateRanges["to"] != "YYYY-MM-DD")) {

            if (($dateRanges["to"] != "")) {
                $toDate = $dateRanges["to"];
            }
            if (($dateRanges["from"] != "")) {
                $fromDate = $dateRanges["from"];
            }
        } else if (($dateRanges["from"] == "YYYY-MM-DD") && ($dateRanges["to"] != "YYYY-MM-DD")) {
            if (($dateRanges["to"] != "")) {
                $toDate = $dateRanges["to"];
            }
        } else if (($dateRanges["from"] != "YYYY-MM-DD") && ($dateRanges["to"] == "YYYY-MM-DD")) {
            if (($dateRanges["from"] != "")) {
                $fromDate = $dateRanges["from"];
            }
        }

//        print_r($fromDate);
//        print_r($toDate);

        return "( " . $fieldName . " " . $this->getWhereClauseCondition() . " '" . $fromDate . "' AND '" . $toDate . "' )";
    }

}

