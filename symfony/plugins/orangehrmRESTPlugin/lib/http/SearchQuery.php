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
namespace Orangehrm\Rest\http;

class SearchQuery
{

    /**
     * OPERATORS
     */

    const EQUAL_OPT ="==";
    const GREATER_THAN_OPT =">";
    const LESS_THAN_OPT ="<";


    /**
     * Employee Constants
     */

    const EMPLOYEE_ID = "empId";
    const EMPLOYEE_FIRST_NAME ="empFirstName";
    const EMPLOYEE_LAST_NAME ="empLastName";
    const EMPLOYEE_MIDDLE_NAME ="empMiddleName";
    const EMPLOYEE_AGE ="empAge";


    /**
     * Actions
     */

    const GET_EMPLOYEE_DETAILS = "getEmployeeDetails";
    const GET_EMPLOYEE_DEPENDANTS = "getEmployeeDependants";




    /**
     * Getting Employee search parameters
     *
     * @return array
     */
    public function getEmployeeSearchParams($request){

        $empSearchParams = array();

        if($request->getActionRequest() != null){

            $searchString = $request->getActionRequest()->getParameter('search');
            $parametersList = explode(";",$searchString);
            $empFirstName = explode("==", $parametersList[0]);
            $empSearchParams[$this::EMPLOYEE_FIRST_NAME]  = $empFirstName[1];

        }
        return $empSearchParams;

    }

    /**
     * Getting Employee dependants
     *
     * @return array
     */
    public function getEmployeeDependantsParams($request){

        $empSearchParams = array();

        if($request->getActionRequest() != null){
            $action = $request->getActionRequest()->getParameter('action');
            if($action == "getEmployeeDependants"){
                $empSearchParams[SearchQuery::EMPLOYEE_ID] =  $request->getActionRequest()->getParameter('emp_number');
            }

        }
        return $empSearchParams;

    }

    /**
     * Extract Search Parameters
     *
     * @param $request
     * @return array
     */
    public function getSearchParams($request)
    {
        $searchParams = array();
        $action = $request->getActionRequest()->getParameter('action');

        if($action == SearchQuery::GET_EMPLOYEE_DETAILS){

            if ($request->getActionRequest() != null) {
                $searchParams[SearchQuery::EMPLOYEE_ID] = $request->getActionRequest()->getParameter('id');

            }
        }
        else if($action == SearchQuery::GET_EMPLOYEE_DEPENDANTS){

            if ($request->getActionRequest() != null) {

                $searchParams[SearchQuery::EMPLOYEE_ID] =  $request->getActionRequest()->getParameter('id');

            }
        }

        return $searchParams;

    }



}