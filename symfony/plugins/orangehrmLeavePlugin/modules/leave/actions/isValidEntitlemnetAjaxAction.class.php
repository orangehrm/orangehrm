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
class isValidEntitlemnetAjaxAction extends sfAction {
	
	/**
	 *
	 * @param <type> $request
	 * @return <type>
	 */
	public function execute($request) {
        
         sfConfig::set('sf_web_debug', false);
        sfConfig::set('sf_debug', false);
        
       $isValidEntitlement = true;

		if ($this->getRequest()->isXmlHttpRequest()) {
			$this->getResponse()->setHttpHeader('Content-Type', 'application/json; charset=utf-8');
		}
        $id                 = $request->getParameter('id');
       
        if( $id > 0){
            $entitlementValue = $request->getParameter('entitlements');
          
            $entitlmentService = new LeaveEntitlementService();
             $entitlment = $entitlmentService->getLeaveEntitlement( $id );
             if( $entitlment->getDaysUsed() > $entitlementValue['entitlement']){
                 $isValidEntitlement = false;
             }
        }


        $response = $this->getResponse();
        $response->setHttpHeader('Expires', '0');
        $response->setHttpHeader("Cache-Control", "must-revalidate, post-check=0, pre-check=0, max-age=0");
        $response->setHttpHeader("Cache-Control", "private", false);

        
        return $this->renderText(json_encode($isValidEntitlement))  ;
        
        

		
	}

}

?>
