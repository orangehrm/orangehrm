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
 * Custom execution filter that includes form js and css files in the request
 */
class orangehrmExecutionFilter extends sfExecutionFilter {

    /**
     * Executes the execute method of an action.
     *
     * @param sfAction $actionInstance An sfAction instance
     *
     * @return string The view type
     */
    protected function executeAction($actionInstance) {
        // execute the action
        $viewName = parent::executeAction($actionInstance);

        // Add form js and stylesheets to response
        if ($viewName != sfView::NONE) {

            $response = $actionInstance->getResponse();

            $actionVars = $actionInstance->getVarHolder()->getAll();
            foreach ($actionVars as $var) {
                if ($var instanceof sfForm) {

                    foreach ($var->getStylesheets() as $file => $media) {
                        $response->addStylesheet($file, '', array('media' => $media));
                    }
                    foreach ($var->getJavascripts() as $file) {
                        $response->addJavascript($file);
                    }
                }
            }
        }

        return $viewName;
    }

}
