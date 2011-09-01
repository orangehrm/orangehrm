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
        $this->addMessage('invalid', 'From date should be before to date.');

        $this->addOption('required', false);
        $this->addMessage('bad_format', '"%value%" does not match the date format ( yyyy-mm-dd).');
        $this->addMessage('max', 'The date must be before %max%.');
        $this->addMessage('min', 'The date must be after %min%.');

        $this->addOption('date_format', "/^(\d\d\d\d)-(\d\d?)-(\d\d?)$/");
        $this->addOption('with_time', false);
        $this->addOption('date_output', 'Y-m-d');
        $this->addOption('datetime_output', 'Y-m-d H:i:s');
        $this->addOption('date_format_error');
        $this->addOption('min', null);
        $this->addOption('max', null);
        $this->addOption('date_format_range_error', 'd/m/Y H:i:s');
//        parent::configure($options, $messages);
    }

    /**
     * @see sfValidatorBase
     */
    protected function doClean($value) {

        $dateValidator = new sfValidatorDate();

        $from = $value["from"];
        $to = $value["to"];
//    $value["from"] = $dateValidator->clean(isset($value["from"]) ? $value["from"] : null);
//    $value["to"]   = $dateValidator->clean(isset($value["to"]) ? $value["to"] : null);

        if (($from != "YYYY-MM-DD") && ($to != "YYYY-MM-DD")) {
            try {
                parent::doClean($value["from"]);
            } catch (Exception $exc) {
                try {
                    parent::doClean($value["to"]);
                } catch (Exception $exc) {
                    $this->setMessage('invalid', 'Insert valid "from" and "to" date');

                    $this->setMessage("bad_format", "From date and To date values do not match the date format ( yyyy-mm-dd).");
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
            if ($to != "") {
                $this->setMessage('invalid', 'Insert a valid "to" date');
                $this->setMessage("bad_format", "To date value does not match the date format ( yyyy-mm-dd).");
                parent::doClean($value["to"]);
            }
        } else if (($from != "YYYY-MM-DD") && ($to == "YYYY-MM-DD")) {
            if ($from != "") {
                $this->setMessage('invalid', 'Insert a valid "from" date');
                $this->setMessage("bad_format", "From date value does not match the date format ( yyyy-mm-dd).");
                parent::doClean($value["from"]);
            }
        } else if (($from == "YYYY-MM-DD") && ($to == "YYYY-MM-DD")) {
            return $value;
        }

        if ($value["from"] && $value["to"]) {
            $this->setMessage('invalid', 'From date should be before to date.');
            $v = new ohrmValidatorSchemaDateRange("project_date_range", sfValidatorSchemaCompare::LESS_THAN_EQUAL, "project_date_range", array('throw_global_error' => true), array('invalid' => $this->getMessage('invalid')));
            $v->clean($value);
        }

        return $value;
    }

}

