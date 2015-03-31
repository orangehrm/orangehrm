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
 *
 */

/**
 * Description of getLeaveCommentAjaxActin
 */
class getLeaveCommentsAjaxAction extends baseCoreLeaveAction {

    public function execute($request) {
        
        sfConfig::set('sf_web_debug', false);
        sfConfig::set('sf_debug', false);
       
        $leaveId = $request->getParameter("leaveId");
        $leaveRequestId = $request->getParameter('leaveRequestId');
        
        $comments = array();
        $leaveRequestService = $this->getLeaveRequestService();
        
        if (!empty($leaveRequestId)) {
            $comments = $leaveRequestService->getLeaveRequestComments($leaveRequestId);
        } else if (!empty($leaveId)) {
            $comments = $leaveRequestService->getLeaveComments($leaveId);
        }        

        
        $returnData = array();
        
        if (count($comments) > 0) {
            
            foreach ($comments as $comment) {
                $commentDate = new DateTime($comment->getCreated());
                $row['date'] = set_datepicker_date_format($commentDate->format('Y-m-d'));
                $row['time'] = $commentDate->format('H:i');
                $row['author'] = $comment->getCreatedByName();
                $row['comments'] = $comment->getComments();
                
                $returnData[] = $row;
            }
        }
        
        
        $response = $this->getResponse();
        $response->setHttpHeader('Expires', '0');
        $response->setHttpHeader("Cache-Control", "must-revalidate, post-check=0, pre-check=0, max-age=0");
        $response->setHttpHeader("Cache-Control", "private", false);
        
        return $this->renderText(json_encode($returnData));
    }    
}
