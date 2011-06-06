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

/**
 * getAvailableCurrenciesJsonAction action
 */
class getAvailableCurrenciesJsonAction extends basePimAction {

    private $currencyService;
    

    /**
     * Get CurrencyService
     * @returns CurrencyService
     */
    public function getCurrencyService() {
        if(is_null($this->currencyService)) {
            $this->currencyService = new CurrencyService();
        }
        return $this->currencyService;
    }

    /**
     * Set CurrencyService
     * @param CurrencyService $currencyService
     */
    public function setCurrencyService(CurrencyService $currencyService) {
        $this->currencyService = $currencyService;
    }
    
    /**
     * List unassigned currencies for given employee and pay grade
     * @param sfWebRequest $request
     * @return void
     */
    public function execute($request) {
       $this->setLayout(false);
       sfConfig::set('sf_web_debug', false);
       sfConfig::set('sf_debug', false);

       $currencies = array();

       if ($this->getRequest()->isXmlHttpRequest()) {
           $this->getResponse()->setHttpHeader('Content-Type','application/json; charset=utf-8');
       }

       $payGrade = $request->getParameter('paygrade');
       $empNumber = $request->getParameter('empNumber');

       if (!empty($payGrade) && !empty($empNumber)) {

           $employeeService = $this->getEmployeeService();

           // TODO: call method that returns data in array format (or pass parameter)
           $currencies = $employeeService->getUnAssignedCurrencyList($empNumber, $payGrade, true);
       } else {
           
           // 
           // Return full currency list
           //
           $currencyService = $this->getCurrencyService();
           $currencies = $currencyService->getCurrencyList(true);           
       }

       return $this->renderText(json_encode($currencies));
    }

}