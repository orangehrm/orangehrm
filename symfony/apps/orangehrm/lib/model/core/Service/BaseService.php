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
    private function _decorateQuery_SQL($query, array $extensions, array $parameters) {

        if (!empty($extensions['select'])) {
            $select = array();
            foreach ($extensions['select'] as $selectFieldParams) {
                if (!$this->_shouldOmmit($selectFieldParams, $parameters)) {
                    $select[] = $this->_generateSelectField($selectFieldParams);
                }
            }

            if (!empty($select)) {
                $fieldList = implode(', ', $select);
                list($left, $right) = explode(' FROM ', $query, 2);
                $left .= ", {$fieldList}";
                $query = "{$left} FROM {$right}";
            }
        }

        if (!empty($extensions['join'])) {
            $join = '';
            foreach ($extensions['join'] as $joinParams) {
                $joinCondition = "{$joinParams['type']} JOIN {$joinParams['table']}";
                if (isset($joinParams['alias'])) {
                    $joinCondition .= " {$joinParams['alias']}";
                }
                $joinCondition .= " ON {$joinParams['condition']}";
                $join .= ' ' . $joinCondition;
            }

            if (preg_match('/\ (INNER|OUTER|LEFT) JOIN\ /', $query)) {
                $query = preg_replace('/ (INNER|OUTER|LEFT) JOIN /', " {$join} $0", $query, 1);
            } else {
                if (preg_match('/ (WHERE|GROUP\ BY|ORDER\ BY|LIMIT) /', $query)) {
                    $query = preg_replace('/ (WHERE|GROUP\ BY|ORDER\ BY|LIMIT) /', " {$join} $0", $query, 1);
                } else {
                    $query .= ' ' . $join;
                }
            }
        }

        if (!empty($extensions['where'])) {
            $where = array();
            foreach ($extensions['where'] as $whereParams) {
                if (!$this->_shouldOmmit($whereParams, $parameters)) {
                    $whereClausePortion = $this->_generateWhereClause($whereParams);

                    $regExp = '/^\[replace\:.{1,}\]/';
                    if (preg_match($regExp, $whereClausePortion)) {
                        $replacementRegExp = substr($whereClausePortion, 9, strripos($whereClausePortion, ']', 9) - 9);
                        $whereClausePortion = preg_replace($regExp, '', $whereClausePortion);
                        $query = preg_replace($replacementRegExp, '', $query);
                    }
                    $where[] = $whereClausePortion;
                }
            }

            if (!empty($where)) {
                $whereClause = implode(' AND ', $where);
                if (preg_match('/\ WHERE\ /', $query)) {
                    $matchedDelimiter = '';
                    list($left, $matchedDelimiter, $right) = preg_split('/(GROUP\ BY|ORDER\ BY|LIMIT)/', $query, 2, PREG_SPLIT_DELIM_CAPTURE);

                    if (isset($whereParams['position']) && $whereParams['position'] == 'begining') {
                        $left = str_replace('WHERE', 'WHERE ' . $whereClause . ' AND ', $left);
                    } else {
                        $left = rtrim($left) . ' AND ' . $whereClause . ' ';
                    }

                    $query = $left . $matchedDelimiter . $right;
                } else {
                    $query .= ' WHERE ' . $whereClause;
                }
            }
        }

        if (!empty($extensions['orderBy'])) {

            foreach ($extensions['orderBy'] as $orderByParams) {
                $orderByField = "`{$orderByParams['field']}` {$orderByParams['order']}";
                $prependingFields = array();
                $appendingFields = array();

                if (isset($orderByParams['dependsOn'])) {
                    if (!preg_match("/{$orderByParams['dependsOn']}/", $query)) {
                        continue;
                    }
                }

                if (isset($orderByParams['position']) && $orderByParams['position'] == 'before') {
                    $prependingFields[] = $orderByField;
                } else {
                    $appendingFields[] = $orderByField;
                }
            }

            if (!empty($appendingFields) || !empty($prependingFields)) {
                if (preg_match('/\ ORDER\ BY\ /', $query)) {
                    $prependingFields = empty($prependingFields) ? '' : implode(', ', $prependingFields);
                    $appendingFields = empty($appendingFields) ? '' : implode(', ', $appendingFields);

                    $matchedDelimiter = '';
                    list($left, $right) = preg_split('/LIMIT/', $query, 2, PREG_SPLIT_DELIM_CAPTURE);
                    $left .= " {$appendingFields}";
                    $query = "{$left} LIMIT {$right}";

                    $query = str_replace('ORDER BY ', "ORDER BY {$prependingFields}, ", $query);
                } else {
                    $orderFieldList = implode(', ', array_merge($prependingFields, $appendingFields));
                    $query .= ' ORDER BY ' . $orderFieldList;
                }
            }
        }

        $query = $this->_fillPlaceholders($query, $parameters, true);

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

    /**
     *
     * @param string $query
     * @param array $parameters
     * @return string 
     */
    private function _fillPlaceholders($query, $parameters, $fulltext = false) {
        $patterns = array();
        $replacements = array();
        $pdo = Doctrine_Manager::connection()->getDbh();
        foreach ($parameters as $key => $value) {
            $patterns[] = "/\{{$key}\}/";
            $replacement = ($fulltext) ? preg_replace("/\b([a-zA-z_0-9\\-\\+\\*\\#]{3})\b/", "$0_", $value) : $value;
            
            // Escape the replacement and remove the quotes from it, because the quotes will be added in the query_extensions.yml files
            $replacements[] = substr($pdo->quote($replacement), 1, -1);
        }
        return preg_replace($patterns, $replacements, $query);
    }

    private function _generateSelectField($selectFieldParams) {
        $field = null;
        if (is_array($selectFieldParams)) {
            if (array_key_exists('clause', $selectFieldParams)) {
                $field = $selectFieldParams['clause'];
                if (isset($selectFieldParams['alias'])) {
                    $field .= " AS `{$selectFieldParams['alias']}`";
                }
            } else {
                $field = "`{$selectFieldParams['field']}`";
                if (isset($selectFieldParams['alias'])) {
                    $field .= " AS `{$selectFieldParams['alias']}`";
                }
                if (isset($selectFieldParams['table'])) {
                    $field = "{$selectFieldParams['table']}.{$field}";
                }
            }
        } else {
            if (preg_match('/\./', $selectFieldParams)) {
                $field = preg_replace('/\./', '.`', $selectFieldParams) . '`';
            } else {
                $field = "`{$selectFieldParams}`";
            }
        }

        return $field;
    }

    public function _generateWhereClause($whereClauseParams) {
        $whereClause = '';
        if (array_key_exists('clause', $whereClauseParams)) {
            $whereClause = $whereClauseParams['clause'];
        } else {
            $operator = $whereClauseParams['operator'];

            if ($operator == 'IN') {
                $value = "({$whereClauseParams['value']})";
            } else {
                $value = "'{$whereClauseParams['value']}'";
            }

            $table = isset($whereClauseParams['table']) ? "{$whereClauseParams['table']}." : '';

            $whereClause = "{$table}`{$whereClauseParams['field']}` {$operator} {$value}";

            if (array_key_exists('replace', $whereClauseParams)) {
                $whereClause = "[replace:{$whereClauseParams['replace']}]" . $whereClause;
            }
        }

        return $whereClause;
    }

    public function _shouldOmmit($queryParams, $valueParams) {
        $shouldOmmit = false;
        if (isset($queryParams['ommitOnEmptyParams'])) {
            $checkingIndex = $queryParams['ommitOnEmptyParams'];
            $value = isset($valueParams[$checkingIndex]) ? $valueParams[$checkingIndex] : null;
            $shouldOmmit = empty($value);
        }
        return $shouldOmmit;
    }

}
