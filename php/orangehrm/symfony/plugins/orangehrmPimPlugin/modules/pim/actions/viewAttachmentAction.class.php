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
 * Actions class for PIM module viewAttachment
 */

class viewAttachmentAction extends basePimAction {

    /**
     * Add / update employee customFields
     *
     * @param int $empNumber Employee number
     *
     * @return boolean true if successfully assigned, false otherwise
     */
    public function execute($request) {
        
        // this should probably be kept in session?
        $screen = $request->getParameter('screen');

        $empNumber = $request->getParameter('empNumber');
        $attachId = $request->getParameter('attachId');

        $employeeService = $this->getEmployeeService();
        $attachment = $employeeService->getAttachment($empNumber, $attachId);

        $response = $this->getResponse();

        if (!empty($attachment)) {
            $contents = $attachment->attachment;
            $contentType = $attachment->file_type;
            $fileName = $attachment->filename;
            $fileLength = $attachment->size;

            //$response->addCacheControlHttpHeader('no-cache');

            $response->setHttpHeader('Pragma', 'public');
            //$response->setContentType($contentType);

            $response->setHttpHeader('Expires', '0');
            $response->setHttpHeader("Cache-Control", "must-revalidate, post-check=0, pre-check=0");
            $response->setHttpHeader("Cache-Control", "private", false);
            $response->setHttpHeader("Content-Type", $contentType);
            $response->setHttpHeader("Content-Disposition", 'attachment; filename="' . $fileName . '";');
            $response->setHttpHeader("Content-Transfer-Encoding", "binary");
            $response->setHttpHeader("Content-Length", $fileLength);

            $response->setContent($contents);
            $response->send();
        } else {
            $response->setStatusCode(404, 'This attachment does not exist');
        }

        return sfView::NONE;
    }

}
