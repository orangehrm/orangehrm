<?php

/*
 * 
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

class BaseService {

    /**
     *
     * @param string $serviceName
     * @param string $methodName
     * @param mixed $query
     * @param mixed $parameters 
     * @return mixed
     * @todo Delegate the operations to a utility class
     */
    public function decorateQuery($serviceName, $methodName, $query, $parameters = array()) {
        $extensions = PluginQueryExtensionManager::instance()->getQueryExtensions($serviceName, $methodName);

        if ($query instanceof Doctrine_Query) {
            return $this->_decorateQuery_DQL($query, $extensions, $parameters);
        } elseif (is_string($query)) {
            return $this->_decorateQuery_SQL($query, $extensions, $parameters);
        } else {
            // TODO: Warn
            return $query;
        }
    }

    /**
     *
     * @param string $query
     * @param array $extensions 
     * @param mixed $parameters
     * @return string SQL query
     */
    private function _decorateQuery_SQL($query, array $extensions, $parameters) {

        if (!empty($extensions['select'])) {
            $select = array();
            foreach ($extensions['select'] as $selectField) {
                $field = null;
                if (is_array($selectField)) {
                    $field = "`{$selectField['field']}`";
                    if (isset($selectField['alias'])) {
                        $field .= " AS `{$selectField['alias']}`";
                    }
                    if (isset($selectField['table'])) {
                        $field = "{$selectField['table']}.{$field}";
                    }
                } else {
                    if (preg_match('/\./', $selectField)) {
                        $field = preg_replace('/\./', '.`', $selectField) . '`';
                    } else {
                        $field = "`{$selectField}`";
                    }
                }
                $select[] = $field;
            }
            $fieldList = implode(', ', $select);

            list($left, $right) = explode(' FROM ', $query, 2);
            $left .= ", {$fieldList}";
            $query = "{$left} FROM {$right}";
        }

        if (!empty($extensions['where'])) {
            $where = array();
            foreach ($extensions['where'] as $whereParams) {
                $value = "'{$whereParams['value']}'";
                $whereClause = "`{$whereParams['field']}` {$whereParams['operator']} {$value}";

                $where[] = $whereClause;
            }

            $whereClause = implode(' AND ', $where);
            if (preg_match('/\ WHERE\ /', $query)) {
                $matchedDelimiter = '';
                list($left, $matchedDelimiter, $right) = preg_split('/(GROUP\ BY|ORDER\ BY|LIMIT)/', $query, 2, PREG_SPLIT_DELIM_CAPTURE);
                echo $more;
                $left = rtrim($left) . ' AND ' . $whereClause . ' ';
                $query = $left . $matchedDelimiter . $right;
            } else {
                $query .= ' WHERE ' . $whereClause;
            }
        }

        if (!empty($extensions['orderBy'])) {

            $orderFieldList = '`' . implode('`, `', $extensions['orderBy']) . '`';

            if (preg_match('/\ ORDER\ BY\ /', $query)) {
                $matchedDelimiter = '';
                list($left, $matchedDelimiter, $right) = preg_split('/LIMIT/', $query, 2, PREG_SPLIT_DELIM_CAPTURE);
                $left .= ", {$orderFieldList}";
                $query = "{$left} {$matchedDelimiter} {$right}";
            } else {
                $query .= ' ORDER BY ' . $orderFieldList;
            }
        }

        return trim($query);
    }

    /**
     * @todo Implement this method
     * 
     * @param Doctrine_Query $query
     * @param array $extensions 
     * @param mixed $parameters
     * @return Doctrine_Query
     */
    private function _decorateQuery_DQL(Doctrine_Query $query, array $extensions, $parameters) {
        return $query;
    }

}