<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com

 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.

 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTabILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA 
 */

require_once ROOT_PATH . '/lib/models/report/ReportModuleObject.php';

class ReportField extends ReportModuleObject {
    const SINGLE_VALUE = 'single-value';
    const COMPOSITE_VALUE = 'composite-value';
    const DIRECT_QUERY = 'direct-query';
    const SINGLE_REFERENCE = 'single-reference';
    const MULTIPLE_REFERENCE = 'multi-reference';

    const COMPOSITE_SEPARATOR = ', ';
    const GROUP_SEPARATOR = '[:]';
    const EMPTY_MARKER = '―';
    const LEFT_TABLE = 'hs_hr_employee';

    protected $dataValues = array (
        'type' => null,
        'table' => null,
        'field' => null,
        'pk' => null,
        'fk' => null,
		'ternaryTable' => null,
    );

    public function __construct($field, $type = self :: SINGLE_VALUE, $table = null, $pk = null, $fk = null, $ternaryTable = null) {
        $this->dataValues = array (
            'type' => $type,
            'table' => $table,
            'field' => $field,
            'pk' => $pk,
            'fk' => $fk,
			'ternaryTable' => $ternaryTable,
        );
    }

    public function __toString() {
        return $this->dataValues['field'];
    }
}
