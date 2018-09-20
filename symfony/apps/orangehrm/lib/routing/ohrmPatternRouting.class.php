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
 * Class ohrmPatternRouting
 */
class ohrmPatternRouting extends sfPatternRouting
{

    /**
     * This is overridden method of sfPatternRouting class
     * @param $url
     * @return array|bool
     * @throws sfException
     */
    protected function getRouteThatMatchesUrl($url)
    {

        /* Allow if only one match `.html` or `.rss` pattern in the URL. */
        if (
            (preg_match_all("/\.html/i", $url) == 1 || preg_match_all("/\.rss/i", $url) == 1) &&
            preg_match_all("/(\.)+/", $url) == 1
        ) {

            /* Deny if there any characters after `.html` and `.rss`. */
            if (preg_match("/\.html(.)+/i", $url) || preg_match("/\.rss(.)+/i", $url)) {
                return false;
            }
        }

        /* Deny any URL pattern comes with `.` or ends with '~'. */
        elseif (preg_match("/(.)*\.(.)*/", $url) || preg_match("/((.)*~)$/", $url)) {

            /* Only allow `.` for floating point numbers in the URL. */
            if (!preg_match("/[0-9]+\.[0-9]+/", $url)) {
                return false;
            }
        }

        /* Check each and every routes which is match to particular URL in the `routing.yml` file. */
        foreach ($this->routes as $name => $route) {
            $route = $this->getRoute($name);
            $parameters = $route->matchesUrl($url, $this->options['context']);

            if (false === $parameters) {
                continue;
            }

            /* Action name should be alphabetic characters. */
            if (!preg_match("/^([a-z,A-Z]+)$/", $parameters['action'])) {
                return false;
            }

            /* Module name should be alphabetic characters or alphanumeric characters started with `api`. */
            if (
                !preg_match("/^([a-z,A-Z]+)$/", $parameters['module']) &&
                !preg_match("/^api([a-z,A-Z,0-9]+)$/", $parameters['module'])
            ) {
                return false;
            }

            return array('name' => $name, 'pattern' => $route->getPattern(), 'parameters' => $parameters);
        }

        return false;
    }

}
