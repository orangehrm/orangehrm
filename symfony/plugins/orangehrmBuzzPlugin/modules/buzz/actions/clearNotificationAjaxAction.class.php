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
 * Boston, MA 02110-1301, USA
 */

class clearNotificationAjaxAction extends BaseBuzzAction
{
    /**
     * @var BuzzNotificationService|null
     */
    protected $buzzNotificationService = null;

    /**
     * @return BuzzNotificationService
     */
    public function getBuzzNotificationService(): BuzzNotificationService
    {
        if (!($this->buzzNotificationService instanceof BuzzNotificationService)) {
            $this->buzzNotificationService = new BuzzNotificationService();
        }
        return $this->buzzNotificationService;
    }

    /**
     * @param BuzzNotificationService $buzzNotificationService
     */
    public function setBuzzNotificationService(BuzzNotificationService $buzzNotificationService)
    {
        $this->buzzNotificationService = $buzzNotificationService;
    }

    public function execute($request)
    {
        $success = false;
        $message = "Invalid HTTP method";
        if ($request->isMethod(sfRequest::POST)) {
            try {
                $empNumber = $this->getUser()->getEmployeeNumber();

                $buzzNotificationMetadata = $this->getBuzzNotificationService()->getBuzzNotificationMetadata($empNumber);
                if (!$buzzNotificationMetadata instanceof BuzzNotificationMetadata) {
                    $buzzNotificationMetadata = new BuzzNotificationMetadata();
                    $buzzNotificationMetadata->setEmpNumber($empNumber);
                }
                $buzzNotificationMetadata->setLastClearNotifications(date("Y-m-d H:i:s"));
                $this->getBuzzNotificationService()->saveBuzzNotificationMetadata($buzzNotificationMetadata);
                $success = true;
                $message = "Success";
            } catch (Exception $e) {
                $message = $e->getMessage();
            }
        }

        if (!$success) {
            $this->handleBadRequest();
        }
        return $this->renderJson(["success" => $success, "message" => $message]);
    }
}
