<?php
/**
 * OrangeHRM Enterprise is a closed sourced comprehensive Human Resource Management (HRM)
 * System that captures all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM Inc is the owner of the patent, copyright, trade secrets, trademarks and any
 * other intellectual property rights which subsist in the Licensed Materials. OrangeHRM Inc
 * is the owner of the media / downloaded OrangeHRM Enterprise software files on which the
 * Licensed Materials are received. Title to the Licensed Materials and media shall remain
 * vested in OrangeHRM Inc. For the avoidance of doubt title and all intellectual property
 * rights to any design, new software, new protocol, new interface, enhancement, update,
 * derivative works, revised screen text or any other items that OrangeHRM Inc creates for
 * Customer shall remain vested in OrangeHRM Inc. Any rights not expressly granted herein are
 * reserved to OrangeHRM Inc.
 *
 * Please refer http://www.orangehrm.com/Files/OrangeHRM_Commercial_License.pdf for the license which includes terms and conditions on using this software.
 *
 */

/**
 * Class FormatWithPayGradeId
 */
class FormatWithPayPeriodId implements ValueFormatter
{

    const NO_PAY_GRADE = 'No Pay Grade Details';

    /**
     * @param $entityValue
     * @return mixed|string
     */
    public function getFormattedValue($entityValue)
    {
        $payPeriods = Doctrine::getTable('Payperiod')->findAll();
        if ($payPeriods[$entityValue - 1]->getName()) {
            return $payPeriods[$entityValue - 1]->getName();
        } else {
            return self::NO_PAY_GRADE;
        }

    }
}
