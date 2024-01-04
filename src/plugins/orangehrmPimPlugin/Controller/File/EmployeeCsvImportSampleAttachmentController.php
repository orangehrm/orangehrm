<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software: you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with OrangeHRM.
 * If not, see <https://www.gnu.org/licenses/>.
 */

namespace OrangeHRM\Pim\Controller\File;

use OrangeHRM\Core\Controller\AbstractFileController;
use OrangeHRM\Framework\Http\Request;
use OrangeHRM\Framework\Http\Response;

class EmployeeCsvImportSampleAttachmentController extends AbstractFileController
{
    /**
     * @param Request $request
     * @return Response
     */
    public function handle(Request $request): Response
    {
        $response = $this->getResponse();
        $content = "first_name,middle_name,last_name,employee_id,other_id,driver's_license_no,license_expiry_date,gender,marital_status,nationality,date_of_birth,address_street_1,address_street_2,city,state/province,zip/postal_code,country,home_telephone,mobile,work_telephone,work_email,other_email";
        $this->setCommonHeadersToResponse(
            'importData.csv',
            'application/csv',
            strlen($content),
            $response
        );
        $response->setContent($content);
        return $response;
    }
}
