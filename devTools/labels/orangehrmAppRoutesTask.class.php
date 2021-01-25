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

class orangehrmAppRoutesTask extends sfBaseTask {

    protected
        $routes = array();

    /**
     * @see sfTask
     */
    protected function configure() {
        $this->addArguments(array(
            new sfCommandArgument('application', sfCommandArgument::OPTIONAL, 'The application name', 'orangehrm'),
        ));

        $this->namespace = 'orangehrm';
        $this->name = 'routes-with-labels';
        $this->briefDescription = 'Displays current routes with labels for a given application';

        $this->detailedDescription = <<<EOF
The [orangehrm:routes-with-labels|INFO] displays the current routes with labels for an application:

  [./symfony orangehrm:routes-with-labels]
EOF;
    }

    /**
     * Executes the current task.
     *
     * @param array $arguments An array of arguments
     * @param array $options An array of options
     *
     * @return integer 0 if everything went fine, or an error code
     */
    protected function execute($arguments = array(), $options = array()) {
        $this->routes = $this->getRouting()->getRoutes();

        // display
        $this->outputRoutes($arguments['application']);
    }

    protected function outputRoutes($application) {
        foreach ($this->routes as $name => $route) {
            if(
                $route->getDefaults()['module'] == 'api' ||
                is_null($route->getDefaults()['module']) ||
                preg_match('/Ajax$/', $route->getDefaults()['action'])
            ) {
                unset($this->routes[$name]);
                continue;
            }
        }

        $allRoutes = array();
        foreach ($this->routes as $name => $route) {
            $label = $route->getDefaults()['module']."_".$route->getDefaults()['action'];
            $allRoutes[] = array(
                'State_Name' => $name,
                'Label' => $label,
                'Url_Pattern' => $route->getPattern()
            );
        }
        $this->log(json_encode($allRoutes, JSON_PRETTY_PRINT));
    }
}
