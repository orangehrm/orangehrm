<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfValidatorDateRange validates a range of date. It also converts the input values to valid dates.
 *
 * @package    symfony
 * @subpackage validator
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfValidatorDateRange.class.php 15966 2009-03-03 17:29:06Z hartym $
 */
class ohrmValidatorDateRange extends sfValidatorDate {

    /**
     * Configures the current validator.
     *
     * Available options:
     *
     *  * from_date:   The from date validator (required)
     *  * to_date:     The to date validator (required)
     *  * from_field:  The name of the "from" date field (optional, default: from)
     *  * to_field:    The name of the "to" date field (optional, default: to)
     *
     * @param array $options    An array of options
     * @param array $messages   An array of error messages
     *
     * @see sfValidatorBase
     */
    protected function configure($options = array(), $messages = array()) {
        $this->addMessage('invalid', 'The begin date must be before the end date.');

        parent::configure($options, $messages);
    }

    /**
     * @see sfValidatorBase
     */
    protected function doClean($value) {

        $dateValidator = new sfValidatorDate();

        $from = $value["from"];
        $to = $value["to"];


    $value["from"] = $dateValidator->clean(isset($value["from"]) ? $value["from"] : null);
    $value["to"]   = $dateValidator->clean(isset($value["to"]) ? $value["to"] : null);

        if (($from != "YYYY-MM-DD") && ($to != "YYYY-MM-DD")) {


            try {
                parent::doClean($value["from"]);
            } catch (Exception $exc) {
                try {
                    parent::doClean($value["to"]);
                } catch (Exception $exc) {
                    $this->setMessage('invalid', 'Insert valid "from" and "to" date');
                    throw $exc;
                }
                $this->setMessage('invalid', 'Insert a valid "from" date');
                throw $exc;
            }

            try {
                parent::doClean($value["to"]);
            } catch (Exception $exc) {
                $this->setMessage('invalid', 'Insert a valid "to" date');
                throw $exc;
            }
        } else if (($from == "YYYY-MM-DD") && ($to != "YYYY-MM-DD")) {
            $this->setMessage('invalid', 'Insert a valid "to" date');
            parent::doClean($value["to"]);
        } else if (($from != "YYYY-MM-DD") && ($to == "YYYY-MM-DD")) {
            $this->setMessage('invalid', 'Insert a valid "from" date');
            parent::doClean($value["from"]);
        }


        if ($value["from"] && $value["to"]) {
            $v = new ohrmValidatorSchemaDateRange("project_date_range", sfValidatorSchemaCompare::LESS_THAN_EQUAL, "project_date_range", array('throw_global_error' => true), array('invalid' => $this->getMessage('invalid')));
            $v->clean($value);
        }

        return $value;
    }

}

